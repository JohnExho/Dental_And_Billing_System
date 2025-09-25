<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ToothList extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'tooth_list';

    protected $primaryKey = 'tooth_list_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'tooth_list_id';

    protected $fillable = [
        'name',
        'name_hash',
        'number',
        'default_price',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'default_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function clinicPrices()
    {
        return $this->hasMany(ClinicToothPrice::class, 'tooth_list_id', 'tooth_list_id');
    }
}
