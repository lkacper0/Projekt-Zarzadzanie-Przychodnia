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
                <th class="col-id">Id</th>
                <th class="col-patient">Pacjent</th>
                <th class="col-doctor">Lekarz</th>
                <th class="col-rating">Ocena</th>
                <th class="col-comment">Komentarz</th>
                <th class="col-actions">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)

            <tr class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">

                <td>{{ $review->id }}</td>
                <td>

                    @if($review->patient)

                        {{ $review->patient->first_name }} {{ $review->patient->last_name }}

                    @else

                        <span class="text-muted-italic">Brak danych</span>

                    @endif

                </td>
                <td>
                    @if($review->doctor && $review->doctor->user)

                        {{ $review->doctor->user->first_name }} {{ $review->doctor->user->last_name }}
                    @else

                        <span class="text-muted-italic">Brak danych</span>

                    @endif
                </td>
                <td class="rating-badge">

                    {{ $review->rating }} / 5

                </td>
                <td>{{ $review->comment ?? '-' }}</td>

                <td class="text-center">
                    <form action="{{ url('/admin/reviews/'.$review->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Na pewno chcesz usunąć tę opinię?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn-delete">Usuń</button>

                    </form>
                </td>
            </tr>
            @empty
            <tr>

                <td colspan="6" class="text-center text-muted-italic" style="padding: 20px;"> Brak opini w bazie danych. </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
