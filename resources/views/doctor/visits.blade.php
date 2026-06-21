@extends('layouts.app')

@section('title', 'Lista Wizyt - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Lista Wizyt</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="doc-grid">
        <div class="doc-card photo-card">
            <div class="photo-wrapper">
                @if($profile && $profile->profile_photo)
                    <img src="{{ asset($profile->profile_photo) }}" alt="Zdjęcie profilowe" class="profile-img">
                @else
                    <div class="profile-placeholder">
                        <span>{{ strtoupper(substr($profile->user->first_name, 0, 1)) }}{{ strtoupper(substr($profile->user->last_name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <p class="doc-name">{{ $profile->user->first_name }} {{ $profile->user->last_name }}</p>
            <p class="doc-email">{{ $profile->user->email }}</p>
            <span class="badge-active">Panel Lekarza</span>
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
