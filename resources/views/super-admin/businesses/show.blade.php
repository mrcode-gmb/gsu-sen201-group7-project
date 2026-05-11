@extends('layouts.super-admin')

@section('header', 'Business Details')

@section('slot')
    <div class="mb-6">
        <a href="{{ route('super-admin.businesses.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Businesses
        </a>
    </div>

    <!-- Business Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ $business->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $business->business_type ?? 'N/A' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $business->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($business->status) }}
                </span>
                <a href="{{ route('super-admin.businesses.edit', $business) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Business
                </a>
            </div>
        </div>

        @if($business->description)
            <p class="text-gray-700 mb-4">{{ $business->description }}</p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @if($business->email)
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $business->email }}
                </div>
            @endif
            @if($business->phone)
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    {{ $business->phone }}
                </div>
            @endif
            @if($business->address)
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $business->address }}
                </div>
            @endif
        </div>
    </div>
    <!-- Business Capital -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Capital</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Capital</p>
                <p class="text-3xl font-bold text-gray-900">₦{{ number_format($business->businessCapital->balance, 2) }}</p>
                <p class="text-sm font-medium text-gray-600 mt-2">Total Business Assets</p>
                <p class="text-xl font-bold text-gray-900">₦{{ number_format($actualWalletBalance, 2) }}</p>
                <p class="text-sm ">Business Assets - Business Capital = Actual Profit</p>
                <p class="text-gray-900">Actual profit: <b>₦{{ number_format($actualProfitFromAssets, 2) }}</b></p>

            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('super-admin.capital.create', ['business' => $business, 'type' => 'deposit']) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Add Capital</a>
                <a href="{{ route('super-admin.capital.create', ['business' => $business, 'type' => 'withdrawal']) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Withdraw Capital</a>
            </div>
        </div>
    </div>
    <!-- Wallet Manager -->
    @livewire('wallet-manager', ['wallet' => $business->wallet])



    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['total_admins'] }} admins, {{ $stats['total_salespeople'] }} staff</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Purchase</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sales</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_sales'], 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sales On Credit</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_credit_sales'], 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Creditor Balance</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($business->creditors->sum("balance"), 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Credit Paid</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($creditPaid, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Profit</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_profit'], 2) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Profit On Credit</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_profit_on_credit'], 2) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_expenses'], 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Commission</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($total_commission, 2) }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Remaining Product Assignment</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($productAssignment, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div> --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net Profit</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($net_profit, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>


    </div>

    @php
        $totalCreditIssued = $totalCreditorBalance + $creditPaid;
        $creditCollectionPercentage = $totalCreditIssued > 0 ? min(100, ($creditPaid / $totalCreditIssued) * 100) : 0;
    @endphp

    <div class="mb-6 rounded-lg bg-white p-6 shadow-md">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Creditor Overview</h3>
                <p class="mt-1 text-sm text-gray-500">Review every creditor in this business and track how much credit has been recovered.</p>
            </div>
            <a href="{{ route('super-admin.businesses.creditors', $business) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                View Creditor Records
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_260px]">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-gray-500">
                    <span>Total credit issued: <span class="font-semibold text-gray-900">₦{{ number_format($totalCreditIssued, 2) }}</span></span>
                    <span>Recovered: <span class="font-semibold text-blue-700">{{ number_format($creditCollectionPercentage, 1) }}%</span></span>
                </div>
                <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-blue-100">
                    <div
                        class="h-full rounded-full"
                        style="width: {{ $creditCollectionPercentage }}%; background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);"
                    ></div>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-500">
                    <span>Paid: ₦{{ number_format($creditPaid, 2) }}</span>
                    <span>Outstanding: ₦{{ number_format($totalCreditorBalance, 2) }}</span>
                </div>
            </div>
            <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                <p class="text-sm font-medium text-blue-700">Active Creditors</p>
                <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format($business->creditors->count()) }}</p>
                <p class="mt-2 text-sm text-blue-800">Open the creditor page to inspect balances, recovered amounts, and progress for each creditor.</p>
            </div>
        </div>
    </div>

    <!-- Inventory & Sales Summary -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory & Sales Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Inventory Value -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Current Inventory Value</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['current_inventory_value']  ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['current_stock_quantity'] ?? 0 }} items in stock</p>
            </div>

            <!-- Product Assignment Cost -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Assignment Inventory Cost</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($productAssignmentCost ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $productAssignmentQuantity ?? 0 }} items with staff</p>
            </div>

            <!-- Total Purchases -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Purchases</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_purchases'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_purchase_quantity'] ?? 0 }} units purchased</p>
            </div>

            <!-- Sales Performance -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_sales'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-2">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_quantity_sold'] ?? 0 }} units sold</p>
            </div>

            <!-- Profit Summary -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Gross Profit</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['gross_profit'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['profit_margin'] ?? 0 }}% profit margin</p>
            </div>
        </div>

        <!-- Inventory Turnover -->
        @if(isset($stats['inventory_turnover']))
        <div class="mt-6 pt-4 border-t border-gray-100">
            <h4 class="text-sm font-medium text-gray-600 mb-2">Inventory Turnover</h4>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, $stats['inventory_turnover']) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                {{ number_format($stats['inventory_turnover'] ?? 0, 2) }}x -
                @if(($stats['inventory_turnover'] ?? 0) > 5)
                    <span class="text-green-600">High Turnover</span>
                @elseif(($stats['inventory_turnover'] ?? 0) > 2)
                    <span class="text-yellow-600">Moderate Turnover</span>
                @else
                    <span class="text-red-600">Low Turnover</span>
                @endif
            </p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('super-admin.businesses.users', $business) }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            View Users
        </a>
        <a href="{{ route('super-admin.businesses.analytics', $business) }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            View Analytics
        </a>
        <form method="POST" action="{{ route('super-admin.businesses.toggle-status', $business) }}" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-3 {{ $business->status === 'active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition">
                {{ $business->status === 'active' ? 'Deactivate' : 'Activate' }} Business
            </button>
        </form>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Users</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($business->users->take(10) as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No users yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sales History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sale->unique_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->purchase->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->user->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No sales yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Purchases History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchases History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchased By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $purchase->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($purchase->total_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->user->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No purchases yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>

    <!-- Expenses History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Expenses History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $expense->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->user->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No expenses yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Creditor Transactions History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Creditor Transactions History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creditor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($creditorTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->creditor->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'debit' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($transaction->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No creditor transactions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $creditorTransactions->links() }}
        </div>
    </div>

    <!-- Database Diagnostics Section (for debugging) -->
    {{-- <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-yellow-900 mb-4">🔍 Database Diagnostics (Debug Mode)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Assignments Check -->
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-gray-900 mb-3">Product Assignments</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Assignments:</span>
                        <span class="font-semibold">{{ $diagnostics['total_assignments'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">With Commission:</span>
                        <span class="font-semibold">{{ $diagnostics['assignments_with_commission'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Commission (DB):</span>
                        <span class="font-semibold text-blue-600">₦{{ number_format($diagnostics['total_commission_from_db'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Commission Used:</span>
                        <span class="font-semibold text-green-600">₦{{ number_format($diagnostics['commission_used_in_calc'], 2) }}</span>
                    </div>
                    @if($diagnostics['total_commission_from_db'] != $diagnostics['commission_used_in_calc'])
                        <div class="mt-2 p-2 bg-red-100 border border-red-300 rounded">
                            <span class="text-red-700 font-semibold">⚠️ MISMATCH!</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sales Check -->
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-gray-900 mb-3">Sales Records</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Sales:</span>
                        <span class="font-semibold">{{ $diagnostics['total_sales_count'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Profit Sum (DB):</span>
                        <span class="font-semibold text-blue-600">₦{{ number_format($diagnostics['sales_profit_sum'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Profit in Stats:</span>
                        <span class="font-semibold text-green-600">₦{{ number_format($diagnostics['sales_profit_in_stats'], 2) }}</span>
                    </div>
                    @if($diagnostics['sales_profit_sum'] != $diagnostics['sales_profit_in_stats'])
                        <div class="mt-2 p-2 bg-red-100 border border-red-300 rounded">
                            <span class="text-red-700 font-semibold">⚠️ MISMATCH!</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expenses Check -->
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-gray-900 mb-3">Expenses Records</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Expenses:</span>
                        <span class="font-semibold">{{ $diagnostics['expenses_count'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expenses Sum (DB):</span>
                        <span class="font-semibold text-blue-600">₦{{ number_format($diagnostics['expenses_sum'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expenses in Stats:</span>
                        <span class="font-semibold text-green-600">₦{{ number_format($diagnostics['expenses_in_stats'], 2) }}</span>
                    </div>
                    @if($diagnostics['expenses_sum'] != $diagnostics['expenses_in_stats'])
                        <div class="mt-2 p-2 bg-red-100 border border-red-300 rounded">
                            <span class="text-red-700 font-semibold">⚠️ MISMATCH!</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Inventory Check -->
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-gray-900 mb-3">Inventory Status</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Purchase Records:</span>
                        <span class="font-semibold">{{ $diagnostics['total_purchase_records'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Warehouse Qty:</span>
                        <span class="font-semibold">{{ $diagnostics['warehouse_qty'] }} units</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Warehouse Cost:</span>
                        <span class="font-semibold">₦{{ number_format($diagnostics['warehouse_cost'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Assigned Qty:</span>
                        <span class="font-semibold">{{ $diagnostics['assigned_qty'] }} units</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Assigned Cost:</span>
                        <span class="font-semibold">₦{{ number_format($diagnostics['assigned_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Calculation Breakdown -->
        <div class="mt-6 bg-white p-4 rounded-lg border border-yellow-200">
            <h4 class="font-semibold text-gray-900 mb-3">Profit Calculation Breakdown</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Sales Profit:</span>
                    <span class="font-semibold text-green-600">₦{{ number_format($profitBreakdown['sales_profit'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">- Expenses:</span>
                    <span class="font-semibold text-red-600">₦{{ number_format($profitBreakdown['expenses'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">- Commission:</span>
                    <span class="font-semibold text-red-600">₦{{ number_format($profitBreakdown['commission'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t-2 border-gray-300">
                    <span class="text-gray-900 font-bold">= Net Profit:</span>
                    <span class="font-bold text-blue-600 text-lg">₦{{ number_format($profitBreakdown['net_profit'], 2) }}</span>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
