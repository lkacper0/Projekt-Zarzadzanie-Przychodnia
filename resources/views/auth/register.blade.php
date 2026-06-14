@extends('layouts.app')

@section('title', 'Rejestracja - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-body">
    <div class="auth-container" style="max-width: 600px;">
        <div class="auth-header">
            <h1>Zarejestruj się</h1>
            <p>Stwórz nowe konto w przychodni ProHealth</p>
        </div>

        <div id="alert-container"></div>

        <form id="register-form">
            @csrf
            


            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="first_name">Imię</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="np. Jan" required>
                    <span class="error-feedback" id="error-first_name"></span>
                </div>

                <div class="form-group">
                    <label for="last_name">Nazwisko</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="np. Kowalski" required>
                    <span class="error-feedback" id="error-last_name"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adres e-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="np. jan.kowalski@example.com" required autocomplete="email">
                <span class="error-feedback" id="error-email"></span>
            </div>

            <div class="form-group">
                <label for="pesel">Numer PESEL</label>
                <input type="text" name="pesel" id="pesel" class="form-control" placeholder="np. 90010112345" maxlength="11" required>
                <span class="error-feedback" id="error-pesel"></span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                    <span class="error-feedback" id="error-password"></span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Powtórz hasło</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                    <span class="error-feedback" id="error-password_confirmation"></span>
                </div>
            </div>



            <button type="submit" id="submit-btn" class="btn-auth" style="margin-top: 15px;">
                <span class="spinner" id="btn-spinner" style="display: none;"></span>
                <span id="btn-text">Zarejestruj się</span>
            </button>
        </form>

        <div class="auth-footer">
            Masz już konto? <a href="{{ url('/login') }}">Zaloguj się</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnText = document.getElementById('btn-text');
    const alertContainer = document.getElementById('alert-container');
    const doctorDetailsSection = document.getElementById('doctor-details-section');



    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Reset states
        alertContainer.innerHTML = '';
        document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        
        // Button loading state
        submitBtn.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Rejestracja...';

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        try {
            const response = await fetch('/api/auth/register', {
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
                alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            } else {
                submitBtn.disabled = false;
                btnSpinner.style.display = 'none';
                btnText.textContent = 'Zarejestruj się';

                if (response.status === 422 && result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) input.classList.add('is-invalid');
                        
                        const feedback = document.getElementById(`error-${field}`);
                        if (feedback) feedback.textContent = result.errors[field][0];
                    });
                } else {
                    const errMsg = result.message || 'Wystąpił błąd podczas rejestracji.';
                    alertContainer.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
                }
            }
        } catch (error) {
            submitBtn.disabled = false;
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Zarejestruj się';
            
            alertContainer.innerHTML = '<div class="alert alert-danger">Błąd połączenia z serwerem. Spróbuj ponownie.</div>';
            console.error('Error during register fetch:', error);
        }
    });
});
</script>
@endsection
