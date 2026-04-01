<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\ExpeditionController;
use App\Http\Controllers\Master\FarmController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Monitor\LiveMonitorController;
use App\Http\Controllers\Transaction\ConditionController;
use App\Http\Controllers\Transaction\HangingFormController;
use App\Http\Controllers\Transaction\HangingLandingController;
use App\Http\Controllers\Transaction\MonitorControlController;
use App\Http\Controllers\Transaction\ReturMatiController;
use App\Http\Controllers\Transaction\ReturMatiLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('expeditions', ExpeditionController::class);
        Route::resource('farms', FarmController::class);
    });

    // Kontrol Monitor
    Route::resource('monitor-controls', MonitorControlController::class)->except(['show']);
    Route::post('monitor-controls/{monitorControl}/start', [MonitorControlController::class, 'start'])
        ->name('monitor-controls.start');

    Route::get('/retur-mati', [ReturMatiLandingController::class, 'index'])->name('retur-mati.landing');
    Route::post('/retur-mati/open/{monitorControl}', [ReturMatiLandingController::class, 'open'])->name('retur-mati.open');

    Route::post('monitor-controls/{monitorControl}/move', [MonitorControlController::class, 'moveTruckNo'])
        ->name('monitor-controls.move');

    Route::resource('monitor-controls', MonitorControlController::class);
    Route::get('/hanging', [HangingLandingController::class, 'index'])->name('hanging.landing');
    Route::post('/hanging/open/{monitorControl}', [HangingLandingController::class, 'open'])->name('hanging.open');
    Route::post('/hanging/start/{hangingForm}', [HangingLandingController::class, 'start'])->name('hanging.start');

    // Form Hanging
    Route::get('hanging-forms/{hangingForm}', [HangingFormController::class, 'show'])->name('hanging-forms.show');
    Route::post('hanging-forms/{hangingForm}/finish', [HangingFormController::class, 'finish'])
        ->name('hanging-forms.finish');
    Route::patch('hanging-cells/{hangingLineSet}', [HangingFormController::class, 'updateCell'])->name('hanging-cells.update');
    Route::get('/retur-mati/{hangingForm}', [ReturMatiController::class, 'edit'])->name('retur-mati.edit');
    Route::post('/retur-mati/{hangingForm}', [ReturMatiController::class, 'update'])->name('retur-mati.update');

    Route::get('/conditions', [ConditionController::class, 'landing'])->name('conditions.landing');
    Route::post('/conditions/open/{monitorControl}', [ConditionController::class, 'open'])->name('conditions.open');
    Route::get('/conditions/{hangingForm}', [ConditionController::class, 'edit'])->name('conditions.edit');
    Route::post('/conditions/{hangingForm}', [ConditionController::class, 'update'])->name('conditions.update');

    // Live Monitor
    Route::get('monitor/{location}', [LiveMonitorController::class, 'show'])->name('monitor.show');
    Route::get('monitor/{location}/data', [LiveMonitorController::class, 'data'])->name('monitor.data');
});