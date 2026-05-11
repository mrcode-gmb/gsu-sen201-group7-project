<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expenses;
use App\Models\ProductAssignment;

class AssignExistingDataToBusiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:assign-existing-data 
                            {--business-name=Default Business : The name of the business to create or use}
                            {--business-id= : Use existing business ID instead of creating new}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign existing users and data to a business';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting data assignment process...');
        $this->newLine();

        // Get or create business
        if ($this->option('business-id')) {
            $business = Business::find($this->option('business-id'));
            if (!$business) {
                $this->error('Business not found with ID: ' . $this->option('business-id'));
                return 1;
            }
            $this->info('Using existing business: ' . $business->name);
        } else {
            $businessName = $this->option('business-name');
            
            $business = Business::create([
                'name' => $businessName,
                'slug' => \Illuminate\Support\Str::slug($businessName),
                'business_type' => 'Palm Oil',
                'status' => 'active',
                'description' => 'Migrated from single-business system',
            ]);
            
            $this->info('✅ Created new business: ' . $business->name . ' (ID: ' . $business->id . ')');
        }

        $this->newLine();

        // Assign users
        $usersCount = User::whereNull('business_id')
            ->where('role', '!=', 'super_admin')
            ->count();
        
        if ($usersCount > 0) {
            $this->info('Assigning ' . $usersCount . ' users to business...');
            User::whereNull('business_id')
                ->where('role', '!=', 'super_admin')
                ->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $usersCount . ' users');
        } else {
            $this->info('ℹ️  No users to assign');
        }

        // Assign products
        $productsCount = Product::whereNull('business_id')->count();
        if ($productsCount > 0) {
            $this->info('Assigning ' . $productsCount . ' products to business...');
            Product::whereNull('business_id')->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $productsCount . ' products');
        } else {
            $this->info('ℹ️  No products to assign');
        }

        // Assign sales
        $salesCount = Sale::whereNull('business_id')->count();
        if ($salesCount > 0) {
            $this->info('Assigning ' . $salesCount . ' sales to business...');
            Sale::whereNull('business_id')->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $salesCount . ' sales');
        } else {
            $this->info('ℹ️  No sales to assign');
        }

        // Assign purchases
        $purchasesCount = Purchase::whereNull('business_id')->count();
        if ($purchasesCount > 0) {
            $this->info('Assigning ' . $purchasesCount . ' purchases to business...');
            Purchase::whereNull('business_id')->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $purchasesCount . ' purchases');
        } else {
            $this->info('ℹ️  No purchases to assign');
        }

        // Assign expenses
        $expensesCount = Expenses::whereNull('business_id')->count();
        if ($expensesCount > 0) {
            $this->info('Assigning ' . $expensesCount . ' expenses to business...');
            Expenses::whereNull('business_id')->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $expensesCount . ' expenses');
        } else {
            $this->info('ℹ️  No expenses to assign');
        }

        // Assign product assignments
        $assignmentsCount = ProductAssignment::whereNull('business_id')->count();
        if ($assignmentsCount > 0) {
            $this->info('Assigning ' . $assignmentsCount . ' product assignments to business...');
            ProductAssignment::whereNull('business_id')->update(['business_id' => $business->id]);
            $this->info('✅ Assigned ' . $assignmentsCount . ' product assignments');
        } else {
            $this->info('ℹ️  No product assignments to assign');
        }

        $this->newLine();
        $this->info('🎉 Data assignment completed successfully!');
        $this->newLine();
        
        $this->table(
            ['Business ID', 'Business Name', 'Users', 'Products', 'Sales', 'Purchases'],
            [[
                $business->id,
                $business->name,
                $business->users()->count(),
                $business->products()->count(),
                $business->sales()->count(),
                $business->purchases()->count(),
            ]]
        );

        return 0;
    }
}
