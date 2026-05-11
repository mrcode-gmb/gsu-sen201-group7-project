<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductAssignment;
use App\Models\User;
use App\Models\Purchase;
use App\Models\SalePrice;
use App\Traits\BusinessScoped;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductAssignmentController extends Controller
{
    use BusinessScoped;

    /**
     * Display a listing of product assignments
     */
    public function index(Request $request)
    {
        $query = $this->scopeToCurrentBusiness(ProductAssignment::class)->with(['user', 'purchase.product', 'collectionHistories', 'salePrices']);

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active');
            } elseif ($request->status === 'completed') {
                $query->where('status', 'completed');
            } elseif ($request->status === 'overdue') {
                $query->where('due_date', '<', now())->where('status', 'active');
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Handle PDF export
        if ($request->has('export') && $request->export === 'pdf') {
            $filters = [
                'user_id' => $request->user_id,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ];

            $assignments = $query->get();

            $pdf = \PDF::loadView('exports.assignments-pdf', [
                'assignments' => $assignments,
                'filters' => $filters
            ])->setPaper('a4', 'landscape');

            return $pdf->download('assignments-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        // Calculate summary statistics
        $totalAssignments = $assignments->count();
        $activeAssignments = $assignments->where('status', 'assigned')->count() + $assignments->where('status', 'in_progress')->count();
        $completedAssignments = $assignments->where('status', 'completed')->count();
        $overdueAssignments = $assignments->filter(function($assignment) {
            return $assignment->isOverdue();
        })->count();

        $totalAssignedQty = $assignments->sum('assigned_quantity');
        $totalSoldQty = $assignments->sum('sold_quantity');
        $totalCollectedQty = $assignments->sum('total_collected_quantity');
        $totalRemainingQty = $assignments->sum('remaining_quantity');

        $totalExpectedRevenue = $assignments->sum(function($assignment) {
            return $assignment->expected_selling_price * $assignment->assigned_quantity;
        });
        $totalActualSales = $assignments->sum('actual_total_sales');
        $totalExpectedProfit = $assignments->sum('expected_profit');
        $totalActualProfit = $assignments->sum('actual_profit');

        // Calculate total cost of assigned products (purchase price * assigned quantity)
        $totalCost = $assignments->sum(function($assignment) {
            return $assignment->purchase->purchase_price * $assignment->assigned_quantity;
        });

        // Get users for filter dropdown - only from current business
        $users = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get the current URL with all query parameters for export
        $exportUrl = url()->current() . '?' . http_build_query(array_merge(
            $request->query(),
            ['export' => 'pdf']
        ));

        return view('assignments.index', compact(
            'assignments',
            'users',
            'exportUrl',
            'totalAssignments',
            'activeAssignments',
            'completedAssignments',
            'overdueAssignments',
            'totalAssignedQty',
            'totalSoldQty',
            'totalCollectedQty',
            'totalRemainingQty',
            'totalExpectedRevenue',
            'totalActualSales',
            'totalExpectedProfit',
            'totalActualProfit',
            'totalCost'
        ));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        $staff = $this->scopeToCurrentBusiness(User::class)
            ->where('role', 'salesperson')
            ->where('status', 'active')
            ->get();
        $products = $this->scopeToCurrentBusiness(Purchase::class)
            ->with('product')
            ->where('quantity', '>', 0)
            ->get();

        return view('assignments.create', compact('staff', 'products'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'purchase_id' => 'required|exists:purchases,id',
            'assigned_quantity' => 'required|numeric|min:1',
            'expected_selling_price' => 'required|numeric|min:10',
            'commission_rate' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'customer_types' => 'nullable|array',
            'customer_types.*' => 'required|string|in:Dealer,Wholesaler 1,Wholesaler 2,Wholesaler 3,Consumer',
            'sale_prices' => 'nullable|array',
            'sale_prices.*' => 'required|numeric|min:0.01',
        ]);

        // Validate that both arrays have the same length and no empty combinations
        if ($request->customer_types && $request->sale_prices) {
            if (count($request->customer_types) !== count($request->sale_prices)) {
                return back()->withErrors(['sale_prices' => 'Customer types and prices must match.']);
            }

            foreach ($request->customer_types as $index => $type) {
                if (empty($type) && !empty($request->sale_prices[$index])) {
                    return back()->withErrors(['customer_types' => 'Please select customer type for all price entries.']);
                }
                if (!empty($type) && empty($request->sale_prices[$index])) {
                    return back()->withErrors(['sale_prices' => 'Please enter price for all customer types.']);
                }
            }
        }

        // Check if enough quantity is available in the purchase
        $purchase = $this->scopeToCurrentBusiness(Purchase::class)->findOrFail($request->purchase_id);

        // Calculate already assigned quantity
        $assignedQuantity = $this->scopeToCurrentBusiness(ProductAssignment::class)
            ->where('purchase_id', $request->purchase_id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->sum('assigned_quantity');

        // $availableQuantity = $purchase->quantity - $assignedQuantity;

        if ($request->assigned_quantity > $purchase->quantity) {
            return back()->withErrors(['assigned_quantity' => 'Not enough quantity available. Available: ' .  $purchase->quantity]);
        }

        // Calculate commission amount
        $totalExpectedSales = $request->assigned_quantity * $request->expected_selling_price;


        // Create the assignment with business_id
        $data = $this->addBusinessId([
            'user_id' => $request->user_id,
            'purchase_id' => $request->purchase_id,
            'assigned_quantity' => $request->assigned_quantity,
            'expected_selling_price' => $request->expected_selling_price,
            'commission_rate' => $request->commission_rate,
            'commission_amount' => $request->commission_rate,
            'assigned_date' => now(),
            'due_date' => $request->due_date,
            'status' => 'assigned',
            'notes' => $request->notes,
        ]);

        $assignment = ProductAssignment::create($data);

        // Create sale prices if provided
        if ($request->customer_types && $request->sale_prices) {
            $salePrices = [];
            foreach ($request->customer_types as $index => $customerType) {
                if (!empty($customerType) && !empty($request->sale_prices[$index])) {
                    $salePrices[] = [
                        'product_assignment_id' => $assignment->id,
                        'customer_type' => $customerType,
                        'sale_price' => $request->sale_prices[$index],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($salePrices)) {
                \App\Models\SalePrice::insert($salePrices);
            }
        }

        // Update inventory
        $purchase->decrement('quantity', $request->assigned_quantity);
        $purchase->product->decrement('current_stock', $request->assigned_quantity);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Product assigned successfully!');
    }
    public function show(ProductAssignment $assignment)
    {
        $assignment->load(['user', 'purchase.product', 'sales', 'collectionHistories.collectedBy', 'salePrices']);
        return view('assignments.show', compact('assignment'));
    }

    /**
     * Show assignments for the authenticated staff member
     */
    public function myAssignments()
    {
        $user = auth()->user();
        $assignments = $user->assignments()->with(['purchase.product', 'sales', 'salePrices'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assignments.my-assignments', compact('assignments'));
    }

    /**
     * Collect returned products in batches
     */
    public function collectReturn(Request $request, ProductAssignment $assignment)
    {
        $request->validate([
            'returned_quantity' => 'nullable|numeric|min:0.01|required_without:collected_quantity',
            'collected_quantity' => 'nullable|numeric|min:0.01|required_without:returned_quantity',
            'notes' => 'nullable|string|max:1000',
        ]);

        $collectedQuantity = (float) ($request->input('returned_quantity') ?? $request->input('collected_quantity'));
        $lockedAssignment = null;
        $newRemainingQuantity = 0.0;

        DB::transaction(function () use ($assignment, $request, $collectedQuantity, &$lockedAssignment, &$newRemainingQuantity) {
            $lockedAssignment = $this->scopeToCurrentBusiness(ProductAssignment::class)
                ->whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedPurchase = $this->scopeToCurrentBusiness(Purchase::class)
                ->whereKey($lockedAssignment->purchase_id)
                ->lockForUpdate()
                ->firstOrFail();

            $totalCollected = $lockedAssignment->collectionHistories()->sum('collected_quantity');
            $remainingQuantity = max(
                0,
                (float) $lockedAssignment->assigned_quantity - (float) $lockedAssignment->sold_quantity - (float) $totalCollected
            );

            if ($collectedQuantity > $remainingQuantity) {
                throw ValidationException::withMessages([
                    'returned_quantity' => "Cannot collect more than remaining quantity ({$remainingQuantity})",
                ]);
            }

            $newRemainingQuantity = $remainingQuantity - $collectedQuantity;

            $lockedAssignment->collectionHistories()->create([
                'collected_by' => auth()->id(),
                'collected_quantity' => $collectedQuantity,
                'remaining_quantity_before' => $remainingQuantity,
                'remaining_quantity_after' => $newRemainingQuantity,
                'notes' => $request->notes,
                'collected_at' => now(),
            ]);

            $lockedPurchase->increment('quantity', $collectedQuantity);
        });

        if (!$lockedAssignment) {
            return back()->withErrors(['returned_quantity' => 'Unable to process return quantity.']);
        }

        if ($newRemainingQuantity <= 0) {
            $lockedAssignment->update([
                'status' => 'completed',
                'returned_date' => now(),
            ]);
            $message = 'All remaining products collected successfully! Assignment marked as completed.';
        } else {
            $message = "Collected {$collectedQuantity} units successfully! {$newRemainingQuantity} units remaining.";
        }

        return redirect()->route('admin.assignments.show', $lockedAssignment)
            ->with('success', $message);
    }
}
