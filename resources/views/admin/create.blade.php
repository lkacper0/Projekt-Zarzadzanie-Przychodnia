@extends('layouts.app')

@section('title', 'Dodaj Użytkownika - Admin')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/edit.css') }}">

<div class="admin-form-container">

    <h2 class="admin-form-title">Dodaj Nowego Użytkownika</h2>

    @if ($errors->any())

        <div class="alert-danger">

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    <form action="{{ url('/admin') }}" method="POST">
        
        @csrf

        <div class="form-group">

            <label for="first_name">Imię:</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label for="last_name">Nazwisko:</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label for="email">Adres E-mail:</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="form-group">

            <label for="pesel">PESEL:</label>
            <input type="text" name="pesel" id="pesel" value="{{ old('pesel') }}" class="form-control">
        </div>

        <div class="form-group">

            <label for="role">Rola:</label>
            <select name="role" id="role" class="form-select" required>
                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Pacjent</option>
                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Lekarz</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
        </div>

        <div class="form-group mb-20">

            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-actions">

            <button type="submit" class="btn btn-success">Zapisz</button>
            <a href="{{ url('/admin') }}" class="btn btn-secondary">Anuluj</a>

        </div>
    </form>
</div>

@endsection