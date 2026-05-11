<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Expenses;
use App\Models\Purchase;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Traits\BusinessScoped;
use App\Models\ProductAssignment;
use App\Exports\ExpensesPdfExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpensesController extends Controller
{
    use BusinessScoped;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->scopeToCurrentBusiness(Expenses::class)->with(['user'])
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->when($request->supplier, fn($q) => $q->where('name', 'like', '%' . $request->supplier . '%'))
            ->latest();
        $query2 = $this->scopeToCurrentBusiness(ProductAssignment::class)->with(['user', 'purchase.product', 'collectionHistories', 'salePrices']);
        $quantities = $query2->where("status", "!=", "completed")->get()->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'user_id' => $assignment->user_id,
                'user_name' => $assignment->user->name,
                'product_id' => $assignment->product_id,
                'product_name' => $assignment->purchase->product->name,
                'assigned_quantity' => $assignment->assigned_quantity,
                'sold_quantity' => $assignment->sold_quantity,
                'returned_quantity' => $assignment->returned_quantity,
                'quantity' => $assignment->remaining_quantity,
                'expected_selling_price' => $assignment->expected_selling_price,
                'commission_rate' => $assignment->commission_rate,
                'due_date' => $assignment->due_date,
                'status' => $assignment->status

            ];
        });
        foreach ($quantities as $quantity) {
            if ($quantity['status'] != 'completed' && $quantity['quantity'] == 0) {
                // $quantity['status'] = 'completed';
                ProductAssignment::where("id", $quantity['id'])->update([
                    'status' => 'completed'
                ]);
            }
        }
        // Handle PDF export
        if ($request->has('export') && $request->export === 'pdf') {
            $filters = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'supplier' => $request->supplier
            ];

            $expenses = $query->get();

            $pdf = PDF::loadView('exports.expenses-pdf', [
                'expenses' => $expenses,
                'filters' => $filters
            ])->setPaper('a4', 'portrait');

            return $pdf->download('expenses-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $expenses = $query->paginate(10);

        // Get the current URL with all query parameters for export
        $exportUrl = url()->current() . '?' . http_build_query(array_merge(
            $request->query(),
            ['export' => 'pdf']
        ));

        return view('expenses.index', compact('expenses', 'exportUrl'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $products = Purchase::with("product")->get();
        return view('expenses.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $today = Carbon::today();
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $user_id = auth()->id();



        $todayProfit = $this->scopeToCurrentBusiness(Sale::class)
            ->whereDate('sale_date', $today)
            ->sum('net_profit_per_unit');

        // Create expense with business_id
        $data = $this->addBusinessId([
            'user_id' => $user_id,
            'name' => $request->name,
            'today_profit' => $todayProfit,
            'amount' => $request->amount,
            'today_net_profit' => $todayProfit - $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        DB::transaction(function () use ($data) {
            $expense = Expenses::create($data);
            $wallet = $this->lockBusinessWallet();

            $this->recordWalletMovement(
                $wallet,
                (float) $expense->amount,
                'debit',
                'Expense recorded: ' . $expense->name,
                [
                    'expense_id' => $expense->id,
                    'expense_name' => $expense->name,
                    'expense_date' => $expense->date,
                    'notes' => $expense->notes,
                ]
            );
        });

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expenses $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expenses $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expenses $expense)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $expense) {
            $lockedExpense = $this->scopeToCurrentBusiness(Expenses::class)
                ->whereKey($expense->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldAmount = (float) $lockedExpense->amount;
            $newAmount = (float) $request->amount;
            $amountDelta = $newAmount - $oldAmount;

            $lockedExpense->update([
                'name' => $request->name,
                'amount' => $newAmount,
                'today_net_profit' => (float) ($lockedExpense->today_profit ?? 0) - $newAmount,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            if ($amountDelta == 0.0) {
                return;
            }

            $wallet = $this->lockBusinessWallet();

            $this->recordWalletMovement(
                $wallet,
                abs($amountDelta),
                $amountDelta > 0 ? 'debit' : 'credit',
                $amountDelta > 0
                    ? 'Expense update adjustment (increase)'
                    : 'Expense update adjustment (refund)',
                [
                    'expense_id' => $lockedExpense->id,
                    'expense_name' => $lockedExpense->name,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'amount_delta' => $amountDelta,
                ]
            );
        });

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expenses $expense)
    {
        DB::transaction(function () use ($expense) {
            $lockedExpense = $this->scopeToCurrentBusiness(Expenses::class)
                ->whereKey($expense->id)
                ->lockForUpdate()
                ->firstOrFail();

            $wallet = $this->lockBusinessWallet();

            $this->recordWalletMovement(
                $wallet,
                (float) $lockedExpense->amount,
                'credit',
                'Expense deletion refund',
                [
                    'expense_id' => $lockedExpense->id,
                    'expense_name' => $lockedExpense->name,
                    'expense_date' => $lockedExpense->date,
                ]
            );

            $lockedExpense->delete();
        });

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }

    private function lockBusinessWallet(): Wallet
    {
        $businessId = $this->getBusinessId();

        $wallet = Wallet::where('business_id', $businessId)
            ->lockForUpdate()
            ->first();

        if ($wallet) {
            return $wallet;
        }

        $wallet = Wallet::create([
            'business_id' => $businessId,
            'balance' => 0,
            'currency' => 'NGN',
            'status' => 'active',
        ]);

        return Wallet::whereKey($wallet->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function recordWalletMovement(Wallet $wallet, float $amount, string $type, string $description, array $metadata = []): void
    {
        if ($amount <= 0) {
            return;
        }

        $wallet->transactions()->create([
            'wallet_id' => $wallet->id,
            'business_id' => $wallet->business_id,
            'amount' => $amount,
            'type' => $type,
            'reference' => 'EXPENSE-' . strtoupper(Str::random(10)),
            'description' => $description,
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        $wallet->balance += $type === 'credit' ? $amount : -$amount;
        $wallet->last_transaction_at = now();
        $wallet->save();
    }
}
