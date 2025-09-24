<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


class ClinicToothPrice extends Model
{

    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'clinic_tooth_prices';

    protected $primaryKey = 'clinic_tooth_prices_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'clinic_tooth_prices_id';

    protected $fillable = [
        'tooth_list_id',
        'clinic_id',
        'price',
    ];

    protected $casts = [
        'price' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Connections
    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function tooth()
    {
        return $this->belongsTo(ToothList::class, 'tooth_list_id', 'tooth_list_id');
    }

}

