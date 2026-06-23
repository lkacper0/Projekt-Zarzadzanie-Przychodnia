@extends('layouts.app')

@section('title', 'Zarządzanie Usługami Lekarzy - Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="admin-container">
    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/godziny-pracy') }}" class="nav-btn">Godziny Pracy</a>
        <a href="{{ url('/admin/wizyty') }}" class="nav-btn">Wizyty Lekarzy</a>
        <a href="{{ url('/admin/uslugi') }}" class="nav-btn active">Usługi</a>
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="doc-card" style="margin-bottom: 24px; padding: 20px;">
        <form method="GET" action="{{ url('/admin/uslugi') }}" class="admin-search-form">
            <label for="doctor_id" style="font-weight: bold; display: block; margin-bottom: 8px;">Wybierz lekarza:</label>
            <div style="display: flex; gap: 10px;">
                <select name="doctor_id" id="doctor_id" class="form-control" style="padding: 10px; flex-grow: 1; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Wybierz lekarza z listy --</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ isset($selectedDoctorId) && $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                            dr {{ $doc->user->first_name }} {{ $doc->user->last_name }} ({{ $doc->user->email }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="nav-btn active" style="border: none; cursor: pointer;">Pokaż usługi</button>
            </div>
        </form>
    </div>

    @if(!$profile)
        <div class="empty-state">
            <p>Wybierz lekarza z powyższej listy, aby zarządzać jego usługami i cennikiem.</p>
        </div>
    @else
        <div class="services-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="section-title">Usługi lekarza: dr {{ $profile->user->first_name }} {{ $profile->user->last_name }}</h2>
                <p style="color: #718096; margin: 4px 0 0 0;">Zarządzasz usługami lekarza jako Administrator.</p>
            </div>
            <a href="{{ url('/admin/uslugi/dodaj?doctor_id='.$profile->id) }}" class="btn btn-success">+ Dodaj usługę</a>
        </div>

        @if($services->isEmpty())
            <div class="empty-state">
                <p>Ten lekarz nie ma jeszcze zdefiniowanych żadnych usług.</p>
            </div>
        @else
            <div class="services-grid">
                @foreach($services as $service)
                <div class="service-card">
                    <div class="service-header">
                        <h3 class="service-name">{{ $service->name }}</h3>
                        <span class="service-price">{{ number_format($service->price, 2) }} zł</span>
                    </div>
                    @if($service->description)
                        <p class="service-desc">{{ $service->description }}</p>
                    @endif
                    <div class="service-footer">
                        <span class="service-duration">⏱ {{ $service->duration_minutes }} min</span>
                        <div class="service-actions">
                            <a href="{{ url('/admin/uslugi/'.$service->id.'/edytuj') }}" class="btn btn-warning btn-sm">Edytuj</a>
                            <form action="{{ url('/admin/uslugi/'.$service->id) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Usunąć tę usługę?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
