@extends('layouts.app')

@section('title', 'Diagnozy i Zalecenia - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
@endpush

@section('content')
<div class="patient-container">
    <h1 class="panel-title">Diagnoza i Zalecenia</h1>

    <div class="panel-card">
        @if($appointments && $appointments->count() > 0)
            <div class="recommendations-list">
                @foreach($appointments as $app)
                    <div class="rec-item">
                        <div class="rec-header">
                            <span class="rec-doctor">
                                @if($app->slot && $app->slot->doctor && $app->slot->doctor->user)
                                    dr {{ $app->slot->doctor->user->first_name }} {{ $app->slot->doctor->user->last_name }}
                                @else
                                    Nieznany Lekarz
                                @endif
                            </span>
                            <span class="rec-date">
                                {{ $app->slot ? $app->slot->start_time->format('d.m.Y H:i') : 'brak daty' }}
                            </span>
                        </div>
                        <div class="rec-body">
                            <strong>Zalecenia medyczne / Recepta:</strong>
                            <p style="margin-top: 8px; white-space: pre-line; line-height: 1.6; color: #475569;">
                                {{ $app->medical_note }}
                            </p>
                        </div>
                        <span class="rec-service">
                            {{ $app->service ? $app->service->name : 'Konsultacja ogólna' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <p>Nie znaleziono żadnych wpisów dotyczących zaleceń lub diagnoz lekarskich.</p>
                <p style="font-size: 14px; margin-top: 5px; color: #94a3b8;">Zalecenia pojawią się tutaj po zakończeniu wizyty przez lekarza.</p>
            </div>
        @endif
    </div>
</div>
@endsection
