<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class Address extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'addresses';

    protected $primaryKey = 'address_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'address_id';

    protected $fillable = [
        'account_id',
        'clinic_id',
        'laboratory_id',
        'associate_id',
        'house_no',
        'street',
        'barangay_name',
        'city_name',
        'province_name',
        'barangay_id',
        'city_id',
        'province_id',
        'patient_id',
    ];

    protected $casts = [
        'house_no' => 'encrypted',
        'street' => 'encrypted',
        'barangay_name' => 'encrypted',
        'city_name' => 'encrypted',
        'province_name' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Accessor for full address
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->house_no,
            $this->street,
            $this->barangay_name,
            $this->city_name,
            $this->province_name,
        ]);

        return implode(', ', $parts);
    }

    // Connections

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'laboratory_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class, 'associate_id', 'associate_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
