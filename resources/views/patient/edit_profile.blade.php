@extends('layouts.app')

@section('title', 'Edycja Profilu - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-body">
    <div class="auth-container" style="max-width: 600px;">
        <div class="auth-header">
            <h1>Edytuj Profil</h1>
            <p>Zmień swoje dane osobowe lub zaktualizuj hasło</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left: 15px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/PanelUzytkownika/edycja') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="first_name">Imię</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Nazwisko</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adres e-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label for="pesel">Numer PESEL</label>
                <input type="text" name="pesel" id="pesel" class="form-control" value="{{ old('pesel', $user->pesel) }}" maxlength="11" required>
            </div>

            <hr style="border: 0; border-top: 1px solid #cbd5e1; margin: 25px 0;">

            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Pozostaw poniższe pola puste, jeśli nie chcesz zmieniać hasła:</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="password">Nowe hasło</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Powtórz nowe hasło</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button type="submit" class="btn-auth">
                    Zapisz zmiany
                </button>
                <a href="{{ url('/PanelUzytkownika') }}" class="btn-auth" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
