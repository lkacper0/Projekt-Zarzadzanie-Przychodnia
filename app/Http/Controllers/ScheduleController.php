<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AvailabilitySlot;
use App\Models\DoctorProfile;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{

    public function doctorSchedule()
    {
        $user    = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $slots = AvailabilitySlot::where('doctor_id', $profile->id)
            ->where('start_time', '>=', Carbon::today())
            ->orderBy('start_time')
            ->with('appointment.patient')
            ->get()
            ->groupBy(fn($s) => $s->start_time->toDateString());

        return view('doctor.schedule', compact('profile', 'slots'));
    }

    public function generateSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|in:10,15,20,30,45,60',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $date = $request->date;
        $slotLen = (int) $request->slot_minutes;
        $current = Carbon::parse("$date {$request->start_time}");
        $endLimit  = Carbon::parse("$date {$request->end_time}");

        $created = 0;
        while ($current->copy()->addMinutes($slotLen)->lte($endLimit)) {
            $slotEnd = $current->copy()->addMinutes($slotLen);

            $exists = AvailabilitySlot::where('doctor_id', $profile->id)
                ->where('start_time', $current)
                ->exists();

            if (!$exists) {
                AvailabilitySlot::create([
                    'doctor_id' => $profile->id,
                    'start_time' => $current,
                    'end_time' => $slotEnd,
                    'is_booked' => false,
                ]);
                $created++;
            }

            $current->addMinutes($slotLen);
        }

        return redirect('/PanelLekarza/harmonogram')
            ->with('success', "Dodano $created slotów na dzień $date.");
    }

    public function deleteSlot($id)
    {
        $user    = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $slot    = AvailabilitySlot::where('id', $id)
            ->where('doctor_id', $profile->id)
            ->where('is_booked', false)
            ->firstOrFail();
        $slot->delete();

        return redirect('/PanelLekarza/harmonogram')->with('success', 'Slot usunięty.');
    }



    public function bookingIndex()
    {
        $doctors = DoctorProfile::where('is_accepted', true)
            ->whereHas('slots', fn($q) => $q->where('is_booked', false)->where('start_time', '>=', now()))
            ->with('user')
            ->get();

        return view('booking.index', compact('doctors'));
    }

    public function bookingDoctor($doctorId)
    {
        $doctor = DoctorProfile::with('user', 'services')->findOrFail($doctorId);

        $slots = AvailabilitySlot::where('doctor_id', $doctorId)
            ->where('is_booked', false)
            ->where('start_time', '>=', Carbon::today())
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($s) => $s->start_time->toDateString());

        return view('booking.doctor', compact('doctor', 'slots'));
    }

    public function bookSlot(Request $request, $slotId)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect('/login')->with('error', 'Musisz być zalogowany, aby zarezerwować wizytę.');
        }

        $slot = AvailabilitySlot::where('id', $slotId)
            ->where('is_booked', false)
            ->where('start_time', '>=', now())
            ->lockForUpdate()
            ->firstOrFail();

        $service = Service::where('id', $request->service_id)
            ->where('doctor_id', $slot->doctor_id)
            ->firstOrFail();

        $slot->is_booked = true;
        $slot->save();

        Appointment::create([
            'slot_id' => $slot->id,
            'patient_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);

        return redirect('/Rezerwacja/lekarz/' . $slot->doctor_id)
            ->with('success', 'Wizyta zarezerwowana na ' . $slot->start_time->format('d.m.Y H:i') . '!');
    }
}
