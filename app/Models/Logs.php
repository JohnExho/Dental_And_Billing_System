<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logs extends Model
{
    /** @use HasFactory<\Database\Factories\LogsFactory> */
    use HasFactory, SoftDeletes, Notifiable, HasUuid;

    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'log_id';

    protected $fillable = [
        'account_id',
        'account_name_snapshot',
        'loggable_id',
        'loggable_type',
        'loggable_snapshot',
        'log_type',
        'action',
        'description',
        'private_description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'account_name_snapshot' => 'encrypted',
        'loggable_snapshot' => 'encrypted:array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    // Who did it
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    // What it was done to
    public function loggable()
    {
        return $this->morphTo();
    }


}
