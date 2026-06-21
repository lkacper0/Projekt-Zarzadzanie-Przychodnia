@extends('layouts.app')

@section('title', 'Moje Wizyty - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
@endpush

@section('content')
<div class="patient-container">
    <h1 class="panel-title">Moje Wizyty</h1>

    <div class="visit-tabs patient-tabs">
        <button class="visit-tab active" data-tab="upcoming">Nadchodzące ({{ $upcomingAppointments->count() }})</button>
        <button class="visit-tab" data-tab="completed">Wykonane ({{ $completedAppointments->count() }})</button>
        <button class="visit-tab" data-tab="history">Historia ({{ $pastAppointments->count() }})</button>
    </div>

    <div class="visit-tab-panel active" id="tab-upcoming">
        <div class="panel-card">
            @if($upcomingAppointments->count() > 0)
                @include('patient.partials.visits_table', ['appointments' => $upcomingAppointments])
            @else
                <div class="empty-state" style="padding: 30px 20px;">
                    <p>Nie masz zaplanowanych żadnych nadchodzących wizyt.</p>
                    <a href="{{ url('/Rezerwacja') }}" class="btn-auth" style="display:inline-block; margin-top:15px; padding:10px 20px; width:auto; text-decoration:none;">Zarezerwuj wizytę</a>
                </div>
            @endif
        </div>
    </div>

    <div class="visit-tab-panel" id="tab-completed">
        <div class="panel-card">
            @if($completedAppointments->count() > 0)
                @include('patient.partials.visits_table', ['appointments' => $completedAppointments])
            @else
                <div class="empty-state" style="padding: 30px 20px;">
                    <p>Brak wykonanych wizyt.</p>
                    <p style="font-size: 14px; margin-top: 5px; color: #94a3b8;">Wizyty pojawią się tutaj po zakończeniu przez lekarza.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="visit-tab-panel" id="tab-history">
        <div class="panel-card">
            @if($pastAppointments->count() > 0)
                @include('patient.partials.visits_table', ['appointments' => $pastAppointments])
            @else
                <div class="empty-state" style="padding: 30px 20px;">
                    <p>Brak wcześniejszych wizyt w historii.</p>
                </div>
            @endif
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
