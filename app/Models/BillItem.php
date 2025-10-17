<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class BillItem extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'bill_items';

    protected $primaryKey = 'bill_item_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'bill_item_id';

    protected $fillable = [
        'bill_id',
        'account_id',
        'item_type',
        'medicine_id',
        'service_id',
        'tooth_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'item_type' => 'string',
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

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function tooth()
    {
        return $this->belongsTo(ToothList::class, 'tooth_id', 'tooth_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }
}
