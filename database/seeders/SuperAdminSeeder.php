<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the initial Super Admin account for Smabgroup
     */
    public function run(): void
    {
        // Check if super admin already exists
        $superAdmin = User::where('role', 'super_admin')->first();

        if (!$superAdmin) {
            User::create([
                'name' => 'Smabgroup Super Admin',
                'email' => 'superadmin@smabgroup.com',
                'password' => Hash::make('SuperAdmin@123'), // Change this in production!
                'role' => 'super_admin',
                'status' => 'active',
                'business_id' => null, // Super Admin doesn't belong to any business
            ]);

            $this->command->info('Super Admin account created successfully!');
            $this->command->info('Email: superadmin@smabgroup.com');
            $this->command->info('Password: SuperAdmin@123');
            $this->command->warn('IMPORTANT: Please change the password after first login!');
        } else {
            $this->command->info('Super Admin account already exists.');
        }
    }
}
