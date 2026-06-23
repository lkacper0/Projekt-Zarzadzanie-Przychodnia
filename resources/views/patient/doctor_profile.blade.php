@extends('layouts.app')

@section('title', 'dr ' . $doctor->user->first_name . ' ' . $doctor->user->last_name . ' - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
    <style>
        .profile-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 24px;
            transition: color 0.2s;
        }
        .back-link:hover { color: #357abd; }

        .profile-hero {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,51,102,0.07);
            border: 1px solid #e2e8f0;
            padding: 35px;
            display: flex;
            gap: 30px;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        @media(max-width: 600px) {
            .profile-hero { flex-direction: column; align-items: center; text-align: center; }
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #4a90e2;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(74,144,226,0.2);
        }
        .profile-avatar-initials {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            font-size: 42px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(74,144,226,0.2);
        }
        .profile-info { flex: 1; }
        .profile-name {
            font-size: 28px;
            font-weight: 700;
            color: #003366;
            margin: 0 0 6px 0;
        }
        .profile-rating {
            font-size: 18px;
            color: #e6a817;
            font-weight: 600;
            margin: 0 0 14px 0;
        }
        .profile-rating.no-rating { color: #94a3b8; }
        .profile-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 20px;
        }
        .badge-spec {
            font-size: 12px;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge-tag {
            font-size: 12px;
            background-color: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .profile-bio {
            font-size: 15px;
            color: #475569;
            line-height: 1.7;
            margin: 0;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,51,102,0.05);
            border: 1px solid #e2e8f0;
            padding: 28px 30px;
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #003366;
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #e0f2fe;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }
        .gallery-grid img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .gallery-grid img:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 16px rgba(0,51,102,0.12);
        }

        .reviews-list { display: flex; flex-direction: column; gap: 16px; }
        .review-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px 20px;
            border: 1px solid #e2e8f0;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .review-author {
            font-weight: 700;
            color: #003366;
            font-size: 14px;
        }
        .review-stars {
            color: #e6a817;
            font-weight: 700;
            font-size: 14px;
        }
        .review-comment {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .book-bar {
            position: sticky;
            bottom: 20px;
            display: flex;
            justify-content: center;
            pointer-events: none;
        }
        .book-bar a {
            pointer-events: all;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            font-size: 16px;
            font-weight: 700;
            padding: 16px 48px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(74,144,226,0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .book-bar a:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(74,144,226,0.45);
        }

        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .lightbox-overlay.active { display: flex; }
        .lightbox-overlay img {
            max-width: 90vw;
            max-height: 85vh;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .lightbox-close {
            position: fixed;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 36px;
            cursor: pointer;
            line-height: 1;
        }
    </style>
@endpush

@section('content')
<div class="profile-wrapper">

    <a href="{{ url('/Lekarze') }}" class="back-link">← Wróć do wyszukiwania</a>

    <div class="profile-hero">
        @if($doctor->profile_photo)
            <img src="{{ asset($doctor->profile_photo) }}" alt="Zdjęcie lekarza" class="profile-avatar">
        @else
            <div class="profile-avatar-initials">
                {{ strtoupper(substr($doctor->user->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->user->last_name, 0, 1)) }}
            </div>
        @endif

        <div class="profile-info">
            <h1 class="profile-name">dr {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}</h1>

            @if($doctor->avg_rating > 0)
                <p class="profile-rating">⭐ {{ number_format($doctor->avg_rating, 1) }} / 5.0
                    <span style="font-size:14px; color:#94a3b8; font-weight:400;">({{ $reviews->count() }} {{ $reviews->count() === 1 ? 'opinia' : ($reviews->count() < 5 ? 'opinie' : 'opinii') }})</span>
                </p>
            @else
                <p class="profile-rating no-rating">Brak ocen</p>
            @endif

            <div class="profile-badges">
                @foreach($doctor->specializations as $spec)
                    <span class="badge-spec">{{ $spec->name }}</span>
                @endforeach
                @foreach($doctor->tags as $tag)
                    <span class="badge-tag">{{ $tag->name }}</span>
                @endforeach
            </div>

            <p class="profile-bio">{{ $doctor->bio ?? 'Ten specjalista nie posiada jeszcze opisu profilu.' }}</p>
        </div>
    </div>

    @if($doctor->gallery->count() > 0)
        <div class="section-card">
            <h2 class="section-title">📷 Galeria</h2>
            <div class="gallery-grid">
                @foreach($doctor->gallery as $photo)
                    <img src="{{ asset($photo->path) }}" alt="Zdjęcie z galerii" onclick="openLightbox(this.src)">
                @endforeach
            </div>
        </div>
    @endif

    <div class="section-card">
        <h2 class="section-title">💬 Opinie pacjentów</h2>
        @if($reviews->count() > 0)
            <div class="reviews-list">
                @foreach($reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-author">
                                {{ $review->patient ? $review->patient->first_name . ' ' . substr($review->patient->last_name, 0, 1) . '.' : 'Anonimowy pacjent' }}
                            </span>
                            <span class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '⭐' : '☆' }}
                                @endfor
                                {{ $review->rating }}/5
                            </span>
                        </div>
                        @if($review->comment)
                            <p class="review-comment">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="color:#94a3b8; text-align:center; padding: 20px 0; font-size:15px;">Ten lekarz nie ma jeszcze żadnych opinii.</p>
        @endif
    </div>

    <div class="book-bar">
        <a href="{{ url('/Rezerwacja/lekarz/'.$doctor->id) }}">
            📅 Zarezerwuj wizytę
        </a>
    </div>
</div>

<div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">✕</span>
    <img id="lightbox-img" src="" alt="Podgląd zdjęcia">
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endsection
