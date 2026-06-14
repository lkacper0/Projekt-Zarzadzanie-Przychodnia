<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{

    public function panel()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->with('services')->first();

        return view('doctor.panel', compact('user', 'profile'));
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $profile->bio = $request->bio;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'doctor_' . $profile->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/doctors'), $filename);
            $profile->profile_photo = 'uploads/doctors/' . $filename;
        }

        $profile->save();

        return redirect('/PanelLekarza')->with('success', 'Profil został zaktualizowany!');
    }


    public function services()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $services = Service::where('doctor_id', $profile->id)->orderBy('id', 'asc')->get();

        return view('doctor.services', compact('profile', 'services'));
    }


    public function createService()
    {
        return view('doctor.service_create');
    }


    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $user    = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        Service::create([
            'doctor_id' => $profile->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect('/PanelLekarza/uslugi')->with('success', 'Usługa została dodana!');
    }


    public function editService($id)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $service = Service::where('id', $id)->where('doctor_id', $profile->id)->firstOrFail();

        return view('doctor.service_edit', compact('service'));
    }


    public function updateService(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $service = Service::where('id', $id)->where('doctor_id', $profile->id)->firstOrFail();

        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->duration_minutes = $request->duration_minutes;
        $service->save();

        return redirect('/PanelLekarza/uslugi')->with('success', 'Usługa zaktualizowana!');
    }


    public function destroyService($id)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $service = Service::where('id', $id)->where('doctor_id', $profile->id)->firstOrFail();
        $service->delete();

        return redirect('/PanelLekarza/uslugi')->with('success', 'Usługa usunięta!');
    }

    /**
     * Display list of appointments/visits for the doctor.
     */
    public function visits()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $appointments = \App\Models\Appointment::whereHas('slot', function ($query) use ($profile) {
                $query->where('doctor_id', $profile->id);
            })
            ->with(['patient', 'slot', 'service'])
            ->orderBy('id', 'desc')
            ->get();

        return view('doctor.visits', compact('profile', 'appointments'));
    }

    /**
     * Display working hours/availability slots.
     */
    public function workingHours()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $slots = \App\Models\AvailabilitySlot::where('doctor_id', $profile->id)
            ->orderBy('start_time', 'asc')
            ->get();

        return view('doctor.working_hours', compact('profile', 'slots'));
    }

    /**
     * Display patient records list.
     */
    public function records()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $patients = \App\Models\User::whereHas('appointments.slot', function ($q) use ($profile) {
                $q->where('doctor_id', $profile->id);
            })
            ->distinct()
            ->get();

        return view('doctor.records', compact('profile', 'patients'));
    }

    /**
     * Display medical notes / history of patients.
     */
    public function history()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $appointments = \App\Models\Appointment::whereHas('slot', function ($q) use ($profile) {
                $q->where('doctor_id', $profile->id);
            })
            ->whereNotNull('medical_note')
            ->where('medical_note', '!=', '')
            ->with(['patient', 'service', 'slot'])
            ->orderBy('id', 'desc')
            ->get();

        return view('doctor.history', compact('profile', 'appointments'));
    }
}

