<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MasterData\AreaController;
use App\Http\Controllers\MasterData\DepartmentController;
use App\Http\Controllers\MasterData\ProblemCategoryController;

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
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');

    // Edit & Update (use {id} for encrypted ID, no model binding)
    Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReportController::class, 'update'])->name('update');
    Route::patch('/{id}/update-status', [ReportController::class, 'updateStatus'])->name('update-status');

    // Completion (Tindakan) - use {id} for encrypted ID
    Route::get('/{id}/tindakan', [ReportController::class, 'tindakan'])->name('tindakan');
    Route::post('/{id}/tindakan', [ReportController::class, 'storeTindakan'])->name('storeTindakan');

    // AJAX Endpoints - use {id} for encrypted ID
    Route::get('/{id}/penyelesaian', [ReportController::class, 'getPenyelesaian'])->name('penyelesaian');

    // Delete - use {id} for encrypted ID
    Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');

    // Show Detail LAST (catch-all for single parameter)
    Route::get('/{id}', [ReportController::class, 'show'])->name('show');
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
Route::get('/supervisor/{id}', [ReportController::class, 'getSupervisor'])->name('supervisor');
Route::post('/api/stations', [ReportController::class, 'getPenanggungJawab'])->name('penanggung.jawab');

// ============================================================================
// MASTER DATA (Department, Area, Problem Category)
// ============================================================================
    Route::prefix('master-data')->name('master-data.')->group(function () {
        // Department Management
        Route::resource('department', DepartmentController::class);
        Route::post('department/{department}/restore', [DepartmentController::class, 'restore'])->name('department.restore');
        Route::delete('department/{department}/force-delete', [DepartmentController::class, 'forceDelete'])->name('department.force-delete');

        // Area Management
        Route::resource('area', AreaController::class);

        // Problem Category Management
        Route::resource('problem-category', ProblemCategoryController::class);
    });

// ============================================================================
// API Routes for AJAX calls
// ============================================================================
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/users-for-pic', [ReportController::class, 'getUsersForPic'])->name('users-for-pic');
    Route::get('/all-penanggung-jawab', [ReportController::class, 'getAllPenanggungJawab'])->name('all-penanggung-jawab');
});