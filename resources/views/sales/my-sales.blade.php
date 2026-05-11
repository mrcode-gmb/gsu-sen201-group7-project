<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ auth()->user()->isAdmin() ? 'All Goods Dispatches' : 'My Goods Dispatches' }}
            </h2>
            @if (auth()->user()->isSalesperson())
                <div>
                    <a href="{{ route('sales.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Record Goods Dispatch
                    </a>

                    @if (!$sales->isEmpty())
                        <button type="button" id="printSelected"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 ml-2">
                            Print Selected
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>
    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-semibold mb-6">My Goods Dispatches</h2>

                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('sales.my-sales') }}" method="GET"
                            class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                            <div class="flex-1">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start
                                    Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ request('start_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex-1">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex-1">
                                <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment
                                    Type</label>
                                <select name="payment_type" id="payment_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="all">All Types</option>
                                    <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    <option value="card" {{ request('payment_type') === 'card' ? 'selected' : '' }}>
                                        Card</option>
                                    <option value="transfer"
                                        {{ request('payment_type') === 'transfer' ? 'selected' : '' }}>Bank Transfer
                                    </option>
                                    <option value="credit" {{ request('payment_type') === 'credit' ? 'selected' : '' }}>
                                        Credit</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Dispatch Summary -->
                    @if ($paymentSummary->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Dispatch Summary</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                @foreach ($paymentSummary as $summary)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm font-medium text-gray-500">
                                            {{ ucfirst($summary->payment_type) }}</p>
                                        <p class="text-xl font-semibold">{{ $summary->count }} dispatches</p>
                                        <p class="text-lg">₦{{ number_format($summary->total, 2) }}</p>
                                    </div>
                                @endforeach
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-blue-700">Total Dispatch Value</p>
                                    <p class="text-xl font-semibold text-blue-700">₦{{ number_format($totalSales, 2) }}
                                    </p>
                                </div>


                            </div>
                        </div>
                    @endif

                    <!-- Sales Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'sale_date', 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === 'sale_date' ? 'desc' : 'asc']) }}"
                                            class="flex items-center">
                                            Date
                                            @if (request('sort_by') === 'sale_date')
                                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit Price</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Method</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @if (auth()->user()->isAdmin())
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Staff</th>
                                    @endif
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Receiver</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="sale_ids[]" value="{{ $sale->id }}"
                                                class="sale-checkbox rounded text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $sale->sale_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $sale->purchase->product->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ ucfirst($sale->unique_id) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($sale->quantity, 1) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₦{{ number_format($sale->selling_price_per_unit, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                            ₦{{ number_format($sale->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($sale->payment_type ?? 'cash')
                                                @case('cash') bg-green-100 text-green-800 @break
                                                @case('bank_transfer') bg-blue-100 text-blue-800 @break
                                                @case('pos') bg-purple-100 text-purple-800 @break
                                                @case('mobile_money') bg-yellow-100 text-yellow-800 @break
                                                @case('credit') bg-red-100 text-red-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                                @if (($sale->payment_type ?? 'cash') === 'bank_transfer')
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-800"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                @elseif(($sale->payment_type ?? 'cash') === 'pos')
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-800"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                @elseif(($sale->payment_type ?? 'cash') === 'mobile_money')
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-800"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                @elseif(($sale->payment_type ?? 'cash') === 'credit')
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-800"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                @else
                                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-800"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                @endif
                                                {{ $paymentTypes[$sale->payment_type ?? 'cash'] ?? ucfirst(str_replace('_', ' ', $sale->payment_type ?? 'cash')) }}
                                            </span>
                                        </td>
                                        @if (auth()->user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $sale->user->name }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $sale->customer_name ?: 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('salesperson.show', $sale) }}"
                                                    class="text-blue-600 hover:text-blue-900">View</a>
                                                @if (auth()->user()->isAdmin())
                                                    <a href="{{ route('sales.edit', $sale) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                                                        class="inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this sale?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? '9' : '8' }}"
                                            class="px-6 py-4 text-center text-sm text-gray-500">
                                            No sales found.
                                            @if (auth()->user()->isSalesperson())
                                                <a href="{{ route('sales.create') }}"
                                                    class="text-blue-600 hover:text-blue-500 ml-1">Record your first
                                                    sale</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- <!-- Pagination -->
                    <div class="mt-4">
                        {{ $sales->appends(request()->except('page'))->links() }}
                    </div> --}}
                </div>
            </div>
        </div>
        <!-- Add this right before the closing </div> of the main content -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select all checkboxes
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.sale-checkbox');
                const printBtn = document.getElementById('printSelected');

                if (selectAll && checkboxes.length > 0) {
                    // Toggle all checkboxes
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                    });

                    // Update "Select All" when individual checkboxes change
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                            selectAll.checked = allChecked;
                        });
                    });
                }


                if (printBtn) {
                    printBtn.addEventListener('click', function() {
                        const selectedCheckboxes = Array.from(document.querySelectorAll(
                            '.sale-checkbox:checked'));

                        if (selectedCheckboxes.length === 0) {
                            alert('Please select at least one sale to print');
                            return;
                        }

                        // Get all selected sale IDs
                        const saleIds = selectedCheckboxes.map(checkbox => checkbox.value);

                        // Debug: Log the selected sale IDs
                        console.log('Selected sale IDs:', saleIds);

                        // Open the multiple receipts view
                        const url = "{{ route('sales.print-multiple-receipts') }}?sale_ids=" + saleIds.join(
                            ',');
                        console.log('Generated URL:', url); // Debug log

                        const printWindow = window.open(url, '_blank');

                        // Add error handling for popup blockers
                        if (!printWindow || printWindow.closed || typeof printWindow.closed === 'undefined') {
                            alert('Popup was blocked. Please allow popups for this site and try again.');
                        }
                    });
                }
            });
        </script>
    </div>
</x-shop-layout>
