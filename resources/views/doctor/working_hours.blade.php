@extends('layouts.app')

@section('title', 'Godziny Pracy - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">
    <h1 class="doc-title">Godziny Pracy</h1>

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
            <h2 class="card-title font-title">Terminarz i Grafiki</h2>

            @if($slots && $slots->count() > 0)
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #1a2b4a;">
                            <th style="padding: 12px 10px;">Dzień</th>
                            <th style="padding: 12px 10px;">Od</th>
                            <th style="padding: 12px 10px;">Do</th>
                            <th style="padding: 12px 10px;">Zarezerwowany</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slots as $slot)
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 16px 10px; font-weight: 600;">
                                    {{ $slot->start_time->format('d.m.Y') }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    {{ $slot->start_time->format('H:i') }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    {{ $slot->end_time->format('H:i') }}
                                </td>
                                <td style="padding: 16px 10px;">
                                    @if($slot->is_booked)
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; background: #d4edda; color: #155724;">Tak</span>
                                    @else
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; background: #ebf8ff; color: #2b6cb0;">Wolny</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #a0aec0;">
                    <span style="font-size: 40px; display: block; margin-bottom: 15px;">⏰</span>
                    Brak zdefiniowanych godzin pracy w grafiku.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
