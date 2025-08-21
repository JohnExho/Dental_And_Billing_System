<?php

namespace App\Models;

use App\Traits\HasUuid;
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
        'patient_id',
        'associate_id',
        'clinic_id',
        'laboratory_id',
        'log_type',
        'action',
        'description',
    ];

    protected $cast = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function account(){
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}
