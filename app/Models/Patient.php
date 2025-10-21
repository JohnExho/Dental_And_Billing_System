<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'patients';

    protected $primaryKey = 'patient_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'patient_id';

    protected $fillable = [
        'account_id',
        'clinic_id',
        'qr_id',
        'first_name',
        'middle_name',
        'last_name',
        'mobile_no',
        'contact_no',
        'email',
        'email_hash',
        'last_name_hash',
        'profile_picture',
        'sex',
        'civil_status',
        'date_of_birth',
        'referral',
        'occupation',
        'company',
        'weight',
        'height',
        'school',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'mobile_no' => 'encrypted',
        'contact_no' => 'encrypted',
        'email' => 'encrypted',
        'profile_picture' => 'encrypted',
        'sex' => 'encrypted',
        'civil_status' => 'encrypted',
        'date_of_birth' => 'encrypted',
        'referral' => 'encrypted',
        'occupation' => 'encrypted',
        'company' => 'encrypted',
        'weight' => 'encrypted',
        'height' => 'encrypted',
        'school' => 'encrypted',
    ];

    protected $appends = ['full_name', 'full_address']; // optional: auto-include in JSON

    public function getFullNameAttribute()
    {
        return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}") ?: 'N/A';
    }

    public function getFullAddressAttribute()
    {
        if (!$this->address) {
            return 'N/A';
        }

        $house = trim((string) $this->address->house_no) ?: 'Unknown';
        $street = trim((string) $this->address->street) ?: 'Unknown';
        $barangay = optional($this->address->barangay)->name ?: 'N/A';
        $city = optional($this->address->city)->name ?: 'N/A';
        $province = optional($this->address->province)->name ?: 'N/A';

        return trim("{$house}, {$street}, {$barangay}, {$city}, {$province}");
    }


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
        return $this->hasOne(Address::class, 'patient_id', 'patient_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    //     public function patientQR()
    // {
    //     return $this->belongsTo(Clinic::class, 'qr_id', 'qr_id');
    // }
    public function waitlist(){
        
        return $this->hasMany(Waitlist::class, 'patient_id', 'patient_id');
    }
}
