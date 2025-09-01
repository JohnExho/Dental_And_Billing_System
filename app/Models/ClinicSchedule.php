<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicSchedule extends Model
{
     use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'clinic_schedules';
    protected $primaryKey = 'clinic_schedule_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'clinic_schedule_id';

    protected $fillable = [
        'clinic_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'encrypted',
        'start_time' => 'encrypted',
        'end_time' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    //Connections
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

}