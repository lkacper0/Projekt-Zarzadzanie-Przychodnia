@extends('layouts.app')

@section('title', 'Edytuj Użytkownika - Admin')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/edit.css') }}">

<div class="admin-form-container">

    <h2 class="admin-form-title">Edytuj Użytkownika (ID: {{ $user->id }})</h2>

    @if ($errors->any())

        <div class="alert-danger">

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>
    @endif

    <form action="{{ url('/admin/'.$user->id) }}" method="POST">
        
        @csrf
        @method('PUT')

        <div class="form-group">

            <label for="first_name">Imię:</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label herf="last_name">Nazwisko:</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label for="email">Adres E-mail:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label for="pesel">PESEL:</label>

            <input type="text" name="pesel" id="pesel" value="{{ old('pesel', $user->pesel) }}" class="form-control">
        </div>

        <div class="form-group">

            <label for="role">Rola:</label>

            <select name="role" id="role" class="form-select" required>
                <option value="patient" {{ old('role', $user->role) == 'patient' ? 'selected' : '' }}>Pacjent</option>
                <option value="doctor" {{ old('role', $user->role) == 'doctor' ? 'selected' : '' }}>Lekarz</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
        </div>

        <div class="form-group mb-20">

            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="form-text">Pozostaw puste, jeśli nie chcesz zmieniać hasła.</small>
        </div>

        <div class="form-actions">

            <button type="submit" class="btn btn-success">Zapisz Zmiany</button>
            <a href="{{ url('/admin') }}" class="btn btn-secondary">Anuluj</a>


        </div>
    </form>
</div>

@endsection