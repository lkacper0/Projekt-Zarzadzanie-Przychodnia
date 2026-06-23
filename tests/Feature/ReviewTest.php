<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Specialization;
use App\Models\DoctorProfile;
use App\Models\AvailabilitySlot;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctorUser;
    private $doctorProfile;
    private $service;
    private $slot;

    protected function setUp(): void
    {
        parent::setUp();

        Specialization::create(['name' => 'Kardiologia']);

        $this->patient = User::create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'pacjent@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'pesel' => '90010112345',
            'is_active' => true,
        ]);

        $this->doctorUser = User::create([
            'first_name' => 'Adam',
            'last_name' => 'Lekarski',
            'email' => 'lekarz@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'doctor',
            'pesel' => '90010112346',
            'is_active' => true,
        ]);

        $this->doctorProfile = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'bio' => 'Lekarz serc.',
            'is_accepted' => true,
            'avg_rating' => 0.00,
        ]);

        $this->service = Service::create([
            'doctor_id' => $this->doctorProfile->id,
            'name' => 'Ekg',
            'price' => 120.00,
            'duration_minutes' => 30,
        ]);

        $this->slot = AvailabilitySlot::create([
            'doctor_id' => $this->doctorProfile->id,
            'start_time' => now()->subDay(),
            'end_time' => now()->subDay()->addMinutes(30),
            'is_booked' => true,
        ]);
    }

    public function test_guest_cannot_access_review_form(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        $response = $this->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertRedirect('/login');
    }

    public function test_doctor_cannot_access_review_form(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->doctorUser)->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertStatus(403);
    }

    public function test_patient_cannot_review_non_completed_visit(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->patient)->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertStatus(404);
    }

    public function test_patient_cannot_review_someone_elses_visit(): void
    {
        $otherPatient = User::create([
            'first_name' => 'Kamil',
            'last_name' => 'Inny',
            'email' => 'inny@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'patient',
            'pesel' => '90010112347',
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $otherPatient->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->patient)->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertStatus(404);
    }

    public function test_patient_can_review_completed_visit_and_updates_avg_rating(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->patient)->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertStatus(200);

        $response = $this->actingAs($this->patient)->post('/Wizyta/' . $appointment->id . '/Opinia', [
            'rating' => 5,
            'comment' => 'Swietny lekarz!',
        ]);

        $response->assertRedirect('/ListaWizyt');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'appointment_id' => $appointment->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctorProfile->id,
            'rating' => 5,
            'comment' => 'Swietny lekarz!',
        ]);

        $this->doctorProfile->refresh();
        $this->assertEquals(5.00, $this->doctorProfile->avg_rating);
    }

    public function test_patient_cannot_review_same_visit_twice(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        Review::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctorProfile->id,
            'rating' => 4,
            'comment' => 'OK',
        ]);

        $response = $this->actingAs($this->patient)->get('/Wizyta/' . $appointment->id . '/Opinia');
        $response->assertRedirect('/ListaWizyt');

        $response = $this->actingAs($this->patient)->post('/Wizyta/' . $appointment->id . '/Opinia', [
            'rating' => 5,
        ]);
        $response->assertRedirect('/ListaWizyt');
    }

    public function test_admin_can_delete_appointment(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin_test@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'admin',
            'pesel' => '90010199999',
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'confirmed',
        ]);

        // Assert slot is booked
        $this->slot->refresh();
        $this->assertTrue((bool)$this->slot->is_booked);

        $response = $this->actingAs($admin)->delete('/admin/wizyty/' . $appointment->id);
        $response->assertStatus(302); // redirect back

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);

        // Slot should be free now
        $this->slot->refresh();
        $this->assertFalse((bool)$this->slot->is_booked);
    }

    public function test_non_admin_cannot_delete_appointment(): void
    {
        $appointment = Appointment::create([
            'slot_id' => $this->slot->id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->patient)->delete('/admin/wizyty/' . $appointment->id);
        $response->assertStatus(403);

        $response = $this->actingAs($this->doctorUser)->delete('/admin/wizyty/' . $appointment->id);
        $response->assertStatus(403);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
        ]);
    }
}
