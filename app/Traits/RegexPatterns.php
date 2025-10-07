<?php

namespace App\Traits;

trait RegexPatterns
{
    /**
     * Only letters and spaces, with optional hyphens/apostrophes
     */
    public static function namePattern(): string
    {
        return "/^[a-zA-ZÀ-ÿ' -]+$/";
    }

    /**
     * Allow letters, numbers, spaces, hyphens
     */
    public static function alphaNumericPattern(): string
    {
        return "/^[a-zA-Z0-9À-ÿ' -]+$/";
    }

    /**
     * Email local-part cannot be all numbers
     */
    public static function emailLocalPartNotAllDigits(string $email): bool
    {
        $local = explode('@', $email)[0];
        return ! preg_match('/^\d+$/', $local);
    }

    // Add more patterns as needed
}
