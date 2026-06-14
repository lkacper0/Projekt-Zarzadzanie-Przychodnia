<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Specializations
        $specs = [
            'Internista',
            'Kardiologia',
            'Pediatria',
            'Dermatologia',
            'Neurologia',
            'Ginekologia',
            'Chirurgia'
        ];
        
        $specializationModels = [];
        foreach ($specs as $specName) {
            $specializationModels[$specName] = \App\Models\Specialization::create(['name' => $specName]);
        }

        // Seed Tags
        $tagsData = ['NFZ', 'Prywatnie', 'Dzieci', 'Teleporada', 'Konsultacja online'];
        $tagModels = [];
        foreach ($tagsData as $tagName) {
            $tagModels[$tagName] = \App\Models\Tag::create(['name' => $tagName]);
        }

        $patient = User::factory()->create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'pacjent@example.com',
            'role' => 'patient',
        ]);

        $admin = User::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $doctorUser = User::factory()->create([
            'first_name' => 'Adam',
            'last_name' => 'Lekarski',
            'email' => 'lekarz@example.com',
            'role' => 'doctor',
        ]);
        
        $doctorProfile = \App\Models\DoctorProfile::create([
            'user_id' => $doctorUser->id,
            'bio' => 'Doświadczony lekarz chorób wewnętrznych.',
            'is_accepted' => true,
            'avg_rating' => 4.50,
        ]);
        // Attach Specialization and Tags
        $doctorProfile->specializations()->attach($specializationModels['Internista']->id);
        $doctorProfile->tags()->attach([$tagModels['NFZ']->id, $tagModels['Teleporada']->id]);

        $candidateUser = User::factory()->create([
            'first_name' => 'Marta',
            'last_name' => 'Kandydatka',
            'email' => 'kandydat@example.com',
            'role' => 'patient',
        ]);

        $candidateProfile = \App\Models\DoctorProfile::create([
            'user_id' => $candidateUser->id,
            'bio' => 'Chciałabym dołączyć do zespołu jako pediatra.',
            'is_accepted' => false,
            'avg_rating' => 0.00,
        ]);
        // Attach Specialization and Tags
        $candidateProfile->specializations()->attach($specializationModels['Pediatria']->id);
        $candidateProfile->tags()->attach([$tagModels['Prywatnie']->id, $tagModels['Dzieci']->id]);

        $service = \App\Models\Service::create([
            'doctor_id' => $doctorProfile->id,
            'name' => 'Konsultacja internistyczna',
            'description' => 'Standardowa wizyta u internisty',
            'price' => 150.00,
            'duration_minutes' => 30,
        ]);

        $slot = \App\Models\AvailabilitySlot::create([
            'doctor_id' => $doctorProfile->id,
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addMinutes(30),
            'is_booked' => true,
        ]);

        $appointment = \App\Models\Appointment::create([
            'slot_id' => $slot->id,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
        ]);

        \App\Models\Review::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctorProfile->id,
            'rating' => 5,
            'comment' => 'Bardzo profesjonalne podejście do pacjenta, polecam!',
        ]);
    }
}
