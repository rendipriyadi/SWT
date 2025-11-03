<?php

namespace App\Http\Controllers\Concerns;

use Carbon\Carbon;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\PenanggungJawab;
use App\Models\ProblemCategory;

trait HandlesLaporan
{
    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('penanggung_jawab_id')) {
            $query->where('penanggung_jawab_id', $request->penanggung_jawab_id);
        }

        if ($request->filled('problem_category_id')) {
            $query->where('problem_category_id', $request->problem_category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenggat_bulan')) {
            $month = $request->tenggat_bulan;
            $query->whereMonth('tenggat_waktu', $month);
        }

        return $query;
    }

    /**
     * Detect changes between old and new report data
     *
     * @param array $oldData
     * @param array $newData
     * @param string $oldArea
     * @param string $oldPenanggungJawab
     * @param string $oldStation
     * @return array
     */
    private function detectChanges($oldData, $newData, $oldArea, $oldPenanggungJawab, $oldStation = '-')
    {
        $perubahan = [];

        // Check area change
        if ($oldData['area_id'] != $newData['area_id']) {
            $newArea = Area::find($newData['area_id']);
            $perubahan[] = [
                'field' => 'Area',
                'old' => $oldArea,
                'new' => $newArea ? $newArea->name : '-'
            ];
        }

        // Check PIC change - handle both single PIC and multiple PICs
        if ($oldData['penanggung_jawab_id'] != ($newData['penanggung_jawab_id'] ?? null)) {
            $newPenanggungJawab = null;
            
            // Determine old PIC display value
            $oldPICDisplay = $oldPenanggungJawab;
            
            // If old PIC was null, show all PICs from old area
            if (empty($oldData['penanggung_jawab_id']) && !empty($oldData['area_id'])) {
                $oldAreaObj = Area::with('penanggungJawabs')->find($oldData['area_id']);
                if ($oldAreaObj && $oldAreaObj->penanggungJawabs->count() > 0) {
                    $oldPICNames = $oldAreaObj->penanggungJawabs->pluck('name')->toArray();
                    $oldPICDisplay = implode(', ', $oldPICNames);
                }
            }

            // If new data has specific PIC
            if (!empty($newData['penanggung_jawab_id'])) {
                $newPenanggungJawab = PenanggungJawab::find($newData['penanggung_jawab_id']);
                $newPICName = $newPenanggungJawab ? $newPenanggungJawab->name : '-';
                $newStation = $newPenanggungJawab && $newPenanggungJawab->station ? $newPenanggungJawab->station : '-';
            }
            // If no specific PIC, get all PICs from the area
            else if (!empty($newData['area_id'])) {
                $newArea = Area::with('penanggungJawabs')->find($newData['area_id']);
                if ($newArea && $newArea->penanggungJawabs->count() > 0) {
                    $picNames = $newArea->penanggungJawabs->pluck('name')->toArray();
                    $newPICName = implode(', ', $picNames);
                    $newStation = '-';
                } else {
                    $newPICName = '-';
                    $newStation = '-';
                }
            } else {
                $newPICName = '-';
                $newStation = '-';
            }

            $perubahan[] = [
                'field' => 'Person in Charge',
                'old' => $oldPICDisplay,
                'new' => $newPICName
            ];

            // Add station change if different (use parameter $oldStation)
            if ($oldStation !== $newStation) {
                $perubahan[] = [
                    'field' => 'Station',
                    'old' => $oldStation,
                    'new' => $newStation
                ];
            }
        }
        // Also check if area changed but PIC stayed null - this means multiple PICs might have changed
        else if ($oldData['penanggung_jawab_id'] === null &&
                 ($newData['penanggung_jawab_id'] ?? null) === null &&
                 $oldData['area_id'] != $newData['area_id']) {

            // Get old PICs
            $oldArea = Area::with('penanggungJawabs')->find($oldData['area_id']);
            $oldPICNames = '-';
            if ($oldArea && $oldArea->penanggungJawabs->count() > 0) {
                $oldPICNames = implode(', ', $oldArea->penanggungJawabs->pluck('name')->toArray());
            }

            // Get new PICs
            $newArea = Area::with('penanggungJawabs')->find($newData['area_id']);
            $newPICNames = '-';
            if ($newArea && $newArea->penanggungJawabs->count() > 0) {
                $newPICNames = implode(', ', $newArea->penanggungJawabs->pluck('name')->toArray());
            }

            // Only add if PICs actually changed
            if ($oldPICNames !== $newPICNames) {
                $perubahan[] = [
                    'field' => 'Person in Charge',
                    'old' => $oldPICNames,
                    'new' => $newPICNames
                ];
            }
        }

        // Check problem category change
        if ($oldData['problem_category_id'] != $newData['problem_category_id']) {
            $oldCategory = ProblemCategory::find($oldData['problem_category_id']);
            $newCategory = ProblemCategory::find($newData['problem_category_id']);
            $perubahan[] = [
                'field' => 'Problem Category',
                'old' => $oldCategory ? $oldCategory->name : '-',
                'new' => $newCategory ? $newCategory->name : '-'
            ];
        }

        // Check description change
        if ($oldData['deskripsi_masalah'] != $newData['deskripsi_masalah']) {
            $perubahan[] = [
                'field' => 'Description',
                'old' => $oldData['deskripsi_masalah'],
                'new' => $newData['deskripsi_masalah']
            ];
        }

        // Check deadline change - compare dates only, not time
        $oldDeadline = Carbon::parse($oldData['tenggat_waktu'])->format('Y-m-d');
        $newDeadline = Carbon::parse($newData['tenggat_waktu'])->format('Y-m-d');

        if ($oldDeadline != $newDeadline) {
            $perubahan[] = [
                'field' => 'Deadline',
                'old' => Carbon::parse($oldData['tenggat_waktu'])->format('d/m/Y'),
                'new' => Carbon::parse($newData['tenggat_waktu'])->format('d/m/Y')
            ];
        }

        return $perubahan;
    }

}
