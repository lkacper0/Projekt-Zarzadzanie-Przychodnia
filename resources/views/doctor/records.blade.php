@extends('layouts.app')

@section('title', 'Kartoteka Pacjentów - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Kartoteka Pacjentów</h1>

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
            <h2 class="card-title font-title">Moi Pacjenci</h2>

            @if($patients && $patients->count() > 0)
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #1a2b4a;">
                            <th style="padding: 12px 10px;">Imię i Nazwisko</th>
                            <th style="padding: 12px 10px;">E-mail</th>
                            <th style="padding: 12px 10px;">PESEL</th>
                            <th style="padding: 12px 10px;">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 16px 10px; font-weight: 600;">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    {{ $patient->email }}
                                </td>
                                <td style="padding: 16px 10px; font-family: monospace;">
                                    {{ $patient->pesel ?? 'brak danych' }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    <a href="{{ url('/PanelLekarza/pacjent/' . $patient->id . '/diagnoza') }}" class="btn btn-primary btn-sm" style="background-color: #4a90e2; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem;">Wystaw diagnozę</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #a0aec0;">
                    <span style="font-size: 40px; display: block; margin-bottom: 15px;">👥</span>
                    Brak pacjentów przypisanych do Twojej kartoteki.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
