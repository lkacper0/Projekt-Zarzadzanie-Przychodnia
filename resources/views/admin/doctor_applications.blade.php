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
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif

    <table class="admin-table">

        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-candidate">Kandydat</th>
                <th class="col-email">E-mail</th>
                <th class="col-bio">Biogram (Opis)</th>
                <th class="col-actions">Akcje</th>
            </tr>
        </thead>
        <tbody>

            @forelse($applications as $app)
            <tr class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">

                <td>{{ $app->id }}</td>
                <td>

                    @if($app->user)
                        {{ $app->user->first_name }} {{ $app->user->last_name }}
                    @else
                        <span class="text-muted-italic">Brak danych</span>
                    @endif

                </td>
                <td>{{ $app->user->email ?? '-' }}</td>
                <td>{{ $app->bio ?? 'Brak opisu.' }}</td>

                <td class="text-center">

                    <form action="{{ url('/admin/doctor-applications/'.$app->id.'/approve') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-approve">Akceptuj</button>
                    </form>
                    <form action="/admin/doctor-applications/{{ $app->id }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz odrzucic to zgłoszenie?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Odrzuć zgłoszenie</button>
                    </form>
                </td>
            </tr>
            @empty

            <tr>
                <td colspan="5" class="text-center text-muted-italic" style="padding: 20px;">Brak nowych zgłoszeń na lekarza.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
