<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Operations Summary Report
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.profit.pdf', ['period' => $period, 'year' => $year] + request()->query() ) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Export as PDF
                </a>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                        <select id="period" 
                                name="period" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily (Current Month)</option>
                        </select>
                    </div>

                    @if($period == 'monthly')
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                            <select id="year" 
                                    name="year" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @endif

                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Total Dispatch Value</p>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($profits->sum('sales'), 2) }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Performance Value</p>
                    <p class="text-2xl font-bold text-blue-600">₦{{ number_format($profits->sum('profit'), 2) }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500">Average Performance Rate</p>
                    <p class="text-2xl font-bold text-purple-600">
                        {{ $profits->sum('sales') > 0 ? number_format(($profits->sum('profit') / $profits->sum('sales')) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Operations Chart Visualization -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $period == 'monthly' ? 'Monthly' : 'Daily' }} Operations Trend
                </h3>
                <div class="h-64 flex items-end justify-between space-x-2">
                    @foreach($profits as $index => $profit)
                        @php
                            $maxProfit = $profits->max('profit');
                            $height = $maxProfit > 0 ? ($profit['profit'] / $maxProfit) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-500 rounded-t" style="height: {{ $height }}%"></div>
                            <div class="text-xs text-gray-600 mt-2 text-center">
                                {{ $period == 'monthly' ? substr($profit['period'], 0, 3) : substr($profit['period'], -2) }}
                            </div>
                            <div class="text-xs text-blue-600 font-medium">
                                ₦{{ number_format($profit['profit'], 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Detailed Operations Data -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detailed Operations Analysis</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispatch Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($profits as $profit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $profit['period'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                        ₦{{ number_format($profit['sales'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                        ₦{{ number_format($profit['profit'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($profit['margin'], 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($profit['margin'] >= 30)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Excellent
                                            </span>
                                        @elseif($profit['margin'] >= 20)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Good
                                            </span>
                                        @elseif($profit['margin'] >= 10)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Average
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Poor
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Insights -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Key Insights</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Best Performing Period</h4>
                        @php
                            $bestPeriod = $profits->sortByDesc('profit')->first();
                        @endphp
                        @if($bestPeriod)
                            <div class="bg-green-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-green-800">{{ $bestPeriod['period'] }}</p>
                                <p class="text-xs text-green-600">₦{{ number_format($bestPeriod['profit'], 2) }} profit ({{ number_format($bestPeriod['margin'], 1) }}% margin)</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Lowest Performing Period</h4>
                        @php
                            $worstPeriod = $profits->sortBy('profit')->first();
                        @endphp
                        @if($worstPeriod)
                            <div class="bg-red-50 p-3 rounded-md">
                                <p class="text-sm font-medium text-red-800">{{ $worstPeriod['period'] }}</p>
                                <p class="text-xs text-red-600">₦{{ number_format($worstPeriod['profit'], 2) }} profit ({{ number_format($worstPeriod['margin'], 1) }}% margin)</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Recommendations</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        @if($profits->avg('margin') < 20)
                            <li>• Consider reviewing pricing strategy to improve profit margins</li>
                        @endif
                        @if($profits->where('profit', 0)->count() > 0)
                            <li>• Focus on periods with zero profit to identify improvement opportunities</li>
                        @endif
                        <li>• Analyze best performing periods to replicate success strategies</li>
                        <li>• Monitor inventory turnover to optimize stock levels</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
