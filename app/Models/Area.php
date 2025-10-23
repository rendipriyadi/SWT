<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
// SoftDeletes dihapus karena kolom deleted_at telah di-drop

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    
    protected $fillable = [
        'name',
        'slug',
    ];
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($area) {
            if (empty($area->slug)) {
                $area->slug = Str::slug($area->name);
            }
        });

        static::updating(function ($area) {
            if (empty($area->slug)) {
                $area->slug = Str::slug($area->name);
            }
        });
    }
    
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