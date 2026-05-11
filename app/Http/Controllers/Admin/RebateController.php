<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Rebate;
use App\Traits\BusinessScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RebateController extends Controller
{
    use BusinessScoped;

    public function __construct()
    {
        $this->authorizeResource(Rebate::class, 'rebate');
    }

    /**
     * Display a listing of rebates.
     */
    public function index(Request $request)
    {
        $query = Rebate::query()
            ->with(['purchase.product', 'creator'])
            ->whereHas('purchase', function ($purchaseQuery) {
                $this->applyBusinessScope($purchaseQuery);
            });

        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->purchase_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $rebates = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $purchases = $this->scopeToCurrentBusiness(Purchase::class)
            ->with('product')
            ->orderByDesc('id')
            ->get();

        return view('rebates.index', compact('rebates', 'purchases'));
    }

    /**
     * Show the form for creating a new rebate.
     */
    public function create(Request $request)
    {
        $purchases = $this->scopeToCurrentBusiness(Purchase::class)
            ->with('product')
            ->orderByDesc('id')
            ->get();

        $selectedPurchaseId = $request->filled('purchase_id') ? (int) $request->purchase_id : null;

        if (!$purchases->contains('id', $selectedPurchaseId)) {
            $selectedPurchaseId = null;
        }

        return view('rebates.create', compact('purchases', 'selectedPurchaseId'));
    }

    /**
     * Store a newly created rebate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => [
                'required',
                'integer',
                Rule::exists('purchases', 'id')->where(function ($query) {
                    $query->where('business_id', auth()->user()->business_id);
                }),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ]);

        $rebate = DB::transaction(function () use ($validated) {
            $purchase = $this->scopeToCurrentBusiness(Purchase::class)
                ->whereKey($validated['purchase_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (int) $validated['quantity'];
            $unitPurchasePrice = (float) $purchase->purchase_price;
            $totalCost = round($quantity * $unitPurchasePrice, 2);

            $purchase->update([
                'quantity' => (float) $purchase->quantity + $quantity,
            ]);

            $rebate = Rebate::create([
                'purchase_id' => $purchase->id,
                'quantity' => $quantity,
                'unit_purchase_price' => $unitPurchasePrice,
                'total_cost' => $totalCost,
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->logRebateInPurchaseHistory(
                $purchase,
                $quantity,
                $unitPurchasePrice,
                $totalCost,
                $validated['note'] ?? null
            );

            return $rebate;
        });

        return redirect()->route('rebates.show', $rebate)->with('success', 'Rebate recorded successfully!');
    }

    /**
     * Display the specified rebate.
     */
    public function show(Rebate $rebate)
    {
        $rebate->load(['purchase.product', 'creator']);

        return view('rebates.show', compact('rebate'));
    }

    /**
     * Remove the specified rebate from storage.
     */
    public function destroy(Rebate $rebate)
    {
        DB::transaction(function () use ($rebate) {
            $purchase = $this->scopeToCurrentBusiness(Purchase::class)
                ->whereKey($rebate->purchase_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $purchase->quantity < (float) $rebate->quantity) {
                throw ValidationException::withMessages([
                    'rebate' => 'Cannot delete this rebate because stock would become negative.',
                ]);
            }

            $purchase->update([
                'quantity' => (float) $purchase->quantity - (float) $rebate->quantity,
            ]);

            $rebate->delete();
        });

        return redirect()->route('rebates.index')->with('success', 'Rebate deleted successfully and stock reverted.');
    }

    /**
     * Log rebate into purchase_histories if the table exists.
     */
    private function logRebateInPurchaseHistory(
        Purchase $purchase,
        int $quantity,
        float $unitPurchasePrice,
        float $totalCost,
        ?string $note = null
    ): void {
        if (!Schema::hasTable('purchase_histories')) {
            return;
        }

        $columns = Schema::getColumnListing('purchase_histories');
        $now = now();
        $payload = [];

        if (in_array('purchase_id', $columns, true)) {
            $payload['purchase_id'] = $purchase->id;
        }
        if (in_array('product_id', $columns, true)) {
            $payload['product_id'] = $purchase->product_id;
        }
        if (in_array('user_id', $columns, true)) {
            $payload['user_id'] = auth()->id();
        }
        if (in_array('business_id', $columns, true)) {
            $payload['business_id'] = auth()->user()->business_id;
        }
        if (in_array('supplier_name', $columns, true)) {
            $payload['supplier_name'] = $purchase->supplier_name ?: 'REBATE';
        }
        if (in_array('supplier_phone', $columns, true)) {
            $payload['supplier_phone'] = $purchase->supplier_phone;
        }
        if (in_array('quantity', $columns, true)) {
            $payload['quantity'] = $quantity;
        }
        if (in_array('qty_in', $columns, true)) {
            $payload['qty_in'] = $quantity;
        }
        if (in_array('purchase_price', $columns, true)) {
            $payload['purchase_price'] = $unitPurchasePrice;
        }
        if (in_array('total_cost', $columns, true)) {
            $payload['total_cost'] = $totalCost;
        }
        if (in_array('selling_price', $columns, true)) {
            $payload['selling_price'] = $purchase->selling_price;
        }
        if (in_array('seller_profit', $columns, true)) {
            $payload['seller_profit'] = $purchase->seller_profit;
        }
        if (in_array('purchase_date', $columns, true)) {
            $payload['purchase_date'] = $now->toDateString();
        }
        if (in_array('type', $columns, true)) {
            $payload['type'] = 'REBATE';
        }
        if (in_array('notes', $columns, true)) {
            $payload['notes'] = $note ? 'REBATE: ' . $note : 'REBATE';
        }
        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = $now;
        }
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = $now;
        }

        if (empty($payload)) {
            return;
        }

        // Purchase history logging is auxiliary for rebates; never block rebate creation on log schema mismatch.
        try {
            DB::table('purchase_histories')->insert($payload);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
