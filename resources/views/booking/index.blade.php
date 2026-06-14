@extends('layouts.app')

@section('title', 'Rezerwacja wizyty - ProHealth')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">

<div class="booking-container">

    <h1 class="booking-title">Zarezerwuj wizytę</h1>
    <p class="booking-subtitle">Wybierz lekarza, u którego chcesz się umówić</p>

    @if($doctors->isEmpty())
        <div class="empty-state">
            <p>Brak dostępnych lekarzy w tej chwili. Spróbuj później.</p>
        </div>
    @else
        <div class="doctors-grid">
            @foreach($doctors as $doctor)
            <a href="{{ url('/Rezerwacja/lekarz/'.$doctor->id) }}" class="doctor-card">
                <div class="doctor-avatar">
                    @if($doctor->profile_photo)
                        <img src="{{ asset($doctor->profile_photo) }}" alt="Zdjęcie lekarza">
                    @else
                        <span>{{ strtoupper(substr($doctor->user->first_name,0,1)) }}{{ strtoupper(substr($doctor->user->last_name,0,1)) }}</span>
                    @endif
                </div>
                <div class="doctor-info">
                    <p class="doctor-name">dr {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}</p>
                    @if($doctor->avg_rating > 0)
                        <p class="doctor-rating">⭐ {{ number_format($doctor->avg_rating,1) }}</p>
                    @endif
                    <p class="doctor-cta">Sprawdź terminy →</p>
                </div>
            </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
