<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin account
        Account::create([
            'last_name' => 'Admin User',
            'middle_name' => null,
            'first_name' => 'Admin',
            'email' => 'admin@example.com',
            'email_hash' => hash('sha256', strtolower('admin@example.com')),
            'password' => Hash::make('password'), // Always hash passwords
            'role' => 'admin',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);

        // Example of another account
        Account::create([
            'last_name' => 'Doe',
            'middle_name' => 'M',
            'first_name' => 'John',
            'email' => 'rci.bsis.hensonjohnvictor@gmail.com',
            'email_hash' => hash('sha256', strtolower('rci.bsis.hensonjohnvictor@gmail.com')),
            'password' => Hash::make('secret123'),
            'role' => 'staff',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);
    }
}
