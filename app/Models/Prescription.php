<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\PatientVisit;
use App\Models\Patient;
use App\Models\Account;
use App\Models\Medicine;
use App\Models\Clinic;
use App\Models\Tooth;
use App\Models\Logs;

class Prescription extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'prescriptions';

    protected $primaryKey = 'prescription_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'prescription_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'clinic_id',
        'patient_visit_id',
        'prescription_type',
        'medicine_id',
        'tooth_id',
        'prescription_details',
        'prescribed_at',
    ];

    protected $casts = [
        'prescribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function tooth()
    {
        return $this->belongsTo(ToothList::class, 'tooth_list_id', 'tooth_list_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
