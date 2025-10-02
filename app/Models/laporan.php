<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class laporan extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'laporan';

    protected $fillable = [
        'Tanggal',
        'Foto',
        'departemen_supervisor_id', // Tetap menyimpan untuk backward compatibility
        'area_id',                  // Kolom baru
        'penanggung_jawab_id',      // Kolom baru
        'problem_category_id',      // Kolom baru untuk relationship
        'deskripsi_masalah',
        'tenggat_waktu',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'Foto' => 'array',
    ];

    /**
     * Get array of valid photos (existing files)
     *
     * @return array
     */
    public function getValidPhotosAttribute()
    {
        // Cek apakah Foto adalah array dan tidak kosong
        if (!empty($this->Foto) && is_array($this->Foto)) {
            // Filter foto yang valid (berkas ada)
            return collect($this->Foto)->filter(function($foto) {
                return file_exists(public_path('images/' . $foto));
            })->toArray();
        }
        
        return [];
    }

    public function penyelesaian()
    {
        return $this->hasOne(\App\Models\Penyelesaian::class, 'laporan_id');
    }

    public function departemenSupervisor()
    {
        return $this->belongsTo(DepartemenSupervisor::class, 'departemen_supervisor_id', 'id');
    }
    
    // Relasi baru untuk area
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    
    // Relasi baru untuk penanggung jawab
    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawab::class, 'penanggung_jawab_id');
    }

    // Relasi untuk problem category
    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class, 'problem_category_id');
    }
}