<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ToothList extends Model
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'tooth_list';
    protected $primaryKey = 'tooth_list_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'tooth_list_id';

    protected $fillable = [
        'name',
        'name_hash',
        'number',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function teeth()
    {
        return $this->hasMany(Logs::class, 'tooth_list_id', 'tooth_list_id');
    }
}
