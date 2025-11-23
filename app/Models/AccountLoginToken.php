<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLoginToken extends Model
{
    protected $fillable = [
        'account_id',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
