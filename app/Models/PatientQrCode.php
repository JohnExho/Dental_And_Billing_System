<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PatientQrCode extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'patient_qr_codes';

    protected $primaryKey = 'qr_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $uuidColumn = 'qr_id';

    protected $fillable = [
        'qr_id',
        'qr_code',
        'qr_password',
        'clinic_id',
    ];

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
