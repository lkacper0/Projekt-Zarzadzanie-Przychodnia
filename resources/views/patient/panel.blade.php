@extends('layouts.app')

@section('title', 'Panel Użytkownika - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patient/panel.css') }}">
@endpush

@section('content')
<div class="patient-container">
    <h1 class="panel-title">Moje Dane</h1>

    <div class="panel-grid">
        <!-- Profile Card -->
        <div class="panel-card profile-card" style="height: fit-content;">
            <div class="avatar-placeholder">
                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
            </div>
            <h2 class="patient-name">{{ $user->first_name }} {{ $user->last_name }}</h2>
            <p class="patient-email">{{ $user->email }}</p>

            <div class="profile-details">
                <div class="detail-item">
                    <label>Rola systemowa</label>
                    <span style="font-weight: 600; color: #4a90e2;">Pacjent</span>
                </div>
                <div class="detail-item">
                    <label>Numer PESEL</label>
                    <span>{{ $user->pesel }}</span>
                </div>
                <div class="detail-item">
                    <label>Konto utworzone</label>
                    <span>{{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : 'b/d' }}</span>
                </div>
            </div>
            
            <a href="{{ url('/PanelUzytkownika/edycja') }}" class="btn-auth" style="display:inline-block; width:auto; padding:8px 16px; font-size:13px; margin-top:10px; background:linear-gradient(135deg, #64748b 0%, #475569 100%); box-shadow:none;">Edytuj Dane</a>
        </div>

        <!-- Main Dashboard Actions -->
        <div>
            <div class="panel-card actions-card" style="margin-bottom: 30px;">
                <h2>Szybki dostęp</h2>
                <p style="color: #64748b; margin-top: -10px; margin-bottom: 25px;">Wybierz jedną z sekcji, aby przejść do szczegółów:</p>
                
                <div class="actions-list">
                    <a href="{{ url('/Lekarze') }}" class="action-btn">
                        <span class="icon">🧑‍⚕️</span>
                        <span>Wyszukaj Lekarza</span>
                    </a>

                    <a href="{{ url('/ListaWizyt') }}" class="action-btn">
                        <span class="icon">💼</span>
                        <span>Moje Wizyty</span>
                    </a>

                    <a href="{{ url('/DiagnozaZalecenia') }}" class="action-btn">
                        <span class="icon">📝</span>
                        <span>Diagnoza i Zalecenia</span>
                    </a>
                </div>
            </div>

            <!-- Doctor Application Section -->
            <div class="panel-card">
                <h2>Dołącz do zespołu ProHealth</h2>
                
                <div id="alert-container"></div>

                @if(!$application)
                    <p style="color: #64748b; margin-top: -10px; margin-bottom: 25px;">
                        Jesteś lekarzem? Wyślij podanie o aktywację konta lekarza. Wypełnij poniższe dane, aby administrator mógł zweryfikować Twój profil.
                    </p>

                    <form id="apply-doctor-form">
                        @csrf
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="specialization" style="display:block; font-weight:600; margin-bottom:8px; color:#334155;">Specjalizacja</label>
                            <select name="specialization" id="specialization" class="form-control" style="width:100%; padding:12px; border:1.5px solid #cbd5e1; border-radius:10px;" required>
                                <option value="">Wybierz specjalizację...</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                @endforeach
                            </select>
                            <span class="error-feedback" id="error-specialization" style="color:red; font-size:12px;"></span>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="bio" style="display:block; font-weight:600; margin-bottom:8px; color:#334155;">Opis profilu / Bio</label>
                            <textarea name="bio" id="bio" class="form-control" rows="5" style="width:100%; padding:12px; border:1.5px solid #cbd5e1; border-radius:10px;" placeholder="Napisz o swoim doświadczeniu zawodowym, wykształceniu i dlaczego chcesz do nas dołączyć..." required></textarea>
                            <span class="error-feedback" id="error-bio" style="color:red; font-size:12px;"></span>
                        </div>

                        <button type="submit" id="submit-btn" class="btn-auth" style="margin-top: 10px; padding: 12px 25px; width: auto;">
                            <span class="spinner" id="btn-spinner" style="display: none; border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid #fff; width: 14px; height: 14px; border-radius: 50%; display: inline-block; animation: spin 0.8s linear infinite; margin-right: 8px;"></span>
                            <span id="btn-text">Wyślij podanie lekarza</span>
                        </button>
                    </form>
                @else
                    @if(!$application->is_accepted)
                        <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 20px; display: flex; gap: 15px; align-items: flex-start;">
                            <span style="font-size: 24px;">⏳</span>
                            <div>
                                <h4 style="margin: 0 0 6px 0; color: #b45309; font-weight: 600; font-size: 16px;">Podanie w trakcie weryfikacji</h4>
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.5;">
                                    Twoje podanie o aktywację konta lekarza zostało pomyślnie wysłane. Profil oczekuje na weryfikację przez administratora przychodni.
                                </p>
                            </div>
                        </div>
                    @else
                        <div style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 20px; display: flex; gap: 15px; align-items: flex-start;">
                            <span style="font-size: 24px;">✅</span>
                            <div>
                                <h4 style="margin: 0 0 6px 0; color: #166534; font-weight: 600; font-size: 16px;">Podanie zaakceptowane!</h4>
                                <p style="margin: 0; color: #14532d; font-size: 14px; line-height: 1.5;">
                                    Twój profil lekarza został pomyślnie aktywowany. Zostaniesz automatycznie przekierowany do Panelu Lekarza przy kolejnym logowaniu lub przeładowaniu strony.
                                </p>
                                <a href="{{ url('/PanelLekarza') }}" class="btn-auth" style="margin-top: 15px; padding: 8px 18px; font-size: 13px; width: auto; background: #166534;">Przejdź do Panelu Lekarza</a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$application)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('apply-doctor-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnText = document.getElementById('btn-text');
    const alertContainer = document.getElementById('alert-container');

    // Hide spinner initially
    btnSpinner.style.display = 'none';

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        alertContainer.innerHTML = '';
        document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');
        
        submitBtn.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Wysyłanie...';

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        try {
            const response = await fetch('/PanelUzytkownika/aplikuj', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alertContainer.innerHTML = `<div style="background-color:#dcfce7; color:#14532d; padding:15px; border-radius:10px; margin-bottom:20px; font-weight:500;">${result.message}</div>`;
                form.reset();
                
                // Reload page after short delay to show the pending card state
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                submitBtn.disabled = false;
                btnSpinner.style.display = 'none';
                btnText.textContent = 'Wyślij podanie lekarza';

                if (response.status === 422 && result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const feedback = document.getElementById(`error-${field}`);
                        if (feedback) feedback.textContent = result.errors[field][0];
                    });
                } else {
                    const errMsg = result.message || 'Wystąpił błąd podczas składania podania.';
                    alertContainer.innerHTML = `<div style="background-color:#fee2e2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px; font-weight:500;">${errMsg}</div>`;
                }
            }
        } catch (error) {
            submitBtn.disabled = false;
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Wyślij podanie lekarza';
            
            alertContainer.innerHTML = '<div style="background-color:#fee2e2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px; font-weight:500;">Błąd połączenia z serwerem. Spróbuj ponownie.</div>';
            console.error('Error during apply fetch:', error);
        }
    });
});
</script>
@endif
@endsection
