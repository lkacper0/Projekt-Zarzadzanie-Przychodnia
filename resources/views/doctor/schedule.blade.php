@extends('layouts.app')

@section('title', 'Godziny Pracy - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Godziny Pracy</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn">Usługi &amp; Cennik</a>
        <a href="{{ url('/GodzinyPracy') }}" class="nav-btn active">Godziny Pracy</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

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
                <p class="booking-subtitle">Ustaw dni i godziny, w których przyjmujesz pacjentów. Wygenerowane terminy będą widoczne w rezerwacji.</p>
            </div>
        </div>
    </div>

    @include('partials.schedule_form', [
        'formAction' => url('/GodzinyPracy/generuj'),
    ])

    @include('partials.schedule_slots', [
        'slots' => $slots,
        'deleteSlotUrl' => fn ($id) => url('/GodzinyPracy/slot/'.$id.'/usun'),
    ])

</div>
@endsection
