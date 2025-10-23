<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// SoftDeletes dihapus karena kolom deleted_at telah di-drop

class Penyelesaian extends Model
{
    use HasFactory;

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
        // Return all photos without file existence check
        if (!empty($this->Foto) && is_array($this->Foto)) {
            return $this->Foto;
        }
        
        return [];
    }

    public function laporan()
    {
        return $this->belongsTo(\App\Models\laporan::class, 'laporan_id');
    }
}
