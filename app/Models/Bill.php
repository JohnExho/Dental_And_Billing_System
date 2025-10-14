<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Bill extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'bills';

    protected $primaryKey = 'bill_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'bill_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'associate_id',
        'clinic_id',
        'laboratory_id',
        'patient_visit_id',
        'amount',
        'discount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships (ready to hook up)
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

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'laboratory_id');
    }

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
