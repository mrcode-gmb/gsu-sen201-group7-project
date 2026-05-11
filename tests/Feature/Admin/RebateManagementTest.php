<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Rebate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RebateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_rebate_and_inventory_quantity_increases(): void
    {
        $business = $this->createBusiness();
        $admin = $this->createAdmin($business);
        $purchase = $this->createPurchase($business, $admin, 10, 2500.00);

        $response = $this->actingAs($admin)->post(route('rebates.store'), [
            'purchase_id' => $purchase->id,
            'quantity' => 3,
            'note' => 'Items returned to warehouse',
        ]);

        $response->assertRedirect();

        $purchase->refresh();
        $this->assertSame(13.0, (float) $purchase->quantity);

        $rebate = Rebate::first();
        $this->assertNotNull($rebate);
        $this->assertSame($purchase->id, $rebate->purchase_id);
        $this->assertSame(3, (int) $rebate->quantity);
    }

    public function test_non_admin_cannot_access_rebate_routes(): void
    {
        $business = $this->createBusiness();
        $admin = $this->createAdmin($business);
        $salesperson = $this->createSalesperson($business);
        $purchase = $this->createPurchase($business, $admin, 8, 1800.00);

        $this->actingAs($salesperson)
            ->get(route('rebates.index'))
            ->assertForbidden();

        $this->actingAs($salesperson)
            ->post(route('rebates.store'), [
                'purchase_id' => $purchase->id,
                'quantity' => 2,
            ])
            ->assertForbidden();
    }

    public function test_rebate_records_store_unit_purchase_price_and_total_cost_correctly(): void
    {
        $business = $this->createBusiness();
        $admin = $this->createAdmin($business);
        $purchase = $this->createPurchase($business, $admin, 5, 1234.50);

        $this->actingAs($admin)->post(route('rebates.store'), [
            'purchase_id' => $purchase->id,
            'quantity' => 2,
            'note' => 'Correction entry',
        ])->assertRedirect();

        $rebate = Rebate::firstOrFail();

        $this->assertSame($admin->id, $rebate->created_by);
        $this->assertSame(1234.50, (float) $rebate->unit_purchase_price);
        $this->assertSame(2469.00, (float) $rebate->total_cost);

        $this->assertDatabaseHas('rebates', [
            'id' => $rebate->id,
            'purchase_id' => $purchase->id,
            'quantity' => 2,
            'created_by' => $admin->id,
        ]);
    }

    private function createBusiness(): Business
    {
        return Business::create([
            'name' => 'Test Business ' . Str::random(5),
            'slug' => 'test-business-' . Str::lower(Str::random(8)),
            'status' => 'active',
        ]);
    }

    private function createAdmin(Business $business): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'business_id' => $business->id,
        ]);
    }

    private function createSalesperson(Business $business): User
    {
        return User::factory()->create([
            'role' => 'salesperson',
            'status' => 'active',
            'business_id' => $business->id,
        ]);
    }

    private function createPurchase(Business $business, User $admin, float $quantity, float $price): Purchase
    {
        $product = Product::create([
            'business_id' => $business->id,
            'name' => 'Palm Oil Drum',
            'unit_type' => 'Customize',
            'current_stock' => $quantity,
            'low_stock' => 5,
            'low_stock_threshold' => 10,
            'description' => 'Test product',
        ]);

        return Purchase::create([
            'business_id' => $business->id,
            'product_id' => $product->id,
            'user_id' => $admin->id,
            'supplier_name' => 'Test Supplier',
            'supplier_phone' => '08000000000',
            'quantity' => $quantity,
            'purchase_price' => $price,
            'total_cost' => $quantity * $price,
            'selling_price' => 0,
            'seller_profit' => 0,
            'purchase_date' => now()->toDateString(),
            'notes' => 'Test purchase',
        ]);
    }
}

