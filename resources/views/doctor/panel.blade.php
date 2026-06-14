@extends('layouts.app')

@section('title', 'Panel Lekarza - ProHealth')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn active">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn">Usługi &amp; Cennik</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="doc-grid">

        {{-- Zdjęcie profilowe --}}
        <div class="doc-card photo-card">
            <div class="photo-wrapper">
                @if($profile && $profile->profile_photo)
                    <img src="{{ asset($profile->profile_photo) }}" alt="Zdjęcie profilowe" class="profile-img">
                @else
                    <div class="profile-placeholder">
                        <span>{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <p class="doc-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            <p class="doc-email">{{ $user->email }}</p>
            @if($profile && $profile->avg_rating > 0)
                <p class="doc-rating">⭐ {{ number_format($profile->avg_rating, 1) }} / 5.0</p>
            @endif
            @if($profile && !$profile->is_accepted)
                <span class="badge-pending">Oczekuje na akceptację</span>
            @else
                <span class="badge-active">Profil aktywny</span>
            @endif
        </div>

        {{-- Formularz edycji profilu --}}
        <div class="doc-card form-card">
            <h2 class="card-title">Edytuj Profil</h2>

            <form action="{{ url('/PanelLekarza/profil') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="profile_photo">Zdjęcie profilowe</label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="form-control">
                    <small>Dozwolone formaty: JPG, PNG, WEBP. Maks. 2 MB.</small>
                </div>

                <div class="form-group">
                    <label for="bio">O mnie / Bio</label>
                    <textarea name="bio" id="bio" rows="6" class="form-control" placeholder="Napisz coś o sobie, swoim doświadczeniu, specjalizacji...">{{ old('bio', $profile->bio ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </form>
        </div>

    </div>
</div>

@endsection
