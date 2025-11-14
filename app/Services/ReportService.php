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
     * @param array $filters
     * @return array
     */
    public function getDashboardStats(array $filters = []): array
    {
        $query = Laporan::query();
        
        // Apply filters
        $query = $this->applyDashboardFilters($query, $filters);
        
        return [
            'total' => (clone $query)->count(),
            'in_progress' => (clone $query)->where('status', 'Assigned')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
        ];
    }

    /**
     * Apply dashboard filters to query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDashboardFilters($query, array $filters)
    {
        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('problem_category_id', $filters['category_id']);
        }

        // Filter by specific date
        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }
        // Filter by month and year
        elseif (!empty($filters['month']) || !empty($filters['year'])) {
            if (!empty($filters['year'])) {
                $query->whereYear('created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('created_at', $filters['month']);
            }
        }

        return $query;
    }

    /**
     * Get reports per month (last 12 months)
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getReportsPerMonth(array $filters = [])
    {
        $query = Laporan::selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan, COUNT(*) as total');
        
        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('problem_category_id', $filters['category_id']);
        }
        
        // Apply date filters
        if (!empty($filters['date'])) {
            // For specific date, only show that month
            $date = \Carbon\Carbon::parse($filters['date']);
            $query->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
        } elseif (!empty($filters['month']) || !empty($filters['year'])) {
            // For month/year filters, show filtered period
            if (!empty($filters['year'])) {
                $query->whereYear('created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('created_at', $filters['month']);
            }
        } else {
            // Default: last 12 months
            $query->where('created_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        
        return $query->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
    }

    /**
     * Get reports by area per month
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getReportsByAreaPerMonth(array $filters = [])
    {
        $query = Laporan::join('areas', 'laporan.area_id', '=', 'areas.id')
            ->selectRaw('areas.name as area_name, YEAR(laporan.created_at) as tahun, MONTH(laporan.created_at) as bulan, COUNT(*) as total');
            
        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('laporan.problem_category_id', $filters['category_id']);
        }
        
        // Apply date filters
        if (!empty($filters['date'])) {
            // For specific date, only show that month
            $date = \Carbon\Carbon::parse($filters['date']);
            $query->whereYear('laporan.created_at', $date->year)
                  ->whereMonth('laporan.created_at', $date->month);
        } elseif (!empty($filters['month']) || !empty($filters['year'])) {
            // For month/year filters, show filtered period
            if (!empty($filters['year'])) {
                $query->whereYear('laporan.created_at', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('laporan.created_at', $filters['month']);
            }
        } else {
            // Default: last 12 months
            $query->where('laporan.created_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        
        return $query->groupBy('areas.name', 'tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
    }

    /**
     * Get reports by category for current month
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getReportsByCategoryCurrentMonth(array $filters = [])
    {
        \Log::info('🔍 getReportsByCategoryCurrentMonth called with filters', $filters);

        $query = Laporan::with('problemCategory')
            ->selectRaw('problem_category_id, COUNT(*) as total');

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('problem_category_id', $filters['category_id']);
        }

        $hasDateFilter = !empty($filters['date']);
        $hasMonthFilter = !empty($filters['month']);
        $hasYearFilter = !empty($filters['year']);
        $hasAnyFilter = $this->hasActiveFilters($filters);

        // Apply date filters
        if ($hasDateFilter) {
            // Specific date filter
            $query->whereDate('created_at', $filters['date']);
            \Log::info('🗓️ Applied date filter', ['date' => $filters['date']]);
        } elseif ($hasMonthFilter || $hasYearFilter) {
            // Month/Year filters
            if ($hasYearFilter) {
                $query->whereYear('created_at', $filters['year']);
                \Log::info('📅 Applied year filter', ['year' => $filters['year']]);
            }

            if ($hasMonthFilter) {
                $query->whereMonth('created_at', $filters['month']);
                \Log::info('📅 Applied month filter', ['month' => $filters['month']]);
            }
        } else {
            // Default to current month if no filters
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
            \Log::info('📅 Applied default current month/year filter');
        }

        $reports = $query->groupBy('problem_category_id')->get()
            ->map(function ($item) {
                return [
                    'problem_category' => $item->problemCategory,
                    'total' => $item->total,
                ];
            });

        \Log::info('📊 Category reports result', ['data' => $reports->toArray()]);

        // Fallback to last 3 months only when no filters are applied and data is empty
        if ($reports->isEmpty() && !$hasAnyFilter) {
            \Log::info('📊 No category data for current month - using fallback data');

            $reports = Laporan::with('problemCategory')
                ->selectRaw('problem_category_id, COUNT(*) as total')
                ->where('created_at', '>=', now()->subMonths(3))
                ->groupBy('problem_category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'problem_category' => $item->problemCategory,
                        'total' => $item->total,
                    ];
                });
        }

        return $reports;
    }

    /**
     * Get recent reports by status for dashboard cards
     * 
     * @param string $status
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentReports(string $status, int $limit = 5)
    {
        return Laporan::with(['area', 'penanggungJawab', 'problemCategory'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($laporan) {
                // Get station info from PIC
                $station = $laporan->penanggungJawab->station ?? null;
                
                return [
                    'id' => $laporan->id,
                    'tanggal' => \Carbon\Carbon::parse($laporan->Tanggal)->format('Y-m-d'),
                    'deskripsi' => \Illuminate\Support\Str::limit($laporan->deskripsi_masalah, 50),
                    'tenggat_waktu' => \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('Y-m-d'),
                    'area_name' => $laporan->area->name ?? 'N/A',
                    'station' => $station,
                    'category_name' => $laporan->problemCategory->name ?? 'N/A',
                    'status' => $laporan->status,
                    'created_at' => $laporan->created_at
                ];
            });
    }

    private function hasActiveFilters(array $filters): bool
    {
        return (bool) array_filter([
            $filters['category_id'] ?? null,
            $filters['month'] ?? null,
            $filters['year'] ?? null,
            $filters['date'] ?? null,
        ], function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
