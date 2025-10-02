<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\laporan;
use App\Models\Penyelesaian;
use App\Models\DepartemenSupervisor; // Tetap pertahankan untuk backward compatibility
use App\Models\Area; // Model baru
use App\Models\PenanggungJawab; // Model baru
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Mail\LaporanDitugaskanSupervisor;
use App\Mail\LaporanDieditSupervisor;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class laporanController extends Controller
{
    public function index()
    {
        // Get all areas for filter
        $areas = Area::all();

        return view('walkandtalk.sejarah', compact('areas'));
    }

    public function create()
    {
        // Get all areas and their penanggung_jawab
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

        // Proses upload foto
        $fotoFileNames = [];
        if ($request->hasFile('Foto')) {
            foreach ($request->file('Foto') as $foto) {
                $fileName = time() . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('images'), $fileName);
                $fotoFileNames[] = $fileName;
            }
        }

        // Buat laporan baru
        $laporan = laporan::create([
            'area_id' => $request->area_id,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'problem_category_id' => $request->problem_category_id,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => 'Ditugaskan',
            'Foto' => count($fotoFileNames) > 0 ? $fotoFileNames : null,
        ]);

        // Kirim email ke penanggung jawab
        $this->sendSupervisorNotifications($laporan);

        // Redirect dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Report created successfully.');
    }

    public function edit($id)
    {
        $laporan = laporan::with(['area', 'penanggungJawab', 'problemCategory'])->findOrFail($id);
        $areas = Area::with('penanggungJawabs')->get();
        $problemCategories = \App\Models\ProblemCategory::active()->ordered()->get();
        return view('walkandtalk.edit', compact('laporan', 'areas', 'problemCategories'));
    }

    public function update(Request $request, $id)
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

        // Cari laporan berdasarkan ID
        $laporan = laporan::findOrFail($id);

        // Simpan data awal sebelum perubahan
        $oldData = [
            'area_id' => $laporan->area_id,
            'penanggung_jawab_id' => $laporan->penanggung_jawab_id,
            'problem_category_id' => $laporan->problem_category_id,
            'deskripsi_masalah' => $laporan->deskripsi_masalah,
            'tenggat_waktu' => $laporan->tenggat_waktu,
        ];

        // Simpan nama area lama untuk perbandingan
        $oldArea = $laporan->area ? $laporan->area->name : '-';
        $oldPenanggungJawab = $laporan->penanggungJawab ? $laporan->penanggungJawab->name : '-';

        // Proses foto
        $existingPhotos = $request->input('existing_photos', []);
        $newlyUploadedPhotos = [];

        if ($request->hasFile('Foto')) {
            foreach ($request->file('Foto') as $foto) {
                $fileName = time() . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('images'), $fileName);
                $newlyUploadedPhotos[] = $fileName;
            }
        }

        $allPhotos = array_merge($existingPhotos, $newlyUploadedPhotos);

        // Hapus file foto lama yang tidak ada di `existing_photos`
        $oldPhotos = $laporan->Foto ?: [];
        $photosToDelete = array_diff($oldPhotos, $existingPhotos);
        foreach ($photosToDelete as $photo) {
            $filePath = public_path('images/' . $photo);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Perbarui data di database
        $laporan->update([
            'area_id' => $request->area_id,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'problem_category_id' => $request->problem_category_id,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'Foto' => count($allPhotos) > 0 ? $allPhotos : null,
        ]);

        // Lacak perubahan yang terjadi
        $perubahan = $this->detectChanges($oldData, [
            'area_id' => $request->area_id,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'problem_category_id' => $request->problem_category_id,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
        ], $oldArea, $oldPenanggungJawab);

        // Reload laporan untuk mendapatkan data relasi terbaru
        $laporan = laporan::with(['area', 'penanggungJawab'])->find($id);

        // Kirim notifikasi perubahan jika ada perubahan
        if (!empty($perubahan)) {
            $this->sendEditNotifications($laporan, $perubahan);
        }


        // Redirect dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Report updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Cari laporan yang akan dihapus
            $laporan = laporan::findOrFail($id);

            // Periksa apakah laporan memiliki penyelesaian dan hapus jika ada
            if ($laporan->penyelesaian) {
                // Hapus foto penyelesaian jika ada
                if (!empty($laporan->penyelesaian->Foto) && is_array($laporan->penyelesaian->Foto)) {
                    foreach ($laporan->penyelesaian->Foto as $foto) {
                        $path = public_path('images/' . $foto);
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                }

                // Hapus record penyelesaian
                $laporan->penyelesaian->delete();
            }

            // Hapus foto laporan jika ada
            if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                foreach ($laporan->Foto as $foto) {
                    $path = public_path('images/' . $foto);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            // Hapus laporan
            $laporan->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting laporan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard()
    {
        // Hitung total laporan
        $totalLaporan = laporan::count();
        $laporanDitugaskan = laporan::where('status', 'Ditugaskan')->count();
        $laporanSelesai = laporan::where('status', 'Selesai')->count();

        // Get all areas for filter
        $areas = Area::all();
        
        // Data untuk grafik laporan per bulan (12 bulan terakhir)
        $laporanPerBulan = laporan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(11))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Data untuk grafik area yang melapor per bulan
        $areaPerBulan = laporan::join('areas', 'laporan.area_id', '=', 'areas.id')
            ->selectRaw('areas.name as area_name, MONTH(laporan.created_at) as bulan, COUNT(*) as total')
            ->where('laporan.created_at', '>=', now()->subMonths(11))
            ->groupBy('areas.name', 'bulan')
            ->orderBy('bulan')
            ->get();

        // Data untuk grafik pie category per bulan (bulan ini)
        $categoryPerBulan = laporan::with('problemCategory')
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

        // Jika tidak ada data bulan ini, ambil data dari 3 bulan terakhir
        if ($categoryPerBulan->isEmpty()) {
            $categoryPerBulan = laporan::with('problemCategory')
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
            'laporanDitugaskan',
            'laporanSelesai',
            'areas',
            'laporanPerBulan',
            'areaPerBulan',
            'categoryPerBulan'
        ));
    }

    public function tindakan($id)
    {
        $laporan = laporan::with(['area', 'area.penanggungJawabs', 'penanggungJawab', 'problemCategory', 'penyelesaian'])->findOrFail($id);
        return view('walkandtalk.tindakan', compact('laporan'));
    }

    public function storeTindakan(Request $request, $id)
    {
        $rules = [
            'status' => 'required|string|in:Ditugaskan,Selesai'
        ];

        $messages = [
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status harus salah satu dari: Ditugaskan, Selesai.'
        ];

        if ($request->status === 'Selesai') {
            $rules['Tanggal'] = 'required|date';
            $rules['deskripsi_penyelesaian'] = 'required|string';
            $rules['Foto'] = 'nullable|array';
            $rules['Foto.*'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048';

            $messages['Tanggal.required'] = 'Tanggal penyelesaian harus diisi.';
            $messages['deskripsi_penyelesaian.required' ]= 'Deskripsi penyelesaian harus diisi.';
        }

        $request->validate($rules, $messages);

        $laporan = laporan::findOrFail($id);

        if ($request->status === 'Selesai') {
            $fotoFileNames = [];
            if ($request->hasFile('Foto')) {
                foreach ($request->file('Foto') as $file) {
                    $fileName = time() . '-' . $file->getClientOriginalName();
                    $file->move(public_path('images'), $fileName);
                    $fotoFileNames[] = $fileName;
                }
            }

            // Buat atau perbarui record penyelesaian
            $penyelesaian = Penyelesaian::updateOrCreate(
                ['laporan_id' => $laporan->id],
                [
                    'Tanggal' => $request->Tanggal,
                    'Foto' => $fotoFileNames,
                    'deskripsi_penyelesaian' => $request->deskripsi_penyelesaian
                ]
            );
        }

        $laporan->update(['status' => $request->status]);

        return redirect()->route('dashboard')->with('success', 'Report status updated successfully.');
    }

    public function dashboardDatatables(Request $request)
    {
        // Inisialisasi query dengan filter default
        $query = laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', '!=', 'Selesai'); // Semua kecuali status Selesai

        // Terapkan filter tambahan dari request
        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('DT_RowIndex', function ($laporan) {
                return '<div class="text-center fw-bold">' . $laporan->DT_RowIndex . '</div>';
            })
            ->editColumn('Tanggal', function ($laporan) {
                return Carbon::parse($laporan->created_at)->format('l, j-n-Y');
            })
            ->addColumn('foto', function ($laporan) {
                // Kode yang sudah ada untuk menampilkan foto
                if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                    $foto = $laporan->Foto[0];
                    $fotoPath = asset('images/' . $foto);
                    $photoUrls = [];
                    foreach ($laporan->Foto as $foto) {
                        $photoUrls[] = asset('images/' . $foto);
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
                        // Gunakan station jika tersedia, jika kosong fallback ke nama PIC
                        $stationOrPic = trim((string)($laporan->penanggungJawab->station ?? ''));
                        if ($stationOrPic === '') {
                            $stationOrPic = $laporan->penanggungJawab->name ?? '';
                        }

                        if ($stationOrPic !== '') {
                            $html = '<span class="fw-bold">' . $areaName . ' (' . e($stationOrPic) . ')</span>';
                        } else {
                            $html = '<span class="fw-bold">' . $areaName . '</span>';
                        }
                    } else {
                        // Fallback: jika tidak ada penanggung_jawab tersimpan, coba ambil station pertama dari area terkait
                        $firstStation = optional($laporan->area->penanggungJawabs()->orderBy('id')->first())->station;
                        if ($firstStation) {
                            $html = '<span class="fw-bold">' . $areaName . ' (' . e($firstStation) . ')</span>';
                        } else {
                            $html = '<span class="fw-bold">' . $areaName . '</span>';
                        }
                    }
                }
                return $html;
            })
            ->addColumn('problem_category', function ($laporan) {
                if ($laporan->problemCategory) {
                    $color = $laporan->problemCategory->color;
                    $name = $laporan->problemCategory->name;
                    return '<span class="badge problem-category-badge" style="background-color: ' . $color . '; color: white; max-width: 150px; white-space: normal; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; display: inline-block; line-height: 1.3; height: auto;">' . e($name) . '</span>';
                }
                return '<span class="text-muted">No Category</span>';
            })
            ->addColumn('deskripsi_masalah', function ($laporan) {
                $description = $laporan->deskripsi_masalah;
                $maxLength = 80; // tampilkan ringkas di table, detail via modal row
                $shortDescription = strlen($description) > $maxLength
                    ? substr($description, 0, $maxLength) . '...'
                    : $description;
                return '<div class="description-container" title="' . e($laporan->deskripsi_masalah) . '">' . e($shortDescription) . '</div>';
            })
            ->addColumn('deskripsi_masalah_full', function ($laporan) {
                return $laporan->deskripsi_masalah ?? '';
            })
            ->addColumn('tenggat_waktu', function ($laporan) {
                return Carbon::parse($laporan->tenggat_waktu)->format('l, j-n-Y');
            })
            ->addColumn('status', function ($laporan) {
                if ($laporan->status == 'Ditugaskan') {
                    return '<span class="status-badge status-assigned"><i class="fas fa-exclamation-circle"></i> Assigned</span>';
                } else if ($laporan->status == 'Selesai') {
                    return '<span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span>';
                }
                return '<span class="badge bg-secondary">' . $laporan->status . '</span>';
            })
            ->addColumn('penyelesaian', function ($laporan) {
                if ($laporan->penyelesaian) {
                    return '<button class="btn btn-sm btn-info lihat-penyelesaian-btn" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian" data-id="' . $laporan->id . '"><i class="fas fa-eye"></i> View</button>';
                }
                return '<a href="' . route('laporan.tindakan', $laporan->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-tasks"></i> Action</a>';
            })
            ->addColumn('aksi', function ($laporan) {
                // Tombol Edit dan Delete terpisah
                $editUrl = route('index.edit', $laporan->id);
                $deleteUrl = route('laporan.destroy', $laporan->id);
                
                $actionHtml = '
                <div class="d-flex gap-1">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-btn" 
                            data-id="' . $laporan->id . '" 
                            data-delete-url="' . $deleteUrl . '" 
                            data-return-url="' . url()->current() . '"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

                return $actionHtml;
            })
            ->rawColumns(['foto', 'departemen', 'problem_category', 'deskripsi_masalah', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function sejarahDatatables(Request $request)
    {
        // Inisialisasi query dengan filter default status 'Selesai'
        $query = laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', 'Selesai'); // Filter default untuk halaman Sejarah

        // Terapkan filter tambahan dari request
        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('DT_RowIndex', function ($laporan) {
                return '<div class="text-center fw-bold">' . $laporan->DT_RowIndex . '</div>';
            })
            ->editColumn('Tanggal', function ($laporan) {
                return Carbon::parse($laporan->created_at)->format('l, j-n-Y');
            })
            ->addColumn('foto', function ($laporan) {
                // Kode yang sudah ada untuk menampilkan foto
                if (!empty($laporan->Foto) && is_array($laporan->Foto)) {
                    $foto = $laporan->Foto[0];
                    $fotoPath = asset('images/' . $foto);
                    $photoUrls = [];
                    foreach ($laporan->Foto as $foto) {
                        $photoUrls[] = asset('images/' . $foto);
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
                        // Gunakan station jika tersedia, jika kosong fallback ke nama PIC
                        $stationOrPic = trim((string)($laporan->penanggungJawab->station ?? ''));
                        if ($stationOrPic === '') {
                            $stationOrPic = $laporan->penanggungJawab->name ?? '';
                        }

                        if ($stationOrPic !== '') {
                            $html = '<span class="fw-bold">' . $areaName . ' (' . e($stationOrPic) . ')</span>';
                        } else {
                            $html = '<span class="fw-bold">' . $areaName . '</span>';
                        }
                    } else {
                        // Fallback: jika tidak ada penanggung_jawab tersimpan, coba ambil station pertama dari area terkait
                        $firstStation = optional($laporan->area->penanggungJawabs()->orderBy('id')->first())->station;
                        if ($firstStation) {
                            $html = '<span class="fw-bold">' . $areaName . ' (' . e($firstStation) . ')</span>';
                        } else {
                            $html = '<span class="fw-bold">' . $areaName . '</span>';
                        }
                    }
                }
                return $html;
            })
            ->addColumn('problem_category', function ($laporan) {
                if ($laporan->problemCategory) {
                    $color = $laporan->problemCategory->color;
                    $name = $laporan->problemCategory->name;
                    return '<span class="badge problem-category-badge" style="background-color: ' . $color . '; color: white; max-width: 150px; white-space: normal; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; display: inline-block; line-height: 1.3; height: auto;">' . e($name) . '</span>';
                }
                return '<span class="text-muted">No Category</span>';
            })
            ->addColumn('deskripsi_masalah', function ($laporan) {
                $description = $laporan->deskripsi_masalah;
                $maxLength = 80;
                $shortDescription = strlen($description) > $maxLength
                    ? substr($description, 0, $maxLength) . '...'
                    : $description;
                return '<div class="description-container" title="' . e($laporan->deskripsi_masalah) . '">' . e($shortDescription) . '</div>';
            })
            ->addColumn('deskripsi_masalah_full', function ($laporan) {
                return $laporan->deskripsi_masalah ?? '';
            })
            ->addColumn('tenggat_waktu', function ($laporan) {
                return Carbon::parse($laporan->tenggat_waktu)->format('l, j-n-Y');
            })
            ->addColumn('status', function ($laporan) {
                if ($laporan->status == 'Ditugaskan') {
                    return '<span class="badge bg-warning">Assigned</span>';
                } else if ($laporan->status == 'Selesai') {
                    return '<span class="badge bg-success">Completed</span>';
                }
                return '<span class="badge bg-secondary">' . $laporan->status . '</span>';
            })
            ->addColumn('penyelesaian', function ($laporan) {
                if ($laporan->penyelesaian) {
                    return '<button class="btn btn-sm btn-info lihat-penyelesaian-btn" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian" data-id="' . $laporan->id . '"><i class="fas fa-eye"></i> View</button>';
                }
                return '<a href="' . route('laporan.tindakan', $laporan->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-tasks"></i> Action</a>';
            })
            ->addColumn('aksi', function ($laporan) {
                // Tombol Edit dan Delete terpisah
                $editUrl = route('index.edit', $laporan->id);
                $deleteUrl = route('laporan.destroy', $laporan->id);
                
                $actionHtml = '
                <div class="d-flex gap-1">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-btn" 
                            data-id="' . $laporan->id . '" 
                            data-delete-url="' . $deleteUrl . '" 
                            data-return-url="' . url()->current() . '"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

                return $actionHtml;
            })
            ->rawColumns(['foto', 'departemen', 'problem_category', 'deskripsi_masalah', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function getSupervisor($id)
    {
        // Cast $id ke integer untuk memastikan perbandingan numerik yang benar
        $id = (int)$id;

        // Check if this is an area or penanggung_jawab
        if ($id <= 3) { // IDs 1-3 are areas
            $area = Area::with('penanggungJawabs')->find($id);

            if (!$area) {
                return response()->json([
                    'error' => 'Area tidak ditemukan'
                ], 404);
            }

            // Return all penanggung jawab names for this area
            $supervisorNames = $area->penanggungJawabs->pluck('name')->toArray();
            return response()->json([
                'group_members' => $supervisorNames
            ]);
        } else {
            // This is a specific penanggung_jawab
            $penanggungJawab = PenanggungJawab::find($id);

            if (!$penanggungJawab) {
                return response()->json([
                    'error' => 'Penanggung jawab tidak ditemukan'
                ], 404);
            }

            // Return only this specific penanggung_jawab name
            return response()->json([
                'group_members' => [$penanggungJawab->name]
            ]);
        }
    }

    public function getPenyelesaian($id)
    {
        $laporan = laporan::with('penyelesaian')->find($id);

        if (!$laporan || !$laporan->penyelesaian) {
            return response()->json(['success' => false]);
        }

        $penyelesaian = $laporan->penyelesaian;
        $fotoUrls = [];

        if (!empty($penyelesaian->Foto) && is_array($penyelesaian->Foto)) {
            foreach ($penyelesaian->Foto as $foto) {
                $fotoUrls[] = asset('images/' . $foto);
            }
        }

        return response()->json([
            'success' => true,
            'Tanggal' => Carbon::parse($penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY'),
            'Foto' => $fotoUrls,
            'deskripsi_penyelesaian' => $penyelesaian->deskripsi_penyelesaian
        ]);
    }

    // Download sejarah laporan
    public function downloadSejarah(Request $request)
    {
        try {
            // Inisialisasi query
            $query = laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
                ->where('status', 'Selesai');

            // Terapkan filter yang sama dengan tampilan sejarah
            $query = $this->applyFilters($request, $query);

            // Get data
            $laporan = $query->get();

            // Format periode untuk judul
            $periode = 'Semua Waktu';
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->format('j-n-Y');
                $endDate = Carbon::parse($request->end_date)->format('j-n-Y');
                $periode = $startDate . ' - ' . $endDate;
            }

            // Generate PDF
            $pdf = PDF::loadView('walkandtalk.pdf.laporan-selesai', compact('laporan', 'periode'));
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download('Laporan-Safety-Walk-and-Talk-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            // Log error
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download report: ' . $e->getMessage());
        }
    }

    // Helper function untuk menerapkan filter pada query
    private function applyFilters(Request $request, $query)
    {
        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter penanggung jawab / station
        if ($request->filled('penanggung_jawab_id')) {
            $query->where('penanggung_jawab_id', $request->penanggung_jawab_id);
        }

        // Filter problem category
        if ($request->filled('problem_category_id')) {
            $query->where('problem_category_id', $request->problem_category_id);
        }

        // Filter status (hanya jika secara eksplisit diminta dari form filter)
        if ($request->filled('status')) {
            // Override filter default jika pengguna secara eksplisit memilih filter status
            $query->where('status', $request->status);
        }

        // Filter tenggat waktu berdasarkan bulan
        if ($request->filled('tenggat_bulan')) {
            $month = $request->tenggat_bulan;
            $query->whereMonth('tenggat_waktu', $month);
        }



        return $query;
    }

    /**
     * Helper method to send notifications to penanggung jawab
     */
    private function sendSupervisorNotifications($laporan)
    {
        try {
            $laporan = laporan::with(['area', 'penanggungJawab'])->find($laporan->id);

            // Tentukan penerima email berdasarkan penanggung_jawab atau area
            $recipients = [];

            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                // Jika ada penanggung jawab spesifik
                $recipients[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                // Jika hanya area yang dipilih, kirim ke semua penanggung jawab area tersebut
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $recipients[] = $pj->email;
                    }
                }
            }

            // Hapus duplikat email
            $recipients = array_unique($recipients);

            // Kirim email ke semua penerima
            foreach ($recipients as $email) {
                Mail::to($email)->send(new LaporanDitugaskanSupervisor($laporan));
            }
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan aplikasi
            Log::error("Error sending notification email: " . $e->getMessage());
        }
    }

    /**
     * Deteksi perubahan yang terjadi pada laporan
     *
     * @param array $oldData Data laporan lama
     * @param array $newData Data laporan baru
     * @param string $oldArea Nama area lama
     * @param string $oldPenanggungJawab Nama penanggung jawab lama
     * @return array
     */
    private function detectChanges(array $oldData, array $newData, string $oldArea, string $oldPenanggungJawab): array
    {
        $perubahan = [];

        // Buat nama-nama field yang lebih user-friendly
        $fieldNames = [
            'problem_category_id' => 'Problem Category',
            'deskripsi_masalah' => 'Deskripsi Masalah',
            'tenggat_waktu' => 'Tenggat Waktu',
        ];

        // Periksa perubahan area
        if ($oldData['area_id'] != $newData['area_id']) {
            $newArea = Area::find($newData['area_id'])->name ?? '-';
            $perubahan['Area'] = [
                'old' => $oldArea,
                'new' => $newArea
            ];
        }

        // Periksa perubahan penanggung jawab
        if ($oldData['penanggung_jawab_id'] != $newData['penanggung_jawab_id']) {
            $newPJ = PenanggungJawab::find($newData['penanggung_jawab_id'])->name ?? '-';
            $perubahan['Penanggung Jawab'] = [
                'old' => $oldPenanggungJawab,
                'new' => $newPJ
            ];
        }

        // Periksa perubahan pada field lainnya
        foreach (['problem_category_id', 'deskripsi_masalah'] as $field) {
            if ($oldData[$field] != $newData[$field]) {
                $perubahan[$fieldNames[$field]] = [
                    'old' => $oldData[$field],
                    'new' => $newData[$field]
                ];
            }
        }

        // Periksa perubahan tanggal (format agar lebih mudah dibaca)
        if ($oldData['tenggat_waktu'] != $newData['tenggat_waktu']) {
            $perubahan[$fieldNames['tenggat_waktu']] = [
                'old' => Carbon::parse($oldData['tenggat_waktu'])->format('d/m/Y'),
                'new' => Carbon::parse($newData['tenggat_waktu'])->format('d/m/Y')
            ];
        }

        return $perubahan;
    }

    /**
     * Helper method to send notifications about edited reports
     *
     * @param laporan $laporan
     * @param array $perubahan
     */
    private function sendEditNotifications($laporan, array $perubahan)
    {
        try {
            // Tentukan penerima email berdasarkan penanggung_jawab atau area
            $recipients = [];

            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                // Jika ada penanggung jawab spesifik
                $recipients[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                // Jika hanya area yang dipilih, kirim ke semua penanggung jawab area tersebut
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $recipients[] = $pj->email;
                    }
                }
            }

            // Hapus duplikat email
            $recipients = array_unique($recipients);

            // Kirim email ke semua penerima
            foreach ($recipients as $email) {
                Mail::to($email)->send(new LaporanDieditSupervisor($laporan, $perubahan));
            }
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan aplikasi
            Log::error("Error sending edit notification email: " . $e->getMessage());
        }
    }

    public function getPenanggungJawab($areaId)
    {
        // Cast to integer untuk memastikan perbandingan numerik yang benar
        $areaId = (int)$areaId;

        // Cek jika areaId lebih besar dari 3, ini adalah ID penanggung jawab
        if ($areaId > 3) {
            $pj = PenanggungJawab::find($areaId);
            if (!$pj) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penanggung jawab tidak ditemukan'
                ], 404);
            }

            // Untuk Station, hanya return penanggung jawab spesifik
            return response()->json([
                'success' => true,
                'supervisors' => [$pj->name]
            ]);
        }

        // Jika areaId 1-3, ini adalah ID area
        $area = Area::find($areaId);

        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area tidak ditemukan'
            ], 404);
        }

        // Untuk Area, return semua penanggung jawab di area tersebut
        $penanggungJawabs = $area->penanggungJawabs;
        $supervisors = $penanggungJawabs->pluck('name')->toArray();

        return response()->json([
            'success' => true,
            'supervisors' => $supervisors
        ]);
    }
}