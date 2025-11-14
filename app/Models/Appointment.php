<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'appointments';

    protected $primaryKey = 'appointment_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'appointment_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'associate_id',
        'clinic_id',
        'appointment_date',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    // Relationships
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id', 'associate_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
