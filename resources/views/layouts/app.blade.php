<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ProHealth')</title>
    
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    @php
        $user = auth()->user();
    @endphp

    <div id="gora">
        <div id="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('media/prohealth.png') }}" alt="logo">
            </a>
        </div>

        <div id="zakladki">
            @if($user)
                @if($user->isAdmin())
                    <a href="{{ url('/') }}">Strona Główna</a>
                    <a href="{{ url('/onas') }}">O nas</a>
                    <a href="{{ url('/kontakt') }}">Kontakt</a>
                    <a href="{{ url('/admin') }}">Panel Administratora</a>
                @elseif($user->isDoctor())
                    <a href="{{ url('/PanelLekarza') }}">Moje Dane</a>
                    <a href="{{ url('/ListaWizyt') }}">Lista Wizyt</a>
                    <a href="{{ url('/GodzinyPracy') }}">Godziny Pracy</a>
                    <a href="{{ url('/Kartoteka') }}">Kartoteka Pacjentów</a>
                    <a href="{{ url('/HistoriaPacjenta') }}">Historia Pacjenta</a>
                @else
                    <a href="{{ url('/NajlepsiLekarze') }}">Najlepsi Lekarze</a>
                    <a href="{{ url('/PanelUzytkownika') }}">Moje Dane</a>
                    <a href="{{ url('/Lekarze') }}">Wyszukaj Lekarza</a>
                    <a href="{{ url('/ListaWizyt') }}">Moje Wizyty</a>
                    <a href="{{ url('/DiagnozaZalecenia') }}">Diagnoza i Zalecenia</a>
                @endif
            @else
                <a href="{{ url('/') }}">Strona Główna</a>
                <a href="{{ url('/onas') }}">O nas</a>
                <a href="{{ url('/kontakt') }}">Kontakt</a>
            @endif
        </div>

        <div id="login">
            @auth
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font:inherit;font-size:20px;font-weight:600;color:white;background-color:#4a90e2;padding:10px 22px;border-radius:25px;">
                        Wyloguj się
                    </button>
                </form>
            @else
                <a href="{{ url('/login') }}">Zaloguj się</a>
                <a href="{{ url('/Rejestracja') }}">Rejestracja</a>
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px;text-align:center;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:12px;text-align:center;">{{ session('error') }}</div>
    @endif

    @yield('content')

    <footer>
        <p>© 2026 ProHealth. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
