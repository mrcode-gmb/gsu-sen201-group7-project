<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stock Inventory Report
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Total Products</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $inventoryData->count() }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Total Stock Value</p>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($totalStockValue, 2) }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Potential Margin</p>
                    <p class="text-2xl font-bold text-purple-600">₦{{ number_format($totalPotentialProfit, 2) }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-red-600">{{ $inventoryData->whereIn('status', ['low', 'out'])->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Stock Status Overview -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Status Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl font-bold text-green-600">{{ $inventoryData->where('status', 'healthy')->count() }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Well Stocked</p>
                        <p class="text-xs text-gray-500">Above configured threshold</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl font-bold text-yellow-600">{{ $inventoryData->where('status', 'low')->count() }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Low Stock</p>
                        <p class="text-xs text-gray-500">Above zero but at/below threshold</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-2xl font-bold text-red-600">{{ $inventoryData->where('status', 'out')->count() }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Out of Stock</p>
                        <p class="text-xs text-gray-500">0 units</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Inventory Data -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detailed Inventory Review</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Threshold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Purchased</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Cost Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Profit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inventoryData as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item['product']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ ucfirst($item['product']->unit_type) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item['product']->category?->name ?: 'Uncategorized' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item['product']->sku ?: 'Not assigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item['product']->storage_location ?: 'No location assigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $item['status'] === 'out' ? 'text-red-600' : ($item['status'] === 'low' ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($item['current_stock'], 1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($item['low_stock_threshold']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($item['total_purchased'], 1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($item['total_sold'], 1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₦{{ number_format($item['average_cost_price'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        ₦{{ number_format($item['stock_value'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-purple-600">
                                        ₦{{ number_format($item['potential_profit'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item['status'] === 'out')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Out of Stock
                                            </span>
                                        @elseif($item['status'] === 'low')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                In Stock
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Insights -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Insights</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Most Valuable Stock</h4>
                        @php
                            $mostValuable = $inventoryData->sortByDesc('stock_value')->first();
                        @endphp
                        @if($mostValuable)
                            <div class="bg-blue-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-blue-800">{{ $mostValuable['product']->name }}</p>
                                <p class="text-xs text-blue-600">₦{{ number_format($mostValuable['stock_value'], 2) }} stock value</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Highest Potential Profit</h4>
                        @php
                            $highestProfit = $inventoryData->sortByDesc('potential_profit')->first();
                        @endphp
                        @if($highestProfit)
                            <div class="bg-purple-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-purple-800">{{ $highestProfit['product']->name }}</p>
                                <p class="text-xs text-purple-600">₦{{ number_format($highestProfit['potential_profit'], 2) }} potential profit</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Action Items</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        @if($inventoryData->where('status', 'out')->count() > 0)
                            <li class="text-red-600">• {{ $inventoryData->where('status', 'out')->count() }} product(s) are out of stock and need immediate replenishment</li>
                        @endif
                        @if($inventoryData->where('status', 'low')->count() > 0)
                            <li class="text-yellow-600">• {{ $inventoryData->where('status', 'low')->count() }} product(s) are at or below their configured threshold</li>
                        @endif
                        <li>• Total inventory value: ₦{{ number_format($totalStockValue, 2) }}</li>
                        <li>• Potential profit from current stock: ₦{{ number_format($totalPotentialProfit, 2) }}</li>
                        @if($totalStockValue > 0)
                            <li>• Overall profit margin: {{ number_format(($totalPotentialProfit / $totalStockValue) * 100, 1) }}%</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Restock Recommendations -->
        @php
            $needRestock = $inventoryData->whereIn('status', ['low', 'out']);
        @endphp
        @if($needRestock->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-yellow-800">Restock Recommendations</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($needRestock as $item)
                            <div class="bg-yellow-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-yellow-800">{{ $item['product']->name }}</p>
                                <p class="text-xs text-yellow-600">Current: {{ number_format($item['current_stock'], 1) }} units</p>
                                <p class="text-xs text-yellow-600">Threshold: {{ number_format($item['low_stock_threshold']) }} units</p>
                                <p class="text-xs text-yellow-600">Location: {{ $item['product']->storage_location ?: 'No location assigned' }}</p>
                                <a href="{{ route('purchases.create') }}?product={{ $item['product']->id }}" class="text-xs text-yellow-700 hover:text-yellow-600 underline">Restock now</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>
