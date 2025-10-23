<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DepartemenSupervisor extends Model
{
    protected $table = 'departemen_supervisors';
    protected $fillable = ['departemen', 'supervisor', 'workgroup', 'email', 'slug'];

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

        static::creating(function ($department) {
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->supervisor . '-' . $department->departemen);
            }
        });

        static::updating(function ($department) {
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->supervisor . '-' . $department->departemen);
            }
        });
    }

    public function laporan()
    {
        return $this->hasMany(laporan::class, 'departemen_supervisor_id', 'id');
    }
}
