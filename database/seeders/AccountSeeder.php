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
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Always hash passwords
            'role' => 'admin',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);

        // Example of another account
        Account::create([
            'name' => 'John Doe',
            'email' => 'rci.bsis.hensonjohnvictor@gmail.com',
            'password' => Hash::make('secret123'),
            'role' => 'staff',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);
    }
}
