@extends('layouts.app')

@section('title', 'Edytuj usługę - Admin')

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

    @if($errors->any())
        <div class="alert-error">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="doc-card form-card" style="max-width:600px;margin:30px auto; padding: 25px;">
        <h2 class="card-title">Edytuj usługę dla dr {{ $profile->user->first_name }} {{ $profile->user->last_name }}</h2>

        <form action="{{ url('/admin/uslugi/'.$service->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nazwa usługi *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="description">Opis</label>
                <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Cena (zł) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="duration_minutes">Czas trwania (min) *</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="5" max="480" class="form-control" required>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <a href="{{ url('/admin/uslugi?doctor_id='.$profile->id) }}" class="btn btn-secondary">Anuluj</a>
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </div>
        </form>
    </div>

</div>
@endsection
