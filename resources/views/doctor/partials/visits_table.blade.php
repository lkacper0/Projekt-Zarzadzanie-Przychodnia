@if($appointments->count() > 0)
    <table class="visits-table">
        <thead>
            <tr>
                <th>Pacjent</th>
                <th>Usługa</th>
                <th>Data i Godzina</th>
                <th>Status</th>
                @if($showActions !== 'completed' && $showActions !== 'cancelled')
                    <th>Akcje</th>
                @elseif($showActions === 'completed')
                    <th>Diagnoza / Zalecenia</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $app)
                <tr>
                    <td class="visit-patient">
                        {{ $app->patient ? $app->patient->first_name . ' ' . $app->patient->last_name : 'Nieznany Pacjent' }}
                        <div class="visit-pesel">PESEL: {{ $app->patient->pesel ?? 'brak' }}</div>
                    </td>
                    <td>{{ $app->service ? $app->service->name : 'Wizyta' }}</td>
                    <td>{{ $app->slot ? $app->slot->start_time->format('d.m.Y H:i') : 'brak terminu' }}</td>
                    <td>
                        @php
                            $badgeClass = 'badge-pending';
                            $statusText = 'Oczekująca';
                            if ($app->status === 'completed') {
                                $badgeClass = 'badge-completed';
                                $statusText = 'Zakończona';
                            } elseif ($app->status === 'confirmed') {
                                $badgeClass = 'badge-confirmed';
                                $statusText = 'Potwierdzona';
                            } elseif ($app->status === 'cancelled') {
                                $badgeClass = 'badge-cancelled';
                                $statusText = 'Odwołana';
                            }
                        @endphp
                        <span class="visit-badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    @if($showActions === 'pending')
                        <td class="visit-actions">
                            <form action="{{ url('/ListaWizyt/'.$app->id.'/potwierdz') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Potwierdź</button>
                            </form>
                            <form action="{{ url('/ListaWizyt/'.$app->id.'/zakoncz') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Zakończ wizytę</button>
                            </form>
                        </td>
                    @elseif($showActions === 'confirmed')
                        <td class="visit-actions">
                            <form action="{{ url('/ListaWizyt/'.$app->id.'/zakoncz') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Zakończ wizytę</button>
                            </form>
                            <form action="{{ url('/ListaWizyt/'.$app->id.'/odrzuc') }}" method="POST" style="display:inline;" onsubmit="return confirm('Odwołać tę wizytę?');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Odwołaj</button>
                            </form>
                        </td>
                    @elseif($showActions === 'completed')
                        <td class="visit-note">{{ $app->medical_note ?: '—' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="visit-empty">
        <span>📅</span>
        Brak wizyt w tej kategorii.
    </div>
@endif

