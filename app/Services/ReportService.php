<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\Penyelesaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ReportService
 * 
 * Handles business logic for Walk and Talk reports
 */
class ReportService
{
    /**
     * Create a new report
     * 
     * @param array $data
     * @param array $photos
     * @return Laporan
     */
    public function createReport(array $data, array $photos = []): Laporan
    {
        $data['Foto'] = $photos;
        $data['status'] = 'Assigned';
        
        return Laporan::create($data);
    }

    /**
     * Update an existing report
     * 
     * @param Laporan $laporan
     * @param array $data
     * @param array $photos
     * @return Laporan
     */
    public function updateReport(Laporan $laporan, array $data, array $photos = []): Laporan
    {
        // Handle photos - data['Foto'] already contains merged photos from controller
        // No need to merge again here to avoid duplication
        
        $laporan->update($data);
        
        return $laporan->fresh();
    }

    /**
     * Update report status
     * 
     * @param Laporan $laporan
     * @param string $status
     * @return Laporan
     */
    public function updateStatus(Laporan $laporan, string $status): Laporan
    {
        $laporan->update(['status' => $status]);
        
        return $laporan->fresh();
    }

    /**
     * Complete a report (store completion data)
     * 
     * @param Laporan $laporan
     * @param array $completionData
     * @param array $photos
     * @return Penyelesaian
     */
    public function completeReport(Laporan $laporan, array $completionData, array $photos = []): Penyelesaian
    {
        $completionData['Foto'] = $photos;
        
        $penyelesaian = Penyelesaian::updateOrCreate(
            ['laporan_id' => $laporan->id],
            $completionData
        );

        // Update report status to completed
        $this->updateStatus($laporan, 'Completed');

        return $penyelesaian;
    }

    /**
     * Delete a report and its associated data
     * 
     * @param Laporan $laporan
     * @return bool
     */
    public function deleteReport(Laporan $laporan): bool
    {
        try {
            // Delete completion photos if exists
            if ($laporan->penyelesaian && !empty($laporan->penyelesaian->Foto)) {
                $this->deleteCompletionPhotos($laporan->penyelesaian->Foto);
                $laporan->penyelesaian->delete();
            }

            // Delete report photos
            if (!empty($laporan->Foto)) {
                $this->deleteReportPhotos($laporan->Foto);
            }

            return $laporan->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting report: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete report photos from storage
     * 
     * @param array $photos
     * @return void
     */
    private function deleteReportPhotos(array $photos): void
    {
        foreach ($photos as $photo) {
            $path = public_path('images/reports/' . $photo);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Delete completion photos from storage
     * 
     * @param array $photos
     * @return void
     */
    private function deleteCompletionPhotos(array $photos): void
    {
        foreach ($photos as $photo) {
            $path = public_path('images/completions/' . $photo);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Get dashboard statistics
     * 
     * @return array
     */
    public function getDashboardStats(): array
    {
        return [
            'total' => Laporan::count(),
            'in_progress' => Laporan::where('status', 'Assigned')->count(),
            'completed' => Laporan::where('status', 'Completed')->count(),
        ];
    }

    /**
     * Get reports per month (last 12 months)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getReportsPerMonth()
    {
        return Laporan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(11))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
    }

    /**
     * Get reports by area per month
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getReportsByAreaPerMonth()
    {
        return Laporan::join('areas', 'laporan.area_id', '=', 'areas.id')
            ->selectRaw('areas.name as area_name, MONTH(laporan.created_at) as bulan, COUNT(*) as total')
            ->where('laporan.created_at', '>=', now()->subMonths(11))
            ->groupBy('areas.name', 'bulan')
            ->orderBy('bulan')
            ->get();
    }

    /**
     * Get reports by category for current month
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getReportsByCategoryCurrentMonth()
    {
        $reports = Laporan::with('problemCategory')
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

        // Fallback to last 3 months if current month is empty
        if ($reports->isEmpty()) {
            $reports = Laporan::with('problemCategory')
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

        return $reports;
    }
}
