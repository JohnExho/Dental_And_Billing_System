<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Model
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'clinics';
    protected $primaryKey = 'clinic_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'clinic_id';

    protected $fillable = [
        'name',
        'description',
        'specialty',
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
        'mobile_no' => 'encrypted',
        'contact_no' => 'encrypted',
        'email' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    //Connections
    public function logs()
    {
        return $this->hasMany(Logs::class, 'clinic_id', 'clinic_id');
    }

   public function account(){
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

}
