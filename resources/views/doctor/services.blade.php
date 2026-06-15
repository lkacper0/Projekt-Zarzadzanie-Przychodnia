@extends('layouts.app')

@section('title', 'Usługi - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn active">Usługi &amp; Cennik</a>
        <a href="{{ url('/GodzinyPracy') }}" class="nav-btn">Godziny Pracy</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="services-header">
        <h2 class="section-title">Moje Usługi</h2>
        <a href="{{ url('/PanelLekarza/uslugi/dodaj') }}" class="btn btn-success">+ Dodaj usługę</a>
    </div>

    @if($services->isEmpty())
        <div class="empty-state">
            <p>Nie masz jeszcze żadnych usług. Dodaj pierwszą!</p>
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
                        <a href="{{ url('/PanelLekarza/uslugi/'.$service->id.'/edytuj') }}" class="btn btn-warning btn-sm">Edytuj</a>
                        <form action="{{ url('/PanelLekarza/uslugi/'.$service->id) }}" method="POST" style="display:inline;"
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

</div>

@endsection
