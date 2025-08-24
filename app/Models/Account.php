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
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'otp',
        'otp_expires_at',
    ];

    protected $casts = [
        'role' => 'string',
        'is_active' => 'boolean',
        'otp' => 'string',
        'otp_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
 
    //Connections
    public function logs()
    {
        return $this->hasMany(Logs::class, 'account_id', 'account_id');
    }
}
