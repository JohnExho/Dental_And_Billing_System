<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Associate extends Model
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'associates';
    protected $primaryKey = 'associate_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'associate_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'last_name_hash',
        'specialty',
        'mobile_no',
        'contact_no',
        'email',
        'is_active',
        'email_hash',
        'account_id',
        'clinic_id',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'specialty' => 'encrypted',
        'mobile_no' => 'encrypted',
        'contact_no' => 'encrypted',
        'email' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

        protected $appends = ['full_name']; // optional: auto-include in JSON

    public function getFullNameAttribute()
    {
    return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}") ?: 'N/A';
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'associate_id', 'associate_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'associate_id', 'associate_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'laboratory_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id', 'clinic_id');
    }
}
