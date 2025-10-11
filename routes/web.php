<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| All routes are public (admin mode - no authentication required)
*/

// ============================================================================
// DASHBOARD
// ============================================================================
Route::get('/', function () { return redirect()->route('dashboard'); });
Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/datatables', [ReportController::class, 'dashboardDatatables'])->name('dashboard.datatables');

// ============================================================================
// REPORTS (Walk and Talk)
// ============================================================================
Route::prefix('laporan')->name('laporan.')->group(function () {
    // List & Create
    Route::get('/', function(){ 
        $areas = \App\Models\Area::all();
        return view('walkandtalk.reports', compact('areas')); 
    })->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    
    // Edit & Update
    Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReportController::class, 'update'])->name('update');
    Route::patch('/{id}/update-status', [ReportController::class, 'updateStatus'])->name('update-status');
    
    // Delete
    Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');
    
    // Completion (Tindakan)
    Route::get('/{id}/tindakan', [ReportController::class, 'tindakan'])->name('tindakan');
    Route::post('/{id}/tindakan', [ReportController::class, 'storeTindakan'])->name('storeTindakan');
    
    // AJAX Endpoints
    Route::get('/penyelesaian/{id}', [ReportController::class, 'getPenyelesaian'])->name('penyelesaian');
});

// ============================================================================
// HISTORY (Sejarah)
// ============================================================================
Route::prefix('sejarah')->name('sejarah.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');
    Route::get('/datatables', [HistoryController::class, 'sejarahDatatables'])->name('datatables');
    Route::get('/download', [HistoryController::class, 'downloadSejarah'])->name('download');
});

// ============================================================================
// AJAX HELPERS
// ============================================================================
Route::get('/get-supervisor/{id}', [ReportController::class, 'getSupervisor'])->name('get.supervisor');
Route::get('/get-penanggung-jawab/{areaId}', [ReportController::class, 'getPenanggungJawab'])->name('get.penanggung.jawab');

// Master Data Routes (now public - admin mode)
Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('department', App\Http\Controllers\MasterData\DepartmentController::class);
    Route::post('department/{id}/restore', [App\Http\Controllers\MasterData\DepartmentController::class, 'restore'])->name('department.restore');
    Route::delete('department/{id}/force-delete', [App\Http\Controllers\MasterData\DepartmentController::class, 'forceDelete'])->name('department.force-delete');
    
    Route::resource('area', App\Http\Controllers\MasterData\AreaController::class);
    Route::resource('problem-category', App\Http\Controllers\MasterData\ProblemCategoryController::class);
});