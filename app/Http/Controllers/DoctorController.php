<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\DoctorGallery;
use App\Models\Service;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function panel()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->with(['services', 'tags', 'gallery', 'specializations'])->first();
        $allTags = Tag::orderBy('name')->get();
        $allSpecializations = \App\Models\Specialization::orderBy('name')->get();

        return view('doctor.panel', compact('user', 'profile', 'allTags', 'allSpecializations'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'pesel' => 'required|string|size:11|unique:users,pesel,' . $user->id,
            'bio' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Podaj poprawny adres e-mail.',
            'email.unique' => 'Ten adres e-mail jest już zajęty.',
            'pesel.required' => 'PESEL jest wymagany.',
            'pesel.size' => 'PESEL musi składać się z 11 cyfr.',
            'pesel.unique' => 'Ten PESEL jest już zarejestrowany w systemie.',
            'password.min' => 'Hasło musi mieć co najmniej 6 znaków.',
            'password.confirmed' => 'Hasła nie są identyczne.',
            'bio.max' => 'Bio może mieć maksymalnie 2000 znaków.',
            'profile_photo.image' => 'Zdjęcie profilowe musi być plikiem graficznym.',
            'profile_photo.mimes' => 'Dozwolone formaty to: jpg, jpeg, png, webp.',
            'profile_photo.max' => 'Zdjęcie profilowe może mieć maksymalnie 2 MB.',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->pesel = $request->pesel;

        if ($request->filled('password')) {
            $user->password_hash = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

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

    public function syncTags(Request $request)
    {
        $request->validate([
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'new_tag' => 'nullable|string|max:60',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $tagIds = $request->input('tags', []);

        if ($request->filled('new_tag')) {
            $name = trim($request->new_tag);
            $tag = Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $profile->tags()->sync(array_unique($tagIds));

        return redirect('/PanelLekarza')->with('success', 'Tagi zostały zaktualizowane!');
    }

    public function addSpecialization(Request $request)
    {
        $request->validate([
            'specialization_id' => 'required|integer|exists:specializations,id',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        if (!$profile->specializations->contains($request->specialization_id)) {
            $profile->specializations()->attach($request->specialization_id);
        }

        return redirect('/PanelLekarza')->with('success', 'Specjalizacja została dodana do Twojego profilu!');
    }

    public function removeSpecialization($id)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        $profile->specializations()->detach($id);

        return redirect('/PanelLekarza')->with('success', 'Specjalizacja została usunięta z Twojego profilu!');
    }

    public function uploadGallery(Request $request)
    {
        $request->validate([
            'gallery_photos' => 'required|array|max:10',
            'gallery_photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();

        foreach ($request->file('gallery_photos') as $file) {
            $filename = 'gallery_' . $profile->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/doctors/gallery'), $filename);

            DoctorGallery::create([
                'doctor_id' => $profile->id,
                'image_url' => 'uploads/doctors/gallery/' . $filename,
            ]);
        }

        return redirect('/PanelLekarza')->with('success', 'Zdjęcia dodane do galerii!');
    }

    public function deleteGallery($id)
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $photo = DoctorGallery::where('id', $id)->where('doctor_id', $profile->id)->firstOrFail();

        $path = public_path($photo->image_url);
        if (file_exists($path)) {
            unlink($path);
        }

        $photo->delete();

        return redirect('/PanelLekarza')->with('success', 'Zdjęcie usunięte z galerii!');
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

    public function workingHours()
    {
        $user = Auth::user();
        $profile = DoctorProfile::where('user_id', $user->id)->firstOrFail();
        $slots = \App\Models\AvailabilitySlot::where('doctor_id', $profile->id)
            ->orderBy('start_time', 'asc')
            ->get();

        return view('doctor.working_hours', compact('profile', 'slots'));
    }

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
