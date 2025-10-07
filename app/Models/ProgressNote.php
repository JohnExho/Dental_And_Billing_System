<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ProgressNote extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'progress_notes';

    protected $primaryKey = 'progress_note_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'progress_note_id';

    protected $fillable = [
        'account_id',
        'patient_id',
        'associate_id',
        'visit_id',
        'summary',
        'progress_note',
    ];

    protected $casts = [
        'summary' => 'encrypted',
        'progress_note' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
}
