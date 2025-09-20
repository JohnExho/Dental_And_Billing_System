<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MedicineClinic extends Pivot
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'medicine_clinics';

    protected $primaryKey = 'medicine_clinic_id';

    public $incrementing = false; // because you’re using UUIDs

    protected $keyType = 'string';

    protected $uuidColumn = 'medicine_id';

    protected $fillable = [
        'medicine_id',
        'clinic_id',
        'stock',
        'price',
    ];

    // Optional: if you want timestamps to work with Pivot
    public $timestamps = true;

    // Relationships back to Medicine or Clinic (if needed)
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
