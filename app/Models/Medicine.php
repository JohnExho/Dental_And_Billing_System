<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Medicine extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'medicines';

    protected $primaryKey = 'medicine_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'medicine_id';

    protected $fillable = [
        'name',
        'name_hash',
        'description',
        'default_price',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->morphMany(Logs::class, 'loggable');
    }

    public function medicineClinics()
    {
        return $this->hasMany(MedicineClinic::class, 'medicine_id', 'medicine_id');
    }
}
