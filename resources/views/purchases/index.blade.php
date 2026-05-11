<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Goods Receipt Register
            </h2>
            <a href="{{ route('purchases.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Record Goods Receipt
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
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

                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input type="text"
                               id="supplier"
                               name="supplier"
                               value="{{ request('supplier') }}"
                               placeholder="Search by supplier name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 mr-2">
                            Filter
                        </button>
                        <a href="{{ request()->url() }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Goods Receipt Summary -->
        @if($purchases->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Receipt Value</p>
                        <p class="text-2xl font-bold text-blue-600">₦{{ number_format($purchases->sum('total_cost'), 2) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Quantity</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($purchases->sum('quantity'), 1) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Receipts</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $purchases->count() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Goods Receipt Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($purchases as $purchase)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $purchase->purchase_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $purchase->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ ucfirst($purchase->product->unit_type) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $purchase->supplier_name }}</div>
                                    @if($purchase->supplier_phone)
                                        <div class="text-sm text-gray-500">{{ $purchase->supplier_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($purchase->quantity, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($purchase->purchase_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    ₦{{ number_format($purchase->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $purchase->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('purchases.edit', $purchase) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No goods receipts found.
                                    <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:text-blue-500 ml-1">Record your first goods receipt</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-shop-layout>
