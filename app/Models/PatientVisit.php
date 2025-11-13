<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PatientVisit extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'patient_visits';

    protected $primaryKey = 'patient_visit_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'patient_visit_id';

    protected $fillable = [
        'account_id',
        'clinic_id',
        'patient_id',
        'associate_id',
        'laboratory_id',
        'waitlist_id',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id', 'associate_id');
    }

    // public function laboratory()
    // {
    //     return $this->belongsTo(Laboratories::class, 'laboratory_id', 'laboratory_id');
    // }

    public function note()
    {
        return $this->hasOne(Note::class, 'patient_visit_id', 'patient_visit_id');
    }
}
