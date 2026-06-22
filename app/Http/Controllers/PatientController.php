<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $specializations = \App\Models\Specialization::orderBy('name', 'asc')->get();
        $application = DoctorProfile::where('user_id', $user->id)->first();

        return view('patient.panel', compact('user', 'specializations', 'application'));
    }

    public function visits()
    {
        $user = Auth::user();

        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereHas('slot', function ($query) {
                $query->where('start_time', '>=', now());
            })
            ->with(['slot.doctor.user', 'service'])
            ->orderBy('id', 'asc')
            ->get();

        $completedAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->with(['slot.doctor.user', 'service', 'review'])
            ->orderBy('id', 'desc')
            ->get();

        $pastAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereHas('slot', function ($query) {
                $query->where('start_time', '<', now());
            })
            ->with(['slot.doctor.user', 'service'])
            ->orderBy('id', 'desc')
            ->get();

        return view('patient.visits', compact('upcomingAppointments', 'completedAppointments', 'pastAppointments'));
    }

    public function applyToBeDoctor(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'bio' => 'required|string|max:2000',
            'specialization' => 'required|exists:specializations,id',
        ], [
            'bio.required' => 'Opis profilu (Bio) jest wymagany.',
            'specialization.required' => 'Wybierz specjalizację.',
            'specialization.exists' => 'Wybrana specjalizacja nie istnieje.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        $exists = DoctorProfile::where('user_id', $user->id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Już złożyłeś podanie o konto lekarza.'
            ], 400);
        }

        $profile = DoctorProfile::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
            'is_accepted' => false,
            'avg_rating' => 0.00,
        ]);

        $profile->specializations()->attach($request->specialization);

        return response()->json([
            'success' => true,
            'message' => 'Twoje podanie o konto lekarza zostało złożone pomyślnie!'
        ]);
    }

    public function diagnosis()
    {
        $user = Auth::user();
        $appointments = Appointment::where('patient_id', $user->id)
            ->whereNotNull('medical_note')
            ->where('medical_note', '!=', '')
            ->with(['slot.doctor.user', 'service'])
            ->orderBy('id', 'desc')
            ->get();
        return view('patient.diagnosis', compact('appointments'));
    }

    public function searchDoctors(Request $request)
    {
        $search = $request->get('search');
        $specializationId = $request->get('specialization');
        $tagId = $request->get('tag');
        $tagIds = $request->get('tags');
        if ($tagId && !is_array($tagIds)) {
            $tagIds = [$tagId];
        } elseif (!is_array($tagIds)) {
            $tagIds = [];
        }
        $tagIds = array_map('intval', $tagIds);
        
        $sort = $request->get('sort', 'alphabetical');

        $query = DoctorProfile::where('is_accepted', true)
            ->with(['user', 'specializations', 'tags']);

        if ($search) {
            $driver = \DB::connection()->getDriverName();
            $like = $driver === 'pgsql' ? 'ilike' : 'like';
            $term = '%' . $search . '%';

            $query->where(function ($q) use ($term, $like) {
                $q->where('bio', $like, $term)
                  ->orWhereHas('user', function ($qu) use ($term, $like) {
                      $qu->where('first_name', $like, $term)
                         ->orWhere('last_name', $like, $term);
                  });
            });
        }

        if ($specializationId) {
            $query->whereHas('specializations', function ($q) use ($specializationId) {
                $q->where('specialization_id', $specializationId);
            });
        }

        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tag_id', $tagId);
                });
            }
        }

        if ($sort === 'rating') {
            $query->orderBy('avg_rating', 'desc');
        } elseif ($sort === 'specialization') {
            $query->orderBy(
                \App\Models\Specialization::select('name')
                    ->join('doctor_specializations', 'specializations.id', '=', 'doctor_specializations.specialization_id')
                    ->whereColumn('doctor_specializations.doctor_id', 'doctor_profiles.id')
                    ->limit(1)
            );
        } else {
            $query->orderBy(
                \App\Models\User::select('last_name')
                    ->whereColumn('users.id', 'doctor_profiles.user_id')
                    ->limit(1)
            );
        }

        $doctors = $query->paginate(6)->withQueryString();
        $specializations = \App\Models\Specialization::orderBy('name', 'asc')->get();
        $tags = \App\Models\Tag::orderBy('name', 'asc')->get();

        return view('patient.search_doctors', compact('doctors', 'specializations', 'tags', 'search', 'specializationId', 'tagId', 'tagIds', 'sort'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('patient.edit_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'pesel' => 'required|string|size:11|unique:users,pesel,' . $user->id,
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
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->pesel = $request->pesel;

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        $user->save();

        return redirect('/PanelUzytkownika')->with('success', 'Twoje dane zostały pomyślnie zaktualizowane!');
    }

    public function showReviewForm($id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->with(['slot.doctor.user', 'service'])
            ->findOrFail($id);

        $exists = \App\Models\Review::where('appointment_id', $id)->exists();
        if ($exists) {
            return redirect('/ListaWizyt')->with('error', 'Już dodałeś opinię dla tej wizyty.');
        }

        return view('patient.add_review', compact('appointment'));
    }

    public function storeReview(Request $request, $id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->findOrFail($id);

        $exists = \App\Models\Review::where('appointment_id', $id)->exists();
        if ($exists) {
            return redirect('/ListaWizyt')->with('error', 'Już dodałeś opinię dla tej wizyty.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        \App\Models\Review::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $user->id,
            'doctor_id' => $appointment->slot->doctor_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $doctorId = $appointment->slot->doctor_id;
        $avgRating = \App\Models\Review::where('doctor_id', $doctorId)->avg('rating');
        $doctorProfile = DoctorProfile::findOrFail($doctorId);
        $doctorProfile->avg_rating = round($avgRating, 2);
        $doctorProfile->save();

        return redirect('/ListaWizyt')->with('success', 'Opinia została dodana pomyślnie!');
    }
}
