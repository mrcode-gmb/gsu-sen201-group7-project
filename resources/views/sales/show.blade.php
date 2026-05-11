<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Goods Dispatch Details
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isAdmin() || auth()->user()->id == $sale->user_id)
                    <a href="{{ route('sales.edit', $sale) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Edit Goods Dispatch
                    </a>
                @endif
                <a href="{{ auth()->user()->isAdmin() ? route('sales.index') : route('sales.my-sales') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Goods Dispatch
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Sale Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sale Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sale ID</dt>
                                <dd class="text-lg font-semibold text-gray-900">#{{ $sale->unique_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Product</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->purchase->product->name }} ({{ ucfirst($sale->purchase->product->unit_type) }})</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantity Sold</dt>
                                <dd class="text-lg font-semibold text-blue-600">{{ number_format($sale->quantity, 1) }} units</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price per Unit</dt>
                                <dd class="text-sm text-gray-900">₦{{ number_format($sale->selling_price_per_unit, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                <dd class="text-2xl font-bold text-green-600">₦{{ number_format($sale->total_amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                <dd>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        @switch($sale->payment_type ?? 'cash')
                                            @case('cash') bg-green-100 text-green-800 @break
                                            @case('bank_transfer') bg-blue-100 text-blue-800 @break
                                            @case('pos') bg-purple-100 text-purple-800 @break
                                            @case('mobile_money') bg-yellow-100 text-yellow-800 @break
                                            @case('credit') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst(str_replace('_', ' ', $sale->payment_type ?? 'cash')) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Profit</dt>
                                <dd class="text-lg font-semibold text-purple-600">₦{{ number_format($sale->profit, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Receiver & Dispatch Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Receiver & Dispatch Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Receiver Name</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->customer_name ?: 'Unspecified Receiver' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dispatch Date</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->sale_date->format('l, M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dispatch Time</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->created_at->format('g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Staff</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->user->name }}</dd>
                            </div>
                            @if($sale->notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="text-sm text-gray-900">{{ $sale->notes }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Record Created</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            @if($sale->updated_at != $sale->created_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $sale->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-blue-600">Quantity</p>
                        <p class="text-2xl font-bold text-blue-800">{{ number_format($sale->quantity, 1) }}</p>
                        <p class="text-xs text-blue-600">units</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-green-600">Unit Price</p>
                        <p class="text-2xl font-bold text-green-800">₦{{ number_format($sale->selling_price_per_unit, 2) }}</p>
                        <p class="text-xs text-green-600">per unit</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-yellow-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-yellow-800">₦{{ number_format($sale->total_amount, 2) }}</p>
                        <p class="text-xs text-yellow-600">gross amount</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-purple-600">Profit</p>
                        <p class="text-2xl font-bold text-purple-800">₦{{ number_format($sale->profit, 2) }}</p>
                        <p class="text-xs text-purple-600">net profit</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Product Details</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Product Name:</span>
                                <span class="text-sm font-medium">{{ $sale->purchase->product->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Unit Type:</span>
                                <span class="text-sm font-medium capitalize">{{ $sale->purchase->product->unit_type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current Stock:</span>
                                <span class="text-sm font-medium {{ $sale->purchase->quantity <= $sale->purchase->product->effective_low_stock_threshold ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($sale->purchase->quantity, 1) }} units
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Purchase Price:</span>
                                <span class="text-sm font-medium">₦{{ number_format($sale->purchase->purchase_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Sale vs Current Price</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Sold at:</span>
                                <span class="text-sm font-medium">₦{{ number_format($sale->selling_price_per_unit, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current price:</span>
                                <span class="text-sm font-medium">₦{{ number_format($sale->purchase->purchase_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Price difference:</span>
                                @php
                                    $priceDiff = $sale->purchase->purchase_price - $sale->selling_price_per_unit;
                                @endphp
                                <span class="text-sm font-medium {{ $priceDiff > 0 ? 'text-green-600' : ($priceDiff < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ $priceDiff > 0 ? '+' : '' }}₦{{ number_format($priceDiff, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @if(auth()->user()->isAdmin() || auth()->user()->id == $sale->user_id)
                        <a href="{{ route('sales.edit', $sale) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Goods Dispatch
                        </a>
                    @endif
                    
                    <a href="{{ route('inventory.show', $sale->purchase) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        View Product
                    </a>
                    
                    <a href="{{ route('sales.print-receipt', $sale) }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Receipt
                    </a>
                    
                    <a href="{{ route('sales.create') }}?product={{ $sale->purchase->id }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Sell Again
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
