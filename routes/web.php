<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laporanController;
// use App\Http\Controllers\AuthController;

// Auth routes
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Default landing to login
// Route::get('/', function () { return redirect()->route('login'); });

Route::resource('laporan', laporanController::class);
Route::get('/', [laporanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [laporanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/datatables', [laporanController::class, 'dashboardDatatables'])->name('dashboard.datatables');
Route::get('/laporan', [laporanController::class, 'create'])->name('laporan.create');
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


// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\laporanController;
// use App\Http\Controllers\AuthController;

// // Auth routes
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// // Default landing to login
// Route::get('/', function () { return redirect()->route('login'); });

// Route::resource('laporan', laporanController::class)->middleware('auth');
// Route::get('/dashboard', [laporanController::class, 'dashboard'])->name('dashboard')->middleware('auth');
// Route::get('/dashboard/datatables', [laporanController::class, 'dashboardDatatables'])->name('dashboard.datatables');
// Route::get('/laporan', [laporanController::class, 'create'])->name('laporan.create')->middleware('auth');
// Route::get('/sejarah', [laporanController::class, 'index'])->name('sejarah')->middleware('auth');
// Route::get('/create', [laporanController::class, 'create'])->name('index.create');
// Route::post('/store', [laporanController::class, 'store'])->name('index.store')->middleware('auth');
// Route::get('/edit{id}', [laporanController::class, 'edit'])->name('index.edit');
// Route::put('/update{id}', [laporanController::class, 'update'])->name('index.update')->middleware('auth');
// Route::delete('/laporan/{id}/delete', [laporanController::class, 'destroy'])->name('laporan.destroy')->middleware('auth');
// Route::put('/laporan/{id}', [laporanController::class, 'update'])->name('laporan.update')->middleware('auth');
// Route::get('/laporan/{id}/tindakan', [laporanController::class, 'tindakan'])->name('laporan.tindakan')->middleware('auth');
// Route::post('/laporan/{id}/tindakan', [laporanController::class, 'storeTindakan'])->name('laporan.storeTindakan')->middleware('auth');
// Route::get('/get-supervisor/{id}', [laporanController::class, 'getSupervisor'])->name('get.supervisor')->middleware('auth');
// Route::get('/sejarah/datatables', [laporanController::class, 'sejarahDatatables'])->name('sejarah.datatables')->middleware('auth');
// Route::get('/laporan/penyelesaian/{id}', [laporanController::class, 'getPenyelesaian'])->middleware('auth');
// Route::get('/sejarah/download', [laporanController::class, 'downloadSejarah'])->name('sejarah.download')->middleware('auth');
// Route::get('/get-penanggung-jawab/{areaId}', [laporanController::class, 'getPenanggungJawab'])->name('get.penanggung.jawab')->middleware('auth');