<div>
    <div class="mb-6 rounded-lg bg-white p-6 shadow-md">
        <div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Wallet Information</h3>
                <p class="mt-1 text-sm text-gray-500">Track the current wallet balance and review the latest movements.</p>
            </div>

            @if(auth()->user()->isSuperAdmin())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('super-admin.businesses.wallet-transactions', ['business' => $wallet->business_id]) }}" class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        View Full History
                    </a>
                    <a href="{{ route('super-admin.businesses.wallet-transactions.export', ['business' => $wallet->business_id]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m5 6H4a1 1 0 01-1-1V5a1 1 0 011-1h5.586a1 1 0 01.707.293l8.414 8.414a1 1 0 01.293.707V19a1 1 0 01-1 1z" />
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('super-admin.wallets.deposit', ['business' => $wallet->business_id]) }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                        </svg>
                        Add Funds
                    </a>
                    <a href="{{ route('super-admin.wallets.withdraw', ['business' => $wallet->business_id]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        Withdraw
                    </a>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-blue-100 bg-gradient-to-r from-blue-50 to-blue-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Available Balance</p>
                        <p class="text-3xl font-bold text-blue-900">₦{{ number_format($wallet->balance, 2) }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100/70 p-3">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-blue-600">
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                        {{ strtoupper($wallet->currency) }}
                    </span>
                    <span class="ml-2">{{ ucfirst($wallet->status) }} Wallet</span>
                </div>
                <div class="mt-4 border-t border-blue-100 pt-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-xs text-gray-500">Last Transaction</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $wallet->last_transaction_at ? $wallet->last_transaction_at->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $wallet->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($wallet->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 rounded-lg border border-gray-200 bg-white p-6">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-600">Recent Transactions</h4>
                        <p class="mt-1 text-sm text-gray-500">A quick look at the latest 5 wallet movements.</p>
                    </div>
                    <a href="{{ route('super-admin.businesses.wallet-transactions', ['business' => $wallet->business_id]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Open full history</a>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-emerald-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">Credits</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-800">₦{{ number_format($transactionStats['total_credits'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-rose-700">Debits</p>
                        <p class="mt-2 text-2xl font-bold text-rose-800">₦{{ number_format($transactionStats['total_debits'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-700">Transactions</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800">{{ number_format($transactionStats['total_transactions']) }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($transactions as $transaction)
                        <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-3 last:border-b-0">
                            <div class="flex items-start">
                                <div class="rounded-full p-2 {{ $transaction->type === 'credit' ? 'bg-emerald-50' : 'bg-rose-50' }}">
                                    <svg class="h-5 w-5 {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($transaction->type === 'credit')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        @endif
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->description ?: 'Wallet transaction' }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span>{{ $transaction->reference }}</span>
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600">{{ $transaction->source_label }}</span>
                                        <span>{{ $transaction->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">{{ ucfirst($transaction->status) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No transactions yet</p>
                        </div>
                    @endforelse
                </div>

                @if($transactions->hasPages())
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
