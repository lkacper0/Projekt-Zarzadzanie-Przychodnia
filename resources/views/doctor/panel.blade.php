@extends('layouts.app')

@section('title', 'Panel Lekarza - ProHealth')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn active">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn">Usługi &amp; Cennik</a>
        <a href="{{ url('/GodzinyPracy') }}" class="nav-btn">Godziny Pracy</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="doc-grid">

        <div class="doc-card photo-card">
            <div class="photo-wrapper">
                @if($profile && $profile->profile_photo)
                    <img src="{{ asset($profile->profile_photo) }}" alt="Zdjęcie profilowe" class="profile-img">
                @else
                    <div class="profile-placeholder">
                        <span>{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <p class="doc-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            <p class="doc-email">{{ $user->email }}</p>
            @if($profile && $profile->avg_rating > 0)
                <p class="doc-rating">⭐ {{ number_format($profile->avg_rating, 1) }} / 5.0</p>
            @endif
            @if($profile && !$profile->is_accepted)
                <span class="badge-pending">Oczekuje na akceptację</span>
            @else
                <span class="badge-active">Profil aktywny</span>
            @endif
        </div>

        <div class="doc-card" id="profile-view-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
                <h2 class="card-title" style="margin: 0; border: none; padding: 0;">Mój Profil</h2>
                <button type="button" class="btn btn-primary btn-sm" id="btn-show-edit">Edytuj Profil</button>
            </div>

            <div class="profile-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <strong >Imię i nazwisko:</strong>
                    <p>{{ $user->first_name }} {{ $user->last_name }}</p>
                </div>
                <div>
                    <strong >Adres e-mail:</strong>
                    <p>{{ $user->email }}</p>
                </div>
                <div>
                    <strong >Numer PESEL:</strong>
                    <p>{{ $user->pesel }}</p>
                </div>
                <div>
                    <strong >Specjalizacja:</strong>
                    <p>
                        @if($profile && $profile->specializations->isNotEmpty())
                            {{ $profile->specializations->pluck('name')->join(', ') }}
                        @else
                            <span>Brak zdefiniowanej specjalizacji</span>
                        @endif
                    </p>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <strong >O mnie / Bio:</strong>
                <div>
                    {{ $profile->bio ?? 'Brak opisu profilu.' }}
                </div>
            </div>
        </div>

        <div class="doc-card form-card" id="profile-edit-card" style="display: none;">
            <h2 class="card-title">Edytuj Profil</h2>

            <form action="{{ url('/PanelLekarza/profil') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Imię</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Nazwisko</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Adres e-mail</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="pesel">Numer PESEL</label>
                        <input type="text" name="pesel" id="pesel" class="form-control" value="{{ old('pesel', $user->pesel) }}" maxlength="11" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_photo">Zdjęcie profilowe</label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="form-control">
                    <small>Dozwolone formaty: JPG, PNG, WEBP. Maks. 2 MB.</small>
                </div>

                <div class="form-group">
                    <label for="bio">O mnie / Bio</label>
                    <textarea name="bio" id="bio" rows="6" class="form-control" placeholder="Napisz coś o sobie, swoim doświadczeniu, specjalizacji...">{{ old('bio', $profile->bio ?? '') }}</textarea>
                </div>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                <p style="color: #718096; font-size: 0.85rem; margin-bottom: 12px;">Pozostaw poniższe pola puste, jeśli nie chcesz zmieniać hasła:</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Nowe hasło</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Powtórz nowe hasło</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" autocomplete="new-password">
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                    <button type="button" class="btn btn-secondary" id="btn-hide-edit">Anuluj</button>
                </div>
            </form>
        </div>

    </div>

    <div class="doc-card full-width-card" style="margin-top:30px;">
        <h2 class="card-title">Moje specjalizacje</h2>

        @if($profile && $profile->specializations->isNotEmpty())
            <div class="tags-current" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                @foreach($profile->specializations as $spec)
                    <span class="tag-pill" style="background-color: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem;">
                        {{ $spec->name }}
                        <form action="{{ url('/PanelLekarza/specjalizacje/' . $spec->id) }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #e53e3e; cursor: pointer; font-weight: bold; font-size: 1.1rem; padding: 0; line-height: 1; display: flex; align-items: center;" title="Usuń">&times;</button>
                        </form>
                    </span>
                @endforeach
            </div>
        @else
            <p class="tags-empty">Nie masz jeszcze żadnych specjalizacji.</p>
        @endif

        @php
            $mySpecIds = $profile ? $profile->specializations->pluck('id')->toArray() : [];
            $hasAvailableSpecs = $allSpecializations->reject(fn($s) => in_array($s->id, $mySpecIds))->isNotEmpty();
        @endphp

        @if($hasAvailableSpecs)
            <form action="{{ url('/PanelLekarza/specjalizacje/dodaj') }}" method="POST" style="margin-top: 15px;">
                @csrf
                <div class="form-group" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <label for="specialization_id" style="margin: 0; font-weight: 600;">Wybierz specjalizację, aby ją dodać:</label>
                    <select name="specialization_id" id="specialization_id" class="form-control" style="max-width: 280px;" required>
                        <option value="" disabled selected>-- Wybierz z listy --</option>
                        @foreach($allSpecializations as $spec)
                            @if(!in_array($spec->id, $mySpecIds))
                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Dodaj</button>
                </div>
            </form>
        @else
            @if($allSpecializations->isEmpty())
                <p class="tags-empty" style="color:#718096; font-style:italic; margin-top: 10px;">Brak dostępnych specjalizacji w systemie. Poproś administratora o ich dodanie.</p>
            @else
                <p class="tags-empty" style="color:#2b6cb0; font-style:italic; margin-top: 10px;">Posiadasz już wszystkie specjalizacje dostępne w systemie.</p>
            @endif
        @endif
    </div>

    <div class="doc-card full-width-card" style="margin-top:30px;">
        <h2 class="card-title">Moje tagi</h2>

        @if($profile && $profile->tags->isNotEmpty())
            <div class="tags-current">
                @foreach($profile->tags as $tag)
                    <span class="tag-pill">{{ $tag->name }}</span>
                @endforeach
            </div>
        @else
            <p class="tags-empty">Nie masz jeszcze żadnych tagów.</p>
        @endif

        <form action="{{ url('/PanelLekarza/tagi') }}" method="POST" class="tags-form">
            @csrf

            @if($allTags->isNotEmpty())
                <label class="form-label-sm">Wybierz tagi:</label>
                <div class="tags-checkbox-grid">
                    @foreach($allTags as $tag)
                        <label class="tag-checkbox-label">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ ($profile && $profile->tags->contains('id', $tag->id)) ? 'checked' : '' }}>
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="form-group" style="margin-top:14px;">
                <label for="new_tag">Lub dodaj nowy tag:</label>
                <div style="display:flex;gap:10px;align-items:center;">
                    <input type="text" name="new_tag" id="new_tag" class="form-control" style="max-width:260px;" placeholder="np. Ortopedia">
                    <button type="submit" class="btn btn-primary btn-sm">Zapisz tagi</button>
                </div>
            </div>
        </form>
    </div>

    <div class="doc-card full-width-card" style="margin-top:30px;">
        <h2 class="card-title">Galeria zdjęć</h2>

        @if($profile && $profile->gallery->isNotEmpty())
            <div class="gallery-grid">
                @foreach($profile->gallery as $photo)
                    <div class="gallery-item">
                        <img src="{{ asset($photo->image_url) }}" alt="Zdjęcie galerii">
                        <form action="{{ url('/PanelLekarza/galeria/' . $photo->id) }}" method="POST" class="gallery-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="gallery-delete-btn" title="Usuń zdjęcie">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="tags-empty">Brak zdjęć w galerii.</p>
        @endif

        <form action="{{ url('/PanelLekarza/galeria') }}" method="POST" enctype="multipart/form-data" style="margin-top:18px;">
            @csrf
            <div class="form-group">
                <label for="gallery_photos">Dodaj zdjęcia</label>
                <input type="file" name="gallery_photos[]" id="gallery_photos" accept="image/*" multiple class="form-control">
                <small>Możesz wybrać kilka zdjęć naraz. JPG, PNG, WEBP. Maks. 3 MB na zdjęcie.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Dodaj do galerii</button>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewCard = document.getElementById('profile-view-card');
    const editCard = document.getElementById('profile-edit-card');
    const showEditBtn = document.getElementById('btn-show-edit');
    const hideEditBtn = document.getElementById('btn-hide-edit');

    if (showEditBtn && hideEditBtn && viewCard && editCard) {
        showEditBtn.addEventListener('click', function() {
            viewCard.style.display = 'none';
            editCard.style.display = 'block';
        });

        hideEditBtn.addEventListener('click', function() {
            editCard.style.display = 'none';
            viewCard.style.display = 'block';
        });

        @if($errors->any())
            viewCard.style.display = 'none';
            editCard.style.display = 'block';
        @endif
    }
});
</script>

@endsection
