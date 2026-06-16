@extends('layouts.app')

@section('title', 'Panel Admina - ProHealth')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">

<div class="admin-container">

    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn active">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn">Specjalizacje</a>
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    <div class="admin-toolbar">

        <form action="{{ url('/admin') }}" method="GET" class="search-form">

            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Szukaj po imieniu, nazwisku lub e-mailu..." class="search-input">

            <button type="submit" class="btn btn-primary">Szukaj</button>

            @if($search)
                <a href="{{ url('/admin') }}" class="btn btn-secondary">Wyczyść</a>

            @endif
        </form>

        <a href="{{ url('/admin/create') }}" class="btn btn-success btn-add-user"> Dodaj Nowego Użytkownika </a>
    </div>

    @if(session('success'))

        <div class="alert-success">

            {{ session('success') }}

        </div>
    @endif

    <div class="container">

        <thead>
            <div class="row">
                <div class="col-1">Id</div>
                <div class="col-1">Imię</div>
                <div class="col-1">Nazwisko</div>
                <div class="col-2">E-mail</div>
                <div class="col-2">PESEL</div>
                <div class="col-1">Rola</div>
                <div class="col-1">Aktywny?</div>
                <div class="col-3">Opcje</div>

            </div>
        </thead>
        <tbody>

            @foreach($users as $user)

            <div class="row" class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">

                <div class="col-1" style="border: 1px solid gray;">{{ $user->id }}</div>
                <div class="col-1" style="border: 1px solid gray;">{{ $user->first_name }}</div>
                <div class="col-1" style="border: 1px solid gray;">{{ $user->last_name }}</div>
                <div class="col-2" style="border: 1px solid gray;">{{ $user->email }}</div>
                <div class="col-2" style="border: 1px solid gray;">{{ $user->pesel ?? '-' }}</div>
                <div class="col-1" style="border: 1px solid gray;">{{ $user->role }}</div>
                <div class="col-1" style="border: 1px solid gray;">

                    @if($user->is_active)
                        <span class="status-active">Tak</span>
                    @else
                        <span class="status-blocked">Zablokowany</span>
                    @endif
                </div>
                <div class="col-3" style="border: 1px solid gray;">

                    <div class="actions-wrapper">

                        <a href="{{ url('/admin/'.$user->id.'/edit') }}" class="btn btn-warning">
                            Edytuj
                        </a>

                        <form action="{{ url('/admin/'.$user->id.'/toggle-ban') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                {{ $user->is_active ? 'Zablokuj' : 'Odblokuj' }}
                            </button>
                        </form>

                        <form action="{{ url('/admin/'.$user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Na pewno chcesz usunąć tego użytkownika?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"> Usuń
                            </button>

                        </form>
                    </div>
                </div>
            </div>

            @endforeach

        </tbody>
    </div>
</div>

@endsection
