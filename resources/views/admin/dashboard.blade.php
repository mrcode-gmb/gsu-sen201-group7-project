<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Warehouse Dashboard
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Today's Sales -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Sales</dt>
                                <dd class="text-2xl font-bold text-gray-900">₦{{ number_format($todaySales, 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Expenses -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Expenses</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    ₦{{ number_format($todayExpenses, 2) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Total Stock -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Stock</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($totalStock, 1) }} units
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Inventory Valuation -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Inventory Valuation</dt>
                                <dd class="text-lg font-medium text-gray-900">₦{{ number_format($totalcostInventory, 2) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $lowStockProducts->count() }} items
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Staffs -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <!-- Users Icon -->
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 11a4 4 0 10-8 0 4 4 0 008 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-500 truncate">Total Staffs</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalStaffs->count() }} items
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Monthly Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Performance</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Monthly Dispatch Value</p>
                            <p class="text-2xl font-bold text-green-600">₦{{ number_format($monthlySales, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Year-to-Date Dispatch Value</p>
                            <p class="text-2xl font-bold text-green-500">₦{{ number_format($yearlySales, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Moving Products</h3>
                    <div class="space-y-3">
                        @forelse($topProducts as $item)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->purchase->product->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ number_format($item->total_sold, 1) }} units
                                        sold</p>
                                </div>
                                <p class="text-sm font-medium text-green-600">
                                    ₦{{ number_format($item->total_revenue, 0) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No dispatch data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Dispatches -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Goods Dispatches</h3>
                        <a href="{{ route('sales.index') }}" class="text-sm text-blue-600 hover:text-blue-500">View
                            all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentSales as $sale)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sale->purchase->product->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $sale->user->name }} •
                                        {{ $sale->sale_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">
                                        ₦{{ number_format($sale->total_amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($sale->quantity, 1) }} units</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No recent dispatch records</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Receipts -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Goods Receipts</h3>
                        <a href="{{ route('purchases.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-500">View all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentPurchases as $purchase)
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $purchase->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $purchase->supplier_name }} •
                                        {{ $purchase->purchase_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-blue-600">
                                        ₦{{ number_format($purchase->total_cost, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($purchase->quantity, 1) }} units
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No recent goods receipts</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        @if ($lowStockProducts->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-red-800">Low Stock Alert</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($lowStockProducts as $product)
                            <div class="bg-red-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-red-800">{{ $product->name }}</p>
                                <p class="text-xs text-red-600">
                                    {{ $product->category?->name ?: 'Uncategorized' }} • SKU: {{ $product->sku ?: 'Not assigned' }}
                                </p>
                                <p class="text-xs text-red-600">
                                    Warehouse stock: {{ number_format($product->warehouse_stock ?? 0, 1) }} units • Threshold:
                                    {{ number_format($product->effective_low_stock_threshold) }}
                                </p>
                                <p class="text-xs text-red-600">
                                    Location: {{ $product->storage_location ?: 'No location assigned' }}
                                </p>
                                <a href="{{ route('purchases.create', ['product' => $product->id]) }}"
                                    class="text-xs text-red-700 hover:text-red-600 underline">Record goods receipt</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>
