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
                    <img src="{{ asset($doctor->profile_photo) }}" alt="Zdjęcie lekarza" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
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
                <option value="{{ $svc->id }}" data-duration="{{ $svc->duration_minutes }}">
                    {{ $svc->name }} – {{ number_format($svc->price, 2) }} zł ({{ $svc->duration_minutes }} min)
                </option>
            @endforeach
        </select>
    </div>
    @else
        <div class="alert-error" style="margin-top:20px;">
            Ten lekarz nie ma jeszcze zdefiniowanych usług. Rezerwacja jest tymczasowo niedostępna.
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
                @php $slotDuration = $slot->start_time->diffInMinutes($slot->end_time); @endphp
                <div class="slot-tile slot-free {{ $doctor->services->isNotEmpty() ? 'slot-clickable' : 'slot-disabled' }}"
                     data-slot-id="{{ $slot->id }}"
                     data-time="{{ $slot->start_time->format('H:i') }}"
                     data-date="{{ $slot->start_time->format('d.m.Y') }}"
                     data-slot-duration="{{ $slotDuration }}">
                    <span class="slot-time">{{ $slot->start_time->format('H:i') }}</span>
                    <span class="slot-duration-label">{{ $slotDuration }} min</span>
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
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Anuluj</button>
                <button type="submit" class="btn btn-primary">Rezerwuj!</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tiles = document.querySelectorAll('.slot-tile.slot-free');
    const modal = document.getElementById('booking-modal');
    const form  = document.getElementById('booking-form');
    const serviceSelect = document.getElementById('service_select');

    function getSelectedServiceDuration() {
        if (!serviceSelect) return 0;
        const opt = serviceSelect.options[serviceSelect.selectedIndex];
        return parseInt(opt?.getAttribute('data-duration') || '0', 10);
    }

    function updateSlotAvailability() {
        const svcDuration = getSelectedServiceDuration();
        tiles.forEach(tile => {
            const slotDuration = parseInt(tile.dataset.slotDuration || '0', 10);
            if (svcDuration > slotDuration) {
                tile.classList.remove('slot-clickable');
                tile.classList.add('slot-too-short');
                tile.title = `Wymagane ${svcDuration} min, okienko ma ${slotDuration} min`;
            } else {
                tile.classList.add('slot-clickable');
                tile.classList.remove('slot-too-short');
                tile.title = '';
            }
        });
    }

    if (serviceSelect) {
        serviceSelect.addEventListener('change', updateSlotAvailability);
        updateSlotAvailability();
    }

    document.addEventListener('click', (e) => {
        const tile = e.target.closest('.slot-tile.slot-clickable');
        if (!tile || !serviceSelect) return;
        if (tile.classList.contains('slot-too-short')) return;

        const slotId   = tile.dataset.slotId;
        const time     = tile.dataset.time;
        const date     = tile.dataset.date;
        const svcId    = serviceSelect.value;
        const svcText  = serviceSelect.options[serviceSelect.selectedIndex]?.text ?? '';

        document.getElementById('modal-info').textContent =
            `📅 ${date}  🕐 ${time}  •  ${svcText}`;
        document.getElementById('modal-service-id').value = svcId;
        form.action = `/Rezerwacja/slot/${slotId}`;

        modal.style.display = 'flex';
        document.querySelectorAll('.slot-selected').forEach(t => t.classList.remove('slot-selected'));
        tile.classList.add('slot-selected');
    });

    function closeModal() {
        modal.style.display = 'none';
        document.querySelectorAll('.slot-selected').forEach(t => t.classList.remove('slot-selected'));
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
</script>
@endsection
