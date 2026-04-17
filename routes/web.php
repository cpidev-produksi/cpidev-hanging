<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Master\ExpeditionController;
use App\Http\Controllers\Master\FarmController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Monitor\LiveMonitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Transaction\ConditionController;
use App\Http\Controllers\Transaction\HangingFormController;
use App\Http\Controllers\Transaction\HangingLandingController;
use App\Http\Controllers\Transaction\MonitorControlController;
use App\Http\Controllers\Transaction\MonitorSummaryController;
use App\Http\Controllers\Transaction\PlanningLbController;
use App\Http\Controllers\Transaction\ReturMatiController;
use App\Http\Controllers\Transaction\ReturMatiLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('monitor/{location}', [LiveMonitorController::class, 'show'])->name('monitor.show');
Route::get('monitor/{location}/data', [LiveMonitorController::class, 'data'])->name('monitor.data');


Route::middleware(['auth', 'nocache'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{auditLog}', [HistoryController::class, 'show'])->name('history.show');

    // Planning LB
    Route::middleware('role:adminlb')->resource('planning-lb', PlanningLbController::class);

    // Master Data (Operator TS)
    Route::middleware('role:operator_ts')->prefix('master')->name('master.')->group(function () {
        Route::resource('expeditions', ExpeditionController::class)->except(['destroy']);
        Route::resource('farms', FarmController::class)->except(['destroy']);
    });

    // Delete Master Data (Supervisor only)
    Route::middleware('supervisor')->prefix('master')->name('master.')->group(function () {
        Route::delete('expeditions/{expedition}', [ExpeditionController::class, 'destroy'])->name('expeditions.destroy');
        Route::delete('farms/{farm}', [FarmController::class, 'destroy'])->name('farms.destroy');
    });

    // Master Users (Superadmin only)
    Route::middleware('role:superadmin')->prefix('master')->name('master.')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Monitor Controls (Operator TS)
    Route::middleware('role:operator_ts')->group(function () {
        Route::get('monitor-controls/{monitorControl}/summary', [MonitorSummaryController::class, 'show'])->name('monitor-controls.summary');
        Route::resource('monitor-controls', MonitorControlController::class)->except(['destroy','show']);
        Route::delete('monitor-controls/{monitorControl}', [MonitorControlController::class, 'destroy'])->name('monitor-controls.destroy');
        Route::post('monitor-controls/{monitorControl}/start', [MonitorControlController::class, 'start'])->name('monitor-controls.start');
        Route::post('monitor-controls/{monitorControl}/move', [MonitorControlController::class, 'moveTruckNo'])->name('monitor-controls.move');
    });

    // Delete monitor-controls (Supervisor only)
    // Route::delete('monitor-controls/{monitorControl}', [MonitorControlController::class, 'destroy'])
    //     ->middleware('supervisor')
    //     ->name('monitor-controls.destroy');

    // Hanging (Checker Hanging)
    Route::middleware('role:checker_hanging')->group(function () {
        Route::get('/hanging', [HangingLandingController::class, 'index'])->name('hanging.landing');
        Route::post('/hanging/open/{monitorControl}', [HangingLandingController::class, 'open'])->name('hanging.open');
        Route::post('/hanging/start/{hangingForm}', [HangingLandingController::class, 'start'])->name('hanging.start');
        Route::get('hanging-forms/{hangingForm}', [HangingFormController::class, 'show'])->name('hanging-forms.show');
        Route::post('hanging-forms/{hangingForm}/finish', [HangingFormController::class, 'finish'])->name('hanging-forms.finish');
        Route::patch('hanging-cells/{hangingLineSet}', [HangingFormController::class, 'updateCell'])->name('hanging-cells.update');
    });

    // Retur & Mati (Checker Hanging OR Checker Retur)
    Route::middleware('role:checker_hanging,checker_retur')->group(function () {
        Route::get('/retur-mati', [ReturMatiLandingController::class, 'index'])->name('retur-mati.landing');
        Route::post('/retur-mati/open/{monitorControl}', [ReturMatiLandingController::class, 'open'])->name('retur-mati.open');
        Route::get('/retur-mati/{hangingForm}', [ReturMatiController::class, 'edit'])->name('retur-mati.edit');
        Route::post('/retur-mati/{hangingForm}', [ReturMatiController::class, 'update'])->name('retur-mati.update');
    });

    // QC Kondisi (Operator TS OR QC TS)
    Route::middleware('role:operator_ts,qc_ts')->group(function () {
        Route::get('/conditions', [ConditionController::class, 'landing'])->name('conditions.landing');
        Route::post('/conditions/open/{monitorControl}', [ConditionController::class, 'open'])->name('conditions.open');
        Route::get('/conditions/{hangingForm}', [ConditionController::class, 'edit'])->name('conditions.edit');
        Route::post('/conditions/{hangingForm}', [ConditionController::class, 'update'])->name('conditions.update');
        Route::get('monitor-controls/{monitorControl}/summary', [MonitorSummaryController::class, 'show'])->name('monitor-controls.summary');
    });

    // Summary + Sign + PDF (Supervisor only)
    Route::middleware('supervisor')->group(function () {
        Route::post('monitor-controls/{monitorControl}/summary/sign', [MonitorSummaryController::class, 'sign'])->name('monitor-controls.summary.sign');
        Route::delete('monitor-controls/{monitorControl}/summary/sign', [MonitorSummaryController::class, 'unsign'])->name('monitor-controls.summary.unsign');
        Route::get('monitor-controls/{monitorControl}/summary/pdf', [MonitorSummaryController::class, 'pdf'])->name('monitor-controls.summary.pdf');
    });
});