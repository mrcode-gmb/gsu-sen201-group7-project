<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\BusinessScoped;
use App\Exports\SalesPdfExport;
use App\Models\ProductAssignment;
use App\Exports\SalesReportExport;
use App\Models\Wallet;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    use BusinessScoped;
    
    /**
     * Display a listing of sales
     */
    public function index(Request $request)
    {
        $query = $this->scopeToCurrentBusiness(Sale::class)->with(['purchase.product', 'user', 'assignment']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by salesperson (admin only)
        if (auth()->user()->isAdmin() && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // If salesperson, only show their sales
        if (auth()->user()->isSalesperson()) {
            $query->where('user_id', auth()->id());
        }

        // Get payment summary before pagination
        $paymentSummary = (clone $query)
            ->selectRaw('payment_type, count(*) as count, sum(total_amount) as total')
            ->groupBy('payment_type')
            ->get();

        $totalSales = (clone $query)->sum('total_amount');
        $totalProfit = (clone $query)->sum('profit');

        // Apply sorting and pagination
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $sales = $query->orderBy($sortBy, $sortDirection)->get();

        $salespeople = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->get();
        $paymentTypes = ['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'pos' => 'POS', 'mobile_money' => 'Mobile Money', 'credit' => 'Credit'];

        if ($request->has('export')) {
            if ($request->export === 'pdf') {
                $filters = [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'payment_type' => $request->payment_type,
                    'include_salesperson' => auth()->user()->isAdmin(),
                    'include_customer' => true
                ];
                
                $pdf = PDF::loadView('exports.sales-pdf', [
                    'sales' => $sales,
                    'totalSales' => $totalSales,
                    'totalProfit' => $totalProfit,
                    'paymentSummary' => $paymentSummary,
                    'paymentTypes' => $paymentTypes,
                    'filters' => $filters
                ])->setPaper('a4', 'landscape');
                
                return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
            } else {
                return Excel::download(
                    new SalesReportExport($sales->get()), 
                    'sales-report-' . now()->format('Y-m-d') . '.xlsx'
                );
            }
        }

        $totalCommition = (clone $query)->sum('seller_profit_per_unit');
        // Get the current URL with all query parameters
        $exportUrl = url()->current() . '?' . http_build_query(array_merge(
            $request->query(),
            ['export' => 'pdf']
        ));
        
        return view('sales.index', compact(
            'sales',
            'salespeople',
            'paymentSummary',
            'totalSales',
            'totalCommition',
            'totalProfit',
            'paymentTypes',
            'exportUrl'
        ));
    }

    /**
     * Generate sales report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Generate sales report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $query = Sale::with(['purchase.product', 'user']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by salesperson (admin only)
        if (auth()->user()->isAdmin() && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif (auth()->user()->isSalesperson()) {
            $query->where('user_id', auth()->id());
        }

        // Get payment summary
        $paymentSummary = (clone $query)
            ->selectRaw('payment_type, count(*) as count, sum(total_amount) as total')
            ->groupBy('payment_type')
            ->get();

        $totalSales = (clone $query)->sum('total_amount');
        $totalProfit = (clone $query)->sum('profit');

        // Get all sales for the report
        $sales = $query->orderBy('sale_date', 'desc')->get();

        // If export is requested
        if ($request->has('export')) {
            return Excel::download(
                new SalesReportExport($sales, $request->all()), 
                'sales-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        $salespeople = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->get();
        $paymentTypes = ['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'pos' => 'POS', 'mobile_money' => 'Mobile Money', 'credit' => 'Credit'];

        return view('sales.report', compact(
            'sales',
            'salespeople',
            'paymentSummary',
            'totalSales',
            'totalProfit',
            'paymentTypes'
        ));
    }

    public function getBusinessId()
    {
        return auth()->user()->business_id;
    }
    public function getBusiness()
    {
        $user = auth()->user();
        if (!$user) {
            // This case should ideally be handled by auth middleware
            abort(401, 'Unauthenticated.');
        }

        $business = $user->business;
        if (!$business) {
            // This user is not associated with any business
            abort(403, 'No business is associated with your account.');
        }

        return $business;
    }
    
    /**
     * Export sales report to Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $query = Sale::with(['purchase.product', 'user']);

        // Apply the same filters as the report method
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }
        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }
        if (auth()->user()->isAdmin() && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif (auth()->user()->isSalesperson()) {
            $query->where('user_id', auth()->id());
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        return Excel::download(
            new SalesReportExport($sales, $request->all()),
            'sales-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function mySale(Request $request)
    {
        $query = Sale::with(['purchase.product', 'user', 'assignment']);
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        // If salesperson, only show their sales
        $query->where('user_id', auth()->id());

        $sales = $query->orderBy('created_at', 'desc')->get();
        $salespeople = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->get();

        return view('sales.index', compact('sales', 'salespeople'));
    }


    /**
     * Show the form for creating a new sale
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        $business = $this->getBusiness();
        $creditors = $user->isAdmin()
            ? $business->creditors()->orderBy('name')->get()
            : $business->creditors()->where('user_id', $user->id)->orderBy('name')->get();

        $products = $this->scopeToCurrentBusiness(Purchase::class)
            ->with('product.category')
            ->where('quantity', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        $assignments = collect();
        
        return view('sales.create', compact('products', 'assignments', 'creditors'));
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:purchases,id',
            'products.*.assignment_id' => 'nullable|exists:product_assignments,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.selling_price' => 'required|numeric|min:0.01',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_type' => 'required|in:cash,bank_transfer,pos,mobile_money,credit',
            'creditor_id' => 'nullable|required_if:payment_type,credit|exists:creditors,id',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $totalSaleAmount = 0;
        $saleIds = [];

        try {
            DB::transaction(function () use ($validated, $user, &$totalSaleAmount, &$saleIds) {
                foreach ($validated['products'] as $productData) {
                    $assignment = null;
                    $assignmentId = $productData['assignment_id'] ?? null;

                    if ($assignmentId) {
                        if (!$user->isSalesperson()) {
                            throw new \Exception('Assigned dispatches can only be recorded by a salesperson.');
                        }

                        $assignment = $this->scopeToCurrentBusiness(ProductAssignment::class)
                            ->where('id', $assignmentId)
                            ->where('user_id', $user->id)
                            ->where('purchase_id', $productData['product_id'])
                            ->whereIn('status', ['assigned', 'in_progress'])
                            ->lockForUpdate()
                            ->first();

                        if (!$assignment || (float) $assignment->remaining_quantity < (float) $productData['quantity']) {
                            throw new \Exception('Invalid assignment or insufficient quantity for a product.');
                        }
                    }

                    $product = $this->scopeToCurrentBusiness(Purchase::class)
                        ->with('product')
                        ->whereKey($productData['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (!$assignment && (float) $product->quantity < (float) $productData['quantity']) {
                        throw new \Exception('Insufficient stock for a product.');
                    }

                    if ($productData['selling_price'] < $product->purchase_price) {
                        throw new \Exception('Selling price cannot be less than purchase price for a product.');
                    }

                    $totalAmount = $productData['selling_price'] * $productData['quantity'];
                    $totalCost = $product->purchase_price * $productData['quantity'];
                    $profit = $totalAmount - $totalCost;

                    $seller_profit_per_unit = 0;
                    if ($assignment && isset($assignment->commission_rate)) {
                        $seller_profit_per_unit = $assignment->commission_rate * $productData['quantity'];
                    }
                    $net_profit_per_unit = $profit - $seller_profit_per_unit;

                    $sale = Sale::create([
                        'business_id' => $this->getBusinessId(),
                        'purchase_id' => $productData['product_id'],
                        'user_id' => $user->id,
                        'unique_id' => 'SALE-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd'),
                        'assignment_id' => $assignment?->id,
                        'quantity' => $productData['quantity'],
                        'selling_price_per_unit' => $productData['selling_price'],
                        'cost_price_per_unit' => $product->purchase_price,
                        'total_amount' => $totalAmount,
                        'total_cost' => $totalCost,
                        'profit' => $profit,
                        'seller_profit_per_unit' => $seller_profit_per_unit,
                        'net_profit_per_unit' => $net_profit_per_unit,
                        'customer_name' => $validated['customer_name'],
                        'customer_phone' => $validated['customer_phone'],
                        'payment_type' => $validated['payment_type'],
                        'sale_status' => 'completed',
                        'sale_date' => now(),
                        'notes' => $validated['notes'],
                        'creditor_id' => $validated['payment_type'] === 'credit' ? $validated['creditor_id'] : null,
                        'amount_paid' => $validated['amount_paid'] ?? 0,
                    ]);

                    $saleIds[] = $sale->id;
                    $totalSaleAmount += $totalAmount;

                    if ($assignment) {
                        $assignment->increment('sold_quantity', $productData['quantity']);
                        $assignment->increment('actual_total_sales', $totalAmount);
                    } else {
                        $product->decrement('quantity', $productData['quantity']);
                    }
                }

                // Handle wallet and creditor logic outside the loop
                $business = $this->getBusiness();
                if ($validated['payment_type'] !== 'credit') {
                    $business->wallet->credit($totalSaleAmount, 'Payment for multiple sales');
                } else {
                    if ($validated['amount_paid'] > 0) {
                        $business->wallet->credit($validated['amount_paid'], 'Partial payment for multiple credit sales');
                    }
                    $creditor = \App\Models\Creditor::find($validated['creditor_id']);
                    $creditAmount = $totalSaleAmount - ($validated['amount_paid'] ?? 0);
                    if ($creditAmount > 0) {
                        $creditor->balance += $creditAmount;
                        $creditor->save();
                        $creditor->transactions()->create([
                            'type' => 'debit',
                            'amount' => $creditAmount,
                            'description' => 'Multiple sales on credit',
                            'running_balance' => $creditor->balance,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('sales.success', ['sale_ids' => implode(',', $saleIds)]);
    }

    public function success(Request $request)
    {
        if (!$request->has('sale_ids')) {
            return redirect()->route('sales.index')->with('error', 'No sales to display.');
        }

        $saleIds = explode(',', $request->input('sale_ids'));
        $sales = Sale::with(['purchase.product', 'user', 'business'])->whereIn('id', $saleIds)->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'Could not find the recorded sales.');
        }

        return view('sales.success', compact('sales'));
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale)
    {
        
        // Check if user can view this sale
        // if (auth()->user()->isSalesperson() && $sale->user_id !== auth()->id()) {
        //     abort(403, 'Unauthorized access.');
        // }

        $sale->load(['purchase.product', 'user']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Print receipt for a sale
     */
    // public function printReceipt(Sale $sale)
    // {
    //     $sale->load(['purchase.product', 'user']);
        
    //     // For thermal printer, we'll use a custom view
    //     $pdf = PDF::loadView('sales.print-receipt', compact('sale'))
    //         ->setPaper([0, 0, 226.77, 841.89], 'portrait') // 80mm width in points (80mm = 226.77pt)
    //         ->setOption('margin-top', 0)
    //         ->setOption('margin-bottom', 0)
    //         ->setOption('margin-left', 0)
    //         ->setOption('margin-right', 0);
            
    //     return $pdf->stream("receipt-{$sale->id}.pdf");
    // }

    // public function printReceipt(Sale $sale)
    // {
    //     $sale->load(['purchase.product', 'user', 'business']);
        
    //     // For thermal printer, we'll use a custom view
    //     $pdf = PDF::loadView('sales.print-receipt', compact('sale'))
    //         ->setPaper([0, 0, 226.77, 841.89], 'portrait') // 80mm width in points (80mm = 226.77pt)
    //         ->setOption('margin-top', 2)
    //         ->setOption('margin-bottom', 2)
    //         ->setOption('margin-left', 2)
    //         ->setOption('margin-right', 2);
            
    //     return $pdf->stream("receipt-{$sale->id}.pdf");
    // }

    public function printReceipt(Sale $sale)
{
    $sale->load(['purchase.product', 'user', 'business']);
    
    $pdf = PDF::loadView('sales.print-receipt', compact('sale'))
        ->setPaper([0, 0, 226.77, 841.89], 'portrait')
        ->setOption('margin-top', 2)
        ->setOption('margin-bottom', 2)
        ->setOption('margin-left', 2)
        ->setOption('margin-right', 2);
        
    return $pdf->stream("receipt-{$sale->id}.pdf");
}

public function printMultipleReceipts(Request $request)
{
    // Debug: Log the incoming request data
    \Log::info('Print multiple receipts request:', $request->all());
    
    if (!$request->has('sale_ids') || empty($request->sale_ids)) {
        abort(400, 'No sale IDs provided');
    }

    $saleIds = explode(',', $request->input('sale_ids'));
    
    // Validate that sale_ids are numeric
    if (empty(array_filter($saleIds, 'is_numeric'))) {
        abort(400, 'Invalid sale IDs provided');
    }

    // Load all sales with their relationships
    $sales = Sale::with(['purchase.product', 'user', 'business'])
        ->whereIn('id', $saleIds)
        ->orderBy('created_at')
        ->get();

    if ($sales->isEmpty()) {
        abort(404, 'No sales found with the provided IDs');
    }

    // Use the first sale's business info for the receipt header
    $business = $sales->first()->business;

    $pdf = PDF::loadView('sales.print-multiple-receipts', compact('sales', 'business'))
        ->setPaper([0, 0, 226.77, 841.89], 'portrait')
        ->setOption('margin-top', 2)
        ->setOption('margin-bottom', 2)
        ->setOption('margin-left', 2)
        ->setOption('margin-right', 2);

    return $pdf->stream("receipt-multiple-{$sales->first()->id}-to-{$sales->last()->id}.pdf");
}
/**
     * Show the form for editing the specified sale
     */
    public function edit(Sale $sale)
    {
        // Only admin can edit sales
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can edit sales.');
        }

        // Admin can sell from any available inventory
        $products = $this->scopeToCurrentBusiness(Purchase::class)
            ->with('product.category')
            ->where('quantity', '>', 0)
            ->orderByDesc('updated_at')
            ->get();
        $assignments = collect(); // Empty collection for admin
        $sale->load(['purchase.product', 'user']);
        return view('sales.edit', compact('sale', "products"));
    }

    /**
     * Display the sales for the currently logged-in salesperson
     */
    public function mySales(Request $request)
    {
        // Ensure only salesperson can access this
        if (!auth()->user()->isSalesperson()) {
            abort(403, 'This action is unauthorized.');
        }

        
        $query = Sale::with(['purchase.product', 'assignment'])
            ->where('user_id', auth()->id())
            ->orderBy('sale_date', 'desc');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        // Filter by payment method
        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        // Create a fresh query for payment summary to avoid any lingering order by clauses
        $paymentSummary = Sale::query()
            ->where('user_id', auth()->id())
            ->when($request->filled('start_date'), function($q) use ($request) {
                $q->whereDate('sale_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function($q) use ($request) {
                $q->whereDate('sale_date', '<=', $request->end_date);
            })
            ->when($request->filled('payment_type') && $request->payment_type !== 'all', function($q) use ($request) {
                $q->where('payment_type', $request->payment_type);
            })
            ->selectRaw('payment_type, count(*) as count, sum(total_amount) as total')
            ->groupBy('payment_type')
            ->get();

        // Get total sales
        $totalSales = $query->sum('total_amount');
        $totalCommition = $query->sum('seller_profit_per_unit') * $query->sum('quantity');
        
        // Get paginated results with ordering
        $sales = $query->where("user_id", auth()->id())->orderBy('id', 'desc')->get();

        return view('sales.my-sales', [
            'sales' => $sales,
            'paymentSummary' => $paymentSummary,
            'totalSales' => $totalSales,
            'totalCommition' => $totalCommition,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_type' => $request->payment_type,
        ]);
    }

    /**
     * Update the specified sale
     */
    public function update(Request $request, Sale $sale)
    {
        // Only admin can update sales
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can update sales.');
        }

        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validatedQuantity = (float) $request->quantity;

        DB::transaction(function () use ($sale, $request, $validatedQuantity) {
            $lockedSale = $this->scopeToCurrentBusiness(Sale::class)
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $purchase = $this->scopeToCurrentBusiness(Purchase::class)
                ->whereKey($lockedSale->purchase_id)
                ->lockForUpdate()
                ->firstOrFail();

            $assignment = null;
            if ($lockedSale->assignment_id) {
                $assignment = $this->scopeToCurrentBusiness(ProductAssignment::class)
                    ->whereKey($lockedSale->assignment_id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $oldQuantity = (float) $lockedSale->quantity;
            $sellingPricePerUnit = (float) $lockedSale->selling_price_per_unit;
            $costPricePerUnit = (float) $purchase->purchase_price;
            $totalAmount = $sellingPricePerUnit * $validatedQuantity;
            $totalCost = $costPricePerUnit * $validatedQuantity;
            $profit = $totalAmount - $totalCost;

            if ($assignment) {
                $totalCollected = $assignment->collectionHistories()->sum('collected_quantity');
                $revertedSoldQty = max(0, (float) $assignment->sold_quantity - $oldQuantity);
                $revertedActualSales = max(0, (float) $assignment->actual_total_sales - (float) $lockedSale->total_amount);
                $availableAfterRevert = max(
                    0,
                    (float) $assignment->assigned_quantity - $revertedSoldQty - (float) $totalCollected
                );

                if ($validatedQuantity > $availableAfterRevert) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => "Insufficient assignment stock available. Maximum: {$availableAfterRevert}",
                    ]);
                }

                $sellerProfit = (float) $assignment->commission_rate * $validatedQuantity;
                $assignment->update([
                    'sold_quantity' => $revertedSoldQty + $validatedQuantity,
                    'actual_total_sales' => $revertedActualSales + $totalAmount,
                ]);
            } else {
                $availableAfterRevert = (float) $purchase->quantity + $oldQuantity;
                if ($validatedQuantity > $availableAfterRevert) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => "Insufficient stock available. Maximum: {$availableAfterRevert}",
                    ]);
                }

                $sellerProfit = 0;
                $purchase->update([
                    'quantity' => $availableAfterRevert - $validatedQuantity,
                ]);
            }

            $lockedSale->update([
                'quantity' => $validatedQuantity,
                'cost_price_per_unit' => $costPricePerUnit,
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'profit' => $profit,
                'seller_profit_per_unit' => $sellerProfit,
                'net_profit_per_unit' => $profit - $sellerProfit,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    /**
     * Remove the specified sale
     */
    public function destroy(Sale $sale)
    {
        // Only admin can delete sales
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete sales.');
        }

        DB::transaction(function () use ($sale) {
            $lockedSale = $this->scopeToCurrentBusiness(Sale::class)
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            $purchase = $this->scopeToCurrentBusiness(Purchase::class)
                ->whereKey($lockedSale->purchase_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSale->assignment_id) {
                $assignment = $this->scopeToCurrentBusiness(ProductAssignment::class)
                    ->whereKey($lockedSale->assignment_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $assignment->update([
                    'sold_quantity' => max(0, (float) $assignment->sold_quantity - (float) $lockedSale->quantity),
                    'actual_total_sales' => max(0, (float) $assignment->actual_total_sales - (float) $lockedSale->total_amount),
                ]);
            } else {
                $purchase->increment('quantity', (float) $lockedSale->quantity);
            }

            $lockedSale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully!');
    }
}
