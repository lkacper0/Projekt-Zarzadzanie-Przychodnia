@extends('layouts.app')

@section('title', 'Historia Medyczna - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Historia Pacjentów</h1>

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

        <div class="doc-card">
            <h2 class="card-title font-title">Wystawione Zalecenia i Diagnozy</h2>

            @if($appointments && $appointments->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach($appointments as $app)
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; background-color: #f8fafc;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #edf2f7; padding-bottom: 8px;">
                                <strong style="color: #1a2b4a;">
                                    Pacjent: {{ $app->patient ? $app->patient->first_name . ' ' . $app->patient->last_name : 'Nieznany' }}
                                </strong>
                                <span style="font-size: 0.85rem; color: #718096;">
                                    {{ $app->slot ? $app->slot->start_time->format('d.m.Y H:i') : 'brak daty' }}
                                </span>
                            </div>
                            <div style="font-size: 0.9rem; color: #4a5568; line-height: 1.5;">
                                <span style="font-weight: 600; display: block; margin-bottom: 4px;">Zalecenia:</span>
                                <p style="margin: 0; white-space: pre-line;">{{ $app->medical_note }}</p>
                            </div>
                            <div style="margin-top: 10px; font-size: 0.8rem; font-weight: bold; color: #4a90e2;">
                                Usługa: {{ $app->service ? $app->service->name : 'Standard' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #a0aec0;">
                    <span style="font-size: 40px; display: block; margin-bottom: 15px;">📝</span>
                    Nie wystawiono jeszcze żadnych zaleceń lub diagnoz.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
