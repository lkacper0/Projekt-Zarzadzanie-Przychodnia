@extends('layouts.app')

@section('title', 'Wystaw Diagnozę - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Wystaw Diagnozę dla {{ $patient->first_name }} {{ $patient->last_name }}</h1>

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
            <h2 class="card-title font-title">Zakończone Wizyty Oczekujące na Diagnozę</h2>

            @if($appointmentsToDiagnose && $appointmentsToDiagnose->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach($appointmentsToDiagnose as $app)
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; background-color: #f8fafc;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #edf2f7; padding-bottom: 8px;">
                                <strong style="color: #1a2b4a;">
                                    Usługa: {{ $app->service ? $app->service->name : 'Standard' }}
                                </strong>
                                <span style="font-size: 0.85rem; color: #718096;">
                                    Data: {{ $app->slot ? $app->slot->start_time->format('d.m.Y H:i') : 'brak daty' }}
                                </span>
                            </div>

                            <form action="{{ url('/PanelLekarza/pacjent/'.$patient->id.'/diagnoza') }}" method="POST">
                                @csrf
                                <input type="hidden" name="appointment_id" value="{{ $app->id }}">
                                <div style="margin-top: 10px;">
                                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: #4a5568; margin-bottom: 4px;">Diagnoza i zalecenia:</label>
                                    <textarea name="medical_note" rows="4" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 6px; padding: 8px;" required placeholder="Wpisz diagnozę, zalecenia, receptę...">{{ old('medical_note', $app->medical_note) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Zapisz Diagnozę</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #a0aec0;">
                    <span style="font-size: 40px; display: block; margin-bottom: 15px;">✅</span>
                    Wszystkie wizyty tego pacjenta mają już wystawioną diagnozę.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
