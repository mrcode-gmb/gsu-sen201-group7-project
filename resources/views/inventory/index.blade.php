<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ auth()->user()->isAdmin() ? 'Inventory Register' : 'Available Inventory' }}
            </h2>
            @if (auth()->user()->isAdmin())
                <div class="flex items-center space-x-2">
                    <a href="{{ route('inventory.categories.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Manage Categories
                    </a>
                    <a href="{{ route('inventory.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Add Inventory Item
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (auth()->user()->isAdmin() && $lowStockProducts->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-medium text-red-800">Low Stock Alert</h3>
                                <p class="text-sm text-red-600">These inventory items are at or below their configured
                                    warehouse threshold.</p>
                            </div>
                        </div>
                        <a href="{{ route('reports.inventory') }}" class="text-sm text-blue-600 hover:text-blue-500">
                            View report
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($lowStockProducts->take(6) as $lowStockProduct)
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-red-800">{{ $lowStockProduct->name }}</p>
                                <p class="text-xs text-red-600">
                                    SKU: {{ $lowStockProduct->sku ?: 'Not assigned' }}
                                </p>
                                <p class="text-xs text-red-600">
                                    Stock: {{ number_format($lowStockProduct->warehouse_stock ?? 0, 1) }} units
                                    • Threshold: {{ number_format($lowStockProduct->effective_low_stock_threshold) }}
                                </p>
                                <p class="text-xs text-red-600">
                                    Location: {{ $lowStockProduct->storage_location ?: 'No location assigned' }}
                                </p>
                                <a href="{{ route('purchases.create', ['product' => $lowStockProduct->id]) }}"
                                    class="text-xs text-red-700 hover:text-red-600 underline">
                                    Record goods receipt
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="unit_type" class="block text-sm font-medium text-gray-700">Unit Type</label>
                        <select id="unit_type" name="unit_type"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="Customize" {{ request('unit_type') == 'Customize' ? 'selected' : '' }}>
                                Customize</option>
                            <option value="Uncustomize"
                                {{ request('unit_type') == 'Uncustomize' ? 'selected' : '' }}>Uncustomize</option>
                        </select>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category_id" name="category_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stock_filter" class="block text-sm font-medium text-gray-700">Stock Level</label>
                        <select id="stock_filter" name="stock_filter"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Stock Levels</option>
                            <option value="available"
                                {{ request('stock_filter') == 'available' ? 'selected' : '' }}>Available (> 0)
                            </option>
                            <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Low Stock
                                (Below Threshold)</option>
                            <option value="out" {{ request('stock_filter') == 'out' ? 'selected' : '' }}>Out of Stock
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end space-x-2">
                        <button type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Filter
                        </button>
                        <a href="{{ request()->url() }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Inventory Batches</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $products->count() }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Available Stock</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($products->sum('quantity'), 1) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Low Stock Items</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $products->filter(function ($product) {
                                    return $product->quantity > 0 && $product->quantity <= $product->product->effective_low_stock_threshold;
                                })->count() }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600">{{ $products->where('quantity', '<=', 0)->count() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-lg {{ $product->quantity == 0 ? 'border-l-4 border-red-500' : ($product->quantity <= $product->product->effective_low_stock_threshold ? 'border-l-4 border-yellow-500' : 'border-l-4 border-green-500') }}">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $product->product->name }}</h3>
                                <p class="text-sm text-gray-500 capitalize">{{ $product->product->unit_type }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $product->product->category?->name ?: 'Uncategorized' }}
                                    • SKU: {{ $product->product->sku ?: 'Not assigned' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Location: {{ $product->product->storage_location ?: 'No location assigned' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p
                                    class="text-lg font-bold {{ $product->quantity == 0 ? 'text-red-600' : ($product->quantity <= $product->product->effective_low_stock_threshold ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($product->quantity, 1) }}
                                </p>
                                <p class="text-xs text-gray-500">units</p>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Purchase Price:</span>
                                <span class="text-sm font-medium">₦{{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Low Stock Threshold:</span>
                                <span class="text-sm font-medium">{{ number_format($product->product->effective_low_stock_threshold) }} units</span>
                            </div>
                            @if ($product->notes)
                                <p class="text-sm text-gray-600">{{ $product->notes }}</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            @if ($product->quantity == 0)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($product->quantity <= $product->product->effective_low_stock_threshold)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ auth()->user()->isAdmin() ? route('inventory.show', $product) : route('sales.inventory.show', $product) }}"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-md text-sm font-medium transition-colors duration-200">
                                View Details
                            </a>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('inventory.edit', $product->product) }}"
                                    class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-md text-sm font-medium transition-colors duration-200">
                                    Edit
                                </a>
                            @endif
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex flex-wrap gap-3 text-sm">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('purchases.restock', $product->id) }}"
                                        class="text-green-600 hover:text-green-500">
                                        Restock
                                    </a>
                                @endif
                                @if ($product->quantity > 0)
                                    <a href="{{ route('sales.create') }}?product={{ $product->id }}"
                                        class="text-blue-600 hover:text-blue-500">
                                        Record Goods Dispatch
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-3 bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            {{ auth()->user()->isAdmin() ? 'No inventory items found' : 'No available inventory found' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ auth()->user()->isAdmin() ? 'Get started by adding your first inventory item.' : 'There is no stock available for dispatch right now.' }}
                        </p>
                        @if (auth()->user()->isAdmin())
                            <div class="mt-6">
                                <a href="{{ route('inventory.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add Inventory Item
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-shop-layout>
