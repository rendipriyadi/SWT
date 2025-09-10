<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyelesaian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyelesaian';

    protected $fillable = [
        'laporan_id',
        'Tanggal',
        'Foto',
        'deskripsi_penyelesaian',
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

    public function laporan()
    {
        return $this->belongsTo(\App\Models\laporan::class, 'laporan_id');
    }
}
