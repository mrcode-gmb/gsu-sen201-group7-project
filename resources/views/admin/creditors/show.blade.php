<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Creditors
        </h2>
    </x-slot>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Credit</h3>
            <p class="text-2xl font-bold text-red-600">₦{{ number_format($total_credit, 2) }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Paid</h3>
            <p class="text-2xl font-bold text-green-600">₦{{ number_format($total_paid, 2) }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Balance</h3>
            <p class="text-2xl font-bold text-blue-600">₦{{ number_format($creditor->balance, 2) }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction History</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Running Balance</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Print</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'debit' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($transaction->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($transaction->running_balance, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.creditors.print-transaction', $transaction) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Creditor Information</h3>
                    <a href="{{ route('admin.creditors.print', $creditor) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
                        </svg>
                        Print
                    </a>
                </div>
                <p><strong>Name:</strong> {{ $creditor->name }}</p>
                <p><strong>Email:</strong> {{ $creditor->email }}</p>
                <p><strong>Phone:</strong> {{ $creditor->phone }}</p>
                <p><strong>Address:</strong> {{ $creditor->address }}</p>
                <p class="mt-4 text-xl"><strong>Current Balance:</strong> <span class="text-red-600">₦{{ number_format($creditor->balance, 2) }}</span></p>
            </div>
            @if (!Auth::user()->isAdmin())   
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Record a Payment</h3>
                    <form action="{{ route('admin.creditors.record-payment', $creditor) }}" method="POST">
                        @csrf
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount Paid</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md">Record Payment</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Sales on Credit -->
    <div class="mt-8">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Sales on Credit</h3>
        <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold By</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Print</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('d M, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sale->purchase->product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('sales.print-receipt', $sale) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No sales found for this creditor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{-- {{ $sales->links('vendor.pagination.tailwind') }} --}}
            </div>
        </div>
    </div>
</x-shop-layout>
