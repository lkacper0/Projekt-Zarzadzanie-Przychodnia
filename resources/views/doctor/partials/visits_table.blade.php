@if($appointments->count() > 0)
    <table class="visits-table">
        <thead>
            <tr>
                <th>Pacjent</th>
                <th>Usługa</th>
                <th>Data i Godzina</th>
                <th>Status</th>
                @if($showActions === 'completed')
                    <th>Diagnoza / Zalecenia</th>
                @endif
                @if($showActions !== 'cancelled')
                    <th>Akcje</th>
                @elseif(auth()->user() && auth()->user()->role === 'admin')
                    <th>Akcje</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $app)
                @php $isAdmin = auth()->user() && auth()->user()->role === 'admin'; @endphp
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
                    @if($showActions === 'completed')
                        <td class="visit-note">{{ $app->medical_note ?: '—' }}</td>
                    @endif

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
                            <form action="{{ url('/ListaWizyt/'.$app->id.'/odrzuc') }}" method="POST" style="display:inline;" onsubmit="return confirm('Odwołać tę wizytę?');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Odwołaj</button>
                            </form>
                            @if($isAdmin)
                                <form action="{{ url('/admin/wizyty/'.$app->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz CAŁKOWICIE usunąć tę wizytę z bazy danych?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete-visit">Usuń wizytę</button>
                                </form>
                            @endif
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
                            @if($isAdmin)
                                <form action="{{ url('/admin/wizyty/'.$app->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz CAŁKOWICIE usunąć tę wizytę z bazy danych?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete-visit">Usuń wizytę</button>
                                </form>
                            @endif
                        </td>
                    @elseif($showActions === 'completed')
                        <td class="visit-actions">
                            @if($isAdmin)
                                <form action="{{ url('/admin/wizyty/'.$app->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz CAŁKOWICIE usunąć tę wizytę z bazy danych?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete-visit">Usuń wizytę</button>
                                </form>
                            @endif
                        </td>
                    @elseif($showActions === 'cancelled' && $isAdmin)
                        <td class="visit-actions">
                            <form action="{{ url('/admin/wizyty/'.$app->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz CAŁKOWICIE usunąć tę wizytę z bazy danych?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete-visit">Usuń wizytę</button>
                            </form>
                        </td>
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
