<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory Item Details: {{ $product->product->name }}
            </h2>
            <div class="flex space-x-2">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('inventory.edit', $product->product) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Edit Inventory Item
                    </a>
                @endif
                <a href="{{ auth()->user()->isAdmin() ? route('inventory.index') : route('sales.inventory') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Inventory
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $threshold = $product->product->effective_low_stock_threshold;
    @endphp

    <div class="space-y-6">
        @if ($warehouseStock <= $threshold)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-red-800">Low Stock Alert</h3>
                        <p class="text-sm text-red-600">
                            Warehouse stock is {{ number_format($warehouseStock, 1) }} units and the alert threshold is
                            {{ number_format($threshold) }} units.
                        </p>
                    </div>
                    <a href="{{ route('purchases.create', ['product' => $product->product->id]) }}"
                        class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Record Goods Receipt
                    </a>
                </div>
            </div>
        @endif

        <!-- Inventory Item Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $product->product->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="text-sm text-gray-900">{{ $product->product->category?->name ?: 'Uncategorized' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">SKU Code</dt>
                                <dd class="text-sm text-gray-900">{{ $product->product->sku ?: 'Not assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Storage Location</dt>
                                <dd class="text-sm text-gray-900">{{ $product->product->storage_location ?: 'No location assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Type</dt>
                                <dd class="text-sm text-gray-900 capitalize">{{ $product->product->unit_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purchase Price</dt>
                                <dd class="text-lg font-semibold text-green-600">
                                    ₦{{ number_format($product->purchase_price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Low Stock Threshold</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($threshold) }} units</dd>
                            </div>
                            @if ($product->product->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->product->description }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $product->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Stock Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Information</h3>
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Warehouse Stock</p>
                                        <p
                                            class="text-3xl font-bold {{ $warehouseStock == 0 ? 'text-red-600' : ($warehouseStock <= $threshold ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ number_format($warehouseStock, 1) }}
                                        </p>
                                        <p class="text-sm text-gray-500">units</p>
                                    </div>
                                    <div class="text-right">
                                        @if ($warehouseStock == 0)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Out of Stock
                                            </span>
                                        @elseif($warehouseStock <= $threshold)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                Low Stock
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                In Stock
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-gray-600">Alert threshold: {{ number_format($threshold) }} units</p>
                            </div>

                            <!-- Stock Value -->
                            @php
                                // Calculate average cost price from all purchases
                                $averageCostPrice = $product->purchase_price ?? 0;

                                // Stock value = current stock * average cost
                                $stockValue = $product->quantity * $averageCostPrice;

                                // Potential revenue = current stock * selling price
                                $potentialRevenue = $product->quantity * $product->selling_price;

                                // Potential profit = revenue - stock value
                                $potentialProfit = 0;
                                // $potentialProfit = $potentialRevenue - $stockValue;
                            @endphp


                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-blue-600">Stock Value</p>
                                    <p class="text-lg font-semibold text-blue-800">₦{{ number_format($stockValue, 2) }}
                                    </p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-green-600">Potential Revenue</p>
                                    <p class="text-lg font-semibold text-green-800">
                                        ₦{{ number_format($potentialRevenue, 2) }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-purple-600">Potential Profit</p>
                                    <p class="text-lg font-semibold text-purple-800">
                                        ₦{{ number_format($potentialProfit, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('purchases.create') }}?product={{ $product->id }}"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Record Goods Receipt
                        </a>
                    @endif

                    @if ($product->quantity > 0)
                        <a href="{{ route('sales.create') }}?product={{ $product->id }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Record Goods Dispatch
                        </a>
                    @else
                        <div
                            class="bg-gray-300 text-gray-500 px-4 py-3 rounded-md text-center font-medium cursor-not-allowed">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Out of Stock
                        </div>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('inventory.edit', $product->product) }}"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit Inventory Item
                        </a>
                    @endif

                    <button onclick="window.print()"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Print Details
                    </button>
                </div>
            </div>
        </div>

        <!-- Stock Adjustment (Admin Only) -->
        @if (auth()->user()->isAdmin())
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Manual Stock Adjustment</h3>
                    <form method="POST" action="{{ route('inventory.adjust-stock', $product) }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <div>
                            <x-input-label for="adjustment" :value="__('Adjustment Amount')" />
                            <x-text-input id="adjustment" name="adjustment" type="number" step="0.1"
                                class="mt-1 block w-full" placeholder="e.g., 5 or -2" required />
                            <p class="text-xs text-gray-500 mt-1">Use positive numbers to add stock, negative to reduce
                            </p>
                        </div>
                        <div>
                            <x-input-label for="reason" :value="__('Reason for Adjustment')" />
                            <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full"
                                placeholder="e.g., Damaged goods, Found extra stock" required />
                        </div>
                        <div>
                            <x-primary-button>
                                {{ __('Adjust Stock') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Stock Movement History -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Movement History</h3>
                @if ($stockMovements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stockMovements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($movement['date'])->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($movement['type'] == 'purchase')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Purchase
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Sale
                                                </span>
                                            @endif
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement['quantity'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $movement['quantity'] > 0 ? '+' : '' }}{{ number_format($movement['quantity'], 1) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $movement['description'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $movement['user'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No stock movements</h3>
                        <p class="mt-1 text-sm text-gray-500">This product has no purchase or sales history yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stock adjustment History -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Adjustment History</h3>
                @if ($adjustmentRecord->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Reason</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($adjustmentRecord as $adjustment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($adjustment->created_at)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $adjustment->purchase->product->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($adjustment->purchase->product->unit_type == 'Customize')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Customize
                                                </span>

                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Uncustomize
                                                </span>
                                            @endif
                                        </td>
                                       
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $adjustment->adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $adjustment->adjustment > 0 ? '+' : '' }}{{ number_format($adjustment->adjustment, 1) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $adjustment->reason }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $adjustment['user'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{-- <a href="" class="text-blue-500 font-bold">Edit</a> --}}
                                            <a href="{{route("inventory.adjustStockDelete", $adjustment)}}" class="text-red-500 font-bold">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Adjustment Movements</h3>
                        <p class="mt-1 text-sm text-gray-500">This product has no purchase or sales history yet.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-shop-layout>
