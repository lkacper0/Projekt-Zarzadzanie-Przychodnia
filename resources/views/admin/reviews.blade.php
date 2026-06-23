@extends('layouts.app')

@section('title', 'Zarządzanie Opiniami - Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/reviews.css') }}">

<div class="admin-container">

    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn active">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/godziny-pracy') }}" class="nav-btn">Godziny Pracy</a>
        <a href="{{ url('/admin/wizyty') }}" class="nav-btn">Wizyty Lekarzy</a>
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">

        <div class="row">
            <div class="col-1" style="border: 1px solid gray; background-color: #003366; color: white">Id</div>
            <div class="col-2" style="border: 1px solid gray; background-color: #003366; color: white">Pacjent</div>
            <div class="col-2" style="border: 1px solid gray; background-color: #003366; color: white">Lekarz</div>
            <div class="col-1" style="border: 1px solid gray; background-color: #003366; color: white">Ocena</div>
            <div class="col-4" style="border: 1px solid gray; background-color: #003366; color: white">Komentarz</div>
            <div class="col-2" style="border: 1px solid gray; background-color: #003366; color: white">Akcje</div>
        </div>

        @forelse($reviews as $review)
            <div class="row" style="border: 1px solid gray;">
                <div class="col-1">{{ $review->id }}</div>
                <div class="col-2">
                    @if($review->patient)
                        {{ $review->patient->first_name }} {{ $review->patient->last_name }}
                    @else
                        <span class="text-muted-italic">Brak danych</span>
                    @endif
                </div>
                <div class="col-2">
                    @if($review->doctor && $review->doctor->user)
                        {{ $review->doctor->user->first_name }} {{ $review->doctor->user->last_name }}
                    @else
                        <span class="text-muted-italic">Brak danych</span>
                    @endif
                </div>
                <div class="col-1">
                    {{ $review->rating }} / 5
                </div>
                <div class="col-4">{{ $review->comment ?? '-' }}</div>

                <div class="col-2">
                    <form action="{{ url('/admin/reviews/'.$review->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Na pewno chcesz usunąć tę opinię?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">Usuń</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="row">
                <div class="col-12" style="padding: 20px;"> Brak opinii w bazie danych. </div>
            </div>
        @endforelse

    </div> </div>
     @endsection
