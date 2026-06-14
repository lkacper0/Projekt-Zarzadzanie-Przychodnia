@extends('layouts.app')

@section('title', 'Wyszukiwanie Lekarzy - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
    <style>
        /* Custom styles for search and filters */
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 30px;
        }
        @media(max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .doctor-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.04);
            border: 1px solid #e2e8f0;
            padding: 25px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 51, 102, 0.08);
            border-color: #cbd5e1;
        }
        .doc-card-header {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .doc-avatar {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(74, 144, 226, 0.15);
            flex-shrink: 0;
        }
        .doc-avatar-img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #4a90e2;
        }
        .doc-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .doc-title-name {
            font-family: 'Outfit', sans-serif;
            color: #003366;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }
        .doc-rating-stars {
            font-size: 14px;
            color: #e6a817;
            font-weight: 600;
            margin: 0;
        }
        .doc-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
        .badge-spec {
            font-size: 11px;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .badge-tag {
            font-size: 11px;
            background-color: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .doc-bio-snippet {
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
            margin: 0 0 15px 0;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Pagination Styling */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination-wrapper ul {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 8px;
        }
        .pagination-wrapper li span, 
        .pagination-wrapper li a {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s;
            background-color: white;
        }
        .pagination-wrapper li a:hover {
            border-color: #4a90e2;
            color: #4a90e2;
            background-color: #f0f8ff;
        }
        .pagination-wrapper li.active span {
            background-color: #4a90e2;
            color: white;
            border-color: #4a90e2;
        }
        .pagination-wrapper li.disabled span {
            background-color: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
<div class="patient-container">
    <h1 class="panel-title">Wyszukiwanie Lekarzy</h1>

    <!-- Filters Section -->
    <div class="panel-card" style="margin-bottom: 40px; padding: 25px;">
        <form method="GET" action="{{ url('/Lekarze') }}">
            <div class="filter-form">
                <!-- Search Query -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="search" style="font-weight: 600; color: #475569; margin-bottom: 6px; display: block; font-size: 13px;">Szukaj (Imię, Nazwisko, Bio)</label>
                    <input type="text" name="search" id="search" class="form-control" style="padding: 11px;" value="{{ $search }}" placeholder="Wpisz np. kowalski...">
                </div>

                <!-- Specialization -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="specialization" style="font-weight: 600; color: #475569; margin-bottom: 6px; display: block; font-size: 13px;">Specjalizacja</label>
                    <select name="specialization" id="specialization" class="form-control" style="padding: 11px;">
                        <option value="">Wszystkie specjalizacje</option>
                        @foreach($specializations as $spec)
                            <option value="{{ $spec->id }}" {{ $specializationId == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tag" style="font-weight: 600; color: #475569; margin-bottom: 6px; display: block; font-size: 13px;">Tagi / Umowa</label>
                    <select name="tag" id="tag" class="form-control" style="padding: 11px;">
                        <option value="">Dowolna umowa / tag</option>
                        @foreach($tags as $t)
                            <option value="{{ $t->id }}" {{ $tagId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sorting -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="sort" style="font-weight: 600; color: #475569; margin-bottom: 6px; display: block; font-size: 13px;">Sortowanie</label>
                    <select name="sort" id="sort" class="form-control" style="padding: 11px;">
                        <option value="alphabetical" {{ $sort === 'alphabetical' ? 'selected' : '' }}>Alfabetycznie (Nazwisko)</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Ocena (Najwyższa)</option>
                        <option value="specialization" {{ $sort === 'specialization' ? 'selected' : '' }}>Specjalizacja</option>
                    </select>
                </div>

                <!-- Submit / Reset Buttons -->
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-auth" style="padding: 11px 25px; box-shadow: none; width: auto; white-space: nowrap;">Filtruj</button>
                    <a href="{{ url('/Lekarze') }}" class="btn-auth" style="padding: 11px 20px; box-shadow: none; width: auto; background: #94a3b8; text-decoration: none; display: flex; align-items: center; justify-content: center;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Doctors Listing -->
    @if($doctors && $doctors->count() > 0)
        <div class="doctors-grid">
            @foreach($doctors as $doctor)
                @if($doctor->user)
                    <div class="doctor-card">
                        <div class="doc-card-header">
                            @if($doctor->profile_photo)
                                <img src="{{ asset($doctor->profile_photo) }}" alt="Avatar" class="doc-avatar-img">
                            @else
                                <div class="doc-avatar">
                                    {{ strtoupper(substr($doctor->user->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->user->last_name, 0, 1)) }}
                                </div>
                            @endif
                            
                            <div class="doc-info">
                                <h3 class="doc-title-name">dr {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}</h3>
                                @if($doctor->avg_rating > 0)
                                    <p class="doc-rating-stars">⭐ {{ number_format($doctor->avg_rating, 1) }} / 5.0</p>
                                @else
                                    <p class="doc-rating-stars" style="color: #94a3b8;">Brak ocen</p>
                                @endif
                            </div>
                        </div>

                        <!-- Specializations & Tags -->
                        <div class="doc-badges">
                            @foreach($doctor->specializations as $spec)
                                <span class="badge-spec">{{ $spec->name }}</span>
                            @endforeach
                            @foreach($doctor->tags as $t)
                                <span class="badge-tag">{{ $t->name }}</span>
                            @endforeach
                        </div>

                        <p class="doc-bio-snippet">
                            {{ $doctor->bio ?? 'Ten specjalista nie posiada jeszcze szczegółowego opisu profilu.' }}
                        </p>

                        <button onclick="alert('Moduł rezerwacji terminów wizyt jest w trakcie wdrażania.')" class="btn-auth" style="margin-top: auto; padding: 10px; font-size: 14px; box-shadow: none;">Zarezerwuj wizytę</button>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $doctors->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="panel-card empty-state" style="padding: 60px 20px;">
            <div class="empty-state-icon">🧑‍⚕️</div>
            <p>Nie znaleziono lekarzy spełniających wybrane kryteria.</p>
        </div>
    @endif
</div>
@endsection
