<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesLaporan;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Area;
use App\Models\PenanggungJawab;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    use HandlesLaporan;

    public function dashboard()
    {
        $totalLaporan = Laporan::count();
        $laporanInProgress = Laporan::where('status', 'In Progress')->count();
        $laporanSelesai = Laporan::where('status', 'Selesai')->count();

        $areas = Area::all();

        $laporanPerBulan = Laporan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(11))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $areaPerBulan = Laporan::join('areas', 'laporan.area_id', '=', 'areas.id')
            ->selectRaw('areas.name as area_name, MONTH(laporan.created_at) as bulan, COUNT(*) as total')
            ->where('laporan.created_at', '>=', now()->subMonths(11))
            ->groupBy('areas.name', 'bulan')
            ->orderBy('bulan')
            ->get();

        $categoryPerBulan = Laporan::with('problemCategory')
            ->selectRaw('problem_category_id, COUNT(*) as total')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('problem_category_id')
            ->get()
            ->map(function($item) {
                return [
                    'problem_category' => $item->problemCategory,
                    'total' => $item->total
                ];
            });

        if ($categoryPerBulan->isEmpty()) {
            $categoryPerBulan = Laporan::with('problemCategory')
                ->selectRaw('problem_category_id, COUNT(*) as total')
                ->where('created_at', '>=', now()->subMonths(3))
                ->groupBy('problem_category_id')
                ->get()
                ->map(function($item) {
                    return [
                        'problem_category' => $item->problemCategory,
                        'total' => $item->total
                    ];
                });
        }

        return view('walkandtalk.dashboard', compact(
            'totalLaporan',
            'laporanInProgress',
            'laporanSelesai',
            'areas',
            'laporanPerBulan',
            'areaPerBulan',
            'categoryPerBulan'
        ));
    }

    public function create()
    {
        $areas = Area::with('penanggungJawabs')->get();
        return view('walkandtalk.laporan', compact('areas'));
    }

    public function store(Request $request)
    {
        $messages = [
            'area_id.required' => 'Area harus dipilih.',
            'problem_category_id.required' => 'Kategori masalah harus dipilih.',
            'deskripsi_masalah.required' => 'Deskripsi masalah harus diisi.',
            'tenggat_waktu.required' => 'Tenggat waktu harus diisi.',
        ];

        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'penanggung_jawab_id' => 'nullable|exists:penanggung_jawab,id',
            'problem_category_id' => 'required|exists:problem_categories,id',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
        ], $messages);

        $fotoFileNames = [];
        if ($request->hasFile('Foto')) {
            foreach ($request->file('Foto') as $foto) {
                $fileName = time() . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('images/reports'), $fileName);
                $fotoFileNames[] = $fileName;
            }
        }

        $laporan = Laporan::create([
            'area_id' => $request->area_id,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'departemen_supervisor_id' => 1,
            'problem_category_id' => $request->problem_category_id,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => 'In Progress',
            'Foto' => count($fotoFileNames) > 0 ? $fotoFileNames : null,
        ]);

        // $this->sendSupervisorNotifications($laporan); // Disabled email notifications

        return redirect()->route('laporan.index')->with('success', 'Report created successfully.');
    }

    public function getPenanggungJawab($areaId)
    {
        $areaId = (int) $areaId;
        $area = \App\Models\Area::with('penanggungJawabs')->find($areaId);
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
        return view('walkandtalk.edit', compact('laporan', 'areas', 'problemCategories'));
    }

    public function update(Request $request, $id)
    {
        try {
            $messages = [
                'area_id.required' => 'Area harus dipilih.',
                'problem_category_id.required' => 'Kategori masalah harus dipilih.',
                'deskripsi_masalah.required' => 'Deskripsi masalah harus diisi.',
                'tenggat_waktu.required' => 'Tenggat waktu harus diisi.',
            ];

            $request->validate([
                'area_id' => 'required|exists:areas,id',
                'penanggung_jawab_id' => 'nullable|exists:penanggung_jawab,id',
                'problem_category_id' => 'required|exists:problem_categories,id',
                'deskripsi_masalah' => 'required|string',
                'tenggat_waktu' => 'required|date',
                'Foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ], $messages);

            $laporan = Laporan::findOrFail($id);

            $oldData = [
                'area_id' => $laporan->area_id,
                'penanggung_jawab_id' => $laporan->penanggung_jawab_id,
                'problem_category_id' => $laporan->problem_category_id,
                'deskripsi_masalah' => $laporan->deskripsi_masalah,
                'tenggat_waktu' => $laporan->tenggat_waktu,
            ];

            $oldArea = $laporan->area ? $laporan->area->name : '-';
            $oldPenanggungJawab = $laporan->penanggungJawab ? $laporan->penanggungJawab->name : '-';

            $existingPhotos = $request->input('existing_photos', []);
            $newlyUploadedPhotos = [];

            if ($request->hasFile('Foto')) {
                foreach ($request->file('Foto') as $foto) {
                    $fileName = time() . '_' . $foto->getClientOriginalName();
                    $foto->move(public_path('images/reports'), $fileName);
                    $newlyUploadedPhotos[] = $fileName;
                }
            }

            $allPhotos = array_merge($existingPhotos, $newlyUploadedPhotos);

            $oldPhotos = $laporan->Foto ?: [];
            $photosToDelete = array_diff($oldPhotos, $existingPhotos);
            foreach ($photosToDelete as $photo) {
                // Try reports folder first, then fallback to legacy images folder
                $filePath = public_path('images/reports/' . $photo);
                if (!file_exists($filePath)) {
                    $filePath = public_path('images/' . $photo);
                }
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $laporan->update([
                'area_id' => $request->area_id,
                'penanggung_jawab_id' => $request->penanggung_jawab_id,
                'problem_category_id' => $request->problem_category_id,
                'deskripsi_masalah' => $request->deskripsi_masalah,
                'tenggat_waktu' => $request->tenggat_waktu,
                'Foto' => count($allPhotos) > 0 ? $allPhotos : null,
            ]);

            $perubahan = $this->detectChanges($oldData, [
                'area_id' => $request->area_id,
                'penanggung_jawab_id' => $request->penanggung_jawab_id,
                'problem_category_id' => $request->problem_category_id,
                'deskripsi_masalah' => $request->deskripsi_masalah,
                'tenggat_waktu' => $request->tenggat_waktu,
            ], $oldArea, $oldPenanggungJawab);

            $laporan = Laporan::with(['area', 'penanggungJawab'])->find($id);
            if (!empty($perubahan)) {
                // $this->sendEditNotifications($laporan, $perubahan); // Disabled email notifications
            }

            $returnUrl = $request->input('return_url', route('laporan.index'));
            
            // Prevent redirect to datatables AJAX endpoints
            if (str_contains($returnUrl, '/datatables')) {
                $returnUrl = route('laporan.index');
            }
            
            return redirect($returnUrl)->with('success', 'Report updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error updating report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $returnUrl = $request->input('return_url', route('laporan.index'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update report: ' . $e->getMessage());
        }
    }

    public function tindakan($id)
    {
        $laporan = Laporan::with(['area', 'area.penanggungJawabs', 'penanggungJawab', 'problemCategory', 'penyelesaian'])->findOrFail($id);
        return view('walkandtalk.tindakan', compact('laporan'));
    }

    public function storeTindakan(Request $request, $id)
    {
        $rules = [ 'status' => 'required|string|in:In Progress,Selesai' ];
        $messages = [ 'status.required' => 'Status harus dipilih.', 'status.in' => 'Status harus salah satu dari: In Progress, Selesai.' ];

        if ($request->status === 'Selesai') {
            $rules['Tanggal'] = 'required|date';
            $rules['deskripsi_penyelesaian'] = 'required|string';
            $rules['Foto'] = 'nullable|array';
            $rules['Foto.*'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048';
        }

        $request->validate($rules, $messages);

        $laporan = Laporan::findOrFail($id);

        if ($request->status === 'Selesai') {
            $fotoFileNames = [];
            if ($request->hasFile('Foto')) {
                foreach ($request->file('Foto') as $file) {
                    $fileName = time() . '-' . $file->getClientOriginalName();
                    $file->move(public_path('images/completions'), $fileName);
                    $fotoFileNames[] = $fileName;
                }
            }

            \App\Models\Penyelesaian::updateOrCreate(
                ['laporan_id' => $laporan->id],
                [ 'Tanggal' => $request->Tanggal, 'Foto' => $fotoFileNames, 'deskripsi_penyelesaian' => $request->deskripsi_penyelesaian ]
            );
        }

        $laporan->update(['status' => $request->status]);

        // Redirect to history if status is completed, otherwise to dashboard
        if ($request->status === 'Selesai') {
            return redirect()->route('sejarah')->with('success', 'Report completed successfully and moved to history.');
        }

        return redirect()->route('dashboard')->with('success', 'Report status updated successfully.');
    }

    public function dashboardDatatables(Request $request)
    {
        $query = Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', '!=', 'Selesai');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('Tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('Tanggal', '<=', $request->end_date);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('category_id')) {
            $query->where('problem_category_id', $request->category_id);
        }

        $query->orderBy('Tanggal', 'desc');

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
            $laporan = Laporan::find($id);
            if (!$laporan) {
                return response()->json(['success' => true, 'message' => 'Report already removed.']);
            }

            // Delete associated completion photos if any
            $penyelesaian = \App\Models\Penyelesaian::where('laporan_id', $laporan->id)->first();
            if ($penyelesaian && !empty($penyelesaian->Foto) && is_array($penyelesaian->Foto)) {
            foreach ($penyelesaian->Foto as $foto) {
                $path = public_path('images/completions/' . $foto);
                    if (file_exists($path)) { @unlink($path); }
                }
                $penyelesaian->delete();
            }

            // Delete report photos
            if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                foreach ($laporan->Foto as $foto) {
                    $path = public_path('images/reports/' . $foto);
                    if (file_exists($path)) { @unlink($path); }
                }
            }

            $laporan->delete();

            return response()->json(['success' => true, 'message' => 'Report deleted successfully.']);
        } catch (\Exception $e) {
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
            'deskripsi_penyelesaian' => $laporan->penyelesaian->deskripsi_penyelesaian
        ]);
    }
}



