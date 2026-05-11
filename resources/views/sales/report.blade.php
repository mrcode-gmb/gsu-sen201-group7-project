@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Goods Dispatch Report</h1>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('sales.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Export to Excel
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ request('start_date') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ request('end_date') }}" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                @if(auth()->user()->isAdmin())
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Staff</label>
                        <select id="user_id" 
                                name="user_id" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Staffs</option>
                            @foreach($salespeople as $person)
                                <option value="{{ $person->id }}" {{ request('user_id') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select id="payment_type" 
                            name="payment_type" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Payment Methods</option>
                        @foreach($paymentTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('sales.report') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Dispatch Value</p>
                    <p class="text-2xl font-semibold text-gray-800">₦{{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Estimated Margin</p>
                    <p class="text-2xl font-semibold text-gray-800">₦{{ number_format($totalProfit, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Dispatch Records</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $sales->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Payment Method Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $totalTransactions = $paymentSummary->sum('count');
                    @endphp
                    @foreach($paymentSummary as $payment)
                        @php
                            $percentage = $totalTransactions > 0 ? ($payment->count / $totalTransactions) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $paymentTypes[$payment->payment_type] ?? ucfirst($payment->payment_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₦{{ number_format($payment->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sales Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->sale_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $sale->purchase->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->purchase->product->unit_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($sale->quantity, 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₦{{ number_format($sale->selling_price_per_unit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₦{{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($sale->payment_type)
                                        @case('cash') bg-green-100 text-green-800 @break
                                        @case('bank_transfer') bg-blue-100 text-blue-800 @break
                                        @case('pos') bg-purple-100 text-purple-800 @break
                                        @case('mobile_money') bg-yellow-100 text-yellow-800 @break
                                        @case('credit') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ $paymentTypes[$sale->payment_type] ?? ucfirst($sale->payment_type) }}
                                </span>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sale->user->name }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $sale->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ₦{{ number_format($sale->profit, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? '10' : '9' }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                No sales found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? '5' : '4' }}" class="px-6 py-3 text-right text-sm font-medium text-gray-500">
                                Totals:
                            </td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                ₦{{ number_format($sales->sum('total_amount'), 2) }}
                            </td>
                            <td></td>
                            @if(auth()->user()->isAdmin())
                                <td></td>
                            @endif
                            <td class="px-6 py-3 text-sm font-medium {{ $sales->sum('profit') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ₦{{ number_format($sales->sum('profit'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            font-size: 12px;
        }
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px 8px;
        }
    }
</style>
@endpush

@endsection
