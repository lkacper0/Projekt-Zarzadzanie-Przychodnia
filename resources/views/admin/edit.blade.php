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

        <div class="form-group" id="bio-group" style="display: {{ old('role', $user->role) == 'doctor' ? 'block' : 'none' }};">
            <label for="bio">Opis lekarza (Bio):</label>
            <textarea name="bio" id="bio" class="form-control" rows="5">{{ old('bio', $profile->bio ?? '') }}</textarea>
        </div>

        <div class="form-group" id="tags-group" style="display: {{ old('role', $user->role) == 'doctor' ? 'block' : 'none' }};">
            <label>Tagi lekarza:</label>
            @if($allTags->isNotEmpty())
                <div class="tags-checkbox-grid">
                    @foreach($allTags as $tag)
                        <label class="tag-checkbox-label">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ ($profile && $profile->tags->contains('id', $tag->id)) ? 'checked' : '' }}>
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="tags-empty">Brak dostępnych tagów w systemie.</p>
            @endif

            <div style="margin-top: 15px;">
                <label for="new_tag">Dodaj nowy tag (opcjonalnie):</label>
                <input type="text" name="new_tag" id="new_tag" class="form-control" placeholder="np. Kardiologia dziecięca">
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const bioGroup = document.getElementById('bio-group');
    const tagsGroup = document.getElementById('tags-group');

    function toggleBio() {
        if (roleSelect.value === 'doctor') {
            bioGroup.style.display = 'block';
            tagsGroup.style.display = 'block';
        } else {
            bioGroup.style.display = 'none';
            tagsGroup.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', toggleBio);
});
</script>