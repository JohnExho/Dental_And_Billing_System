<?php

namespace App\Models;

use App\Models\Logs as Log;
use App\Traits\HasUuid;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'accounts';
    protected $primaryKey = 'account_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'account_id';

    protected $fillable = [
        'last_name',
        'middle_name',
        'first_name',
        'email',
        'email_hash',
        'password',
        'role',
        'is_active',
        'otp_hash',
        'otp_expires_at',
    ];

    protected $casts = [
        'last_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'first_name' => 'encrypted',
        'email' => 'encrypted',
        'role' => 'string',
        'is_active' => 'boolean',
        'otp_hash' => 'string',
        'otp_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    //function to get full name
    protected $appends = ['full_name']; // optional: auto-include in JSON

    public function getFullNameAttribute()
    {
    return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}") ?: 'N/A';
    }

    //Connections
    public function logs()
    {
        return $this->hasMany(Logs::class, 'account_id', 'account_id');
    }
}
