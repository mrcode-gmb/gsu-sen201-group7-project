@extends('layouts.super-admin')

@section('header', $business->name . ' - Wallet Transactions')

@section('slot')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <a href="{{ route('super-admin.businesses.show', $business) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Business Details
                </a>
                <h2 class="mt-4 text-3xl font-bold text-gray-900">Wallet Transaction History</h2>
                <p class="mt-2 max-w-3xl text-sm text-gray-600">
                    Review every wallet movement for {{ $business->name }}, monitor inflow and outflow, and export the current view.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ $exportUrl }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m5 6H4a1 1 0 01-1-1V5a1 1 0 011-1h5.586a1 1 0 01.707.293l8.414 8.414a1 1 0 01.293.707V19a1 1 0 01-1 1z"></path>
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('super-admin.wallets.deposit', ['business' => $business]) }}" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path>
                    </svg>
                    Add Funds
                </a>
                <a href="{{ route('super-admin.wallets.withdraw', ['business' => $business]) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Withdraw
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-blue-500 shadow-xl">
            <div class="grid gap-6 px-6 py-8 lg:grid-cols-3 lg:px-8">
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-blue-100">Wallet Overview</p>
                    <p class="mt-4 text-sm text-blue-100">Current balance</p>
                    <p class="mt-2 text-4xl font-bold text-white">₦{{ number_format($summary['current_balance'], 2) }}</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-blue-50">
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-medium backdrop-blur-sm">
                            {{ strtoupper($summary['currency']) }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-medium backdrop-blur-sm">
                            {{ ucfirst($business->wallet->status) }} wallet
                        </span>
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-medium backdrop-blur-sm">
                            {{ $summary['total_count'] }} total transaction{{ $summary['total_count'] === 1 ? '' : 's' }}
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                    <p class="text-sm font-medium text-blue-100">Last wallet activity</p>
                    <p class="mt-3 text-2xl font-semibold text-white">
                        {{ $summary['latest_transaction_at'] ? $summary['latest_transaction_at']->diffForHumans() : 'No activity yet' }}
                    </p>
                    <p class="mt-2 text-sm text-blue-100">
                        {{ $summary['latest_transaction_at'] ? $summary['latest_transaction_at']->format('M d, Y h:i A') : 'Transactions will appear here once the wallet is used.' }}
                    </p>

                    @if($summary['latest_filtered_transaction'])
                        <div class="mt-5 rounded-xl bg-slate-950/25 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-blue-100">Latest matching transaction</p>
                            <p class="mt-2 text-sm font-semibold text-white">{{ $summary['latest_filtered_transaction']->description ?: 'Wallet transaction' }}</p>
                            <div class="mt-3 flex items-center justify-between text-sm text-blue-100">
                                <span>{{ strtoupper($summary['latest_filtered_transaction']->type) }}</span>
                                <span>₦{{ number_format($summary['latest_filtered_transaction']->amount, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
                <p class="text-sm font-medium text-blue-700">{{ $summary['has_filters'] ? 'Filtered Credits' : 'Total Credits' }}</p>
                <p class="mt-3 text-3xl font-bold text-blue-950">₦{{ number_format($summary['filtered_credits'], 2) }}</p>
                <p class="mt-2 text-sm text-blue-600">Money added into the business wallet.</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
                <p class="text-sm font-medium text-blue-700">{{ $summary['has_filters'] ? 'Filtered Debits' : 'Total Debits' }}</p>
                <p class="mt-3 text-3xl font-bold text-blue-950">₦{{ number_format($summary['filtered_debits'], 2) }}</p>
                <p class="mt-2 text-sm text-blue-600">Money removed from the wallet.</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
                <p class="text-sm font-medium text-blue-700">{{ $summary['has_filters'] ? 'Filtered Net Flow' : 'Net Flow' }}</p>
                <p class="mt-3 text-3xl font-bold {{ $summary['filtered_net_flow'] >= 0 ? 'text-blue-900' : 'text-blue-800' }}">
                    {{ $summary['filtered_net_flow'] >= 0 ? '+' : '-' }}₦{{ number_format(abs($summary['filtered_net_flow']), 2) }}
                </p>
                <p class="mt-2 text-sm text-blue-600">Credits minus debits for the current view.</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
                <p class="text-sm font-medium text-blue-700">{{ $summary['has_filters'] ? 'Matching Transactions' : 'Total Transactions' }}</p>
                <p class="mt-3 text-3xl font-bold text-blue-950">{{ number_format($summary['filtered_count']) }}</p>
                <p class="mt-2 text-sm text-blue-600">Count of transactions in the current result set.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-semibold text-gray-900">Filter Transactions</h3>
                <p class="mt-1 text-sm text-gray-500">Search by reference or description, narrow by source, type, status, or focus on a date range.</p>
            </div>

            <form method="GET" action="{{ route('super-admin.businesses.wallet-transactions', $business) }}" class="grid gap-4 px-6 py-6 md:grid-cols-2 xl:grid-cols-8">
                <div class="xl:col-span-2">
                    <label for="search" class="mb-2 block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Reference or description" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="source" class="mb-2 block text-sm font-medium text-gray-700">Source</label>
                    <select id="source" name="source" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All sources</option>
                        <option value="purchase" @selected(request('source') === 'purchase')>Purchase</option>
                        <option value="sales" @selected(request('source') === 'sales')>Sales</option>
                        <option value="creditor" @selected(request('source') === 'creditor')>Creditor</option>
                        <option value="capital" @selected(request('source') === 'capital')>Capital</option>
                        <option value="manual_deposit" @selected(request('source') === 'manual_deposit')>Manual Deposit</option>
                        <option value="manual_withdrawal" @selected(request('source') === 'manual_withdrawal')>Manual Withdrawal</option>
                        <option value="deposit" @selected(request('source') === 'deposit')>Deposit</option>
                        <option value="withdrawal" @selected(request('source') === 'withdrawal')>Withdrawal</option>
                        <option value="wallet" @selected(request('source') === 'wallet')>Wallet / Other</option>
                    </select>
                </div>

                <div>
                    <label for="type" class="mb-2 block text-sm font-medium text-gray-700">Type</label>
                    <select id="type" name="type" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All types</option>
                        <option value="credit" @selected(request('type') === 'credit')>Credit</option>
                        <option value="debit" @selected(request('type') === 'debit')>Debit</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All statuses</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="mb-2 block text-sm font-medium text-gray-700">From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="date_to" class="mb-2 block text-sm font-medium text-gray-700">To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="per_page" class="mb-2 block text-sm font-medium text-gray-700">Per Page</label>
                    <select id="per_page" name="per_page" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach([15, 25, 50, 100] as $perPage)
                            <option value="{{ $perPage }}" @selected((int) request('per_page', 15) === $perPage)>{{ $perPage }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap items-end gap-3 md:col-span-2 xl:col-span-8">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19l-4 2v-7.586L3.293 6.707A1 1 0 013 6V4z"></path>
                        </svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('super-admin.businesses.wallet-transactions', $business) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Clear
                    </a>
                    <a href="{{ $exportUrl }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                        Export Current View
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Showing {{ $transactions->count() }} of {{ number_format($summary['filtered_count']) }} transaction{{ $summary['filtered_count'] === 1 ? '' : 's' }}
                        @if($summary['has_filters'])
                            from the filtered results.
                        @else
                            in the wallet history.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($summary['has_filters'])
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">Filters active</span>
                    @endif
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        Last update: {{ $summary['latest_transaction_at'] ? $summary['latest_transaction_at']->format('M d, Y h:i A') : 'No transaction yet' }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Source</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($transactions as $transaction)
                            @php
                                $metadataPreview = collect($transaction->metadata ?? [])
                                    ->map(function ($value, $key) {
                                        return $key . ': ' . (is_array($value) ? json_encode($value) : $value);
                                    })
                                    ->take(2)
                                    ->implode(' | ');
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 align-top text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 align-top text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">{{ $transaction->reference }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">{{ $transaction->description ?: 'Wallet transaction' }}</div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $metadataPreview !== '' ? \Illuminate\Support\Str::limit($metadataPreview, 100) : 'No extra metadata recorded.' }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 align-top text-sm text-gray-700">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                        {{ $transaction->source_label }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 align-top text-sm">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $transaction->type === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ strtoupper($transaction->type) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 align-top text-sm">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $transaction->status === 'completed' ? 'bg-blue-100 text-blue-700' : ($transaction->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right align-top text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex max-w-md flex-col items-center">
                                        <div class="rounded-full bg-slate-100 p-4 text-slate-500">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="mt-4 text-lg font-semibold text-gray-900">No wallet transactions found</h4>
                                        <p class="mt-2 text-sm text-gray-500">
                                            Try changing the filters or add a deposit, withdrawal, sale, or purchase that touches the wallet.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
