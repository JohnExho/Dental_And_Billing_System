<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Service extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'services';

    protected $primaryKey = 'service_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'service_id';

    protected $fillable = [
        'name',
        'name_hash',
        'description',
        'account_id',
        'service_type',

    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'service_type' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_service', 'service_id', 'clinic_id')
            ->withPivot('price')
            ->withTimestamps();
    }
}
