<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\AvailabilitySlot;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getDoctors()
    {
        $doctors = DoctorProfile::with(['user', 'specializations'])
            ->where('is_accepted', true)
            ->get();

        $data = $doctors->map(function ($doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'Brak danych',
                'bio' => $doc->bio,
                'avg_rating' => $doc->avg_rating,
                'specializations' => $doc->specializations->map(function ($spec) {
                    return $spec->name;
                }),
            ];
        });

        return response()->json($data);
    }

    public function getDoctorSlots($id)
    {
        $doctor = DoctorProfile::findOrFail($id);

        $slots = AvailabilitySlot::where('doctor_id', $doctor->id)
            ->where('is_booked', false)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time->toDateTimeString(),
                    'end_time' => $slot->end_time->toDateTimeString(),
                ];
            });

        return response()->json($slots);
    }

    public function bookAppointment(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:users,id',
            'slot_id' => 'required|integer|exists:availability_slots,id',
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $slot = AvailabilitySlot::findOrFail($request->slot_id);

        if ($slot->is_booked) {
            return response()->json([
                'error' => 'Ten termin jest już zarezerwowany!'
            ], 400);
        }

        $appointment = Appointment::create([
            'slot_id' => $request->slot_id,
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'status' => 'pending',
            'medical_note' => null,
        ]);

        $slot->is_booked = true;
        $slot->save();

        return response()->json([
            'message' => 'Wizyta została zarezerwowana pomyślnie!',
            'appointment' => [
                'id' => $appointment->id,
                'slot_id' => $appointment->slot_id,
                'patient_id' => $appointment->patient_id,
                'service_id' => $appointment->service_id,
                'status' => $appointment->status,
            ]
        ], 201);
    }
}
