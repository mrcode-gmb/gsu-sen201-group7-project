<?php

/**
 * Quick script to assign existing users and data to a business
 * Run this with: php assign_existing_users.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expenses;
use App\Models\ProductAssignment;

echo "🔄 Assigning existing data to business...\n\n";

// Check for users without business
$usersWithoutBusiness = User::whereNull('business_id')
    ->where('role', '!=', 'super_admin')
    ->get();

if ($usersWithoutBusiness->isEmpty()) {
    echo "✅ All users already have a business assigned!\n";
    
    // Show current assignments
    echo "\n📊 Current Business Assignments:\n";
    echo "================================\n";
    
    $businesses = Business::with('users')->get();
    foreach ($businesses as $business) {
        echo "\n🏢 {$business->name} (ID: {$business->id})\n";
        echo "   Users: {$business->users->count()}\n";
        foreach ($business->users as $user) {
            echo "   - {$user->name} ({$user->email}) - {$user->role}\n";
        }
    }
    
    exit(0);
}

echo "Found " . $usersWithoutBusiness->count() . " users without business:\n";
foreach ($usersWithoutBusiness as $user) {
    echo "  - {$user->name} ({$user->email}) - {$user->role}\n";
}
echo "\n";

// Create or use Hizabrun Enterprises
$business = Business::firstOrCreate(
    ['slug' => 'hizabrun-enterprises'],
    [
        'name' => 'Hizabrun Enterprises',
        'business_type' => 'Palm Oil',
        'status' => 'active',
        'description' => 'Default business for existing data',
    ]
);

echo "✅ Using business: {$business->name} (ID: {$business->id})\n\n";

// Assign users
$userCount = User::whereNull('business_id')
    ->where('role', '!=', 'super_admin')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$userCount} users\n";

// Assign products
$productCount = Product::whereNull('business_id')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$productCount} products\n";

// Assign sales
$salesCount = Sale::whereNull('business_id')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$salesCount} sales\n";

// Assign purchases
$purchaseCount = Purchase::whereNull('business_id')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$purchaseCount} purchases\n";

// Assign expenses
$expenseCount = Expenses::whereNull('business_id')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$expenseCount} expenses\n";

// Assign product assignments
$assignmentCount = ProductAssignment::whereNull('business_id')
    ->update(['business_id' => $business->id]);
echo "✅ Assigned {$assignmentCount} product assignments\n";

echo "\n🎉 All existing data has been assigned to {$business->name}!\n\n";

// Show summary
echo "📊 Business Summary:\n";
echo "===================\n";
echo "Business: {$business->name}\n";
echo "Users: " . $business->users()->count() . "\n";
echo "Products: " . $business->products()->count() . "\n";
echo "Sales: " . $business->sales()->count() . "\n";
echo "Purchases: " . $business->purchases()->count() . "\n";
echo "\nYou can now login with your existing admin account!\n";
