<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AvailabilitySlot;
use App\Models\DoctorProfile;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function doctorSchedule()
    {
        $user    = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->with('user')->firstOrFail();

        return view('doctor.schedule', [
            'profile' => $profile,
            'slots'   => $this->getGroupedSlots($profile->id),
        ]);
    }

    public function adminSchedule(Request $request)
    {
        $doctors = DoctorProfile::where('is_accepted', true)
            ->with('user')
            ->orderBy('id')
            ->get();

        $selectedDoctorId = $request->get('doctor_id', $doctors->first()?->id);
        $profile = $doctors->firstWhere('id', (int) $selectedDoctorId) ?? $doctors->first();

        if (!$profile) {
            return view('admin.schedule', [
                'doctors' => collect(),
                'profile' => null,
                'slots'   => collect(),
                'selectedDoctorId' => null,
            ]);
        }

        return view('admin.schedule', [
            'doctors'          => $doctors,
            'profile'          => $profile,
            'slots'            => $this->getGroupedSlots($profile->id),
            'selectedDoctorId' => $profile->id,
        ]);
    }

    public function generateSlots(Request $request)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        return $this->processGenerateSlots($request, $profile, '/GodzinyPracy');
    }

    public function adminGenerateSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctor_profiles,id',
        ]);

        $profile = DoctorProfile::where('id', $request->doctor_id)
            ->where('is_accepted', true)
            ->firstOrFail();

        return $this->processGenerateSlots(
            $request,
            $profile,
            '/admin/godziny-pracy?doctor_id=' . $profile->id
        );
    }

    private function processGenerateSlots(Request $request, DoctorProfile $profile, string $redirectUrl)
    {
        $request->validate([
            'date_from'    => 'required|date|after_or_equal:today',
            'date_to'      => 'required|date|after_or_equal:date_from',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'slot_minutes' => 'required|integer|in:10,15,20,30,45,60',
        ]);

        $dateFrom   = Carbon::parse($request->date_from)->startOfDay();
        $dateTo     = Carbon::parse($request->date_to)->startOfDay();
        $weekdays   = [0, 1, 2, 3, 4, 5, 6];
        $slotLen    = (int) $request->slot_minutes;
        $created    = 0;
        $daysCount  = 0;

        $currentDate = $dateFrom->copy();
        while ($currentDate->lte($dateTo)) {
            if (in_array($currentDate->dayOfWeek, $weekdays, true)) {
                $daysCount++;
                $date = $currentDate->toDateString();
                $slotStart = Carbon::parse("$date {$request->start_time}");
                $endLimit  = Carbon::parse("$date {$request->end_time}");

                while ($slotStart->copy()->addMinutes($slotLen)->lte($endLimit)) {
                    $nowInWarsaw = \Carbon\Carbon::now('Europe/Warsaw');
                    $slotStartInWarsaw = \Carbon\Carbon::parse("$date " . $slotStart->format('H:i'), 'Europe/Warsaw');

                    if ($slotStartInWarsaw->lt($nowInWarsaw)) {
                        $slotStart->addMinutes($slotLen);
                        continue;
                    }

                    $slotEnd = $slotStart->copy()->addMinutes($slotLen);

                    $exists = AvailabilitySlot::where('doctor_id', $profile->id)
                        ->where('start_time', $slotStart)
                        ->exists();

                    if (!$exists) {
                        AvailabilitySlot::create([
                            'doctor_id'  => $profile->id,
                            'start_time' => $slotStart,
                            'end_time'   => $slotEnd,
                            'is_booked'  => false,
                        ]);
                        $created++;
                    }

                    $slotStart->addMinutes($slotLen);
                }
            }
            $currentDate->addDay();
        }

        return redirect($redirectUrl)
            ->with('success', "Dodano $created slotów na $daysCount dni.");
    }

    public function deleteSlot($id)
    {
        $user    = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        return $this->processDeleteSlot($profile, $id, '/GodzinyPracy');
    }

    public function adminDeleteSlot(Request $request, $id)
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctor_profiles,id',
        ]);

        $profile = DoctorProfile::where('id', $request->doctor_id)
            ->where('is_accepted', true)
            ->firstOrFail();

        return $this->processDeleteSlot(
            $profile,
            $id,
            '/admin/godziny-pracy?doctor_id=' . $profile->id
        );
    }

    private function processDeleteSlot(DoctorProfile $profile, $id, string $redirectUrl)
    {
        $slot = AvailabilitySlot::where('id', $id)
            ->where('doctor_id', $profile->id)
            ->where('is_booked', false)
            ->firstOrFail();
        $slot->delete();

        return redirect($redirectUrl)->with('success', 'Slot usunięty.');
    }

    private function getGroupedSlots(int $doctorId)
    {
        return AvailabilitySlot::where('doctor_id', $doctorId)
            ->where('start_time', '>=', Carbon::today())
            ->orderBy('start_time')
            ->with('appointment.patient')
            ->get()
            ->groupBy(fn ($s) => $s->start_time->toDateString());
    }

    public function bookingIndex()
    {
        $doctors = DoctorProfile::where('is_accepted', true)
            ->whereHas('slots', fn ($q) => $q->where('is_booked', false)->where('start_time', '>=', now()))
            ->with('user')
            ->get();

        return view('booking.index', compact('doctors'));
    }

    public function bookingDoctor($doctorId)
    {
        $doctor = DoctorProfile::with('user', 'services')->findOrFail($doctorId);

        $slots = AvailabilitySlot::where('doctor_id', $doctorId)
            ->where('is_booked', false)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn ($s) => $s->start_time->toDateString());

        return view('booking.doctor', compact('doctor', 'slots'));
    }

    public function bookSlot(Request $request, $slotId)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $user = Auth::user();
        $slot = AvailabilitySlot::findOrFail($slotId);

        try {
            DB::transaction(function () use ($request, $slotId, $user) {
                $slot = AvailabilitySlot::where('id', $slotId)
                    ->where('is_booked', false)
                    ->where('start_time', '>=', now())
                    ->lockForUpdate()
                    ->firstOrFail();

                $service = Service::where('id', $request->service_id)
                    ->where('doctor_id', $slot->doctor_id)
                    ->firstOrFail();

                $slotDuration = $slot->start_time->diffInMinutes($slot->end_time);
                if ($service->duration_minutes > $slotDuration) {
                    throw new \RuntimeException("Usługa trwa {$service->duration_minutes} min, ale okienko ma tylko {$slotDuration} min.");
                }

                $slot->is_booked = true;
                $slot->save();

                Appointment::create([
                    'slot_id'    => $slot->id,
                    'patient_id' => $user->id,
                    'service_id' => $service->id,
                    'status'     => 'pending',
                ]);
            });
        } catch (\Throwable $e) {
            return redirect('/Rezerwacja/lekarz/' . $slot->doctor_id)
                ->with('error', 'Nie udało się zarezerwować terminu. Wybierz inny slot i spróbuj ponownie.');
        }

        return redirect('/Rezerwacja/lekarz/' . $slot->doctor_id)
            ->with('success', 'Wizyta zarezerwowana na ' . $slot->start_time->format('d.m.Y H:i') . '!');
    }
}
