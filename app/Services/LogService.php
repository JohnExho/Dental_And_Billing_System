<?php


namespace App\Services;

use App\Models\Logs;
use App\Models\Clinic;
use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;


class LogService
{
    public static function record(
        Account $account,
        Model $loggable,
        string $action,
        string $log_type,
        ?string $description = null,
        ?string $private_description = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        Logs::create([
            'log_id' => Str::uuid(),
            'account_id' => $account->account_id,
            'account_name_snapshot' => $account->full_name ?? 'N/A',
            'loggable_id' => $loggable->getKey(),
            'loggable_type' => get_class($loggable),
            'loggable_snapshot' => json_encode(
                self::extractSnapshot($loggable)
            ),
            'log_type' => $log_type,
            'action' => $action,
            'description' => $description ?? ucfirst($action) . ' performed.',
            'private_description' => $private_description,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    protected static function extractSnapshot(Model $model): array
    {
        // Pick fields based on model type
        switch (get_class($model)) {
            case Account::class:
                return [
                    'full_name' => $model->full_name,
                    'email' => $model->email,
                    'role' => $model->role,
                    'is_active' => $model->is_active,
                ];
            case Clinic::class:
                return [
                    'name' => $model->name,
                    'email' => $model->email,
                    'mobile_no' => $model->mobile_no,
                    'contact_no' => $model->contact_no,
                    'address' => optional($model->address)->full_address, // assuming your Address model has full_address accessor
                ];

            // case \App\Models\Bill::class:
            //     return [
            //         'bill_number' => $model->bill_number,
            //         'total'       => $model->total,
            //         'status'      => $model->status,
            //     ];
            // case \App\Models\Patient::class:
            //     return [
            //         'name'  => $model->full_name,
            //         'dob'   => $model->dob,
            //         'phone' => $model->phone,
            //     ];
            // case \App\Models\Appointment::class:
            //     return [
            //         'date'   => $model->scheduled_at,
            //         'status' => $model->status,
            //     ];
            default:
                // Fallback snapshot
                return $model->only([$model->getKeyName()]);
        }
    }
}
