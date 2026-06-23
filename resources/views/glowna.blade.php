@extends('layouts.app')

@section('title', 'Strona Główna - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/Glowna.css') }}">
@endpush

@section('content')
<div id="homepage-container" style="max-width: 1000px; margin: 40px auto; padding: 20px;">
    <div id="homepage-content"></div>

    <h1 id="best-doctors-title" style="display: none;">Najlepsi Lekarze</h1>
    <div id="lekarze"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch('/api/homepage');
        const data = await response.json();
        if (response.ok && data.success) {
            document.getElementById('homepage-content').innerHTML = data.content;

            const doctorsContainer = document.getElementById('lekarze');
            const titleElement = document.getElementById('best-doctors-title');
            doctorsContainer.innerHTML = '';

            if (data.best_doctors && data.best_doctors.length > 0) {
                titleElement.style.display = 'block';
                data.best_doctors.forEach(doc => {
                    const docDiv = document.createElement('div');
                    docDiv.className = 'lekarz';

                    let imgUrl = doc.profile_photo;
                    if (!imgUrl) {
                        const idx = (doc.id % 5) + 1;
                        const ext = idx >= 4 ? '.png' : '.jpg';
                        imgUrl = '/media/lekarz' + idx + ext;
                    }

                    const specText = doc.specializations && doc.specializations.length > 0 ? doc.specializations.join(', ') : 'Specjalista';
                    const ratingText = doc.avg_rating > 0 ? `⭐ ${Number(doc.avg_rating).toFixed(1)} / 5.0` : 'Brak ocen';

                    docDiv.innerHTML = `
                        <img src="${imgUrl}" alt="${doc.name}">
                        <p style="margin-bottom: 2px;">dr ${doc.name}</p>
                        <p style="font-size: 13px; color: #4a90e2; font-weight: bold; margin-top: 0; margin-bottom: 2px;">${specText}</p>
                        <p style="font-size: 13px; color: #e6a817; font-weight: bold; margin-top: 0;">${ratingText}</p>
                    `;
                    doctorsContainer.appendChild(docDiv);
                });
            }
        }
    } catch (err) {
        document.getElementById('homepage-content').innerHTML = '<p>Nie udało się załadować treści strony.</p>';
    }
});
</script>
@endsection
