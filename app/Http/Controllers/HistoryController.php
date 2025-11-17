<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesLaporan;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Area;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    use HandlesLaporan;

    public function index()
    {
        $areas = Area::all();

        // \SharedManager::saveLog('log_swt', "Accessed the [Report History] page swt.");

        return view('walkandtalk.sejarah', compact('areas'));
    }

    public function sejarahDatatables(Request $request)
    {
        $query = Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', 'Completed');

        // Apply date filters
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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('problem_category_id', $request->category_id);
        }

        $query = $this->applyFilters($request, $query);

        // If a date filter is active, sort chronologically (oldest->newest),
        // else keep default newest-first to match previous behavior
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query = $query->orderBy('created_at', 'asc');
        } else {
            $query = $query->orderBy('Tanggal', 'desc');
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
            ->editColumn('DT_RowIndex', function ($laporan) { return '<div class="text-center fw-bold">' . $laporan->DT_RowIndex . '</div>'; })
            ->editColumn('Tanggal', function ($laporan) { return Carbon::parse($laporan->created_at)->format('l, j-n-Y'); })
            ->addColumn('foto', function ($laporan) use ($resolveReportUrl) {
                if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                    $foto = $laporan->Foto[0]; $fotoPath = $resolveReportUrl($foto); $photoUrls = [];
                    foreach ($laporan->Foto as $f) { $photoUrls[] = $resolveReportUrl($f); }
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
                        if ($stationOrPic === '') { $stationOrPic = $laporan->penanggungJawab->name ?? ''; }
                        $html = $stationOrPic !== '' ? '<span class="fw-bold">' . $areaName . ' (' . e($stationOrPic) . ')</span>' : '<span class="fw-bold">' . $areaName . '</span>';
                    } else {
                        // Jika tidak ada PIC spesifik, tampilkan hanya area name
                        $html = '<span class="fw-bold">' . $areaName . '</span>';
                    }
                }
                return $html;
            })
            ->addColumn('problem_category', function ($laporan) {
                if ($laporan->problemCategory) { $color = $laporan->problemCategory->color; $name = $laporan->problemCategory->name; return '<span class="badge problem-category-badge" style="background-color: ' . $color . '; color: white; max-width: 150px; white-space: normal; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; display: inline-block; line-height: 1.3; height: auto;">' . e($name) . '</span>'; }
                return '<span class="text-muted">No Category</span>'; })
            ->addColumn('deskripsi_masalah', function ($laporan) { $d=$laporan->deskripsi_masalah; $m=80; $s=strlen($d)>$m?substr($d,0,$m).'...':$d; return '<div class="description-container" title="' . e($laporan->deskripsi_masalah) . '">' . e($s) . '</div>'; })
            ->addColumn('deskripsi_masalah_full', function ($laporan) { return $laporan->deskripsi_masalah ?? ''; })
            ->addColumn('person_in_charge', function ($laporan) {
                if ($laporan->penanggungJawab) {
                    return $laporan->penanggungJawab->name ?? '';
                }
                return '';
            })
            ->addColumn('tenggat_waktu', function ($laporan) { return Carbon::parse($laporan->tenggat_waktu)->format('l, j-n-Y'); })
            ->addColumn('status', function ($laporan) {
                if ($laporan->status == 'Completed') {
                    return '<span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span>';
                } elseif ($laporan->status == 'Assigned') {
                    return '<span class="status-badge status-assigned"><i class="fas fa-circle"></i> Assigned</span>';
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
                $encryptedId = encrypt($laporan->id);
                $returnUrl = route('sejarah.index');
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

    public function downloadSejarah(Request $request)
    {
        try {
            $query = Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
                ->where('status', 'Completed');
            $query = $this->applyFilters($request, $query);
            // Order like the table: if a date filter is active, oldest -> newest; else newest-first
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $query = $query->orderBy('created_at', 'asc');
            } else {
                $query = $query->orderBy('created_at', 'desc');
            }
            $laporan = $query->get();

            $periode = 'All Time';
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->format('n/j/Y');
                $endDate = Carbon::parse($request->end_date)->format('n/j/Y');
                $periode = $startDate . ' - ' . $endDate;
            }

            $pdf = Pdf::loadView('walkandtalk.pdf.laporan-selesai', compact('laporan', 'periode'));
            $pdf->setPaper('a4', 'portrait');

            // \SharedManager::saveLog('log_swt', "Downloaded [Report History] PDF for period: {$periode} swt.");

            return $pdf->download('Laporan-Safety-Walk-and-Talk-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download report: ' . $e->getMessage());
        }
    }
}



