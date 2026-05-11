<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ auth()->user()->isAdmin() ? 'All Goods Dispatches' : 'My Goods Dispatches' }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('sales.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Record Goods Dispatch
                </a>
                @if (!$sales->isEmpty())
                    <button type="button" id="printSelected"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Print Selected
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

                    @if (auth()->user()->isAdmin())
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Staff</label>
                            <select id="user_id" name="user_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Staffs</option>
                                @foreach ($salespeople as $person)
                                    <option value="{{ $person->id }}"
                                        {{ request('user_id') == $person->id ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select id="payment_type" name="payment_type"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Payment Methods</option>
                            @foreach ($paymentTypes as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('payment_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Filter
                        </button>
                        <a href="{{ request()->url() }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Clear
                        </a>
                        <a href="{{ $exportUrl }}"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dispatch Summary -->
        @if ($paymentSummary->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dispatch Summary</h3>
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                        @foreach ($paymentSummary as $payment)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $paymentTypes[$payment->payment_type] ?? ucfirst($payment->payment_type) }}
                                </div>
                                <div class="mt-1 text-2xl font-semibold text-gray-900">
                                    ₦{{ number_format($payment->total, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->count }}
                                    {{ Str::plural('dispatch', $payment->count) }}</div>
                            </div>
                        @endforeach

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-800">Total Dispatch Value</div>
                            <div class="mt-1 text-2xl font-semibold text-blue-700">₦{{ number_format($totalSales, 2) }}
                            </div>
                            <div class="text-xs text-blue-600">{{ $sales->count() }}
                                {{ Str::plural('dispatch', $sales->count()) }}</div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-800">Dispatch Records</div>
                            <div class="mt-1 text-2xl font-semibold text-green-700">
                                {{ number_format($sales->count()) }}</div>
                            <div class="text-xs text-green-600">
                                {{ Str::plural('dispatch', $sales->count()) }}</div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <!-- Goods Dispatch Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'sale_date', 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === 'sale_date' ? 'desc' : 'asc']) }}"
                                    class="flex items-center">
                                    Date
                                    @if (request('sort_by') === 'sale_date')
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if (auth()->user()->isAdmin())
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Staff</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Receiver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-800" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                        @elseif(($sale->payment_type ?? 'cash') === 'pos')
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-800" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                        @elseif(($sale->payment_type ?? 'cash') === 'mobile_money')
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-800" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                        @elseif(($sale->payment_type ?? 'cash') === 'credit')
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-800" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                        @else
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-800" fill="currentColor"
                                                viewBox="0 0 8 8">
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
                                        <a href="{{ route('sales.show', $sale) }}"
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
                                <td colspan="{{ auth()->user()->isAdmin() ? '10' : '9' }}"
                                    class="px-6 py-4 text-center text-sm text-gray-500">
                                    No goods dispatches found.
                                    @if (auth()->user()->isSalesperson())
                                        <a href="{{ route('sales.create') }}"
                                            class="text-blue-600 hover:text-blue-500 ml-1">Record your first goods dispatch</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            alert('Please select at least one dispatch record to print');
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
