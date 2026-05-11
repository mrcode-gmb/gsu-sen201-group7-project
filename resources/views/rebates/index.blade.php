<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rebate Management
            </h2>
            <a href="{{ route('rebates.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Record New Rebate
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="purchase_id" class="block text-sm font-medium text-gray-700">Inventory
                            Item</label>
                        <select id="purchase_id" name="purchase_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Items</option>
                            @foreach ($purchases as $purchase)
                                <option value="{{ $purchase->id }}"
                                    {{ (string) request('purchase_id') === (string) $purchase->id ? 'selected' : '' }}>
                                    #{{ $purchase->id }} - {{ $purchase->product->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 mr-2">
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

        @if ($rebates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Rebates</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $rebates->total() }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Quantity</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($rebates->sum('quantity')) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">Total Cost Value</p>
                        <p class="text-2xl font-bold text-indigo-600">₦{{ number_format($rebates->sum('total_cost'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purchase</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rebates as $rebate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rebate->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $rebate->purchase_id }}</div>
                                    <div class="text-sm text-gray-500">{{ $rebate->purchase->product->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($rebate->quantity) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($rebate->unit_purchase_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    ₦{{ number_format($rebate->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rebate->creator->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('rebates.show', $rebate) }}"
                                            class="text-blue-600 hover:text-blue-900">View</a>
                                        <form method="POST" action="{{ route('rebates.destroy', $rebate) }}"
                                            class="inline"
                                            onsubmit="return confirm('Delete this rebate and revert its stock increase?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No rebates found.
                                    <a href="{{ route('rebates.create') }}"
                                        class="text-blue-600 hover:text-blue-500 ml-1">Record your first rebate</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($rebates->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $rebates->links() }}
                </div>
            @endif
        </div>
    </div>
</x-shop-layout>

