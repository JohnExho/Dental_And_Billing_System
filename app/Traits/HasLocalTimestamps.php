<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasLocalTimestamps
{
    /**
     * Convert any timestamp to app timezone automatically
     */
    protected function asDateTime($value)
    {
        return parent::asDateTime($value)->timezone(config('app.timezone'));
    }
}
