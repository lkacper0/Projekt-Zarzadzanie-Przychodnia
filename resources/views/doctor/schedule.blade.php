@extends('layouts.app')

@section('title', 'Harmonogram - Panel Lekarza')

@section('content')
<link rel="stylesheet" href="{{ asset('css/doctor/panel.css') }}">
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">

<div class="doc-container">

    <h1 class="doc-title">Panel Lekarza</h1>

    <div class="doc-nav">
        
        <a href="{{ url('/PanelLekarza') }}" class="nav-btn">Mój Profil</a>
        <a href="{{ url('/PanelLekarza/uslugi') }}" class="nav-btn">Usługi &amp; Cennik</a>
        <a href="{{ url('/PanelLekarza/harmonogram') }}" class="nav-btn active">Harmonogram</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="sch-form-card">
        <h2 class="card-title">Dodaj dostępność</h2>

        <form action="{{ url('/PanelLekarza/harmonogram/generuj') }}" method="POST" class="sch-form">
            @csrf

            <div class="sch-form-row">
                <div class="form-group">
                    <label for="date">Data</label>
                    <input type="date" name="date" id="date" class="form-control"
                           value="{{ old('date', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="start_time">Od</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                           value="{{ old('start_time', '08:00') }}" required>
                </div>

                <div class="form-group">
                    <label for="end_time">Do</label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                           value="{{ old('end_time', '16:00') }}" required>
                </div>

                <div class="form-group">
                    <label for="slot_minutes">Slot (min)</label>

                    <select name="slot_minutes" id="slot_minutes" class="form-control">
                        <option value="10">10 min</option>
                        <option value="15" selected>15 min</option>
                        <option value="20">20 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>

                    </select>
                </div>

                <div class="form-group form-group-btn">

                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Generuj sloty</button>

                </div>
            </div>
        </form>
    </div>

    @if($slots->isEmpty())
        <div class="empty-state" style="margin-top:30px;">
            <p>Brak slotów. Użyj formularza powyżej, żeby dodać dostępność.</p>
        </div>
    @else
        @foreach($slots as $date => $daySlots)
            <div class="day-block">
                <h3 class="day-label">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d.m.Y') }}
                </h3>

                <div class="slots-grid">
                    @foreach($daySlots as $slot)
                        <div class="slot-tile {{ $slot->is_booked ? 'slot-booked' : 'slot-free' }}">
                            <span class="slot-time">{{ $slot->start_time->format('H:i') }}</span>

                            @if($slot->is_booked && $slot->appointment?->patient)
                                <span class="slot-patient">
                                    {{ $slot->appointment->patient->first_name }}
                                    {{ $slot->appointment->patient->last_name }}
                                </span>


                            @elseif(!$slot->is_booked)
                                <form action="{{ url('/PanelLekarza/harmonogram/slot/'.$slot->id.'/usun') }}"
                                      method="POST"
                                      onsubmit="return confirm('Usunąć ten slot?');">

                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="slot-delete-btn" title="Usuń">✕</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

</div>
@endsection
