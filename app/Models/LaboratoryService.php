<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class LaboratoryService extends Pivot
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'laboratory_service';

    protected $primaryKey = 'laboratory_service_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'laboratory_service_id';

    protected $fillable = [
        'laboratory_service_id',
        'laboratory_id',
        'service_id',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'laboratory_id');
    }
}
