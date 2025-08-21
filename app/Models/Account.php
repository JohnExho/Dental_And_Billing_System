<?php

namespace App\Models;

use App\Models\Logs;
use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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

    //Logs
    public function logAction(string $action, string $log_type, ?string $description = null): void
    {
        $this->logs()->create([
            'log_id' => Str::uuid(),
            'patient_id' => null,
            'associate_id' => null,
            'clinic_id' => null,
            'laboratory_id' => null,
            'log_type' => $log_type,
            'action' => $action,
            'description' => $description ?? ucfirst($action) . ' performed.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    //Connections
    public function logs()
    {
        return $this->hasMany(Logs::class, 'account_id', 'account_id');
    }
}
