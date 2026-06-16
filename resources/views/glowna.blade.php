@extends('layouts.app')

@section('title', 'Strona Główna - ProHealth')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/Glowna.css') }}">
@endpush

@section('content')
<div id="homepage-container">
    <div id="homepage-editable-content"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch('/api/homepage');
        const data = await response.json();
        if (response.ok && data.success) {
            document.getElementById('homepage-editable-content').innerHTML = data.content;
        }
    } catch (err) {
        console.error(err);
    }
});
</script>
@endsection
