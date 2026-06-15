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

    <table class="admin-table">

        <thead>
            <tr>
                <th>Id</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>E-mail</th>
                <th>PESEL</th>
                <th>Rola</th>
                <th class="text-center">Aktywny?</th>
                <th class="text-center">Opcje</th>

            </tr>
        </thead>
        <tbody>

            @foreach($users as $user)

            <tr class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">

                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->pesel ?? '-' }}</td>
                <td class="text-bold">{{ $user->role }}</td>
                <td class="text-center">

                    @if($user->is_active)
                        <span class="status-active">Tak</span>
                    @else
                        <span class="status-blocked">Zablokowany</span>
                    @endif
                </td>
                <td class="text-center">

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
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>
</div>

@endsection
