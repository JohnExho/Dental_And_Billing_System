<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLoginToken extends Model
{
    protected $primaryKey = 'token_id'; // use token_id as PK
    public $incrementing = false;       // UUIDs are not auto-increment
    protected $keyType = 'string';      // UUID is stored as string

    protected $fillable = [
        'token_id',      // include token_id in fillable
        'account_id',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}
