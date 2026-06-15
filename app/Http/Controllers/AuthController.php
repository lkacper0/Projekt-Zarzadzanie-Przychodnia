<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Specialization;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Wprowadź poprawny adres e-mail.',
            'password.required' => 'Hasło jest wymagane.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'message' => 'Twoje konto zostało zablokowane przez administratora.'
                ], 403);
            }

            $request->session()->regenerate();

            $redirectUrl = $this->getRedirectUrlForUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Zalogowano pomyślnie!',
                'redirect' => $redirectUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Błędny e-mail lub hasło.'
        ], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'pesel' => 'required|string|size:11|unique:users',
        ], [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Podaj poprawny adres e-mail.',
            'email.unique' => 'Ten adres e-mail jest już zajęty.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej 6 znaków.',
            'password.confirmed' => 'Hasła nie są identyczne.',
            'pesel.required' => 'Numer PESEL jest wymagany.',
            'pesel.size' => 'Numer PESEL musi składać się z 11 cyfr.',
            'pesel.unique' => 'Ten numer PESEL jest już zarejestrowany w systemie.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'pesel' => $request->pesel,
            'role' => 'patient',
            'is_active' => true,
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Rejestracja pomyślna!',
            'redirect' => '/PanelUzytkownika'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wylogowano pomyślnie!',
                'redirect' => '/'
            ]);
        }

        return redirect('/')->with('success', 'Zostałeś wylogowany.');
    }

    public function showPending()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $isDoctorCandidate = DoctorProfile::where('user_id', $user->id)
            ->where('is_accepted', false)
            ->exists();

        if (!$isDoctorCandidate) {
            return $this->redirectUserBasedOnRole($user);
        }

        return view('auth.pending');
    }

    private function getRedirectUrlForUser($user)
    {
        if ($user->role === 'admin') {
            return '/admin';
        }

        if ($user->role === 'doctor') {
            return '/PanelLekarza';
        }

        return '/PanelUzytkownika';
    }

    private function redirectUserBasedOnRole($user)
    {
        return redirect($this->getRedirectUrlForUser($user));
    }
}
