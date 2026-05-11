<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Livewire\WithPagination;

class WalletManager extends Component
{
    use WithPagination;

    public $wallet;

    public function mount(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function render()
    {
        $transactionsQuery = $this->wallet->transactions()->latest();
        $transactions = (clone $transactionsQuery)->paginate(5);
        $statsQuery = $this->wallet->transactions();

        $transactionStats = [
            'total_transactions' => (clone $statsQuery)->count(),
            'total_credits' => (clone $statsQuery)
                ->where('type', WalletTransaction::TYPE_CREDIT)
                ->sum('amount'),
            'total_debits' => (clone $statsQuery)
                ->where('type', WalletTransaction::TYPE_DEBIT)
                ->sum('amount'),
        ];

        return view('super-admin.livewire.wallet-manager', [
            'transactions' => $transactions,
            'transactionStats' => $transactionStats,
        ]);
    }
}
