@extends('layouts.app')

@section('title', 'Dodaj usługę - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn active">Usługi &amp; Cennik</a>
    </div>

    @if($errors->any())
        <div class="alert-error">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="doc-card form-card" style="max-width:600px;margin:30px auto;">
        <h2 class="card-title">Dodaj nową usługę</h2>

        <form action="{{ url('/PanelLekarza/uslugi') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nazwa usługi *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required placeholder="np. Konsultacja ogólna">
            </div>

            <div class="form-group">
                <label for="description">Opis</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Krótki opis usługi...">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Cena (zł) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" class="form-control" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="duration_minutes">Czas trwania (min) *</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="5" max="60" class="form-control" required placeholder="30">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ url('/PanelLekarza/uslugi') }}" class="btn btn-secondary">Anuluj</a>
                <button type="submit" class="btn btn-primary">Dodaj usługę</button>
            </div>
        </form>
    </div>

</div>

@endsection
