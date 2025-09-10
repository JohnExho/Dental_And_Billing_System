<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laboratories extends Model
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'laboratories';
    protected $primaryKey = 'laboratory_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'laboratory_id';

    protected $fillable = [
        'name',
        'description',
        'speciality',
        'mobile_no',
        'contact_no',
        'email',
        'email_hash',
        'account_id',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'speciality' => 'encrypted',
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
        return $this->hasMany(Logs::class, 'laboratory_id', 'laboratory_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'laboratory_id', 'laboratory_id');
    }
}
