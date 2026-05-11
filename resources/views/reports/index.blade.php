@push('styles')
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            body * {
                visibility: hidden;
            }
            
            .print-content, .print-content * {
                visibility: visible;
            }
            
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            
            .print-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .print-date {
                font-size: 14px;
                color: #666;
            }
        }
    </style>
@endpush

<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inventory & Warehouse Reports
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Report Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Goods Dispatch Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Goods Dispatch Report</h3>
                            <p class="text-sm text-gray-500">Detailed outbound stock analysis and dispatch history</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-4">Review dispatch records, filter by date and staff, and export summaries for documentation.</p>
                        <a href="{{ route('reports.sales') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition-colors duration-200">
                            View Dispatch Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Operations Summary -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Operations Summary</h3>
                            <p class="text-sm text-gray-500">Monthly and yearly operational performance overview</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-4">Review operational trends over time to support warehouse planning and project analysis.</p>
                        <a href="{{ route('reports.profit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-colors duration-200">
                            View Summary
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inventory Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Inventory Report</h3>
                            <p class="text-sm text-gray-500">Stock levels and inventory valuation</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-4">Monitor stock levels, inventory value, and identify products that need restocking.</p>
                        <a href="{{ route('reports.inventory') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition-colors duration-200">
                            View Inventory Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Warehouse Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($todaySales, 2) }}</p>
                        <p class="text-sm text-gray-500">Today's Dispatch Value</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">₦{{ number_format($todayProfit, 2) }}</p>
                        <p class="text-sm text-gray-500">Today's Operations Value</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">₦{{ number_format($monthlySales, 2) }}</p>
                        <p class="text-sm text-gray-500">Monthly Dispatch Value</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $lowStockItems }}</p>
                        <p class="text-sm text-gray-500">Low Stock Items</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Moving Products -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Moving Products (This Month)</h3>
                    <div class="space-y-3">
                        @forelse($topProducts as $item)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->purchase->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($item->total_sold, 1) }} units sold</p>
                                </div>
                                <p class="text-sm font-medium text-green-600">₦{{ number_format($item->total_revenue, 0) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No dispatch data available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Active Staff -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Active Staff (This Month)</h3>
                    <div class="space-y-3">
                        @forelse($topSalespeople as $performer)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $performer->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $performer->sales_count }} transactions</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">₦{{ number_format($performer->total_sales, 0) }}</p>
                                    <p class="text-xs text-blue-600">₦{{ number_format($performer->total_profit, 0) }} profit</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No staff activity data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Export Reports</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('reports.sales.pdf') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Dispatch PDF
                    </a>
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Expenses
                    </a>
                    <button onclick="printReport()" class="no-print inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Hidden print content that will be shown when printing -->
    <div id="printableArea" class="hidden">
        <div class="print-content">
            <div class="print-header">
                <div class="print-title">Goods Dispatch Report</div>
                <div class="print-date">Generated on: {{ now()->format('F j, Y') }}</div>
            </div>
            
            <!-- Dispatch Summary -->
            @if(isset($sales) && $sales->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Dispatch Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-sm text-gray-500">Total Dispatch Value</div>
                            <div class="text-2xl font-bold">₦{{ number_format($sales->sum('total_amount'), 2) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-sm text-gray-500">Estimated Margin</div>
                            <div class="text-2xl font-bold">₦{{ number_format($sales->sum('profit'), 2) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-sm text-gray-500">Total Items Dispatched</div>
                            <div class="text-2xl font-bold">{{ $sales->sum('quantity') }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Dispatch Table -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Dispatch Details</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $sale->purchase->product->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $sale->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">₦{{ number_format($sale->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">₦{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">+₦{{ number_format($sale->profit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    No sales data available for the selected period.
                </div>
            @endif
            
            <div class="mt-8 text-xs text-gray-500 text-center">
                Printed on {{ now()->format('F j, Y \a\t g:i A') }} from {{ config('app.name') }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function printReport() {
                // Clone the printable area
                const printContent = document.getElementById('printableArea').innerHTML;
                const originalContent = document.body.innerHTML;
                
                // Replace the body content with the printable content
                document.body.innerHTML = printContent;
                
                // Add print-specific styles
                const style = document.createElement('style');
                style.innerHTML = `
                    @media print {
                        @page {
                            size: A4 landscape;
                            margin: 1cm;
                        }
                        
                        body {
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                            line-height: 1.4;
                            color: #333;
                        }
                        
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        
                        th, td {
                            padding: 8px 12px;
                            border: 1px solid #ddd;
                        }
                        
                        th {
                            background-color: #f5f5f5;
                            font-weight: 600;
                            text-align: left;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 20px;
                            padding-bottom: 10px;
                            border-bottom: 2px solid #000;
                        }
                        
                        .print-title {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        
                        .print-date {
                            font-size: 14px;
                            color: #666;
                        }
                        
                        .text-right {
                            text-align: right;
                        }
                        
                        .text-center {
                            text-align: center;
                        }
                        
                        .mb-2 {
                            margin-bottom: 0.5rem;
                        }
                        
                        .mb-4 {
                            margin-bottom: 1rem;
                        }
                        
                        .mb-6 {
                            margin-bottom: 1.5rem;
                        }
                        
                        .mt-8 {
                            margin-top: 2rem;
                        }
                        
                        .p-4 {
                            padding: 1rem;
                        }
                        
                        .bg-gray-50 {
                            background-color: #f9fafb;
                        }
                        
                        .rounded {
                            border-radius: 0.25rem;
                        }
                        
                        .grid {
                            display: grid;
                        }
                        
                        .grid-cols-3 {
                            grid-template-columns: repeat(3, minmax(0, 1fr));
                        }
                        
                        .gap-4 {
                            gap: 1rem;
                        }
                        
                        .text-sm {
                            font-size: 0.875rem;
                        }
                        
                        .text-lg {
                            font-size: 1.125rem;
                        }
                        
                        .text-2xl {
                            font-size: 1.5rem;
                        }
                        
                        .font-bold {
                            font-weight: 700;
                        }
                        
                        .font-semibold {
                            font-weight: 600;
                        }
                        
                        .text-gray-500 {
                            color: #6b7280;
                        }
                        
                        .text-gray-900 {
                            color: #111827;
                        }
                        
                        .text-green-600 {
                            color: #059669;
                        }
                        
                        .hidden {
                            display: none;
                        }
                    }
                `;
                document.head.appendChild(style);
                
                // Show the printable content
                document.getElementById('printableArea').classList.remove('hidden');
                
                // Print the page
                window.print();
                
                // Restore the original content
                document.body.innerHTML = originalContent;
                window.location.reload();
            }
        </script>
    @endpush
</x-shop-layout>
