<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Payment extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'payment_id';

    protected $fillable = [
        'bill_id',
        'account_id',
        'payment_method',
        'amount',
        'paid_at_date',
        'paid_at_time',
        'payment_details',
        'clinic_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'bill_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
