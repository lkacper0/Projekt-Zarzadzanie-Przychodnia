@extends('layouts.app')

@section('title', 'Lista Wizyt - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Lista Wizyt</h1>

    <div class="doc-grid">
        <!-- Profile info sidebar card -->
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

        <!-- Appointments List -->
        <div class="doc-card" style="overflow-x: auto;">
            <h2 class="card-title">Zarezerwowane Wizyty</h2>

            @if($appointments && $appointments->count() > 0)
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #1a2b4a;">
                            <th style="padding: 12px 10px;">Pacjent</th>
                            <th style="padding: 12px 10px;">Usługa</th>
                            <th style="padding: 12px 10px;">Data i Godzina</th>
                            <th style="padding: 12px 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $app)
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 16px 10px; font-weight: 600;">
                                    {{ $app->patient ? $app->patient->first_name . ' ' . $app->patient->last_name : 'Nieznany Pacjent' }}
                                    <div style="font-size: 0.78rem; color: #718096; font-weight: normal;">
                                        PESEL: {{ $app->patient->pesel ?? 'brak' }}
                                    </div>
                                </td>
                                <td style="padding: 16px 10px;">
                                    {{ $app->service ? $app->service->name : 'Wizyta' }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    {{ $app->slot ? $app->slot->start_time->format('d.m.Y H:i') : 'brak terminu' }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    @php
                                        $badgeStyle = 'background: #fff3cd; color: #856404;';
                                        $statusText = 'Oczekująca';
                                        if ($app->status === 'completed') {
                                            $badgeStyle = 'background: #d4edda; color: #155724;';
                                            $statusText = 'Zakończona';
                                        } elseif ($app->status === 'confirmed') {
                                            $badgeStyle = 'background: #ebf8ff; color: #2b6cb0;';
                                            $statusText = 'Potwierdzona';
                                        } elseif ($app->status === 'cancelled') {
                                            $badgeStyle = 'background: #f8d7da; color: #721c24;';
                                            $statusText = 'Odwołana';
                                        }
                                    @endphp
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; {!! $badgeStyle !!}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #a0aec0;">
                    <span style="font-size: 40px; display: block; margin-bottom: 15px;">📅</span>
                    Brak zaplanowanych wizyt pacjentów.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
