<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class BillItemTooth extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'bill_item_tooth';

    protected $primaryKey = 'bill_item_tooth_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'bill_item_tooth_id';

    protected $fillable = [
        'bill_item_id',
        'tooth_list_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function billItem()
    {
        return $this->belongsTo(BillItem::class, 'bill_item_id', 'bill_item_id');
    }

    public function tooth()
    {
        return $this->belongsTo(ToothList::class, 'tooth_list_id', 'tooth_list_id');
    }
}
