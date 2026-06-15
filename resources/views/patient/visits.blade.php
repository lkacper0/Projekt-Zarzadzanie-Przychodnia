@extends('layouts.app')

@section('title', 'Moje Wizyty - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
@endpush

@section('content')
<div class="patient-container">
    <h1 class="panel-title">Moje Wizyty</h1>

    <div class="panel-card" style="margin-bottom: 40px;">
        <h2 style="font-family: 'Outfit', sans-serif; color: #003366; font-size: 22px; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <span>📅</span> Nadchodzące Wizyty
        </h2>

        @if($upcomingAppointments && $upcomingAppointments->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Lekarz</th>
                            <th>Usługa</th>
                            <th>Data i godzina</th>
                            <th>Cena</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingAppointments as $app)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state" style="padding: 30px 20px;">
                <p>Nie masz zaplanowanych żadnych nadchodzących wizyt.</p>
                <a href="{{ url('/Rezerwacja') }}" class="btn-auth" style="display:inline-block; margin-top:15px; padding:10px 20px; width:auto; text-decoration:none;">Zarezerwuj wizytę</a>
            </div>
        @endif
    </div>

    <div class="panel-card">
        <h2 style="font-family: 'Outfit', sans-serif; color: #003366; font-size: 22px; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <span>🕰️</span> Historia Wizyt
        </h2>

        @if($pastAppointments && $pastAppointments->count() > 0)
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Lekarz</th>
                            <th>Usługa</th>
                            <th>Data i godzina</th>
                            <th>Cena</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastAppointments as $app)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state" style="padding: 30px 20px;">
                <p>Brak wcześniejszych wizyt w historii.</p>
            </div>
        @endif
    </div>
</div>
@endsection
