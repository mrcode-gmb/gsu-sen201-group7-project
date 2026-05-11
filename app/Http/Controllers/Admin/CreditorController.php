<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\CreditorTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\BusinessScoped;

class CreditorController extends Controller
{
    use BusinessScoped;

    public function index()
    {
        $user = auth()->user();
        $businessId = $this->getBusinessId();
        $business = Business::findOrFail($businessId);

        $creditorsQuery = $business->creditors();
        if ($user->isSalesperson()) {
            $creditorsQuery->where('user_id', $user->id);
        }

        $creditorIdsSubquery = (clone $creditorsQuery)->select('id');

        $summary = [
            'total_creditors' => (clone $creditorsQuery)->count(),
            'total_balance' => (clone $creditorsQuery)->sum('balance'),
            'total_credit' => CreditorTransaction::whereIn('creditor_id', $creditorIdsSubquery)
                ->where('type', 'debit')
                ->sum('amount'),
            'total_paid' => CreditorTransaction::whereIn('creditor_id', (clone $creditorsQuery)->select('id'))
                ->where('type', 'credit')
                ->sum('amount'),
        ];

        $creditors = $creditorsQuery->latest()->paginate(10);

        return view('admin.creditors.index', compact('creditors', 'summary'));
    }

    public function create()
    {
        return view('admin.creditors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $businessId = $this->getBusinessId();
        $business = \App\Models\Business::findOrFail($businessId);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $business->creditors()->create($data);

        return redirect()->route('admin.creditors.index')->with('success', 'Creditor created successfully.');
    }

    public function show(\App\Models\Creditor $creditor)
    {
        $this->authorize('view', $creditor);
        $transactions = $creditor->transactions()->latest()->paginate(10);
        $sales = $creditor->sales()->with('purchase.product', 'user')->latest()->paginate(10, ['*'], 'sales');

        $total_credit = $creditor->transactions()->where('type', 'debit')->sum('amount');
        $total_paid = $creditor->transactions()->where('type', 'credit')->sum('amount');

        return view('admin.creditors.show', compact('creditor', 'transactions', 'sales', 'total_credit', 'total_paid'));
    }

    public function recordPayment(Request $request, \App\Models\Creditor $creditor)
    {
        $this->authorize('update', $creditor);
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($creditor->balance < $request->amount) {
            return back()->withErrors('The amount is not grater than the current balance.');
        }

        DB::transaction(function () use ($request, $creditor) {
            $creditor->balance -= $request->amount;
            $creditor->save();

            // Credit wallet once; this also logs wallet transaction.
            $business = $this->getBusiness();
            $business->wallet->credit(
                $request->amount,
                $request->description ?? 'Creditor payment',
                ['creditor_id' => $creditor->id]
            );

            $creditor->transactions()->create([
                'type' => 'credit',
                'amount' => $request->amount,
                'description' => $request->description ?? 'Payment received',
                'running_balance' => $creditor->balance,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
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
    public function printTransaction(\App\Models\CreditorTransaction $transaction)
    {
        $this->authorize('view', $transaction->creditor);
        return view('admin.creditors.print-transaction', compact('transaction'));
    }

    public function print(\App\Models\Creditor $creditor)
    {
        $this->authorize('view', $creditor);
        $transactions = $creditor->transactions()->latest()->get();
        $sales = $creditor->sales()->with('purchase.product', 'user')->latest()->get();

        $total_credit = $creditor->transactions()->where('type', 'debit')->sum('amount');
        $total_paid = $creditor->transactions()->where('type', 'credit')->sum('amount');

        return view('admin.creditors.print', compact('creditor', 'transactions', 'sales', 'total_credit', 'total_paid'));
    }

    public function getBusinessId()
    {
        return auth()->user()->business_id;
    }
}
