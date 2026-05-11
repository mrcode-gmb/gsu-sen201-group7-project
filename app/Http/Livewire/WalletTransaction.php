<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletTransaction extends Component
{
    public $wallet;
    public $amount;
    public $description = '';
    public $transactionType;
    public $showModal = false;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
    ];

    public function mount(Wallet $wallet, $type)
    {
        $this->wallet = $wallet;
        $this->transactionType = $type;
    }

    public function processTransaction()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->transactionType === 'deposit') {
                $transaction = $this->wallet->credit(
                    $this->amount,
                    $this->description ?: 'Deposit to wallet'
                );
                $message = 'Funds added successfully!';
            } else {
                $transaction = $this->wallet->debit(
                    $this->amount,
                    $this->description ?: 'Withdrawal from wallet'
                );
                $message = 'Withdrawal successful!';
            }

            DB::commit();
            
            $this->reset(['amount', 'description']);
            $this->emit('transactionCompleted', $message);
            $this->showModal = false;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('transaction', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.wallet-transaction');
    }
}
