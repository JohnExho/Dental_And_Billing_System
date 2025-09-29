<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ClinicService extends Pivot
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'clinic_service';

    protected $primaryKey = 'clinic_service_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'clinic_service_id';

    protected $fillable = [
        'clinic_service_id',
        'clinic_id',
        'service_id',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
