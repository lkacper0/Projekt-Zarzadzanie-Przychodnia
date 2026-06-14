@extends('layouts.app')

@section('title', 'Rezerwacja - dr {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">

<div class="booking-container">

    <div class="booking-doctor-header">
        <a href="{{ url('/Rezerwacja') }}" class="btn btn-secondary btn-sm" style="margin-bottom:16px;">← Wróć</a>
        <div class="booking-doctor-info">

            <div class="doctor-avatar doctor-avatar-lg">
                @if($doctor->profile_photo)
                    <img src="{{ asset($doctor->profile_photo) }}" alt="Zdjęcie lekarza">
                @else
                    <span>{{ strtoupper(substr($doctor->user->first_name,0,1)) }}{{ strtoupper(substr($doctor->user->last_name,0,1)) }}</span>
                @endif
            </div>
            <div>
                <h1 class="booking-title" style="margin-bottom:4px;">
                    dr {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}
                </h1>
                @if($doctor->bio)
                    <p class="booking-subtitle">{{ Str::limit($doctor->bio, 120) }}</p>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if($doctor->services->isNotEmpty())
    <div class="service-select-bar">
        <label for="service_select"><strong>Usługa:</strong></label>
        <select id="service_select" class="form-control" style="max-width:320px;">
            @foreach($doctor->services as $svc)
                <option value="{{ $svc->id }}">
                    {{ $svc->name }} – {{ number_format($svc->price, 2) }} zł ({{ $svc->duration_minutes }} min)
                </option>
            @endforeach
        </select>
    </div>
    @endif

    @if($slots->isEmpty())
        <div class="empty-state" style="margin-top:30px;">
            <p>Ten lekarz nie ma aktualnie wolnych terminów.</p>
        </div>
    @else
        @foreach($slots as $date => $daySlots)
        <div class="day-block">
            <h3 class="day-label">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d.m.Y') }}
            </h3>

            <div class="slots-grid">
                @foreach($daySlots as $slot)
                <div class="slot-tile slot-free slot-clickable"
                     data-slot-id="{{ $slot->id }}"
                     data-time="{{ $slot->start_time->format('H:i') }}"
                     data-date="{{ $slot->start_time->format('d.m.Y') }}">
                    <span class="slot-time">{{ $slot->start_time->format('H:i') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif

</div>


<div id="booking-modal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h2 class="modal-title">Potwierdź rezerwację</h2>
        <p id="modal-info" class="modal-info"></p>

        <form id="booking-form" method="POST" action="">
            @csrf
            <input type="hidden" name="service_id" id="modal-service-id">

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Anuluj</button>
                <button type="submit" class="btn btn-primary">Rezerwuj!</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tiles = document.querySelectorAll('.slot-clickable');
    const modal = document.getElementById('booking-modal');
    const form  = document.getElementById('booking-form');

    tiles.forEach(tile => {
        tile.addEventListener('click', () => {
            const slotId   = tile.dataset.slotId;
            const time     = tile.dataset.time;
            const date     = tile.dataset.date;
            const svcId    = document.getElementById('service_select')?.value ?? '';
            const svcText  = document.getElementById('service_select')?.options[document.getElementById('service_select').selectedIndex]?.text ?? '';

            document.getElementById('modal-info').textContent =
                `📅 ${date}  🕐 ${time}  •  ${svcText}`;
            document.getElementById('modal-service-id').value = svcId;
            form.action = `/Rezerwacja/slot/${slotId}`;

            modal.style.display = 'flex';
            tile.classList.add('slot-selected');
        });
    });

    function closeModal() {
        modal.style.display = 'none';
        document.querySelectorAll('.slot-selected').forEach(t => t.classList.remove('slot-selected'));
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
</script>
@endsection
