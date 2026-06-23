@extends('layouts.app')

@section('title', 'Lista Wizyt - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
<link rel="stylesheet" href="{{ asset('css/doctor/visits.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn">Usługi &amp; Cennik</a>
        <a href="{{ url('/GodzinyPracy') }}" class="nav-btn">Godziny Pracy</a>
        <a href="{{ url('/ListaWizyt') }}" class="nav-btn active">Lista Wizyt</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="booking-doctor-header">
        <div class="booking-doctor-info">
            <div class="doctor-avatar doctor-avatar-lg">
                @if($profile && $profile->profile_photo)
                    <img src="{{ asset($profile->profile_photo) }}" alt="Zdjęcie lekarza">
                @else
                    <span>{{ strtoupper(substr($profile->user->first_name, 0, 1)) }}{{ strtoupper(substr($profile->user->last_name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="doctor-meta">
                <span class="doctor-panel-badge">Panel Lekarza</span>
                <h2 class="booking-title" style="margin-bottom: 4px;">
                    dr {{ $profile->user->first_name }} {{ $profile->user->last_name }}
                </h2>
                <p class="booking-subtitle" style="margin: 0;">Zarządzaj swoimi wizytami – potwierdzaj, kończ lub odwołuj terminy.</p>
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
