<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Clinic extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'clinics';

    protected $primaryKey = 'clinic_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'clinic_id';

    protected $fillable = [
        'name',
        'name_hash',
        'description',
        'specialty',
        'schedule_summary',
        'mobile_no',
        'contact_no',
        'email',
        'email_hash',
        'account_id',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'specialty' => 'encrypted',
        'schedule_summary' => 'encrypted',
        'mobile_no' => 'encrypted',
        'contact_no' => 'encrypted',
        'email' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Connections
    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'clinic_id', 'clinic_id');
    }

    public function clinicSchedules()
    {
        return $this->hasMany(ClinicSchedule::class, 'clinic_id', 'clinic_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'clinic_service', 'clinic_id', 'service_id')
            ->using(ClinicService::class)
            ->withPivot('clinic_service_id', 'price')
            ->withTimestamps();
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_clinic', 'clinic_id', 'medicine_id')
            ->using(MedicineClinic::class)
            ->withPivot('medicine_clinic_id', 'price', 'stock')
            ->withTimestamps();
    }
}
