<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;   // ✅ Use Model, not Pivot
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MedicineClinic extends Model   // ✅ Change here
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'medicine_clinics';

    protected $primaryKey = 'medicine_clinic_id';

    public $incrementing = false; // Using UUIDs
    protected $keyType = 'string';

    // ✅ Fix wrong UUID column
    protected $uuidColumn = 'medicine_clinic_id';

    protected $fillable = [
        'medicine_id',
        'clinic_id',
        'stock',
        'price',
    ];

    public $timestamps = true;

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
