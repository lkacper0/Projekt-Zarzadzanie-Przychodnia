<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Specialization;
use App\Models\DoctorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some specializations
        Specialization::create(['name' => 'Kardiologia']);
        Specialization::create(['name' => 'Pediatria']);
    }

    public function test_patient_can_login_and_redirects_to_patient_panel(): void
    {
        $patient = User::create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'pacjent@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'pesel' => '90010112345',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'pacjent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => '/PanelUzytkownika'
            ]);

        $this->assertAuthenticatedAs($patient);
    }

    public function test_doctor_can_login_and_redirects_to_doctor_panel(): void
    {
        $doctor = User::create([
            'first_name' => 'Adam',
            'last_name' => 'Lekarski',
            'email' => 'lekarz@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'doctor',
            'pesel' => '90010112346',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'user_id' => $doctor->id,
            'bio' => 'Bio',
            'is_accepted' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'lekarz@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => '/PanelLekarza'
            ]);

        $this->assertAuthenticatedAs($doctor);
    }

    public function test_admin_can_login_and_redirects_to_admin_panel(): void
    {
        $admin = User::create([
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'admin',
            'pesel' => '90010112347',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => '/admin'
            ]);

        $this->assertAuthenticatedAs($admin);
    }

    public function test_doctor_candidate_stays_on_patient_panel_while_pending(): void
    {
        $candidate = User::create([
            'first_name' => 'Marta',
            'last_name' => 'Kandydatka',
            'email' => 'kandydat@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'patient', // role is patient
            'pesel' => '90010112348',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'user_id' => $candidate->id,
            'bio' => 'Bio',
            'is_accepted' => false, // pending approval
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'kandydat@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => '/PanelUzytkownika' // redirected to patient panel
            ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'first_name' => 'Zablokowany',
            'last_name' => 'Kowalski',
            'email' => 'banned@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'pesel' => '90010112349',
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'banned@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Twoje konto zostało zablokowane przez administratora.'
            ]);

        $this->assertGuest();
    }

    public function test_can_register_as_patient_with_required_pesel(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'henryk@example.com',
            'pesel' => '12345678901',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect' => '/PanelUzytkownika'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'henryk@example.com',
            'role' => 'patient',
            'pesel' => '12345678901'
        ]);
    }

    public function test_cannot_register_without_pesel(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'henryk@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_patient_can_submit_doctor_application_from_panel(): void
    {
        $patient = User::create([
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'henryk@example.com',
            'pesel' => '12345678901',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $spec = Specialization::where('name', 'Pediatria')->first();

        // Login the user to simulate session
        $this->actingAs($patient);

        $response = $this->postJson('/PanelUzytkownika/aplikuj', [
            'bio' => 'Chcę leczyć dzieci w przychodni.',
            'specialization' => $spec->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Twoje podanie o konto lekarza zostało złożone pomyślnie!'
            ]);

        $this->assertDatabaseHas('doctor_profiles', [
            'user_id' => $patient->id,
            'bio' => 'Chcę leczyć dzieci w przychodni.',
            'is_accepted' => false,
        ]);
    }

    public function test_patient_can_search_and_filter_doctors(): void
    {
        $patient = User::create([
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'patient@example.com',
            'pesel' => '12345678901',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $doctorUser1 = User::create([
            'first_name' => 'Kamil',
            'last_name' => 'Kardiolog',
            'email' => 'kamil@example.com',
            'pesel' => '12345678902',
            'password_hash' => Hash::make('password'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $docProfile1 = DoctorProfile::create([
            'user_id' => $doctorUser1->id,
            'bio' => 'Lekarz serca.',
            'is_accepted' => true,
            'avg_rating' => 5.0,
        ]);
        $specKardio = Specialization::where('name', 'Kardiologia')->first();
        $docProfile1->specializations()->attach($specKardio->id);

        $doctorUser2 = User::create([
            'first_name' => 'Patryk',
            'last_name' => 'Pediatra',
            'email' => 'patryk@example.com',
            'pesel' => '12345678903',
            'password_hash' => Hash::make('password'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $docProfile2 = DoctorProfile::create([
            'user_id' => $doctorUser2->id,
            'bio' => 'Leczę dzieci.',
            'is_accepted' => true,
            'avg_rating' => 4.0,
        ]);
        $specPedia = Specialization::where('name', 'Pediatria')->first();
        $docProfile2->specializations()->attach($specPedia->id);

        $this->actingAs($patient);

        // Filter by specialization
        $response = $this->get('/Lekarze?specialization=' . $specKardio->id);
        $response->assertStatus(200);
        $response->assertSee('dr Kamil Kardiolog');
        $response->assertDontSee('dr Patryk Pediatra');

        // Filter by text search
        $response = $this->get('/Lekarze?search=dzieci');
        $response->assertStatus(200);
        $response->assertSee('dr Patryk Pediatra');
        $response->assertDontSee('dr Kamil Kardiolog');
    }

    public function test_patient_can_update_profile(): void
    {
        $patient = User::create([
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'henryk@example.com',
            'pesel' => '12345678901',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $this->actingAs($patient);

        $response = $this->post('/PanelUzytkownika/edycja', [
            'first_name' => 'Henryk Nowy',
            'last_name' => 'Medyczny',
            'email' => 'henryk_nowy@example.com',
            'pesel' => '12345678901',
        ]);

        $response->assertRedirect('/PanelUzytkownika');
        
        $this->assertDatabaseHas('users', [
            'id' => $patient->id,
            'first_name' => 'Henryk Nowy',
            'email' => 'henryk_nowy@example.com',
        ]);
    }

    public function test_patient_cannot_update_profile_with_duplicate_email(): void
    {
        $patient1 = User::create([
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'henryk@example.com',
            'pesel' => '12345678901',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        User::create([
            'first_name' => 'Zajety',
            'last_name' => 'Kowalski',
            'email' => 'zajety@example.com',
            'pesel' => '12345678902',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $this->actingAs($patient1);

        $response = $this->post('/PanelUzytkownika/edycja', [
            'first_name' => 'Henryk',
            'last_name' => 'Medyczny',
            'email' => 'zajety@example.com',
            'pesel' => '12345678901',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}

