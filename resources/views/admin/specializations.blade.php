@extends('layouts.app')

@section('title', 'Specjalizacje - Admin')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">

<div class="admin-container">

    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/specjalizacje') }}" class="nav-btn active">Specjalizacje</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start; margin-top: 20px;">
        
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: #003366; border-bottom: 2px solid #003366; padding-bottom: 8px; margin-bottom: 15px;">Dodaj Specjalizację</h3>
            
            <form action="{{ url('/admin/specjalizacje') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label for="name" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333; font-size: 14px;">Nazwa specjalizacji:</label>
                    <input type="text" name="name" id="name" required class="search-input" style="width: 100%; box-sizing: border-box;" placeholder="np. Kardiologia">
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%; padding: 10px; font-size: 14px;">Dodaj do systemu</button>
            </form>
        </div>

        <div>
            <table class="admin-table" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nazwa specjalizacji</th>
                        <th style="width: 120px;" class="text-center">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($specializations as $spec)
                        <tr class="{{ $loop->index % 2 == 0 ? 'row-even' : 'row-odd' }}">
                            <td>{{ $spec->id }}</td>
                            <td class="text-bold" style="color: #003366;">{{ $spec->name }}</td>
                            <td class="text-center">
                                <form action="{{ url('/admin/specjalizacje/' . $spec->id) }}" method="POST" class="m-0" onsubmit="return confirm('Czy na pewno chcesz usunąć tę specjalizację? Spowoduje to odpięcie jej od wszystkich lekarzy!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="padding: 20px; color: #777; font-style: italic;">Brak specjalizacji w systemie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
