<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasLocalTimestamps;
/**
 * @method static Builder whereNotIn(string $column, mixed $values)
 * @method static Builder where(string $column, string $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Builder latest(string $column = 'created_at')
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator paginate(int $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
 */

class Logs extends Model
{
    /** @use HasFactory<\Database\Factories\LogsFactory> */
    use HasFactory, SoftDeletes, Notifiable, HasUuid, HasLocalTimestamps;


    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $uuidColumn = 'log_id';

    protected $fillable = [
        'account_id',
        'account_name_snapshot',
        'loggable_id',
        'loggable_type',
        'loggable_snapshot',
        'log_type',
        'action',
        'description',
        'private_description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'account_name_snapshot' => 'encrypted',
        'loggable_snapshot' => 'encrypted:array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    // Who did it
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    // What it was done to
    public function loggable()
    {
        return $this->morphTo();
    }


}
