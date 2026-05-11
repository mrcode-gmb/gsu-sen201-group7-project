<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Product Assignments
            </h2>
            <a href="{{ route('admin.assignments.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Assign Product to Staff
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Assignments Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Assignments</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $totalAssignments }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Assignments Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Assignments</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $activeAssignments }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Assignments Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $completedAssignments }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Assignments Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $overdueAssignments }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quantity & Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quantity Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quantity Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Assigned:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($totalAssignedQty, 2) }} units</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Sold:</span>
                            <span class="text-sm font-semibold text-green-600">{{ number_format($totalSoldQty, 2) }} units</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Collected:</span>
                            <span class="text-sm font-semibold text-blue-600">{{ number_format($totalCollectedQty, 2) }} units</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-sm font-medium text-gray-700">Remaining:</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($totalRemainingQty, 2) }} units</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Cost:</span>
                            <span class="text-sm font-semibold text-red-600">₦{{ number_format($totalCost, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Expected Revenue:</span>
                            <span class="text-sm font-semibold text-gray-900">₦{{ number_format($totalExpectedRevenue, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Actual Sales:</span>
                            <span class="text-sm font-semibold text-green-600">₦{{ number_format($totalActualSales, 2) }}</span>
                        </div>
                        {{-- <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Expected Profit:</span>
                            <span class="text-sm font-semibold text-gray-900">₦{{ number_format($totalExpectedProfit, 2) }}</span>
                        </div> --}}
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-sm font-medium text-gray-700">Actual Profit:</span>
                            <span class="text-sm font-bold text-green-600">₦{{ number_format($totalActualProfit, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('admin.assignments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Staff Filter -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                        <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">All Staff</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <div class="flex space-x-2">
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 whitespace-nowrap">
                                Filter
                            </button>
                            <a href="{{ route('admin.assignments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center whitespace-nowrap">
                                Clear
                            </a>
                            <a href="{{ $exportUrl }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collected</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50 {{ $assignment->isOverdue() ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $assignment->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $assignment->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $assignment->purchase->product->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">₦{{ number_format($assignment->expected_selling_price, 2) }}/unit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->assigned_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->sold_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->total_collected_quantity ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->remaining_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($assignment->status)
                                            @case('assigned') bg-yellow-100 text-yellow-800 @break
                                            @case('in_progress') bg-blue-100 text-blue-800 @break
                                            @case('completed') bg-green-100 text-green-800 @break
                                            @case('returned') bg-gray-100 text-gray-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                    @if($assignment->isOverdue())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-1">
                                            Overdue
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No due date' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.assignments.show', $assignment) }}"
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @if($assignment->status !== 'completed')
                                            <button onclick="showCollectModal({{ $assignment->id }}, '{{ $assignment->user->name }}', '{{ $assignment->purchase->product->name ?? 'N/A' }}')"
                                                    class="text-green-600 hover:text-green-900">Collect Return</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No product assignments found.
                                    <a href="{{ route('admin.assignments.create') }}"
                                        class="text-blue-600 hover:text-blue-500 ml-1">Create your first assignment</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Collect Return Modal -->
    <div id="collectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Collect Return</h3>
                <form id="collectForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Staff & Product</label>
                        <p id="staffProduct" class="text-sm text-gray-900 mt-1"></p>
                    </div>

                    <div class="mb-4">
                        <label for="returned_quantity" class="block text-sm font-medium text-gray-700">Returned Quantity</label>
                        <input type="number" step="0.01" name="returned_quantity" id="returned_quantity" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>



                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full px-3 py-2 resize-none border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideCollectModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</button>
                        <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-md">Collect Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCollectModal(assignmentId, staffName, productName) {
            document.getElementById('staffProduct').textContent = staffName + ' - ' + productName;
            document.getElementById('collectForm').action = `/admin/assignments/${assignmentId}/collect-return`;
            document.getElementById('collectModal').classList.remove('hidden');
        }

        function hideCollectModal() {
            document.getElementById('collectModal').classList.add('hidden');
            document.getElementById('collectForm').reset();
        }
    </script>
</x-shop-layout>
