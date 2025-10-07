<?php

namespace App\Traits;

trait ValidationMessages
{
    /**
     * Custom messages for common fields
     */
    public static function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, or apostrophes.',
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, or apostrophes.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, or apostrophes.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'clinic_id.required' => 'Select a Clinic First.',
            'clinic_id.exists' => 'Selected clinic is invalid.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please provide a valid date.',
            // Add more custom messages as needed
        ];
    }
}
