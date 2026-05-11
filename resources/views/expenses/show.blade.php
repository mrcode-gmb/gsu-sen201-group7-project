<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Purchase Details #{{ $purchase->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('purchases.edit', $purchase) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Edit Purchase
                </a>
                <a href="{{ route('purchases.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Purchases
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Purchase Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Purchase Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purchase ID</dt>
                                <dd class="text-lg font-semibold text-gray-900">#{{ $purchase->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Product</dt>
                                <dd class="text-sm text-gray-900">{{ $purchase->product->name }} ({{ ucfirst($purchase->product->unit_type) }})</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantity Purchased</dt>
                                <dd class="text-lg font-semibold text-blue-600">{{ number_format($purchase->quantity, 1) }} units</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cost per Unit</dt>
                                <dd class="text-sm text-gray-900">₦{{ number_format($purchase->cost_price_per_unit, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                                <dd class="text-2xl font-bold text-red-600">₦{{ number_format($purchase->total_cost, 2) }}</dd>
                            </div>
                            @php
                                $potentialRevenue = $purchase->quantity * $purchase->product->selling_price;
                                $potentialProfit = $potentialRevenue - $purchase->total_cost;
                            @endphp
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Potential Revenue</dt>
                                <dd class="text-lg font-semibold text-green-600">₦{{ number_format($potentialRevenue, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Potential Profit</dt>
                                <dd class="text-lg font-semibold text-purple-600">₦{{ number_format($potentialProfit, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Supplier & Purchase Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier & Transaction Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supplier Name</dt>
                                <dd class="text-sm text-gray-900">{{ $purchase->supplier_name }}</dd>
                            </div>
                            @if($purchase->supplier_phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Supplier Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $purchase->supplier_phone }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                                <dd class="text-sm text-gray-900">{{ $purchase->purchase_date->format('l, M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Recorded By</dt>
                                <dd class="text-sm text-gray-900">{{ $purchase->user->name }}</dd>
                            </div>
                            @if($purchase->notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="text-sm text-gray-900">{{ $purchase->notes }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Record Created</dt>
                                <dd class="text-sm text-gray-900">{{ $purchase->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            @if($purchase->updated_at != $purchase->created_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $purchase->updated_at->format('M d, Y \a\t g:i A') }}</dd>
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Analysis</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-blue-600">Quantity</p>
                        <p class="text-2xl font-bold text-blue-800">{{ number_format($purchase->quantity, 1) }}</p>
                        <p class="text-xs text-blue-600">units</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-red-600">Cost per Unit</p>
                        <p class="text-2xl font-bold text-red-800">₦{{ number_format($purchase->cost_price_per_unit, 2) }}</p>
                        <p class="text-xs text-red-600">purchase price</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-yellow-600">Total Investment</p>
                        <p class="text-2xl font-bold text-yellow-800">₦{{ number_format($purchase->total_cost, 2) }}</p>
                        <p class="text-xs text-yellow-600">total cost</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-purple-600">Profit Margin</p>
                        @php
                            $profitMargin = $purchase->product->selling_price > 0 ? (($purchase->product->selling_price - $purchase->cost_price_per_unit) / $purchase->product->selling_price) * 100 : 0;
                        @endphp
                        <p class="text-2xl font-bold text-purple-800">{{ number_format($profitMargin, 1) }}%</p>
                        <p class="text-xs text-purple-600">per unit</p>
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
                                <span class="text-sm font-medium">{{ $purchase->product->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Unit Type:</span>
                                <span class="text-sm font-medium capitalize">{{ $purchase->product->unit_type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current Stock:</span>
                                <span class="text-sm font-medium {{ $purchase->product->current_stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($purchase->product->current_stock, 1) }} units
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current Selling Price:</span>
                                <span class="text-sm font-medium">₦{{ number_format($purchase->product->selling_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Profitability Analysis</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Purchase cost:</span>
                                <span class="text-sm font-medium">₦{{ number_format($purchase->cost_price_per_unit, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Selling price:</span>
                                <span class="text-sm font-medium">₦{{ number_format($purchase->product->selling_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Profit per unit:</span>
                                <span class="text-sm font-medium text-green-600">
                                    ₦{{ number_format($purchase->product->selling_price - $purchase->cost_price_per_unit, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total potential profit:</span>
                                <span class="text-sm font-medium text-purple-600">
                                    ₦{{ number_format($potentialProfit, 2) }}
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
                    <a href="{{ route('purchases.edit', $purchase) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Purchase
                    </a>
                    
                    <a href="{{ route('inventory.show', $purchase->product) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        View Product
                    </a>
                    
                    <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Details
                    </button>
                    
                    <a href="{{ route('purchases.create') }}?supplier={{ urlencode($purchase->supplier_name) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Buy Again
                    </a>
                </div>
            </div>
        </div>

        <!-- Supplier History -->
        @php
            $supplierPurchases = \App\Models\Purchase::where('supplier_name', $purchase->supplier_name)
                                                   ->where('id', '!=', $purchase->id)
                                                   ->with('product')
                                                   ->orderBy('purchase_date', 'desc')
                                                   ->limit(5)
                                                   ->get();
        @endphp
        
        @if($supplierPurchases->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Purchases from {{ $purchase->supplier_name }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost per Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($supplierPurchases as $supplierPurchase)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $supplierPurchase->purchase_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $supplierPurchase->product->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($supplierPurchase->quantity, 1) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₦{{ number_format($supplierPurchase->cost_price_per_unit, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ₦{{ number_format($supplierPurchase->total_cost, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>

