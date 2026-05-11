<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dispatch Dashboard
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Today's Dispatch Value" value="₦{{ number_format($todaySales, 2) }}" color="green">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </x-slot>
            </x-stat-card>

            <x-stat-card title="Monthly Dispatch Value" value="₦{{ number_format($monthlySales, 2) }}" color="purple">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </x-slot>
            </x-stat-card>

            <x-stat-card title="Available Products" value="{{ number_format($availableProducts->count()) }}" color="blue">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </x-slot>
            </x-stat-card>

            <x-stat-card title="Recent Dispatch Records" value="{{ number_format($recentSales->count()) }}" color="indigo">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </x-slot>
            </x-stat-card>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('sales.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Record Goods Dispatch
                    </a>
                    <a href="{{ route('sales.my-sales') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View Dispatch Records
                    </a>
                    <a href="{{ route('sales.inventory') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md text-center font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Check Inventory
                    </a>
                </div>
            </div>
        </div>

        <!-- Available Products & Recent Dispatches -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Available Products -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Inventory</h3>
                    <div class="space-y-3">
                        @forelse($availableProducts as $product)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-md">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $product->product->category?->name ?: 'Uncategorized' }}
                                        • SKU: {{ $product->product->sku ?: 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ ucfirst($product->product->unit_type) }} • ₦{{ number_format($product->purchase_price, 2) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium {{ $product->quantity <= ($product->product->effective_low_stock_threshold ?? 10) ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($product->quantity, 1) }} units
                                    </p>
                                    @if($product->quantity > 0)
                                        <a href="{{ route('sales.create') }}?product={{ $product->id }}" class="text-xs text-blue-600 hover:text-blue-500">Dispatch now</a>
                                    @else
                                        <span class="text-xs text-red-500">Out of stock</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No inventory available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Dispatches -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">My Recent Dispatches</h3>
                        <a href="{{ route('sales.my-sales') }}" class="text-sm text-blue-600 hover:text-blue-500">View all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentSales as $sale)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sale->purchase->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $sale->sale_date->format('M d, Y') }} • {{ number_format($sale->quantity, 1) }} units</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">₦{{ number_format($sale->total_amount, 2) }}</p>
                                    <p class="text-xs text-blue-600">Dispatch record</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No recent dispatches</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
