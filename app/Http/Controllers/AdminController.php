<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Review;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = User::orderBy('id', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->get();

        return view('admin.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'pesel' => 'nullable|string|max:11',
            'role' => 'required|in:patient,doctor,admin',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'pesel' => $request->pesel,
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect('/admin')->with('success', 'Użytkownik został dodany!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));

    }

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);


        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'pesel' => 'nullable|string|max:11',
            'role' => 'required|in:patient,doctor,admin',
            'password' => 'nullable|string|min:6',
        ]);


        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->pesel = $request->pesel;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($request->password);
        }

        $user->save();

        return redirect('/admin')->with('success', 'Dane zaktualizowane!');
    }




    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect('/admin')->with('success', 'Użytkownik usunięty!');
    }




    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'odblokowany' : 'zablokowany';
        return redirect('/admin')->with('success', "Użytkownik został {$status}!");
    }




    public function reviews()
    {

        $reviews = Review::with(['patient', 'doctor.user'])->orderBy('id', 'desc')->get();
        return view('admin.reviews', compact('reviews'));
    }



    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect('/admin/reviews')->with('success', 'Opinia została usunięta!');
    }


    public function doctorApplications()
    {
        $applications = DoctorProfile::with('user')->where('is_accepted', false)->orderBy('id', 'asc')->get();
        return view('admin.doctor_applications', compact('applications'));
    }




    public function approveDoctor($id)
    {
        $profile = DoctorProfile::findOrFail($id);
        $profile->is_accepted = true;
        $profile->save();



        if ($profile->user) {
            $profile->user->role = 'doctor';
            $profile->user->save();
        }

        return redirect('/admin/doctor-applications')->with('success', 'Zgłoszenie lekarza zostało zaakceptowane!');
    }

    public function destroyDoctorApplication($id){
        $profile = DoctorProfile::findOrFail($id);
        $profile->delete();

        return redirect('/admin/doctor-applications')->with('success', 'Zgłoszenie lekarza zostało odrzucone!');
    }

    public function homepage()
    {
        return view('admin.homepage');
    }
}
