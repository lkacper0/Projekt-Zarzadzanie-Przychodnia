<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('glowna');
});

Route::get('/o-nas', function () {
    return view('about');
});

// Authentication routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/Rejestracja', [AuthController::class, 'showRegister'])->name('register');
});

// Logout and pending activation page
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/oczekiwanie-na-akceptacje', [AuthController::class, 'showPending'])->name('pending');

// Admin panel (role: admin)
Route::middleware(['role:admin'])->group(function () {
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
});

// Doctor panel (role: doctor)
Route::middleware(['role:doctor'])->group(function () {
    Route::get('/PanelLekarza', [DoctorController::class, 'panel']);
    Route::post('/PanelLekarza/profil', [DoctorController::class, 'updateProfile']);

    Route::get('/PanelLekarza/uslugi', [DoctorController::class, 'services']);
    Route::get('/PanelLekarza/uslugi/dodaj', [DoctorController::class, 'createService']);
    Route::post('/PanelLekarza/uslugi', [DoctorController::class, 'storeService']);
    Route::get('/PanelLekarza/uslugi/{id}/edytuj', [DoctorController::class, 'editService']);
    Route::put('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'updateService']);
    Route::delete('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'destroyService']);

    Route::get('/GodzinyPracy', [DoctorController::class, 'workingHours']);
    Route::get('/Kartoteka', [DoctorController::class, 'records']);
    Route::get('/HistoriaPacjenta', [DoctorController::class, 'history']);
});

// Patient panel (role: patient)
Route::middleware(['role:patient'])->group(function () {
    Route::get('/PanelUzytkownika', [PatientController::class, 'index']);
    Route::post('/PanelUzytkownika/aplikuj', [PatientController::class, 'applyToBeDoctor']);
    Route::get('/PanelUzytkownika/edycja', [PatientController::class, 'editProfile']);
    Route::post('/PanelUzytkownika/edycja', [PatientController::class, 'updateProfile']);
    Route::get('/Lekarze', [PatientController::class, 'searchDoctors']);
    Route::get('/DiagnozaZalecenia', [PatientController::class, 'diagnosis']);
});

// Shared visits route switcher (requires session authentication)
Route::get('/ListaWizyt', function () {
    $user = Auth::user();
    if ($user->isDoctor()) {
        return app(DoctorController::class)->visits();
    }
    return app(PatientController::class)->visits();
})->middleware('role:patient,doctor,admin');

