<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// SoftDeletes dihapus karena kolom deleted_at telah di-drop

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    
    protected $fillable = [
        'name',
    ];
    
    /**
     * Get the penanggung jawab associated with the area.
     */
    public function penanggungJawabs()
    {
        return $this->hasMany(PenanggungJawab::class);
    }
    
    /**
     * Get the laporan associated with the area.
     */
    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}