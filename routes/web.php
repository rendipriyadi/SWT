<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laporanController;

// Default landing to dashboard (admin mode - no authentication)
Route::get('/', function () { return redirect()->route('dashboard'); });

// All routes are now public (admin mode - no authentication required)
Route::get('/dashboard', [laporanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/datatables', [laporanController::class, 'dashboardDatatables'])->name('dashboard.datatables');

// Reports listing should show in-progress dashboard table and allow create
Route::get('/laporan', function(){ return view('walkandtalk.reports'); })->name('laporan.index');
Route::get('/laporan/create', [laporanController::class, 'create'])->name('laporan.create');
Route::post('/laporan/store', [laporanController::class, 'store'])->name('laporan.store');
Route::get('/sejarah', [laporanController::class, 'index'])->name('sejarah');
Route::get('/create', [laporanController::class, 'create'])->name('index.create');
Route::post('/store', [laporanController::class, 'store'])->name('index.store');
Route::get('/edit{id}', [laporanController::class, 'edit'])->name('index.edit');
Route::put('/update{id}', [laporanController::class, 'update'])->name('index.update');
Route::delete('/laporan/{id}/delete', [laporanController::class, 'destroy'])->name('laporan.destroy');
Route::put('/laporan/{id}', [laporanController::class, 'update'])->name('laporan.update');
Route::get('/laporan/{id}/tindakan', [laporanController::class, 'tindakan'])->name('laporan.tindakan');
Route::post('/laporan/{id}/tindakan', [laporanController::class, 'storeTindakan'])->name('laporan.storeTindakan');
Route::get('/get-supervisor/{id}', [laporanController::class, 'getSupervisor'])->name('get.supervisor');
Route::get('/sejarah/datatables', [laporanController::class, 'sejarahDatatables'])->name('sejarah.datatables');
Route::get('/laporan/penyelesaian/{id}', [laporanController::class, 'getPenyelesaian']);
Route::get('/sejarah/download', [laporanController::class, 'downloadSejarah'])->name('sejarah.download');
Route::get('/get-penanggung-jawab/{areaId}', [laporanController::class, 'getPenanggungJawab'])->name('get.penanggung.jawab');
Route::patch('/laporan/{id}/update-status', [laporanController::class, 'updateStatus'])->name('laporan.update-status');

// Master Data Routes (now public - admin mode)
Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('department', App\Http\Controllers\MasterData\DepartmentController::class);
    Route::post('department/{id}/restore', [App\Http\Controllers\MasterData\DepartmentController::class, 'restore'])->name('department.restore');
    Route::delete('department/{id}/force-delete', [App\Http\Controllers\MasterData\DepartmentController::class, 'forceDelete'])->name('department.force-delete');
    
    Route::resource('area', App\Http\Controllers\MasterData\AreaController::class);
    Route::resource('problem-category', App\Http\Controllers\MasterData\ProblemCategoryController::class);
});