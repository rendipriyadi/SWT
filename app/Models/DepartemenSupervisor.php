<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Laporan;

class DepartemenSupervisor extends Model
{
    protected $table = 'departemen_supervisors';
    protected $fillable = ['departemen', 'supervisor', 'workgroup', 'email'];

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

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'departemen_supervisor_id', 'id');
    }
}
