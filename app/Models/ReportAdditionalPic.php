<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAdditionalPic extends Model
{
    use HasFactory;

    protected $table = 'report_additional_pics';

    protected $fillable = [
        'laporan_id',
        'departemen_supervisor_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the report that owns this additional PIC
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    /**
     * Get the departemen supervisor (PIC) for this record
     */
    public function departemenSupervisor(): BelongsTo
    {
        return $this->belongsTo(DepartemenSupervisor::class, 'departemen_supervisor_id');
    }

    /**
     * Scope to get additional PICs for a specific report
     */
    public function scopeForReport($query, $laporanId)
    {
        return $query->where('laporan_id', $laporanId);
    }

    /**
     * Scope to get reports with a specific additional PIC
     */
    public function scopeForPic($query, $departemenSupervisorId)
    {
        return $query->where('departemen_supervisor_id', $departemenSupervisorId);
    }
}
