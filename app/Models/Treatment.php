<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\PatientVisit;
use App\Models\Patient;
use App\Models\Associate;
use App\Models\Service;
use App\Models\Clinic;
use App\Models\Laboratory;
use App\Models\BillItem;
use App\Models\Tooth;
use App\Models\Logs;

class Treatment extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'patient_treatments';

    protected $primaryKey = 'patient_treatment_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'patient_treatment_id';

    protected $fillable = [
        'patient_visit_id',
        'patient_id',
        'associate_id',
        'service_id',
        'clinic_id',
        'laboratory_id',
        'bill_item_id',
        'account_id',
        'tooth_id',
        'treatment_date',
    ];

    protected $casts = [
        'treatment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id', 'associate_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'laboratory_id');
    }

    public function billItem()
    {
        return $this->belongsTo(BillItem::class, 'bill_item_id', 'bill_item_id');
    }

    public function tooth()
    {
        return $this->belongsTo(ToothList::class, 'tooth_id', 'tooth_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}