<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sale Recorded Successfully
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="text-center mb-6">
                    <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-2xl font-bold text-gray-900 mt-4">Sale Recorded!</h3>
                    <p class="text-gray-600">The sale has been successfully recorded.</p>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h4>
                    <div class="space-y-4">
                        @php $grandTotal = 0; @endphp
                        @foreach($sales as $sale)
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $sale->purchase->product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $sale->quantity }} x ₦{{ number_format($sale->selling_price_per_unit, 2) }}</p>
                                </div>
                                <p class="font-semibold text-gray-800">₦{{ number_format($sale->total_amount, 2) }}</p>
                            </div>
                            @php $grandTotal += $sale->total_amount; @endphp
                        @endforeach
                    </div>

                    <div class="flex justify-end items-center mt-6 pt-4 border-t">
                        <span class="text-lg font-medium text-gray-700">Grand Total:</span>
                        <span class="text-2xl font-bold text-gray-900 ml-4">₦{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-4">
                    <a href="{{ route('sales.create') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-md transition-colors duration-200">
                        Record Another Sale
                    </a>
                    <a href="{{ route('sales.print-multiple-receipts', ['sale_ids' => implode(',', $sales->pluck('id')->toArray())]) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200">
                        Print Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
