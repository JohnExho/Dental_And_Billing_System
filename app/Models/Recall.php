<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Recall extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'recalls';

    protected $primaryKey = 'recall_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'recall_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'patient_visit_id',
        'recall_date',
        'note_id',
        'recall_reason',
        'status',
    ];

    protected $casts = [
        'recall_date' => 'datetime',
        'recall_reason' => 'encrypted',
        'status' => 'string',
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

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id', 'patient_visit_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
