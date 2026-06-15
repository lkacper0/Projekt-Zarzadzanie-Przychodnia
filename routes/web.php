<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('glowna');
});

Route::get('/o-nas', function () {
    return view('about');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/Rejestracja', [AuthController::class, 'showRegister'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/oczekiwanie-na-akceptacje', [AuthController::class, 'showPending'])->name('pending')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->group(function () {
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

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/PanelLekarza', [DoctorController::class, 'panel']);
    Route::post('/PanelLekarza/profil', [DoctorController::class, 'updateProfile']);

    Route::get('/PanelLekarza/uslugi', [DoctorController::class, 'services']);
    Route::get('/PanelLekarza/uslugi/dodaj', [DoctorController::class, 'createService']);
    Route::post('/PanelLekarza/uslugi', [DoctorController::class, 'storeService']);
    Route::get('/PanelLekarza/uslugi/{id}/edytuj', [DoctorController::class, 'editService']);
    Route::put('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'updateService']);
    Route::delete('/PanelLekarza/uslugi/{id}', [DoctorController::class, 'destroyService']);

    Route::get('/GodzinyPracy', [ScheduleController::class, 'doctorSchedule']);
    Route::post('/GodzinyPracy/generuj', [ScheduleController::class, 'generateSlots']);
    Route::delete('/GodzinyPracy/slot/{id}/usun', [ScheduleController::class, 'deleteSlot']);

    Route::get('/PanelLekarza/lista-godzin', [DoctorController::class, 'workingHours']);

    Route::get('/PanelLekarza/harmonogram', fn () => redirect('/GodzinyPracy'));
    Route::post('/PanelLekarza/harmonogram/generuj', [ScheduleController::class, 'generateSlots']);
    Route::delete('/PanelLekarza/harmonogram/slot/{id}/usun', [ScheduleController::class, 'deleteSlot']);

    Route::get('/Kartoteka', [DoctorController::class, 'records']);
    Route::get('/HistoriaPacjenta', [DoctorController::class, 'history']);
});

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/PanelUzytkownika', [PatientController::class, 'index']);
    Route::post('/PanelUzytkownika/aplikuj', [PatientController::class, 'applyToBeDoctor']);
    Route::get('/PanelUzytkownika/edycja', [PatientController::class, 'editProfile']);
    Route::post('/PanelUzytkownika/edycja', [PatientController::class, 'updateProfile']);
    Route::get('/Lekarze', [PatientController::class, 'searchDoctors']);
    Route::get('/DiagnozaZalecenia', [PatientController::class, 'diagnosis']);

    Route::get('/Rezerwacja', [ScheduleController::class, 'bookingIndex']);
    Route::get('/Rezerwacja/lekarz/{id}', [ScheduleController::class, 'bookingDoctor']);
    Route::post('/Rezerwacja/slot/{id}', [ScheduleController::class, 'bookSlot']);
});

Route::middleware(['auth', 'role:patient,doctor,admin'])->group(function () {
    Route::get('/ListaWizyt', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->role === 'doctor') {
            return app(DoctorController::class)->visits();
        }

        return app(PatientController::class)->visits();
    });
});
