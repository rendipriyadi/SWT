<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenanggungJawab extends Model
{
    use HasFactory;

    protected $table = 'penanggung_jawab';
    
    protected $fillable = [
        'area_id',
        'station',
        'name',
        'email',
    ];
    
    /**
     * Get the area that owns the penanggung jawab.
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    
    /**
     * Get the laporan associated with the penanggung jawab.
     */
    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}