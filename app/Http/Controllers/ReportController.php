<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesLaporan;
use App\Services\ReportService;
use App\Services\FileUploadService;
use App\Repositories\ReportRepository;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Requests\StoreCompletionRequest;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Area;
use App\Models\PenanggungJawab;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    use HandlesLaporan;

    protected $reportService;
    protected $fileUploadService;
    protected $reportRepository;

    public function __construct(
        ReportService $reportService,
        FileUploadService $fileUploadService,
        ReportRepository $reportRepository
    ) {
        $this->reportService = $reportService;
        $this->fileUploadService = $fileUploadService;
        $this->reportRepository = $reportRepository;
    }

    public function dashboard()
    {
        if (!isset($_SERVER['HTTPS'])) {
            $_SERVER['HTTPS'] = 'off';
        }
        \SharedManager::checkAuthToModule(17);
        
        // Get statistics from service
        $stats = $this->reportService->getDashboardStats();
        
        $areas = Area::all();
        $laporanPerBulan = $this->reportService->getReportsPerMonth();
        $areaPerBulan = $this->reportService->getReportsByAreaPerMonth();
        $categoryPerBulan = $this->reportService->getReportsByCategoryCurrentMonth();

        \SharedManager::saveLog('log_sitime', "Accessed the [Dashboard] page swt.");
        
        return view('walkandtalk.dashboard', [
            'totalLaporan' => $stats['total'],
            'laporanInProgress' => $stats['in_progress'],
            'laporanSelesai' => $stats['completed'],
            'areas' => $areas,
            'laporanPerBulan' => $laporanPerBulan,
            'areaPerBulan' => $areaPerBulan,
            'categoryPerBulan' => $categoryPerBulan,
        ]);
    }

    public function create()
    {
        $areas = Area::with('penanggungJawabs')->get();
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Create Report] page swt.");
        
        return view('walkandtalk.laporan', compact('areas'));
    }

    public function store(StoreReportRequest $request)
    {
        try {
            $validated = $request->validated();

            // Upload photos if provided
            $photos = [];
            if ($request->hasFile('Foto')) {
                $photos = $this->fileUploadService->uploadReportPhotos($request->file('Foto'));
            }

            // Add default supervisor ID
            $validated['departemen_supervisor_id'] = 1;

            // Create report using service
            $laporan = $this->reportService->createReport($validated, $photos);

            // $this->sendSupervisorNotifications($laporan); // Disabled email notifications
            \SharedManager::saveLog('log_sitime', "Created new report swt.");
            
            return redirect()->route('dashboard')->with('success', 'Report created successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error creating report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create report. Please try again.');
        }
    }

    public function getPenanggungJawab($areaId)
    {
        $areaId = (int) $areaId;
        $area = Area::with('penanggungJawabs')->find($areaId);
        if (!$area) {
            return response()->json(['error' => 'Area tidak ditemukan'], 404);
        }

        $stations = $area->penanggungJawabs->map(function ($pj) {
            return [
                'id' => $pj->id,
                'station' => $pj->station,
                'name' => $pj->name,
                'email' => $pj->email,
            ];
        });

        return response()->json([
            'stations' => $stations,
            'group_members' => $area->penanggungJawabs->pluck('name')->toArray(),
        ]);
    }

    public function edit($id)
    {
        $laporan = Laporan::with(['area', 'penanggungJawab', 'problemCategory'])->findOrFail($id);
        $areas = Area::with('penanggungJawabs')->get();
        $problemCategories = \App\Models\ProblemCategory::active()->ordered()->get();
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Edit Report] page for ID: {$id} swt.");
        
        return view('walkandtalk.edit', compact('laporan', 'areas', 'problemCategories'));
    }

    public function update(UpdateReportRequest $request, $id)
    {
        try {
            $laporan = $this->reportRepository->findById($id);
            
            if (!$laporan) {
                return redirect()->route('laporan.index')
                    ->with('error', 'Report not found.');
            }

            $validated = $request->validated();

            // Store old data for change detection
            $oldData = [
                'area_id' => $laporan->area_id,
                'penanggung_jawab_id' => $laporan->penanggung_jawab_id,
                'problem_category_id' => $laporan->problem_category_id,
                'deskripsi_masalah' => $laporan->deskripsi_masalah,
                'tenggat_waktu' => $laporan->tenggat_waktu,
            ];

            $oldArea = $laporan->area ? $laporan->area->name : '-';
            $oldPenanggungJawab = $laporan->penanggungJawab ? $laporan->penanggungJawab->name : '-';

            // Handle photo management
            $existingPhotos = $request->input('existing_photos', []);
            $newPhotos = [];
            
            if ($request->hasFile('Foto')) {
                $newPhotos = $this->fileUploadService->uploadReportPhotos($request->file('Foto'));
            }

            $allPhotos = array_merge($existingPhotos, $newPhotos);

            // Delete removed photos
            $oldPhotos = $laporan->Foto ?: [];
            $photosToDelete = array_diff($oldPhotos, $existingPhotos);
            if (!empty($photosToDelete)) {
                $this->fileUploadService->deleteFiles($photosToDelete, 'images/reports');
            }

            // Update report using service
            $validated['Foto'] = $allPhotos;
            $laporan = $this->reportService->updateReport($laporan, $validated);

            // Detect changes for notifications
            $perubahan = $this->detectChanges($oldData, $validated, $oldArea, $oldPenanggungJawab);
            
            if (!empty($perubahan)) {
                // $this->sendEditNotifications($laporan, $perubahan); // Disabled email notifications
            }

            $returnUrl = $request->input('return_url', route('laporan.index'));
            
            // Prevent redirect to datatables AJAX endpoints
            if (str_contains($returnUrl, '/datatables')) {
                $returnUrl = route('laporan.index');
            }
            
            \SharedManager::saveLog('log_sitime', "Updated report ID: {$id} swt.");
            
            return redirect($returnUrl)->with('success', 'Report updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error updating report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $returnUrl = $request->input('return_url', route('laporan.index'));
            return redirect($returnUrl)
                ->with('success', 'Report updated successfully.');
        }
    }

    public function tindakan($id)
    {
        $laporan = Laporan::with(['area', 'area.penanggungJawabs', 'penanggungJawab', 'problemCategory', 'penyelesaian'])->findOrFail($id);
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Completion Action] page for ID: {$id} swt.");
        
        return view('walkandtalk.tindakan', compact('laporan'));
    }

    public function storeTindakan(StoreCompletionRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            $laporan = $this->reportRepository->findById($id);
            
            if (!$laporan) {
                return redirect()->route('dashboard')
                    ->with('error', 'Report not found.');
            }

            if ($validated['status'] === 'Selesai') {
                // Upload completion photos if provided
                $photos = [];
                if ($request->hasFile('Foto')) {
                    $photos = $this->fileUploadService->uploadCompletionPhotos($request->file('Foto'));
                }

                // Complete report using service
                $this->reportService->completeReport($laporan, [
                    'Tanggal' => $validated['Tanggal'],
                    'deskripsi_penyelesaian' => $validated['deskripsi_penyelesaian'],
                ], $photos);

                \SharedManager::saveLog('log_sitime', "Completed report ID: {$id} swt.");
                
                return redirect()->route('sejarah.index')
                    ->with('success', 'Report completed successfully and moved to history.');
            }

            // Just update status if not completed
            $this->reportService->updateStatus($laporan, $validated['status']);

            \SharedManager::saveLog('log_sitime', "Updated report status ID: {$id} swt.");
            
            return redirect()->route('dashboard')
                ->with('success', 'Report created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error completing report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to complete report: ' . $e->getMessage());
        }
    }

    public function dashboardDatatables(Request $request)
    {
        $query = Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', '!=', 'Selesai');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('penanggung_jawab_id')) {
            $query->where('penanggung_jawab_id', $request->penanggung_jawab_id);
        }
        if ($request->filled('kategori')) {
            $query->where('problem_category_id', $request->kategori);
        }
        if ($request->filled('tenggat_bulan')) {
            $query->whereMonth('tenggat_waktu', $request->tenggat_bulan);
        }
        if ($request->filled('category_id')) {
            $query->where('problem_category_id', $request->category_id);
        }

        // Ordering: if date filter is applied, sort chronologically (start->end). Otherwise, latest first.
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Helper to resolve report photo URL with fallback to legacy folder
        $resolveReportUrl = function(string $filename) {
            $candidates = [
                public_path('images/reports/' . $filename),
                public_path('images/' . $filename),
            ];
            foreach ($candidates as $idx => $path) {
                if (file_exists($path)) {
                    return $idx === 0 ? asset('images/reports/' . $filename) : asset('images/' . $filename);
                }
            }
            return asset('images/' . $filename);
        };

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('Tanggal', function ($laporan) {
                return Carbon::parse($laporan->created_at)->format('l, j-n-Y');
            })
            ->addColumn('deskripsi_masalah_full', function ($laporan) {
                return $laporan->deskripsi_masalah ?? '';
            })
            ->addColumn('person_in_charge', function ($laporan) {
                if ($laporan->penanggungJawab) {
                    return $laporan->penanggungJawab->name ?? '';
                }
                return '';
            })
            ->addColumn('foto', function ($laporan) use ($resolveReportUrl) {
                if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                    $foto = $laporan->Foto[0];
                    $fotoPath = $resolveReportUrl($foto);
                    $photoUrls = [];
                    foreach ($laporan->Foto as $f) {
                        $photoUrls[] = $resolveReportUrl($f);
                    }
                    $photoData = json_encode($photoUrls);
                    return '<img src="' . $fotoPath . '" alt="Foto Masalah" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalFotoFull" data-photos=\'' . $photoData . '\'>';
                }
                return '<span class="badge bg-secondary">No photo</span>';
            })
            ->addColumn('departemen', function ($laporan) {
                $html = '';
                if ($laporan->area) {
                    $areaName = $laporan->area->name;
                    if ($laporan->penanggungJawab) {
                        $stationOrPic = trim((string)($laporan->penanggungJawab->station ?? ''));
                        if ($stationOrPic === '') {
                            $stationOrPic = $laporan->penanggungJawab->name ?? '';
                        }
                        $html = $stationOrPic !== '' ? '<span class="fw-bold">' . $areaName . ' (' . e($stationOrPic) . ')</span>' : '<span class="fw-bold">' . $areaName . '</span>';
                    } else {
                        $html = '<span class="fw-bold">' . $areaName . '</span>';
                    }
                }
                return $html;
            })
            ->addColumn('problem_category', function ($laporan) {
                if ($laporan->problemCategory) {
                    $color = $laporan->problemCategory->color ?? '#6c757d';
                    $name = $laporan->problemCategory->name;
                    return '<span class="badge" style="background-color: ' . $color . '; color: white;">' . e($name) . '</span>';
                }
                return '<span class="text-muted">No Category</span>';
            })
            ->addColumn('deskripsi_masalah', function ($laporan) {
                $description = $laporan->deskripsi_masalah;
                $maxLength = 80;
                $shortDescription = strlen($description) > $maxLength ? substr($description, 0, $maxLength) . '...' : $description;
                return '<div class="description-container" title="' . e($laporan->deskripsi_masalah) . '">' . e($shortDescription) . '</div>';
            })
            ->addColumn('deskripsi_masalah_full', function ($laporan) {
                return $laporan->deskripsi_masalah ?? '';
            })
            ->editColumn('tenggat_waktu', function ($laporan) {
                return Carbon::parse($laporan->tenggat_waktu)->format('l, j-n-Y');
            })
            ->addColumn('status', function ($laporan) {
                return $laporan->status == 'In Progress'
                    ? '<span class="status-badge status-in-progress"><i class="fas fa-cog fa-spin"></i> In Progress</span>'
                    : '<span class="badge bg-secondary">' . $laporan->status . '</span>';
            })
            ->addColumn('penyelesaian', function ($laporan) {
                return $laporan->penyelesaian
                    ? '<button class="btn btn-sm btn-info lihat-penyelesaian-btn" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian" data-id="' . $laporan->id . '"><i class="fas fa-eye"></i> View</button>'
                    : '<a href="' . route('laporan.tindakan', $laporan->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-tasks"></i> Action</a>';
            })
            ->addColumn('aksi', function ($laporan) {
                // Use laporan.index route instead of datatables endpoint
                $returnUrl = route('laporan.index');
                $editUrl = route('laporan.edit', ['id' => $laporan->id, 'return_url' => $returnUrl]);
                $deleteUrl = route('laporan.destroy', $laporan->id);
                return '<div class="d-flex gap-1"><a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger delete-btn" data-id="' . $laporan->id . '" data-delete-url="' . $deleteUrl . '" data-return-url="' . $returnUrl . '" title="Delete"><i class="fas fa-trash"></i></button></div>';
            })
            ->rawColumns(['foto', 'departemen', 'problem_category', 'deskripsi_masalah', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function destroy($id)
    {
        try {
            $laporan = $this->reportRepository->findById($id);
            
            if (!$laporan) {
                return response()->json(['success' => true, 'message' => 'Report already removed.']);
            }

            // Delete report using service (handles photos and completion data)
            $deleted = $this->reportService->deleteReport($laporan);

            if ($deleted) {
                \SharedManager::saveLog('log_sitime', "Deleted report ID: {$id} swt.");
            
                return response()->json(['success' => true, 'message' => 'Report deleted successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to delete report.'], 500);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting report: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSupervisor($id)
    {
        $id = (int)$id;
        if ($id <= 3) {
            $area = Area::with('penanggungJawabs')->find($id);
            if (!$area) { return response()->json(['error' => 'Area tidak ditemukan'], 404); }
            $supervisorNames = $area->penanggungJawabs->pluck('name')->toArray();
            return response()->json(['group_members' => $supervisorNames]);
        } else {
            $penanggungJawab = PenanggungJawab::find($id);
            if (!$penanggungJawab) { return response()->json(['error' => 'Penanggung jawab tidak ditemukan'], 404); }
            return response()->json(['group_members' => [$penanggungJawab->name]]);
        }
    }

    public function getPenyelesaian($id)
    {
        $laporan = Laporan::with('penyelesaian')->find($id);
        if (!$laporan || !$laporan->penyelesaian) { return response()->json(['success' => false]); }
        
        // Helper to resolve completion photo URL with fallback to legacy folder
        $resolveCompletionUrl = function(string $filename) {
            $candidates = [
                public_path('images/completions/' . $filename),
                public_path('images/' . $filename),
            ];
            foreach ($candidates as $idx => $path) {
                if (file_exists($path)) {
                    return $idx === 0 ? asset('images/completions/' . $filename) : asset('images/' . $filename);
                }
            }
            return asset('images/' . $filename);
        };
        
        $fotoUrls = [];
        if (!empty($laporan->penyelesaian->Foto) && is_array($laporan->penyelesaian->Foto)) {
            foreach ($laporan->penyelesaian->Foto as $foto) { $fotoUrls[] = $resolveCompletionUrl($foto); }
        }
        return response()->json([
            'success' => true,
            'Tanggal' => Carbon::parse($laporan->penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY'),
            'Foto' => $fotoUrls,
            'deskripsi_penyelesaian' => $laporan->penyelesaian->deskripsi_penyelesaian ?? ''
        ]);
    }
}
