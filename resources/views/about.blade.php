@extends('layouts.app')

@section('title', 'O nas - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/Glowna.css') }}">
@endpush

@section('content')
<div id="about-container" style="max-width: 1000px; margin: 40px auto; padding: 20px;">
    <div id="about-content"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch('/api/about');
        const data = await response.json();
        if (response.ok && data.success) {
            document.getElementById('about-content').innerHTML = data.content;
        }
    } catch (err) {
        document.getElementById('about-content').innerHTML = '<p>Nie udało się załadować treści strony.</p>';
    }
});
</script>
@endsection
