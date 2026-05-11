<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Record New Rebate
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('rebates.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="purchase_id" class="block text-sm font-medium text-gray-700">Inventory Item</label>
                        <select id="purchase_id" name="purchase_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select inventory item</option>
                            @foreach ($purchases as $purchase)
                                <option value="{{ $purchase->id }}"
                                    data-product="{{ $purchase->product->name ?? 'N/A' }}"
                                    data-unit-price="{{ $purchase->purchase_price }}"
                                    data-current-qty="{{ $purchase->quantity }}"
                                    {{ (string) old('purchase_id', $selectedPurchaseId ?? '') === (string) $purchase->id ? 'selected' : '' }}>
                                    #{{ $purchase->id }} - {{ $purchase->product->name ?? 'N/A' }} (Current:
                                    {{ number_format($purchase->quantity, 1) }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity to Add
                                Back</label>
                            <input type="number" id="quantity" name="quantity" min="1" step="1"
                                value="{{ old('quantity') }}" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Purchase Price</label>
                            <div id="unit-price-display"
                                class="mt-1 px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-800">
                                ₦0.00
                            </div>
                        </div>
                    </div>

                    <div id="total-display" class="hidden bg-green-50 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Current Stock</p>
                                <p id="current-stock" class="text-blue-700 font-semibold">0</p>
                            </div>
                            <div>
                                <p class="text-gray-600">New Stock After Rebate</p>
                                <p id="new-stock" class="text-green-700 font-semibold">0</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Total Cost</p>
                                <p id="total-cost" class="text-indigo-700 font-semibold">₦0.00</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700">Note (Optional)</label>
                        <textarea id="note" name="note" rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('rebates.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200">
                            Record Rebate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const purchaseSelect = document.getElementById('purchase_id');
            const quantityInput = document.getElementById('quantity');
            const unitPriceDisplay = document.getElementById('unit-price-display');
            const totalDisplay = document.getElementById('total-display');
            const currentStock = document.getElementById('current-stock');
            const newStock = document.getElementById('new-stock');
            const totalCost = document.getElementById('total-cost');

            function calculate() {
                const selectedOption = purchaseSelect.options[purchaseSelect.selectedIndex];
                const quantity = parseInt(quantityInput.value || '0', 10);

                if (!selectedOption || !selectedOption.value) {
                    unitPriceDisplay.textContent = '₦0.00';
                    totalDisplay.classList.add('hidden');
                    return;
                }

                const unitPrice = parseFloat(selectedOption.dataset.unitPrice || '0');
                const currentQty = parseFloat(selectedOption.dataset.currentQty || '0');
                const total = quantity > 0 ? unitPrice * quantity : 0;
                const resultingQty = quantity > 0 ? currentQty + quantity : currentQty;

                unitPriceDisplay.textContent = `₦${unitPrice.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                currentStock.textContent = currentQty.toFixed(1);
                newStock.textContent = resultingQty.toFixed(1);
                totalCost.textContent = `₦${total.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                if (quantity > 0) {
                    totalDisplay.classList.remove('hidden');
                } else {
                    totalDisplay.classList.add('hidden');
                }
            }

            purchaseSelect.addEventListener('change', calculate);
            quantityInput.addEventListener('input', calculate);

            if (purchaseSelect.value) {
                calculate();
            }
        });
    </script>
</x-shop-layout>

