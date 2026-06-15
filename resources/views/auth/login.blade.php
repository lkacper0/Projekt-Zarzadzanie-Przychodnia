@extends('layouts.app')

@section('title', 'Logowanie - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Witamy ponownie</h1>
            <p>Zaloguj się do swojego konta ProHealth</p>
        </div>

        <div id="alert-container">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

        <form id="login-form">
            @csrf
            
            <div class="form-group">
                <label for="email">Adres e-mail</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="form-control" placeholder="np. pacjent@example.com" required autocomplete="email">
                </div>
                <span class="error-feedback" id="error-email"></span>
            </div>

            <div class="form-group">
                <label for="password">Hasło</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <span class="error-feedback" id="error-password"></span>
            </div>

            <div class="remember-wrapper">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember" id="remember">
                    Zapamiętaj mnie
                </label>
            </div>

            <button type="submit" id="submit-btn" class="btn-auth">
                <span class="spinner" id="btn-spinner" style="display: none;"></span>
                <span id="btn-text">Zaloguj się</span>
            </button>
        </form>

        <div class="auth-footer">
            Nie masz jeszcze konta? <a href="{{ url('/Rejestracja') }}">Zarejestruj się</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnText = document.getElementById('btn-text');
    const alertContainer = document.getElementById('alert-container');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        

        alertContainer.innerHTML = '';
        document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        

        submitBtn.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Logowanie...';

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'remember') {
                data[key] = true;
            } else {
                data[key] = value;
            }
        });

        try {
            const response = await fetch('/api/auth/login', {
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
                }, 800);
            } else {

                submitBtn.disabled = false;
                btnSpinner.style.display = 'none';
                btnText.textContent = 'Zaloguj się';

                if (response.status === 422 && result.errors) {

                    Object.keys(result.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) input.classList.add('is-invalid');
                        
                        const feedback = document.getElementById(`error-${field}`);
                        if (feedback) feedback.textContent = result.errors[field][0];
                    });
                } else {

                    const errMsg = result.message || 'Wystąpił błąd podczas logowania.';
                    alertContainer.innerHTML = `<div class="alert alert-danger">${errMsg}</div>`;
                }
            }
        } catch (error) {
            submitBtn.disabled = false;
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Zaloguj się';
            
            alertContainer.innerHTML = '<div class="alert alert-danger">Błąd połączenia z serwerem. Spróbuj ponownie.</div>';
            console.error('Error during login fetch:', error);
        }
    });
});
</script>
@endsection
