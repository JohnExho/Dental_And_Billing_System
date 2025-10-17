<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Note extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'notes';

    protected $primaryKey = 'note_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'note_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'associate_id',
        'patient_visit_id',
        'summary',
        'note',
        'note_type',
        'clinic_id',
    ];

    protected $casts = [
        'summary' => 'encrypted',
        'note' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id', 'associate_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function patientVisit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }   

    public function clinic(){
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
