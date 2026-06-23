@extends('layouts.app')

@section('title', 'Zgłoszenia Lekarzy - Admin')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/doctor_applications.css') }}">

<div class="admin-container">

    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn active">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/godziny-pracy') }}" class="nav-btn">Godziny Pracy</a>
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

        <thead>
            <div class="row">
                <div class="col-1" style="border: 1px solid gray; background-color: #003366; color: white">Id</div>
                <div class="col-1" style="border: 1px solid gray; background-color: #003366; color: white">Kandydat</div>
                <div class="col-3" style="border: 1px solid gray; background-color: #003366; color: white">E-mail</div>
                <div class="col-5" style="border: 1px solid gray; background-color: #003366; color: white">Biogram (Opis)</div>
                <div class="col-2" style="border: 1px solid gray; background-color: #003366; color: white">Akcje</div>
            </div>
        </thead>
        <tbody>

            @forelse($applications as $app)
            <div class="row" class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">

                <div class="col-1">{{ $app->id }}</div>
                <div class="col-1">

                    @if($app->user)
                        {{ $app->user->first_name }} {{ $app->user->last_name }}
                    @else
                        <span class="text-muted-italic">Brak danych</span>
                    @endif

                </div>
                <div class="col-3">{{ $app->user->email ?? '-' }}</div>
                <div class="col-5">{{ $app->bio ?? 'Brak opisu.' }}</div>

                <div class="col-2">

                    <form action="{{ url('/admin/doctor-applications/'.$app->id.'/approve') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-approve">Akceptuj</button>
                    </form>
                    <form action="/admin/doctor-applications/{{ $app->id }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz odrzucic to zgłoszenie?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Odrzuć zgłoszenie</button>
                    </form>
                </div>
            </div>
            @empty

            <tr>
                <td colspan="5" class="text-center text-muted-italic" style="padding: 20px;">Brak nowych zgłoszeń na lekarza.</td>
            </tr>
            @endforelse
        </tbody>
    </div>
</div>
@endsection
