<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
// SoftDeletes dihapus karena kolom deleted_at telah di-drop

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    
    protected $fillable = [
        'name',
    ];
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the route key for the model (encrypted).
     */
    public function getRouteKey()
    {
        return Crypt::encrypt($this->getKey());
    }

    /**
     * Resolve the route key from encrypted value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $id = Crypt::decrypt($value);
            return $this->where($field ?: $this->getRouteKeyName(), $id)->first();
        } catch (\Exception $e) {
            return null;
        }
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