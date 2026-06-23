@extends('layouts.app')

@section('title', 'Zarządzanie Wizytami Lekarzy - Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="admin-container">
    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/godziny-pracy') }}" class="nav-btn">Godziny Pracy</a>
        <a href="{{ url('/admin/wizyty') }}" class="nav-btn active">Wizyty Lekarzy</a>
        <a href="{{ url('/admin/uslugi') }}" class="nav-btn">Usługi</a>
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

    <div class="doc-card" style="margin-bottom: 24px; padding: 20px;">
        <form method="GET" action="{{ url('/admin/wizyty') }}" class="admin-search-form">
            <label for="doctor_id" style="font-weight: bold; display: block; margin-bottom: 8px;">Wybierz lekarza:</label>
            <div style="display: flex; gap: 10px;">
                <select name="doctor_id" id="doctor_id" class="form-control" style="padding: 10px; flex-grow: 1; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Wybierz lekarza z listy --</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ isset($selectedDoctorId) && $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                            dr {{ $doc->user->first_name }} {{ $doc->user->last_name }} ({{ $doc->user->email }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="nav-btn active" style="border: none; cursor: pointer;">Pokaż wizyty</button>
            </div>
        </form>
    </div>

    @if(!$profile)
        <div class="empty-state">
            <p>Wybierz lekarza z powyższej listy, aby zarządzać jego wizytami.</p>
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
                    <p class="booking-subtitle">Zarządzasz widokiem i akcjami wizyt jako Administrator.</p>
                </div>
            </div>
        </div>

        <div class="doc-card" style="overflow-x: auto;">
            <div class="visit-tabs">
                <button class="visit-tab active" data-tab="pending">Oczekujące ({{ $pendingAppointments->count() }})</button>
                <button class="visit-tab" data-tab="confirmed">Potwierdzone ({{ $confirmedAppointments->count() }})</button>
                <button class="visit-tab" data-tab="completed">Wykonane ({{ $completedAppointments->count() }})</button>
                <button class="visit-tab" data-tab="cancelled">Odwołane ({{ $cancelledAppointments->count() }})</button>
            </div>

            <div class="visit-tab-panel active" id="tab-pending">
                @include('doctor.partials.visits_table', [
                    'appointments' => $pendingAppointments,
                    'showActions' => 'pending',
                ])
            </div>

            <div class="visit-tab-panel" id="tab-confirmed">
                @include('doctor.partials.visits_table', [
                    'appointments' => $confirmedAppointments,
                    'showActions' => 'confirmed',
                ])
            </div>

            <div class="visit-tab-panel" id="tab-completed">
                @include('doctor.partials.visits_table', [
                    'appointments' => $completedAppointments,
                    'showActions' => 'completed',
                ])
            </div>

            <div class="visit-tab-panel" id="tab-cancelled">
                @include('doctor.partials.visits_table', [
                    'appointments' => $cancelledAppointments,
                    'showActions' => 'cancelled',
                ])
            </div>
        </div>
    @endif
</div>

<script>
document.querySelectorAll('.visit-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.visit-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.visit-tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});
</script>
@endsection
