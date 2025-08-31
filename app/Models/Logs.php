<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logs extends Model
{
    /** @use HasFactory<\Database\Factories\LogsFactory> */
    use HasFactory, SoftDeletes, Notifiable, HasUuid;

    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'log_id';

    protected $fillable = [
        'account_id',
        'account_name_snapshot',
        'patient_id',
        'associate_id',
        'clinic_id',
        'laboratory_id',
        'log_type',
        'action',
        'description',
        'private_description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'account_name_snapshot' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    //Logs
  public static function record(
        Account $account,
        ?Clinic $clinic = null,
        string $action,
        string $log_type,
        ?string $description = null,
        ?string $private_description = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $account_name_snapshot = null,
    ): void {
        self::create([
            'log_id'       => Str::uuid(),
            'account_id'   => $account->account_id,
            'patient_id'   => null,
            'associate_id' => null,
            'clinic_id'    => $clinic?->clinic_id,
            'laboratory_id'=> null,
            'log_type'     => $log_type,
            'action'       => $action,
            'description'  => $description ?? ucfirst($action) . ' performed.',
            'private_description' => $private_description ?? null,
            'ip_address'   => $ipAddress ?? request()->ip(),
            'user_agent'   => $userAgent ?? request()->userAgent(),
            'account_name_snapshot' => $account_name_snapshot ?? ($account->full_name ?: 'N/A'),

            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function account(){
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}
