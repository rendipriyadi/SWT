<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\PenanggungJawab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index()
    {
        // Ambil data area dengan penanggung jawab
        $areas = Area::with('penanggungJawabs')->get();
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Area/Station] page swt.");
        
        return view('master-data.area.index', compact('areas'));
    }

    public function create()
    {
        \SharedManager::saveLog('log_sitime', "Accessed the [Create Area/Station] page swt.");
        
        return view('master-data.area.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:areas,name',
            'stations' => 'required|array|min:1',
            'stations.*' => 'required|string|max:255',
            'penanggung_jawab' => 'required|array|min:1',
            'penanggung_jawab.*' => 'required|string|max:255',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255'
        ], [
            'name.required' => 'Nama area harus diisi',
            'name.unique' => 'Nama area sudah ada',
            'stations.required' => 'Minimal satu station harus diisi',
            'stations.*.required' => 'Nama station harus diisi',
            'penanggung_jawab.required' => 'Minimal satu penanggung jawab harus diisi',
            'penanggung_jawab.*.required' => 'Nama penanggung jawab harus diisi'
        ]);

        try {
            DB::beginTransaction();

            // Buat area
            $area = Area::create([
                'name' => $request->name
            ]);

            // Buat penanggung jawab untuk setiap station
            foreach ($request->stations as $index => $station) {
                PenanggungJawab::create([
                    'area_id' => $area->id,
                    'station' => $station,
                    'name' => $request->penanggung_jawab[$index],
                    'email' => $request->emails[$index] ?? null,
                ]);
            }

            DB::commit();

            \SharedManager::saveLog('log_sitime', "Created new area/station: {$request->name} swt.");
            
            return redirect()->route('master-data.area.index')
                ->with('success', 'Area created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show(Area $area)
    {
        $area->load('penanggungJawabs');
        return view('master-data.area.show', compact('area'));
    }

    public function edit(Area $area)
    {
        $area->load('penanggungJawabs');
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Edit Area/Station] page for ID: {$area->id} swt.");
        
        return view('master-data.area.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:areas,name,' . $area->id,
            'stations' => 'required|array|min:1',
            'stations.*' => 'required|string|max:255',
            'penanggung_jawab' => 'required|array|min:1',
            'penanggung_jawab.*' => 'required|string|max:255',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255'
        ], [
            'name.required' => 'Nama area harus diisi',
            'name.unique' => 'Nama area sudah ada',
            'stations.required' => 'Minimal satu station harus diisi',
            'stations.*.required' => 'Nama station harus diisi',
            'penanggung_jawab.required' => 'Minimal satu penanggung jawab harus diisi',
            'penanggung_jawab.*.required' => 'Nama penanggung jawab harus diisi'
        ]);

        try {
            DB::beginTransaction();

            // Update area
            $area->update([
                'name' => $request->name
            ]);

            // Hapus penanggung jawab lama
            $area->penanggungJawabs()->delete();

            // Buat penanggung jawab baru
            foreach ($request->stations as $index => $station) {
                PenanggungJawab::create([
                    'area_id' => $area->id,
                    'station' => $station,
                    'name' => $request->penanggung_jawab[$index],
                    'email' => $request->emails[$index] ?? null,
                ]);
            }

            DB::commit();

            \SharedManager::saveLog('log_sitime', "Updated area/station ID: {$area->id} swt.");
            
            return redirect()->route('master-data.area.index')
                ->with('success', 'Area updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Area $area)
    {
        try {
            // Cek apakah area digunakan di laporan
            $usedInReports = DB::table('laporan')->where('area_id', $area->id)->exists();
            
            if ($usedInReports) {
                return redirect()->back()
                    ->with('error', 'Area tidak dapat dihapus karena masih digunakan dalam laporan!');
            }

            // Hapus penanggung jawab terlebih dahulu
            $area->penanggungJawabs()->delete();
            
            // Hapus area
            $area->delete();

            \SharedManager::saveLog('log_sitime', "Deleted area/station ID: {$area->id} swt.");
            
            return redirect()->route('master-data.area.index')
                ->with('success', 'Area deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
