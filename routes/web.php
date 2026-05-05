<?php

use App\Http\Controllers\Account\RolePermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Inventory\InventoryApiController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Master\ExpeditionController;
use App\Http\Controllers\Master\FarmController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\MenuController;
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
use App\Http\Controllers\Transaction\ReturMatiRecapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('monitor/{location}', [LiveMonitorController::class, 'show'])->name('monitor.show');
Route::get('monitor/{location}/data', [LiveMonitorController::class, 'data'])->name('monitor.data');


Route::middleware(['auth', 'nocache'])->group(function () {
    Route::middleware('role:supervisor,superadmin')
    ->get('/menu', [MenuController::class, 'index'])
    ->name('menu.index');
    Route::get('/api/dashboard/today-stats', [DashboardController::class, 'todayStats'])->name('api.dashboard.today-stats');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/rekap', [DashboardController::class, 'rekap'])->name('dashboard.rekap');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{auditLog}', [HistoryController::class, 'show'])->name('history.show');
    // Planning LB
    Route::middleware('role:adminlb,supervisor,superadmin')->resource('planning-lb', PlanningLbController::class);
    // Master Data (Operator TS)
    Route::middleware('role:operator_ts,supervisor,superadmin')->prefix('master')->name('master.')->group(function () {
        Route::resource('expeditions', ExpeditionController::class)->except(['destroy']);
        Route::resource('farms', FarmController::class)->except(['destroy']);
    });
    // Delete Master Data (Supervisor only)
    Route::middleware('role:supervisor,superadmin')->prefix('master')->name('master.')->group(function () {
        Route::delete('expeditions/{expedition}', [ExpeditionController::class, 'destroy'])->name('expeditions.destroy');
        Route::delete('farms/{farm}', [FarmController::class, 'destroy'])->name('farms.destroy');
    });
    // Master Users (Superadmin only)
    Route::middleware('role:superadmin')->prefix('master')->name('master.')->group(function () {
        Route::resource('users', UserController::class);
    });
    // Monitor Controls (Operator TS)
    Route::middleware('role:operator_ts,supervisor,superadmin')->group(function () {
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
    Route::middleware('role:checker_hanging,supervisor,superadmin')->group(function () {
        Route::get('/hanging', [HangingLandingController::class, 'index'])->name('hanging.landing');
        Route::post('/hanging/open/{monitorControl}', [HangingLandingController::class, 'open'])->name('hanging.open');
        Route::post('/hanging/start/{hangingForm}', [HangingLandingController::class, 'start'])->name('hanging.start');
        Route::get('hanging-forms/{hangingForm}', [HangingFormController::class, 'show'])->name('hanging-forms.show');
        Route::post('hanging-forms/{hangingForm}/finish', [HangingFormController::class, 'finish'])->name('hanging-forms.finish');
        Route::patch('hanging-cells/{hangingLineSet}', [HangingFormController::class, 'updateCell'])->name('hanging-cells.update');
        Route::post('/hanging/finish-shift/{location}/{shift}/{date}', [HangingLandingController::class, 'finishShift'])
            ->name('hanging.finish-shift');
    });
    // Retur & Mati (Checker Hanging OR Checker Retur)
    Route::middleware('role:checker_hanging,checker_retur,supervisor,superadmin')->group(function () {

        // ✅ taruh REKAP dulu (statis)
        Route::get('/retur-mati/rekap', [ReturMatiRecapController::class, 'index'])->name('retur-mati.rekap');
        Route::get('/retur-mati/rekap/export', [ReturMatiRecapController::class, 'export'])->name('retur-mati.rekap.export');
        // baru route yang dinamis
        Route::get('/retur-mati', [ReturMatiLandingController::class, 'index'])->name('retur-mati.landing');
        Route::post('/retur-mati/open/{monitorControl}', [ReturMatiLandingController::class, 'open'])->name('retur-mati.open');
        Route::get('/retur-mati/{hangingForm}', [ReturMatiController::class, 'edit'])->name('retur-mati.edit');
        Route::post('/retur-mati/{hangingForm}', [ReturMatiController::class, 'update'])->name('retur-mati.update');
    });
    // QC Kondisi (Operator TS OR QC TS)
    Route::middleware('role:operator_ts,qc_ts,supervisor,superadmin')->group(function () {
        Route::get('/conditions', [ConditionController::class, 'landing'])->name('conditions.landing');
        Route::post('/conditions/open/{monitorControl}', [ConditionController::class, 'open'])->name('conditions.open');
        Route::get('/conditions/{hangingForm}', [ConditionController::class, 'edit'])->name('conditions.edit');
        Route::post('/conditions/{hangingForm}', [ConditionController::class, 'update'])->name('conditions.update');
        Route::get('monitor-controls/{monitorControl}/summary', [MonitorSummaryController::class, 'show'])->name('monitor-controls.summary');
    });
    // Summary + Sign + PDF (Supervisor only)
    Route::middleware('role:supervisor,superadmin')->group(function () {
        Route::post('monitor-controls/{monitorControl}/summary/sign', [MonitorSummaryController::class, 'sign'])->name('monitor-controls.summary.sign');
        Route::delete('monitor-controls/{monitorControl}/summary/sign', [MonitorSummaryController::class, 'unsign'])->name('monitor-controls.summary.unsign');
        Route::get('monitor-controls/{monitorControl}/summary/pdf', [MonitorSummaryController::class, 'pdf'])->name('monitor-controls.summary.pdf');
    });

    Route::middleware('perm:shfi.view')->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');

        Route::get('/api/roots', [InventoryApiController::class, 'roots'])->name('api.roots');
        Route::get('/api/breadcrumbs', [InventoryApiController::class, 'breadcrumbs'])->name('api.breadcrumbs');
        Route::get('/api/list', [InventoryApiController::class, 'list'])->name('api.list');

        Route::middleware('perm:shfi.upload')->post('/api/upload', [InventoryApiController::class, 'upload'])->name('api.upload');
        Route::middleware('perm:shfi.edit')->post('/api/folder', [InventoryApiController::class, 'createFolder'])->name('api.folder.create');
        Route::middleware('perm:shfi.edit')->post('/api/rename', [InventoryApiController::class, 'rename'])->name('api.rename');
        Route::middleware('perm:shfi.edit')->post('/api/move', [InventoryApiController::class, 'move'])->name('api.move');
        Route::middleware('perm:shfi.edit')->post('/api/copy', [InventoryApiController::class, 'copy'])->name('api.copy');

        Route::middleware('perm:shfi.delete')->delete('/api/delete', [InventoryApiController::class, 'softDelete'])->name('api.delete');

        Route::get('/api/download/{file}', [InventoryApiController::class, 'download'])->name('api.download');
        Route::get('/api/preview/{file}', [InventoryApiController::class, 'preview'])->name('api.preview');

        Route::get('/trash', [InventoryController::class, 'trash'])->name('trash');
        Route::get('/api/trash', [InventoryApiController::class, 'trashList'])->name('api.trash.list');
        Route::middleware('perm:shfi.restore')->post('/api/restore', [InventoryApiController::class, 'restore'])->name('api.restore');
    });
    Route::middleware('role:superadmin')->prefix('account')->name('account.')->group(function () {
        Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
        Route::post('/role-permissions', [RolePermissionController::class, 'store'])->name('role-permissions.store');
    });
});