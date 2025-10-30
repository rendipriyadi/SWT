<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ProblemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
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
     * Get the laporan that belong to this category
     */
    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}