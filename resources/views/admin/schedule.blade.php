@extends('layouts.app')

@section('title', 'Godziny Pracy Lekarzy - Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">

<div class="admin-container">
    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/godziny-pracy') }}" class="nav-btn active">Godziny Pracy</a>
        <a href="{{ url('/admin/wizyty') }}" class="nav-btn">Wizyty Lekarzy</a>
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if(!$profile)
        <div class="empty-state">
            <p>Brak zaakceptowanych lekarzy w systemie.</p>
        </div>
    @else
        <div class="booking-doctor-header" style="margin-bottom: 24px;">
            <div class="booking-doctor-info">
                <div class="doctor-avatar doctor-avatar-lg">
                    @if($profile->profile_photo)
                        <img src="{{ asset($profile->profile_photo) }}" alt="Zdjęcie lekarza">
                    @else
                        <span>{{ strtoupper(substr($profile->user->first_name, 0, 1)) }}{{ strtoupper(substr($profile->user->last_name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h2 class="booking-title" style="margin-bottom: 4px;">
                        dr {{ $profile->user->first_name }} {{ $profile->user->last_name }}
                    </h2>
                    <p class="booking-subtitle">Zarządzaj grafikiem wybranego lekarza.</p>
                </div>
            </div>
        </div>

        @include('partials.schedule_form', [
            'formAction' => url('/admin/godziny-pracy/generuj'),
            'doctors' => $doctors,
            'selectedDoctorId' => $selectedDoctorId,
        ])

        @include('partials.schedule_slots', [
            'slots' => $slots,
            'scheduleTitleSuffix' => 'lekarza',
            'hiddenDoctorId' => $selectedDoctorId,
            'deleteSlotUrl' => fn ($id) => url('/admin/godziny-pracy/slot/'.$id.'/usun'),
        ])
    @endif
</div>
@endsection
