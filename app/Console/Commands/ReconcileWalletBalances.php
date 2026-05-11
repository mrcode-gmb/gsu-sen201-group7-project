<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileWalletBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:reconcile-balances
                            {--business-id= : Reconcile only this business ID}
                            {--apply : Persist the reconciled balances}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile wallet balances against completed wallet transactions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Wallet::query()->with('business')->orderBy('business_id');

        if ($this->option('business-id')) {
            $query->where('business_id', $this->option('business-id'));
        }

        $wallets = $query->get();

        if ($wallets->isEmpty()) {
            $this->warn('No wallets found for reconciliation.');
            return self::SUCCESS;
        }

        $apply = (bool) $this->option('apply');
        $this->info('Wallet reconciliation started (' . ($apply ? 'APPLY' : 'DRY RUN') . ').');

        $rows = [];
        $mismatches = 0;
        $updated = 0;
        $totalDrift = 0.0;

        foreach ($wallets as $wallet) {
            $ledgerCredits = (float) $wallet->transactions()
                ->where('status', 'completed')
                ->where('type', 'credit')
                ->sum('amount');

            $ledgerDebits = (float) $wallet->transactions()
                ->where('status', 'completed')
                ->where('type', 'debit')
                ->sum('amount');

            $ledgerBalance = round($ledgerCredits - $ledgerDebits, 2);
            $currentBalance = round((float) $wallet->balance, 2);
            $drift = round($currentBalance - $ledgerBalance, 2);

            $action = 'ok';
            if (abs($drift) >= 0.01) {
                $mismatches++;
                $totalDrift += $drift;
                $action = 'mismatch';

                if ($apply) {
                    DB::transaction(function () use ($wallet, $ledgerBalance) {
                        $wallet->balance = $ledgerBalance;
                        $wallet->save();
                    });

                    $updated++;
                    $action = 'updated';
                }
            }

            $rows[] = [
                $wallet->business_id,
                $wallet->business->name ?? 'N/A',
                number_format($currentBalance, 2),
                number_format($ledgerBalance, 2),
                number_format($drift, 2),
                $action,
            ];
        }

        $this->table(
            ['Business ID', 'Business', 'Current Balance', 'Ledger Balance', 'Drift', 'Action'],
            $rows
        );

        $this->info('Mismatched wallets: ' . $mismatches);
        $this->info('Total drift: ' . number_format($totalDrift, 2));

        if ($apply) {
            $this->info('Wallets updated: ' . $updated);
        } elseif ($mismatches > 0) {
            $this->warn('Run with --apply to persist reconciled balances.');
        }

        return self::SUCCESS;
    }
}

