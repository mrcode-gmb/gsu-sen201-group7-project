<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Assignment Details - {{ $assignment->user->name }}
            </h2>
            <div class="flex space-x-2">
                @if ($assignment->status !== 'completed')
                    <button
                        onclick="showCollectModal({{ $assignment->id }}, '{{ $assignment->user->name }}', '{{ $assignment->purchase->product->name ?? 'N/A' }}')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Collect Return
                    </button>
                @endif
                <a href="{{ route('admin.assignments.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Assignments 
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Assignment Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Staff Member</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $assignment->user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $assignment->purchase->product->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span
                            class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @switch($assignment->status)
                                @case('assigned') bg-yellow-100 text-yellow-800 @break
                                @case('in_progress') bg-blue-100 text-blue-800 @break
                                @case('completed') bg-green-100 text-green-800 @break
                                @case('returned') bg-gray-100 text-gray-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No due date' }}</p>
                    </div>

                    @if ($assignment->returned_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Expected Selling Price</label>
                            <p class="mt-1 text-sm text-gray-900">
                                ₦{{ number_format($assignment->expected_selling_price, 2) }} per unit</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Commission Rate</label>
                        <p class="mt-1 text-sm text-gray-900">{{ number_format($assignment->commission_rate, 2) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Prices by Customer Type -->
        @if($assignment->salePrices && $assignment->salePrices->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Prices by Customer Type</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sale Price
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assignment->salePrices as $salePrice)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $salePrice->customer_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($salePrice->sale_price, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Quantity & Financial Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quantity Summary -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quantity Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Assigned
                                Quantity:</span>
                            <span class="text-sm text-gray-900">{{ $assignment->assigned_quantity }}
                                units</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Sold Quantity:</span>
                            <span class="text-sm text-gray-900">{{ $assignment->sold_quantity }}
                                units</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Returned
                                Quantity:</span>
                            <span class="text-sm text-gray-900">{{ $assignment->total_collected_quantity }}
                                units</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium text-gray-700">Remaining
                                Quantity:</span>
                            <span
                                class="text-sm font-semibold text-gray-900">{{ $assignment->remaining_quantity }}
                                units</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Expected Selling
                                Price:</span>
                            <span
                                class="text-sm text-gray-900">₦{{ number_format($assignment->expected_selling_price, 2) }}/unit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Expected Revenue:</span>
                            <span
                                class="text-sm text-gray-900">₦{{ number_format($assignment->expected_selling_price * $assignment->assigned_quantity, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Actual Sales:</span>
                            <span
                                class="text-sm text-gray-900">₦{{ number_format($assignment->actual_total_sales, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Expected Profit:</span>
                            <span
                                class="text-sm text-gray-900">₦{{ number_format($assignment->expected_profit, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700">Actual Profit:</span>
                            <span
                                class="text-sm text-gray-900">₦{{ number_format($assignment->actual_profit, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium text-gray-700">Profit Collected:</span>
                            <span
                                class="text-sm font-semibold text-green-600">₦{{ number_format($assignment->profit_collected, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales History -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Sales History</h3>

                                @if ($assignment->sales->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Date</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Customer</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Quantity</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Price/Unit</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Total</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Payment</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Profit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->sales as $sale)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $sale->sale_date->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $sale->customer_name ?: 'N/A' }}
                                                            @if ($sale->customer_phone)
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $sale->customer_phone }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $sale->quantity }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            ₦{{ number_format($sale->selling_price_per_unit, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            ₦{{ number_format($sale->total_amount, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                {{ ucfirst(str_replace('_', ' ', $sale->payment_type)) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            ₦{{ number_format($sale->profit, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No sales recorded yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Collection History -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Collection History</h3>

                                @if ($assignment->collectionHistories->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Collected By</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Quantity</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Before</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        After</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->collectionHistories->sortByDesc('collected_at') as $history)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $history->collected_at->format('M d, Y H:i') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $history->collectedBy->name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ number_format($history->collected_quantity, 2) }} units
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ number_format($history->remaining_quantity_before, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ number_format($history->remaining_quantity_after, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 text-sm text-gray-900">
                                                            {{ $history->notes ?: '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No collections recorded yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        @if ($assignment->notes)
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                                    <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $assignment->notes }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Collect Return Modal -->
                    <div id="collectModal"
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Collect Return</h3>
                                <form id="collectForm" method="POST"
                                    action="{{ route('admin.assignments.collectReturn', $assignment) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Staff &
                                            Product</label>
                                        <p id="staffProduct" class="text-sm text-gray-900 mt-1">
                                            {{ $assignment->user->name }} -
                                            {{ $assignment->purchase->product->name ?? 'N/A' }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="returned_quantity"
                                            class="block text-sm font-medium text-gray-700">Collect Quantity (Max:
                                            {{ $assignment->remaining_quantity }})</label>
                                        <input type="number" step="0.01" name="returned_quantity"
                                            id="returned_quantity" required
                                            max="{{ $assignment->remaining_quantity }}"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">Remaining quantity: {{ $assignment->remaining_quantity }} units</p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="notes"
                                            class="block text-sm font-medium text-gray-700">Notes</label>
                                        <textarea name="notes" id="notes" rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="hideCollectModal()"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</button>
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-md">Collect
                                            Return</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showCollectModal(assignmentId, staffName, productName) {
            document.getElementById('collectModal').classList.remove('hidden');
        }

        function hideCollectModal() {
            document.getElementById('collectModal').classList.add('hidden');
            document.getElementById('collectForm').reset();
        }
    </script>
</x-shop-layout>
