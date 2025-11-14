<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Laporan;
use Illuminate\Http\Request;
use App\Models\PenanggungJawab;
use App\Models\ProblemCategory;
use App\Services\ReportService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Log;
use App\Models\DepartemenSupervisor;
use App\Repositories\ReportRepository;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Library\SWTEmailNotifications;
use App\Http\Requests\StoreCompletionRequest;
use App\Http\Controllers\Concerns\HandlesLaporan;

class ReportController extends Controller
{
    use HandlesLaporan, SWTEmailNotifications;

    protected $reportService;
    protected $reportRepository;
    protected $fileUploadService;

    public function __construct(
        ReportService $reportService,
        ReportRepository $reportRepository,
        FileUploadService $fileUploadService
    ) {
        $this->reportService = $reportService;
        $this->reportRepository = $reportRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Helper method to decrypt encrypted ID and get Laporan model
     */
    private function getLaporanFromEncryptedId($encryptedId)
    {
        try {
            // Use Laravel's decrypt() since we use encrypt() in DataTables
            $id = decrypt($encryptedId);
            return Laporan::findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt report ID: ' . $e->getMessage());
            abort(404, 'Report not found');
        }
    }

    public function dashboard(Request $request)
    {
        if (!isset($_SERVER['HTTPS'])) {
            $_SERVER['HTTPS'] = 'off';
        }

        \SharedManager::checkAuthToModule(17);

        // Get filters from request
        $filters = [
            'category_id' => $request->get('category_id'),
            'month' => $request->get('month'),
            'year' => $request->get('year'),
            'date' => $request->get('date'),
        ];

        // Get statistics from service with filters
        $stats = $this->reportService->getDashboardStats($filters);

        $areas = Area::all();
        $laporanPerBulan = $this->reportService->getReportsPerMonth($filters);
        $areaPerBulan = $this->reportService->getReportsByAreaPerMonth($filters);
        $categoryPerBulan = $this->reportService->getReportsByCategoryCurrentMonth($filters);

        // Get recent reports for dashboard cards
        $recentAssigned = $this->reportService->getRecentReports('Assigned', 5);
        $recentCompleted = $this->reportService->getRecentReports('Completed', 5);

        // Handle AJAX requests for chart updates
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'charts' => [
                    'laporanPerBulan' => $laporanPerBulan,
                    'areaPerBulan' => $areaPerBulan,
                    'categoryPerBulan' => $categoryPerBulan
                ]
            ]);
        }

        \SharedManager::saveLog('log_swt', "Accessed the [Dashboard] page swt.");

        return view('walkandtalk.dashboard', [
            'totalLaporan' => $stats['total'],
            'laporanInProgress' => $stats['in_progress'],
            'laporanSelesai' => $stats['completed'],
            'areas' => $areas,
            'laporanPerBulan' => $laporanPerBulan,
            'areaPerBulan' => $areaPerBulan,
            'categoryPerBulan' => $categoryPerBulan,
            'recentAssigned' => $recentAssigned,
            'recentCompleted' => $recentCompleted,
        ]);
    }

    /**
     * Display the reports list page
     */
    public function index()
    {
        $areas = Area::all();
        return view('walkandtalk.reports', compact('areas'));
    }

    /**
     * Display the specified report detail
     * Redirects to dashboard
     */
    public function show($id)
    {
        try {
            // Try to handle both encrypted and plain ID
            if (is_numeric($id)) {
                // Plain ID from dashboard
                $laporan = Laporan::findOrFail($id);
            } else {
                // Encrypted ID from other sources
                $laporan = $this->getLaporanFromEncryptedId($id);
            }

            $laporan->load(['area', 'penanggungJawab', 'problemCategory', 'penyelesaian']);

            // Return detail view
            return view('walkandtalk.show', compact('laporan'));

        } catch (\Exception $e) {
            \Log::error('Error loading report detail: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Report not found');
        }
    }

    public function create()
    {
        $areas = Area::with('penanggungJawabs')->get();

        \SharedManager::saveLog('log_swt', "Accessed the [Create Report] page swt.");

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

            // Handle additional PICs
            $additionalPics = [];
            if ($request->has('additional_pics')) {
                $additionalPics = array_filter($request->input('additional_pics', []), function($pic) {
                    return !empty($pic);
                });
                \Log::info('📝 Additional PICs from request:', [
                    'raw' => $request->input('additional_pics', []),
                    'filtered' => $additionalPics
                ]);
            }

            // Create report using service
            $laporan = $this->reportService->createReport($validated, $photos);
            \Log::info('📊 Report created with ID: ' . $laporan->id);

            // Store additional PICs if any
            if (!empty($additionalPics)) {
                $this->storeAdditionalPics($laporan->id, $additionalPics);
                \Log::info('💾 Additional PICs stored for report ID: ' . $laporan->id, [
                    'count' => count($additionalPics),
                    'ids' => $additionalPics
                ]);
            } else {
                \Log::info('ℹ️ No additional PICs for report ID: ' . $laporan->id);
            }

            // Send email notification to PIC (including additional PICs)
            \Log::info('📧 Sending email notification for report ID: ' . $laporan->id);
            $this->emailReportAssigned($laporan, $additionalPics);

            \SharedManager::saveLog('log_swt', "Created new report swt.");

            // Redirect to report list page instead of dashboard
            return redirect()->route('laporan.index')->with('success', 'Report created successfully and notification sent.');

        } catch (\Exception $e) {
            \Log::error('Error creating report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create report. Please try again.');
        }
    }

    public function getPenanggungJawab(Request $request)
    {
        // Validate request
        $request->validate([
            'area_id' => 'required|integer|exists:areas,id'
        ]);

        $areaId = (int) $request->input('area_id');

        // Optimized query - only select needed columns
        $area = Area::select('id', 'name')
            ->with(['penanggungJawabs' => function($query) {
                $query->select('id', 'area_id', 'station', 'name', 'email')
                      ->orderBy('station');
            }])
            ->find($areaId);

        if (!$area) {
            return response()->json(['error' => 'Area tidak ditemukan'], 404);
        }

        // Optimized mapping
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
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function edit($id)
    {
        $laporan = $this->getLaporanFromEncryptedId($id);
        $laporan->load(['area', 'penanggungJawab', 'problemCategory']);
        $areas = Area::with('penanggungJawabs')->get();
        $problemCategories = ProblemCategory::active()->ordered()->get();

        \SharedManager::saveLog('log_swt', "Accessed the [Edit Report] page for ID: {$laporan->id} swt.");

        return view('walkandtalk.edit', compact('laporan', 'areas', 'problemCategories'));
    }

    public function update(UpdateReportRequest $request, $id)
    {
        try {
            // Decrypt ID and load laporan
            $laporan = $this->getLaporanFromEncryptedId($id);

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

            // Get old PIC - if specific PIC, show name; if null, show all PICs from area
            if ($laporan->penanggungJawab) {
                $oldPenanggungJawab = $laporan->penanggungJawab->name;
                $oldStation = $laporan->penanggungJawab->station ?? '-';
            } else if ($laporan->area) {
                // No specific PIC, get all PICs from the area
                $laporan->load('area.penanggungJawabs');
                if ($laporan->area->penanggungJawabs->count() > 0) {
                    $picNames = $laporan->area->penanggungJawabs->pluck('name')->toArray();
                    $oldPenanggungJawab = implode(', ', $picNames);
                } else {
                    $oldPenanggungJawab = '-';
                }
                $oldStation = '-';
            } else {
                $oldPenanggungJawab = '-';
                $oldStation = '-';
            }

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

            // Handle additional PICs
            $additionalPics = [];
            if ($request->has('additional_pics')) {
                $additionalPics = array_filter($request->input('additional_pics', []), function($pic) {
                    return !empty($pic);
                });
            }

            // Update report using service
            $validated['Foto'] = $allPhotos;
            $validated['additional_pics'] = $additionalPics;
            $laporan = $this->reportService->updateReport($laporan, $validated);

            // Detect changes for notifications
            $perubahan = $this->detectChanges($oldData, $validated, $oldArea, $oldPenanggungJawab, $oldStation);

            if (!empty($perubahan)) {
                $this->emailReportEdited($laporan, $perubahan);
            }

            $returnUrl = $request->input('return_url', route('laporan.index'));

            // Prevent redirect to datatables AJAX endpoints
            if (str_contains($returnUrl, '/datatables')) {
                $returnUrl = route('laporan.index');
            }

            \SharedManager::saveLog('log_swt', "Updated report ID: {$laporan->id} swt.");

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
        $laporan = $this->getLaporanFromEncryptedId($id);
        $laporan->load(['area', 'area.penanggungJawabs', 'penanggungJawab', 'problemCategory', 'penyelesaian']);

        \SharedManager::saveLog('log_swt', "Accessed the [Completion Action] page for ID: {$laporan->id} swt.");

        return view('walkandtalk.tindakan', compact('laporan'));
    }

    public function storeTindakan(StoreCompletionRequest $request, $id)
    {
        try {
            $laporan = $this->getLaporanFromEncryptedId($id);
            $validated = $request->validated();

            if ($validated['status'] === 'Completed') {
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

                // Send completion email notification
                $this->emailReportCompleted($laporan);

                \SharedManager::saveLog('log_swt', "Completed report ID: {$laporan->id} swt.");

                return redirect()->route('sejarah.index')
                    ->with('success', 'Report completed successfully and moved to history.');
            }

            // Just update status if not completed
            $this->reportService->updateStatus($laporan, $validated['status']);

            \SharedManager::saveLog('log_swt', "Updated report status ID: {$laporan->id} swt.");

            return redirect()->route('laporan.index')
                ->with('success', 'Report status updated successfully.');
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
            ->where('status', '!=', 'Completed');

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

        // Helper to resolve report photo URL
        $resolveReportUrl = function(string $filename) {
            return asset('storage/images/reports/' . $filename);
        };

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('encrypted_id', function ($laporan) {
                return encrypt($laporan->id);
            })
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
                if ($laporan->status == 'Assigned') {
                    return '<span class="status-badge status-assigned"><i class="fas fa-circle"></i> Assigned</span>';
                } elseif ($laporan->status == 'Completed') {
                    return '<span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span>';
                } else {
                    return '<span class="badge bg-secondary">' . $laporan->status . '</span>';
                }
            })
            ->addColumn('penyelesaian', function ($laporan) {
                $encryptedId = encrypt($laporan->id);
                return $laporan->penyelesaian
                    ? '<button class="btn btn-sm btn-info lihat-penyelesaian-btn" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian" data-encrypted-id="' . $encryptedId . '"><i class="fas fa-eye"></i> View</button>'
                    : '<a href="' . route('laporan.tindakan', ['id' => $encryptedId]) . '" class="btn btn-sm btn-primary"><i class="fas fa-tasks"></i> Action</a>';
            })
            ->addColumn('aksi', function ($laporan) {
                // Use encrypted ID for all routes
                $encryptedId = encrypt($laporan->id);
                $returnUrl = route('laporan.index');
                $editUrl = route('laporan.edit', ['id' => $encryptedId, 'return_url' => $returnUrl]);
                $deleteUrl = route('laporan.destroy', ['id' => $encryptedId]);
                return '<div class="d-flex gap-1 justify-content-center"><a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a><button class="btn btn-sm btn-danger delete-btn" data-encrypted-id="' . $encryptedId . '" data-delete-url="' . $deleteUrl . '" data-return-url="' . $returnUrl . '" title="Delete"><i class="fas fa-trash"></i></button></div>';
            })
            ->filterColumn('area.name', function($query, $keyword) {
                // Search in area name OR station name OR PIC name (case-insensitive)
                $keyword = strtolower($keyword);

                $query->where(function($q) use ($keyword) {
                    // Search in area name
                    $q->whereHas('area', function($subQ) use ($keyword) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                    })
                    // OR search in PIC station
                    ->orWhereHas('penanggungJawab', function($subQ) use ($keyword) {
                        $subQ->whereRaw('LOWER(station) LIKE ?', ["%{$keyword}%"]);
                    })
                    // OR search in PIC name
                    ->orWhereHas('penanggungJawab', function($subQ) use ($keyword) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                    });
                });
            })
            ->filterColumn('problemCategory.name', function($query, $keyword) {
                // Search in problem category name
                $query->whereHas('problemCategory', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('deskripsi_masalah', function($query, $keyword) {
                // Search in full description text (not truncated)
                $query->where('deskripsi_masalah', 'like', "%{$keyword}%");
            })
            ->rawColumns(['foto', 'departemen', 'problem_category', 'deskripsi_masalah', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function destroy($id)
    {
        try {
            $laporan = $this->getLaporanFromEncryptedId($id);

            // Delete report using service (handles photos and completion data)
            $deleted = $this->reportService->deleteReport($laporan);

            if ($deleted) {
                \SharedManager::saveLog('log_swt', "Deleted report ID: {$laporan->id} swt.");

                return response()->json(['success' => true, 'message' => 'Report deleted successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to delete report.']);
        } catch (\Exception $e) {
            Log::error('Error deleting report: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete report.']);
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
        try {
            $laporan = $this->getLaporanFromEncryptedId($id);
            $laporan->load(['penyelesaian', 'additionalPicsWithData']);

            if (!$laporan->penyelesaian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Completion data not found.'
                ]);
            }

            // Helper to resolve completion photo URL
            $resolveCompletionUrl = function(string $filename) {
                return asset('storage/images/completions/' . $filename);
            };

            $fotoUrls = [];
            if (!empty($laporan->penyelesaian->Foto) && is_array($laporan->penyelesaian->Foto)) {
                foreach ($laporan->penyelesaian->Foto as $foto) {
                    $fotoUrls[] = $resolveCompletionUrl($foto);
                }
            }

            return response()->json([
                'success' => true,
                'Tanggal' => Carbon::parse($laporan->penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY'),
                'Foto' => $fotoUrls,
                'deskripsi_penyelesaian' => $laporan->penyelesaian->deskripsi_penyelesaian ?? ''
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting completion data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve completion data. Please try again.'
            ], 500);
        }
    }

    /**
     * Get users for additional PIC selection (exclude General PIC)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersForPic()
    {
        try {
            // Get all users from DepartemenSupervisor (exclude General PIC)
            $users = DepartemenSupervisor::where('name', '!=', 'General')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->select('id', 'name', 'email', 'departemen')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users for PIC: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'users' => []
            ]);
        }
    }

    /**
     * Store additional PICs for a report
     *
     * @param int $reportId
     * @param array $additionalPics
     * @return void
     */
    private function storeAdditionalPics(int $reportId, array $additionalPics): void
    {
        try {
            // Store additional PICs as JSON in laporan table
            $laporan = Laporan::find($reportId);
            if ($laporan) {
                $laporan->additional_pics = $additionalPics;
                $laporan->save();

                // Verify the PICs exist and log their details
                $pics = \App\Models\PenanggungJawab::whereIn('id', $additionalPics)
                    ->select('id', 'name', 'station', 'email')
                    ->get();

                Log::info("✅ Additional PICs stored for report {$reportId}", [
                    'pic_ids' => $additionalPics,
                    'found_pics' => $pics->map(function($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'station' => $p->station,
                            'has_email' => !empty($p->email),
                            'email' => $p->email
                        ];
                    })->toArray()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ Error storing additional PICs: " . $e->getMessage());
        }
    }

    /**
     * Get all penanggung jawab from all areas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPenanggungJawab()
    {
        try {
            // Get all penanggung jawab with their area information
            $penanggungJawab = PenanggungJawab::with('area')
                ->select('id', 'name', 'station', 'area_id')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($pj) {
                    return [
                        'id' => $pj->id,
                        'name' => $pj->name,
                        'station' => $pj->station,
                        'area_id' => $pj->area_id,
                        'area_name' => $pj->area ? $pj->area->name : 'Unknown Area'
                    ];
                });

            return response()->json([
                'success' => true,
                'penanggung_jawab' => $penanggungJawab
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all penanggung jawab: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch penanggung jawab',
                'penanggung_jawab' => []
            ]);
        }
    }
}
