@extends('layouts.app')

@section('title', 'Oczekiwanie na akceptację - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-body">
    <div class="auth-container pending-card">
        <div class="pending-icon">⏳</div>
        <h2>Oczekiwanie na akceptację</h2>
        <p>
            Dziękujemy za rejestrację w ProHealth! <br>
            Twój profil lekarza został zapisany w systemie i aktualnie **oczekuje na weryfikację i akceptację** przez administratora przychodni.
        </p>
        <p style="color: #64748b; font-size: 14px; margin-top: -15px; margin-bottom: 30px;">
            Po zatwierdzeniu profilu uzyskasz pełny dostęp do Panelu Lekarza oraz zarządzania wizytami.
        </p>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-auth" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); box-shadow: 0 5px 15px rgba(71, 85, 105, 0.25);">
                Wyloguj się
            </button>
        </form>
    </div>
</div>
@endsection
