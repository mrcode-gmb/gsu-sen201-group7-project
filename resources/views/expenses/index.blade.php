<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Expenses
            </h2>
            <a href="{{ route('expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Record New Expense
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
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700">Expenses Title</label>
                        <input type="text" name="supplier" id="supplier" value="{{ request('supplier') }}" placeholder="Supplier name"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring focus:ring-blue-500">
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filter
                        </button>
                        <a href="{{ route('expenses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                            Clear
                        </a>
                        <a href="{{ $exportUrl }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        @if($expenses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-500">Total Expenses Amount</p>
                    <p class="text-2xl font-bold text-blue-600">₦{{ number_format($expenses->sum('amount'), 2) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-500">Total Expenses</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($expenses->count(), 1) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-500">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $expenses->count() }}</p>
                </div>
            </div>
        @endif

        <!-- Expenses Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier	</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expense Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Today's Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expense Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Today's Net Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>                    
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $expense->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $expense->user->name }}
                            </td>
                            	
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $expense->name}}
                            </td>
                           
                            <td class="px-6 py-4 text-sm text-blue-600 font-medium whitespace-nowrap">
                                ₦{{ number_format($expense->today_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600 font-medium whitespace-nowrap">
                                ₦{{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600 font-medium whitespace-nowrap">
                                ₦{{ number_format($expense->today_net_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $expense->notes  }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ optional($expense->user)->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{-- Optional action buttons --}}
                                <a href="">Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No expenses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($expenses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $expenses->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-shop-layout>
