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
    ];

    protected $casts = [
        'Foto' => 'array',
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
}