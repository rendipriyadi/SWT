<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'area_id',
        'penanggung_jawab_id',
        'departemen_supervisor_id',
        'problem_category_id',
        'deskripsi_masalah',
        'tenggat_waktu',
        'status',
        'Foto',
        'additional_pics',
    ];

    protected $casts = [
        'Foto' => 'array',
        'additional_pics' => 'array',
        'tenggat_waktu' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    // Relasi dengan area
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Relasi dengan penanggung jawab
    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawab::class, 'penanggung_jawab_id');
    }

    // Relasi dengan kategori masalah
    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class, 'problem_category_id');
    }

    // Relasi dengan penyelesaian
    public function penyelesaian()
    {
        return $this->hasOne(Penyelesaian::class, 'laporan_id');
    }

    // Get additional PICs as PenanggungJawab models
    public function getAdditionalPicsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        $picIds = is_string($value) ? json_decode($value, true) : $value;
        
        if (empty($picIds) || !is_array($picIds)) {
            return [];
        }

        // Flatten and filter to ensure only valid IDs
        $picIds = \Illuminate\Support\Arr::flatten($picIds);
        $picIds = array_values(array_filter($picIds, function($item) {
            return is_numeric($item) && $item > 0;
        }));
        
        if (empty($picIds)) {
            return [];
        }

        return PenanggungJawab::whereIn('id', $picIds)->get();
    }

    // Get additional PICs IDs only
    public function getAdditionalPicIdsAttribute()
    {
        $additionalPics = $this->attributes['additional_pics'] ?? null;
        
        if (empty($additionalPics)) {
            return [];
        }

        $decoded = is_string($additionalPics) ? json_decode($additionalPics, true) : $additionalPics;
        
        if (!is_array($decoded)) {
            return [];
        }
        
        // Flatten nested arrays and filter to ensure only valid numeric IDs
        $flattened = \Illuminate\Support\Arr::flatten($decoded);
        return array_values(array_filter($flattened, function($item) {
            return is_numeric($item) && $item > 0;
        }));
    }

    // Get additional PICs as PenanggungJawab objects
    public function getAdditionalPicObjectsAttribute()
    {
        $picIds = $this->additional_pic_ids;
        
        if (empty($picIds)) {
            return collect([]);
        }

        return PenanggungJawab::whereIn('id', $picIds)->get();
    }
}