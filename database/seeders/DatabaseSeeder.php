<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin first
        $this->call(SuperAdminSeeder::class);

        // Create default admin user (only if doesn't exist)
        if (!User::where('email', 'admin@palmoilshop.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@palmoilshop.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
        }

        // Create default salesperson (only if doesn't exist)
        if (!User::where('email', 'sales@palmoilshop.com')->exists()) {
            User::create([
                'name' => 'Sales Person',
                'email' => 'sales@palmoilshop.com',
                'password' => Hash::make('password123'),
                'role' => 'salesperson',
            ]);
        }

        // Create another salesperson (only if doesn't exist)
        if (!User::where('email', 'amina@palmoilshop.com')->exists()) {
            User::create([
                'name' => 'Amina Sani',
                'email' => 'amina@palmoilshop.com',
                'password' => Hash::make('password123'),
                'role' => 'salesperson',
            ]);
        }

        // // Create palm oil products
        // Product::create([
        //     'name' => 'Premium Palm Oil (Litre)',
        //     'unit_type' => 'litre',
        //     'current_stock' => 0,
        //     'selling_price' => 1500.00,
        //     'description' => 'High quality palm oil sold per litre',
        // ]);

        // Product::create([
        //     'name' => 'Premium Palm Oil (Jerrycan)',
        //     'unit_type' => 'jerrycan',
        //     'current_stock' => 0,
        //     'selling_price' => 25000.00,
        //     'description' => 'High quality palm oil sold per jerrycan (25 litres)',
        // ]);

        // Product::create([
        //     'name' => 'Standard Palm Oil (Litre)',
        //     'unit_type' => 'litre',
        //     'current_stock' => 0,
        //     'selling_price' => 1200.00,
        //     'description' => 'Standard quality palm oil sold per litre',
        // ]);

        // Product::create([
        //     'name' => 'Standard Palm Oil (Jerrycan)',
        //     'unit_type' => 'jerrycan',
        //     'current_stock' => 0,
        //     'selling_price' => 20000.00,
        //     'description' => 'Standard quality palm oil sold per jerrycan (25 litres)',
        // ]);
    }
}
