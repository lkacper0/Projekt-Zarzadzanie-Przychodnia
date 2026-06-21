<h2 class="card-title" style="margin-bottom: 16px;">Grafik{{ !empty($scheduleTitleSuffix) ? ' ' . $scheduleTitleSuffix : '' }}</h2>

@if($slots->isEmpty())
    <div class="empty-state" style="margin-top: 10px;">
        <p>Brak zdefiniowanych godzin pracy. Użyj formularza powyżej, aby dodać pierwsze terminy.</p>
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
                            <form action="{{ $deleteSlotUrl($slot->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Usunąć ten slot?');">
                                @csrf
                                @method('DELETE')
                                @if(!empty($hiddenDoctorId))
                                    <input type="hidden" name="doctor_id" value="{{ $hiddenDoctorId }}">
                                @endif
                                <button type="submit" class="slot-delete-btn" title="Usuń">✕</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
