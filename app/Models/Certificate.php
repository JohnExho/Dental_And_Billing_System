<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Certificate extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'certificates';

    protected $primaryKey = 'certificate_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'certificate_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'associate_id',
        'clinic_id',
        'patient_visit_id',
        'certificate_type',
        'certificate_details',
        'issued_at',
        'file_path',
    ];

    protected $casts = [
        'certificate_details' => 'string', // or 'array' if JSON is used
        'certificate_type' => 'string',
        'file_path' => 'string',
        'issued_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
