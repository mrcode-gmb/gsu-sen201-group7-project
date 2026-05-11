<?php

namespace App\Http\Controllers;

use App\Models\AdjustmentProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Traits\BusinessScoped;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    use BusinessScoped;

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $this->scopeToCurrentBusiness(Purchase::class)
            ->with(['product.category', 'user']);

        if ($request->filled('unit_type')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('unit_type', $request->unit_type);
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->integer('category_id'));
            });
        }

        $products = $query->orderByDesc('updated_at')->get();

        if ($request->filled('stock_filter')) {
            $products = $products->filter(function (Purchase $product) use ($request) {
                $threshold = $product->product?->effective_low_stock_threshold ?? 10;

                return match ($request->stock_filter) {
                    'low' => (float) $product->quantity > 0 && (float) $product->quantity <= $threshold,
                    'out' => (float) $product->quantity <= 0,
                    'available' => (float) $product->quantity > 0,
                    default => true,
                };
            })->values();
        }

        $assignments = collect();
        $categories = $this->scopeToCurrentBusiness(Category::class)->orderBy('name')->get();
        $lowStockProducts = $user->isAdmin() ? $this->getLowStockProducts() : collect();

        return view('inventory.index', compact('products', 'assignments', 'categories', 'lowStockProducts'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = $this->scopeToCurrentBusiness(Category::class)->orderBy('name')->get();

        return view('inventory.create', compact('categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->productRules());
        $this->assertValidCategory($validated['category_id'] ?? null);

        $data = $this->addBusinessId([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'sku' => $this->normalizeSku($validated['sku']),
            'storage_location' => $validated['storage_location'] ?: null,
            'unit_type' => $validated['unit_type'],
            'current_stock' => 0,
            'low_stock' => $validated['low_stock_threshold'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
            'description' => $validated['description'] ?? null,
        ]);

        Product::create($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show($productId)
    {
        $product = $this->scopeToCurrentBusiness(Purchase::class)
            ->with(['product.category', 'sales.user', 'user'])
            ->findOrFail($productId);

        $stockMovements = collect();
        $warehouseStock = $this->scopeToCurrentBusiness(Purchase::class)
            ->where('product_id', $product->product_id)
            ->sum('quantity');

        $stockMovements->push([
            'type' => 'purchase',
            'date' => $product->purchase_date ?? null,
            'quantity' => $product->quantity ?? null,
            'description' => "Purchase from {$product->supplier_name}",
            'user' => $product->user->name ?? null,
            'created_at' => $product->created_at ?? null,
        ]);


        // Add sales as negative movements
        foreach ($product->sales as $sale) {
            $stockMovements->push([
                'type' => 'sale',
                'date' => $sale->sale_date,
                'quantity' => -$sale->quantity,
                'description' => 'Goods dispatch to ' . ($sale->customer_name ?: 'unspecified receiver'),
                'user' => $sale->user->name,
                'created_at' => $sale->created_at,
            ]);
        }
        $adjustmentRecord = AdjustmentProduct::with('purchase')
            ->where('purchase_id', $productId)
            ->orderByDesc('created_at')
            ->get();
        $stockMovements = $stockMovements->sortByDesc('created_at');

        return view('inventory.show', compact('product', 'stockMovements', 'adjustmentRecord', 'warehouseStock'));
    }


    /**
     * Show the form for editing the specified product
     */
    public function edit($productId)
    {
        $product = $this->scopeToCurrentBusiness(Product::class)
            ->with(['purchase.sales', 'category'])
            ->findOrFail($productId);
        $categories = $this->scopeToCurrentBusiness(Category::class)->orderBy('name')->get();

        return view('inventory.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $productId)
    {
        $product = $this->scopeToCurrentBusiness(Product::class)->findOrFail($productId);
        $validated = $request->validate($this->productRules($product));
        $this->assertValidCategory($validated['category_id'] ?? null);

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'sku' => $this->normalizeSku($validated['sku']),
            'storage_location' => $validated['storage_location'] ?: null,
            'unit_type' => $validated['unit_type'],
            'low_stock' => $validated['low_stock_threshold'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('inventory.edit', $product)->with('success', 'Inventory item updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        $product = $this->scopeToCurrentBusiness(Product::class)->with('purchase')->findOrFail($id);
        if ($product->purchase()->count() > 0) {
            return redirect()->route('inventory.index')
                ->withErrors(['error' => 'Cannot delete an inventory item with existing warehouse history.']);
        }

        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully!');
    }

    public function adjustStockDelete(AdjustmentProduct $adjustment)
    {
        $this->ensureBusinessOwnership($adjustment->purchase);

        if ($adjustment->purchase->quantity < $adjustment->adjustment) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete adjustment history while don`t have value in purchase.']);
        }
        $oldStock = $adjustment->purchase->quantity;
        $newStock = $oldStock - $adjustment->adjustment;

        $adjustment->purchase->update(['quantity' => $newStock]);
        $adjustment->delete();

        return redirect()->back()->with('success', 'Adjustment deleted successfully!');
    }

    /**
     * Adjust stock manually (admin only)
     */
    public function adjustStock(Request $request, Purchase $product)
    {
        $this->ensureBusinessOwnership($product);

        $request->validate([
            'adjustment' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $oldStock = $product->quantity;
        $newStock = $oldStock + $request->adjustment;

        if ($newStock < 0) {
            return back()->withErrors(['adjustment' => 'Stock adjustment would result in negative stock.']);
        }

        AdjustmentProduct::create([
            'purchase_id' => $product->id,
            'adjustment' => $request->adjustment,
            'reason' => $request->reason,
        ]);

        $product->update(['quantity' => $newStock]);

        return redirect()->route('inventory.show', $product)
            ->with('success', "Stock adjusted from {$oldStock} to {$newStock}. Reason: {$request->reason}");
    }

    private function productRules(?Product $product = null): array
    {
        $businessId = $this->getBusinessId();
        $uniqueSku = Rule::unique('products', 'sku');

        if ($businessId) {
            $uniqueSku->where(fn ($query) => $query->where('business_id', $businessId));
        } else {
            $uniqueSku->whereNull('business_id');
        }

        if ($product) {
            $uniqueSku->ignore($product->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'sku' => ['required', 'string', 'max:64', $uniqueSku],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'unit_type' => ['required', 'in:Customize,Uncustomize'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function assertValidCategory(?int $categoryId): void
    {
        if (!$categoryId) {
            return;
        }

        $this->scopeToCurrentBusiness(Category::class)->whereKey($categoryId)->firstOrFail();
    }

    private function normalizeSku(?string $sku): ?string
    {
        $normalized = strtoupper(trim((string) $sku));

        return $normalized !== '' ? $normalized : null;
    }

    private function getLowStockProducts()
    {
        return $this->scopeToCurrentBusiness(Product::class)
            ->with('category')
            ->withSum('purchase as warehouse_stock', 'quantity')
            ->orderBy('name')
            ->get()
            ->filter(function (Product $product) {
                return (float) ($product->warehouse_stock ?? 0) <= $product->effective_low_stock_threshold;
            })
            ->sortBy(fn (Product $product) => (float) ($product->warehouse_stock ?? 0))
            ->values();
    }
}
