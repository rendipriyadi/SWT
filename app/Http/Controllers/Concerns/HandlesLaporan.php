<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Carbon\Carbon;

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
     * @return array
     */
    private function detectChanges($oldData, $newData, $oldArea, $oldPenanggungJawab)
    {
        $perubahan = [];

        // Check area change
        if ($oldData['area_id'] != $newData['area_id']) {
            $newArea = \App\Models\Area::find($newData['area_id']);
            $perubahan[] = [
                'field' => 'Area',
                'old' => $oldArea,
                'new' => $newArea ? $newArea->name : '-'
            ];
        }

        // Check PIC change
        if ($oldData['penanggung_jawab_id'] != ($newData['penanggung_jawab_id'] ?? null)) {
            $newPenanggungJawab = $newData['penanggung_jawab_id'] 
                ? \App\Models\PenanggungJawab::find($newData['penanggung_jawab_id']) 
                : null;
            $perubahan[] = [
                'field' => 'Person in Charge',
                'old' => $oldPenanggungJawab,
                'new' => $newPenanggungJawab ? $newPenanggungJawab->name : '-'
            ];
        }

        // Check problem category change
        if ($oldData['problem_category_id'] != $newData['problem_category_id']) {
            $oldCategory = \App\Models\ProblemCategory::find($oldData['problem_category_id']);
            $newCategory = \App\Models\ProblemCategory::find($newData['problem_category_id']);
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

        // Check deadline change
        if ($oldData['tenggat_waktu'] != $newData['tenggat_waktu']) {
            $perubahan[] = [
                'field' => 'Deadline',
                'old' => \Carbon\Carbon::parse($oldData['tenggat_waktu'])->format('d/m/Y'),
                'new' => \Carbon\Carbon::parse($newData['tenggat_waktu'])->format('d/m/Y')
            ];
        }

        return $perubahan;
    }

}
