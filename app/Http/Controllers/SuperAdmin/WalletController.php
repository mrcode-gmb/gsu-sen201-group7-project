<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function createDeposit(\App\Models\Business $business)
    {
        return view('super-admin.wallets.deposit', compact('business'));
    }

    public function storeDeposit(Request $request, \App\Models\Business $business)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $business->wallet->credit($request->amount, $request->description ?: 'Manual deposit by Super Admin', [], $business->id);
            
            return redirect()->route('super-admin.businesses.show', $business)->with('success', 'Funds added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createWithdrawal(\App\Models\Business $business)
    {
        return view('super-admin.wallets.withdraw', compact('business'));
    }

    public function storeWithdrawal(Request $request, \App\Models\Business $business)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $business->wallet->balance,
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $business->wallet->debit($request->amount, $request->description ?: 'Manual withdrawal by Super Admin', [], $business->id);
            return redirect()->route('super-admin.businesses.show', $business)->with('success', 'Withdrawal successful.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
