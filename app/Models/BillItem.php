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
        'service_id',
        'amount',
        'prescription_id',
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
        return $this->belongsTo(ToothList::class, 'tooth_list_id', 'tooth_list_id');
    }

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function billItemTooths()
    {
        return $this->hasMany(BillItemTooth::class);
    }

    public function teeth()
    {
        // Many-to-many via pivot table `bill_item_tooth`.
        // Explicit keys are provided because the models use non-standard PK names.
        return $this->belongsToMany(
            \App\Models\ToothList::class,
            'bill_item_tooth', // pivot table
            'bill_item_id', // foreign key on pivot referencing this model
            'tooth_list_id', // related key on pivot referencing ToothList
            'bill_item_id', // local key on this model
            'tooth_list_id' // local key on related model
        );
    }
}
