@extends('layouts.super-admin')

@section('header', 'Business Creditors')

@section('slot')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('super-admin.businesses.show', $business) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Business Details
                </a>
                <h2 class="mt-3 text-3xl font-bold text-gray-900">{{ $business->name }} Creditors</h2>
                <p class="mt-2 text-sm text-gray-600">Track outstanding credit, recovered payments, and the collection progress for each creditor.</p>
            </div>
            <div class="rounded-2xl bg-blue-700 px-5 py-4 text-white shadow-lg">
                <p class="text-xs uppercase tracking-[0.3em] text-blue-100">Collection Progress</p>
                <p class="mt-2 text-3xl font-bold">{{ number_format($summary['collection_percentage'], 1) }}%</p>
                <p class="mt-1 text-sm text-blue-100">Recovered from total credit issued.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total Creditors</p>
                <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($summary['total_creditors']) }}</p>
                <p class="mt-2 text-sm text-gray-500">All creditor accounts in this business.</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total Credit</p>
                <p class="mt-3 text-3xl font-bold text-red-600">₦{{ number_format($summary['total_credit'], 2) }}</p>
                <p class="mt-2 text-sm text-gray-500">Full value sold out on credit.</p>
            </div>
            <div class="rounded-2xl border border-green-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total Paid</p>
                <p class="mt-3 text-3xl font-bold text-green-600">₦{{ number_format($summary['total_paid'], 2) }}</p>
                <p class="mt-2 text-sm text-gray-500">Money recovered from creditors.</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Outstanding Balance</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">₦{{ number_format($summary['total_balance'], 2) }}</p>
                <p class="mt-2 text-sm text-gray-500">Still waiting to be paid.</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recovery Meter</h3>
                    <p class="mt-1 text-sm text-gray-500">Paid amount against total credit issued for this business.</p>
                </div>
                <div class="text-sm text-gray-500">
                    Paid ₦{{ number_format($summary['total_paid'], 2) }} of ₦{{ number_format($summary['total_credit'], 2) }}
                </div>
            </div>

            <div class="mt-5">
                <div class="h-4 w-full overflow-hidden rounded-full bg-blue-100">
                    <div
                        class="h-full rounded-full"
                        style="width: {{ min(100, $summary['collection_percentage']) }}%; background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);"
                    ></div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-500">
                    <span>Outstanding: ₦{{ number_format($summary['total_balance'], 2) }}</span>
                    <span class="font-semibold text-blue-700">{{ number_format($summary['collection_percentage'], 1) }}% collected</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_auto_auto]">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Creditor</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name, email, or phone"
                        class="mt-2 block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                        Apply Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('super-admin.businesses.creditors', $business) }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-50">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Creditor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Credit</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Paid</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Balance</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($creditors as $creditor)
                            @php
                                $totalCredit = (float) ($creditor->total_credit ?? 0);
                                $totalPaid = (float) ($creditor->total_paid ?? 0);
                                $collectionPercentage = (float) ($creditor->collection_percentage ?? 0);
                                $statusClasses = $creditor->balance > 0
                                    ? 'bg-amber-100 text-amber-800'
                                    : 'bg-green-100 text-green-800';
                            @endphp
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($creditor->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $creditor->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500">{{ $creditor->sales_count }} linked sales</p>
                                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                                {{ $creditor->balance > 0 ? 'Outstanding' : 'Settled' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <p>{{ $creditor->email ?: 'No email' }}</p>
                                    <p class="mt-1">{{ $creditor->phone ?: 'No phone' }}</p>
                                </td>
                                <td class="px-6 py-5 text-sm font-semibold text-red-600">
                                    ₦{{ number_format($totalCredit, 2) }}
                                </td>
                                <td class="px-6 py-5 text-sm font-semibold text-green-600">
                                    ₦{{ number_format($totalPaid, 2) }}
                                </td>
                                <td class="px-6 py-5 text-sm font-semibold text-blue-700">
                                    ₦{{ number_format($creditor->balance, 2) }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="min-w-[240px]">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>Paid vs credit</span>
                                            <span>{{ number_format($collectionPercentage, 1) }}%</span>
                                        </div>
                                        <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-blue-100">
                                            <div
                                                class="h-full rounded-full"
                                                style="width: {{ min(100, $collectionPercentage) }}%; background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);"
                                            ></div>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                            <span>₦{{ number_format($totalPaid, 2) }} paid</span>
                                            <span>₦{{ number_format($creditor->balance, 2) }} left</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No creditor records found for this business.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $creditors->links() }}
            </div>
        </div>
    </div>
@endsection
