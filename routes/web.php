<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;

Route::get('/', function () {
    return view('glowna');
});

Route::get('/o-nas', function () {
    return view('about');
});

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/create', [AdminController::class, 'create']);
Route::post('/admin', [AdminController::class, 'store']);

Route::get('/admin/reviews', [AdminController::class, 'reviews']);
Route::delete('/admin/reviews/{id}', [AdminController::class, 'destroyReview']);

Route::get('/admin/doctor-applications', [AdminController::class, 'doctorApplications']);
Route::post('/admin/doctor-applications/{id}/approve', [AdminController::class, 'approveDoctor']);
Route::delete('/admin/doctor-applications/{id}', [AdminController::class, 'destroyDoctorApplication']);

Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
Route::post('/admin/{id}/toggle-ban', [AdminController::class, 'toggleBan']);

Route::get('/PanelLekarza', [DoctorController::class, 'panel']);
Route::post('/PanelLekarza/profil', [DoctorController::class, 'updateProfile']);

Route::get('/PanelLekarza/uslugi', [DoctorController::class, 'services']);
Route::get('/PanelLekarza/uslugi/dodaj', [DoctorController::class, 'createService']);
Route::post('/PanelLekarza/uslugi', [DoctorController::class, 'storeService']);
Route::get('/PanelLekarza/uslugi/{id}/edytuj', [DoctorController::class, 'editService']);
Route::put('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'updateService']);
Route::delete('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'destroyService']);

