<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateWalletsForExistingBusinesses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'wallets:create-for-businesses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create wallets for existing businesses that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for businesses without a wallet...');

        $businesses = \App\Models\Business::whereDoesntHave('wallet')->get();

        if ($businesses->isEmpty()) {
            $this->info('All businesses already have a wallet. No action needed.');
            return 0;
        }

        $this->info('Found ' . $businesses->count() . ' businesses without wallets. Creating them now...');

        $bar = $this->output->createProgressBar($businesses->count());
        $bar->start();

        foreach ($businesses as $business) {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($business) {
                    $business->wallet()->create([
                        'balance' => 0,
                        'currency' => 'NGN', // Default currency
                        'status' => 'active',
                    ]);
                });
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to create wallet for business: {$business->name} (ID: {$business->id})");
                $this->error($e->getMessage());
            }
        }

        $bar->finish();
        $this->info('\n\nWallet creation process completed.');

        return 0;
    }
}
