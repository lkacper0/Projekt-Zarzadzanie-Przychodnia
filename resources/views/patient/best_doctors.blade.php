@extends('layouts.app')

@section('title', 'Najlepsi Lekarze - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/Glowna.css') }}">
@endpush

@section('content')
<div id="best-doctors-container">
    <h1>Najlepsi Lekarze</h1>
    <div id="lekarze"></div>
    <p id="best-doctors-empty" style="display: none; text-align: center; color: #003366; font-size: 18px;">Brak dostępnych lekarzy.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const doctorsContainer = document.getElementById('lekarze');
    const emptyMessage = document.getElementById('best-doctors-empty');

    try {
        const response = await fetch('/api/best-doctors');
        const data = await response.json();

        if (response.ok && data.success && data.doctors && data.doctors.length > 0) {
            data.doctors.forEach(doc => {
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
        } else {
            emptyMessage.style.display = 'block';
        }
    } catch (err) {
        emptyMessage.textContent = 'Nie udało się załadować listy lekarzy.';
        emptyMessage.style.display = 'block';
    }
});
</script>
@endsection
