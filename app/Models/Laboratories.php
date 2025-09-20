<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Laboratories extends Model
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $table = 'laboratories';

    protected $primaryKey = 'laboratory_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $uuidColumn = 'laboratory_id';

    protected $fillable = [
        'name',
        'name_hash',
        'description',
        'speciality',
        'mobile_no',
        'contact_no',
        'contact_person',
        'email',
        'email_hash',
        'account_id',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
        'speciality' => 'encrypted',
        'mobile_no' => 'encrypted',
        'contact_no' => 'encrypted',
        'contact_person' => 'encrypted',
        'email' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Connections
    public function logs()
    {
        return $this->hasMany(Logs::class, 'laboratory_id', 'laboratory_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'laboratory_id', 'laboratory_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'laboratory_service', 'laboratory_id', 'service_id')
            ->using(LaboratoryService::class)
            ->withPivot('laboratory_service_id', 'price')
            ->withTimestamps();
    }
}
