<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Waitlist extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'waitlist';

    protected $primaryKey = 'waitlist_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'waitlist_id';

    protected $fillable = [
        'account_id',
        'clinic_id',
        'patient_id',
        'associate_id',
        'requested_at_date',
        'requested_at_time',
        'queue_position',
        'status',
        'queue_snapshot',
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
}
