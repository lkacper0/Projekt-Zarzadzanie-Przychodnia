@extends('layouts.app')

@section('title', 'Edycja O nas - Admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />

<div class="admin-container">
    <h1 class="admin-title">Panel Administratora</h1>

    <div class="admin-nav">
        <a href="{{ url('/admin') }}" class="nav-btn">Użytkownicy</a>
        <a href="{{ url('/admin/reviews') }}" class="nav-btn">Opinie</a>
        <a href="{{ url('/admin/doctor-applications') }}" class="nav-btn">Zgłoszenia Lekarzy</a>
        <a href="{{ url('/admin/homepage') }}" class="nav-btn">Strona Główna</a>
        <a href="{{ url('/admin/about') }}" class="nav-btn active">O nas</a>
        <a href="{{ url('/admin/contact') }}" class="nav-btn">Kontakt</a>
    </div>

    <div class="admin-toolbar" style="margin-bottom: 25px;">
        <div style="display: flex; gap: 15px; align-items: center; width: 100%; justify-content: space-between;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label style="font-weight: bold; color: #003366;">Importuj treść z pliku HTML:</label>
                <input type="file" id="html-file-input" accept=".html,.txt" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
            </div>
            <button id="save-btn" class="btn btn-success" style="padding: 10px 25px;">Zapisz Zmiany</button>
        </div>
    </div>

    <div id="alert-box" style="display: none; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;"></div>

    <div class="panel-card" style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">
        <div id="editor-container" style="height: 500px; font-size: 16px;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    const alertBox = document.getElementById('alert-box');
    const saveBtn = document.getElementById('save-btn');
    const fileInput = document.getElementById('html-file-input');

    function showAlert(message, isSuccess) {
        alertBox.textContent = message;
        alertBox.style.display = 'block';
        if (isSuccess) {
            alertBox.style.backgroundColor = '#d4edda';
            alertBox.style.color = '#155724';
            alertBox.style.border = '1px solid #c3e6cb';
        } else {
            alertBox.style.backgroundColor = '#f8d7da';
            alertBox.style.color = '#721c24';
            alertBox.style.border = '1px solid #f5c6cb';
        }
    }

    try {
        const response = await fetch('/api/about');
        const data = await response.json();
        if (response.ok && data.success) {
            quill.root.innerHTML = data.content;
        } else {
            showAlert('Błąd podczas pobierania danych strony.', false);
        }
    } catch (err) {
        showAlert('Błąd połączenia z serwerem podczas pobierania danych.', false);
    }

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            quill.root.innerHTML = evt.target.result;
            showAlert('Plik został zaimportowany do edytora. Pamiętaj, aby zapisać zmiany!', true);
        };
        reader.readAsText(file);
    });

    saveBtn.addEventListener('click', async function() {
        saveBtn.disabled = true;
        const currentText = saveBtn.textContent;
        saveBtn.textContent = 'Zapisywanie...';

        try {
            const response = await fetch('/api/admin/about', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: quill.root.innerHTML
                })
            });

            const result = await response.json();
            if (response.ok && result.success) {
                showAlert(result.message, true);
            } else {
                showAlert(result.message || 'Wystąpił błąd podczas zapisywania zmian.', false);
            }
        } catch (err) {
            showAlert('Błąd połączenia z serwerem podczas zapisywania.', false);
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = currentText;
        }
    });
});
</script>
@endsection
