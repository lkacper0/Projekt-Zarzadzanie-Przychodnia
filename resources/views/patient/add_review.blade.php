@extends('layouts.app')

@section('title', 'Dodaj Opinię - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
            margin: 10px 0;
        }
        .star-rating input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .star-rating label {
            font-size: 2.5rem;
            color: #cbd5e1;
            cursor: pointer;
            transition: color 0.15s ease-in-out, transform 0.1s ease;
            margin: 0;
            line-height: 1;
        }
        .star-rating label:hover {
            transform: scale(1.15);
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #e6a817;
        }
    </style>
@endpush

@section('content')
<div class="auth-body">
    <div class="auth-container" style="max-width: 600px;">
        <div class="auth-header">
            <h1>Dodaj Opinię</h1>
            <p>Podziel się swoimi wrażeniami z wizyty u lekarza</p>
        </div>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 25px;">
            <p style="margin: 0 0 5px 0; font-size: 14px; color: #64748b;">Szczegóły wizyty:</p>
            <h4 style="margin: 0 0 8px 0; color: #003366; font-weight: 700;">
                @if($appointment->slot && $appointment->slot->doctor && $appointment->slot->doctor->user)
                    dr {{ $appointment->slot->doctor->user->first_name }} {{ $appointment->slot->doctor->user->last_name }}
                @else
                    Nieznany Lekarz
                @endif
            </h4>
            <p style="margin: 0; font-size: 14px; color: #475569;">
                <strong>Usługa:</strong> {{ $appointment->service ? $appointment->service->name : 'Standardowa wizyta' }}
            </p>
            <p style="margin: 5px 0 0 0; font-size: 14px; color: #475569;">
                <strong>Data:</strong> {{ $appointment->slot ? $appointment->slot->start_time->format('d.m.Y H:i') : 'brak danych' }}
            </p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left: 15px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/Wizyta/'.$appointment->id.'/Opinia') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label style="font-weight: 600; color: #475569; display: block; margin-bottom: 5px;">Ocena wizyty</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" />
                    <label for="star5" title="Bardzo dobra">★</label>
                    
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="Dobra">★</label>
                    
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="Przeciętna">★</label>
                    
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="Słaba">★</label>
                    
                    <input type="radio" id="star1" name="rating" value="1" required />
                    <label for="star1" title="Bardzo słaba">★</label>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label for="comment" style="font-weight: 600; color: #475569; display: block; margin-bottom: 5px;">Komentarz (opcjonalnie)</label>
                <textarea name="comment" id="comment" class="form-control" rows="5" placeholder="Napisz kilka słów o przebiegu wizyty, punktualności, podejściu lekarza..." style="padding: 12px; border-radius: 10px; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button type="submit" class="btn-auth">
                    Opublikuj opinię
                </button>
                <a href="{{ url('/ListaWizyt') }}" class="btn-auth" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
