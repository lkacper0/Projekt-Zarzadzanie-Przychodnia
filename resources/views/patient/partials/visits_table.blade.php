<div class="table-responsive">
    <table class="table-custom">
        <thead>
            <tr>
                <th>Lekarz</th>
                <th>Usługa</th>
                <th>Data i godzina</th>
                <th>Cena</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $app)
                <tr>
                    <td style="font-weight: 600; color: #003366;">
                        @if($app->slot && $app->slot->doctor && $app->slot->doctor->user)
                            dr {{ $app->slot->doctor->user->first_name }} {{ $app->slot->doctor->user->last_name }}
                        @else
                            Nieznany Lekarz
                        @endif
                    </td>
                    <td>{{ $app->service ? $app->service->name : 'Standardowa wizyta' }}</td>
                    <td>
                        @if($app->slot)
                            {{ $app->slot->start_time->format('d.m.Y H:i') }} - {{ $app->slot->end_time->format('H:i') }}
                        @else
                            brak danych
                        @endif
                    </td>
                    <td>{{ $app->service ? number_format($app->service->price, 2) : '0.00' }} zł</td>
                    <td>
                        @php
                            $statusClass = 'badge-pending';
                            $statusText = 'Oczekująca';

                            if ($app->status === 'completed') {
                                $statusClass = 'badge-completed';
                                $statusText = 'Zakończona';
                            } elseif ($app->status === 'confirmed') {
                                $statusClass = 'badge-confirmed';
                                $statusText = 'Potwierdzona';
                            } elseif ($app->status === 'cancelled') {
                                $statusClass = 'badge-cancelled';
                                $statusText = 'Odwołana';
                            }
                        @endphp
                        <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        @if($app->status === 'pending' || $app->status === 'confirmed')
                            <form action="{{ url('/ListaWizyt/' . $app->id . '/odwolaj') }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Czy na pewno chcesz odwołać tę wizytę?');">
                                @csrf
                                <button type="submit" class="btn-cancel" style="background-color: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">Odwołaj</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
