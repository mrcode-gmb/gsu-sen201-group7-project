<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Product Assignments
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="text-2xl font-bold text-blue-600">{{ $assignments->where('status', 'assigned')->count() }}</div>
                    <div class="text-sm text-blue-600">New Assignments</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="text-2xl font-bold text-yellow-600">{{ $assignments->where('status', 'in_progress')->count() }}</div>
                    <div class="text-sm text-yellow-600">In Progress</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="text-2xl font-bold text-green-600">{{ $assignments->where('status', 'completed')->count() }}</div>
                    <div class="text-sm text-green-600">Completed</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="text-2xl font-bold text-red-600">{{ $assignments->filter(function($a) { return $a->isOverdue(); })->count() }}</div>
                    <div class="text-sm text-red-600">Overdue</div>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50 {{ $assignment->isOverdue() ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $assignment->purchase->product->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">Assigned: {{ $assignment->assigned_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->assigned_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignment->sold_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="{{ $assignment->remaining_quantity > 0 ? 'text-orange-600 font-medium' : 'text-gray-500' }}">
                                        {{ $assignment->remaining_quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($assignment->expected_selling_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($assignment->actual_total_sales, 2) }}
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
                                        @if(in_array($assignment->status, ['assigned', 'in_progress']) && $assignment->remaining_quantity > 0)
                                            <a href="{{ route('sales.create') }}?assignment_id={{ $assignment->id }}" 
                                               class="text-blue-600 hover:text-blue-900">Sell</a>
                                        @endif
                                        <button onclick="showAssignmentDetails({{ $assignment->id }})"
                                                class="text-green-600 hover:text-green-900">View Details</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No product assignments found. Contact your administrator for product assignments.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- <!-- Pagination -->
            @if ($assignments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $assignments->appends(request()->query())->links() }}
                </div>
            @endif --}}
        </div>

        <!-- Performance Summary -->
        @if($assignments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Summary</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $assignments->sum('assigned_quantity') }}</div>
                            <div class="text-sm text-blue-600">Total Assigned Units</div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $assignments->sum('sold_quantity') }}</div>
                            <div class="text-sm text-green-600">Total Units Sold</div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">₦{{ number_format($assignments->sum('actual_total_sales'), 2) }}</div>
                            <div class="text-sm text-purple-600">Total Sales Revenue</div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">₦{{ number_format($assignments->sum('profit_collected'), 2) }}</div>
                            <div class="text-sm text-yellow-600">Profit Collected</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Assignment Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Assignment Details</h3>
                    <button onclick="hideDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="assignmentDetails" class="space-y-4">
                    <!-- Details will be loaded here -->
                </div>
                
                <div class="flex justify-end mt-6">
                    <button onclick="hideDetailsModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAssignmentDetails(assignmentId) {
            // Find the assignment data from the current page
            const assignments = @json($assignments);
            const assignment = assignments.find(a => a.id === assignmentId);
            
            if (assignment) {
                const detailsHtml = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Product</label>
                            <p class="text-sm text-gray-900">${assignment.purchase.product.name || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Expected Selling Price</label>
                            <p class="text-sm text-gray-900">₦${parseFloat(assignment.expected_selling_price).toLocaleString()}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assigned Quantity</label>
                            <p class="text-sm text-gray-900">${assignment.assigned_quantity} units</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sold Quantity</label>
                            <p class="text-sm text-gray-900">${assignment.sold_quantity} units</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Remaining Quantity</label>
                            <p class="text-sm text-gray-900">${assignment.remaining_quantity} units</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Actual Sales Revenue</label>
                            <p class="text-sm text-gray-900">₦${parseFloat(assignment.actual_total_sales).toLocaleString()}</p>
                        </div>
                    </div>
                    ${assignment.notes ? `
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">${assignment.notes}</p>
                        </div>
                    ` : ''}
                `;
                
                document.getElementById('assignmentDetails').innerHTML = detailsHtml;
                document.getElementById('detailsModal').classList.remove('hidden');
            }
        }

        function hideDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
    </script>
</x-shop-layout>
