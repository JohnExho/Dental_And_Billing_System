<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
     use HasFactory, SoftDeletes, Notifiable, HasUuid;
    protected $table = 'medicines';
    protected $primaryKey = 'medicine_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'medicine_id';

    protected $fillable = [
        'name',
        'name_hash',
        'description',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'medicine_clinics', 'medicine_id', 'clinic_id')
                    ->withPivot('price', 'stock')
                    ->withTimestamps();
    }
}
