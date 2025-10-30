<?php

namespace App\Repositories;

use App\Models\Laporan;
use Illuminate\Database\Eloquent\Collection;

/**
 * ReportRepository
 * 
 * Handles database queries for reports
 */
class ReportRepository
{
    /**
     * Get all reports with relationships
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])->get();
    }

    /**
     * Find report by ID with relationships
     * 
     * @param int $id
     * @return Laporan|null
     */
    public function findById(int $id): ?Laporan
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->find($id);
    }

    /**
     * Get in-progress reports
     * 
     * @return Collection
     */
    public function getInProgress(): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', '!=', 'Completed')
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Get completed reports
     * 
     * @return Collection
     */
    public function getCompleted(): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', 'Completed')
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Get reports by area
     * 
     * @param int $areaId
     * @return Collection
     */
    public function getByArea(int $areaId): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('area_id', $areaId)
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Get reports by status
     * 
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('status', $status)
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Get reports by date range
     * 
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->whereDate('Tanggal', '>=', $startDate)
            ->whereDate('Tanggal', '<=', $endDate)
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Get reports by problem category
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getByCategory(int $categoryId): Collection
    {
        return Laporan::with(['area', 'penanggungJawab', 'penyelesaian', 'problemCategory'])
            ->where('problem_category_id', $categoryId)
            ->orderBy('Tanggal', 'desc')
            ->get();
    }

    /**
     * Count reports by status
     * 
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return Laporan::where('status', $status)->count();
    }

    /**
     * Get total reports count
     * 
     * @return int
     */
    public function count(): int
    {
        return Laporan::count();
    }
}
