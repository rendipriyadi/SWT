<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;

// Default landing to dashboard (admin mode - no authentication)
Route::get('/', function () { return redirect()->route('dashboard'); });

// All routes are now public (admin mode - no authentication required)
Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/datatables', [ReportController::class, 'dashboardDatatables'])->name('dashboard.datatables');

// Reports listing should show in-progress dashboard table and allow create
Route::get('/laporan', function(){ return view('walkandtalk.reports'); })->name('laporan.index');
Route::get('/laporan/create', [ReportController::class, 'create'])->name('laporan.create');
Route::post('/laporan/store', [ReportController::class, 'store'])->name('laporan.store');
Route::get('/sejarah', [HistoryController::class, 'index'])->name('sejarah');
Route::get('/create', [ReportController::class, 'create'])->name('index.create');
Route::post('/store', [ReportController::class, 'store'])->name('index.store');
// Explicit edit route for reports used by DataTables action buttons
Route::get('/laporan/{id}/edit', [ReportController::class, 'edit'])->name('laporan.edit');
// Keep legacy alias if needed, but primary update is named 'laporan.update'
Route::put('/update{id}', [ReportController::class, 'update'])->name('index.update');
Route::delete('/laporan/{id}/delete', [ReportController::class, 'destroy'])->name('laporan.destroy');
Route::put('/laporan/{id}', [ReportController::class, 'update'])->name('laporan.update');
Route::get('/laporan/{id}/tindakan', [ReportController::class, 'tindakan'])->name('laporan.tindakan');
Route::post('/laporan/{id}/tindakan', [ReportController::class, 'storeTindakan'])->name('laporan.storeTindakan');
Route::get('/get-supervisor/{id}', [ReportController::class, 'getSupervisor'])->name('get.supervisor');
Route::get('/sejarah/datatables', [HistoryController::class, 'sejarahDatatables'])->name('sejarah.datatables');
Route::get('/laporan/penyelesaian/{id}', [ReportController::class, 'getPenyelesaian']);
Route::get('/sejarah/download', [HistoryController::class, 'downloadSejarah'])->name('sejarah.download');
Route::get('/get-penanggung-jawab/{areaId}', [ReportController::class, 'getPenanggungJawab'])->name('get.penanggung.jawab');
Route::patch('/laporan/{id}/update-status', [ReportController::class, 'updateStatus'])->name('laporan.update-status');

// Master Data Routes (now public - admin mode)
Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('department', App\Http\Controllers\MasterData\DepartmentController::class);
    Route::post('department/{id}/restore', [App\Http\Controllers\MasterData\DepartmentController::class, 'restore'])->name('department.restore');
    Route::delete('department/{id}/force-delete', [App\Http\Controllers\MasterData\DepartmentController::class, 'forceDelete'])->name('department.force-delete');
    
    Route::resource('area', App\Http\Controllers\MasterData\AreaController::class);
    Route::resource('problem-category', App\Http\Controllers\MasterData\ProblemCategoryController::class);
});