<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Assign Product to Staff
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-6">
                    @csrf

                    <!-- Staff Selection -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Select Staff
                            Member</label>
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a staff member</option>
                            @foreach ($staff as $member)
                                <option value="{{ $member->id }}"
                                    {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product Selection -->
                    <div>
                        <label for="purchase_id" class="block text-sm font-medium text-gray-700">Select Product</label>
                        <select name="purchase_id" id="purchase_id" required onchange="updateProductInfo()"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a product</option>
                            @foreach ($products as $purchase)
                                <option value="{{ $purchase->id }}" data-available="{{ $purchase->quantity }}"
                                    data-cost="{{ $purchase->purchase_price }}"
                                    data-selling="{{ $purchase->selling_price_per_unit }}"
                                    {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                    {{ $purchase->product->name }} - Available: {{ $purchase->quantity }} units (Cost:
                                    ₦{{ number_format($purchase->purchase_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div id="productInfo" class="mt-2 text-sm text-gray-600 hidden">
                            <p>Available Quantity: <span id="availableQty">0</span> units</p>
                            <p>Cost Price: ₦<span id="costPrice">0.00</span> per unit</p>
                            <p><span id="suggestedPrice"></span></p>
                        </div>
                    </div>

                    <!-- Assignment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="assigned_quantity" class="block text-sm font-medium text-gray-700">Quantity to
                                Assign</label>
                            @error('assigned_quantity')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <input type="number" step="0.01" name="assigned_quantity" id="assigned_quantity"
                                required value="{{ old('assigned_quantity') }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="expected_selling_price" class="block text-sm font-medium text-gray-700">Expected
                                Selling Price per Unit (₦)</label>
                            <input type="number" step="0.01" min="0.01" name="expected_selling_price"
                                id="expected_selling_price" required value="{{ old('expected_selling_price') }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('expected_selling_price')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission Rate -->
                        <div>
                            <label for="commission_rate" class="block text-sm font-medium text-gray-700">Commission
                                Fee ₦ (Optional)</label>
                            <input type="number" name="commission_rate" id="commission_rate"
                                value="{{ old('commission_rate') ?? 0 }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('commission_rate')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sale Prices -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sale Prices by Customer Type</label>
                            <div id="salePricesContainer" class="space-y-3">
                                <div class="sale-price-row flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-600">Customer Type</label>
                                        <select name="customer_types[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Type</option>
                                            <option value="Dealer">Dealer</option>
                                            <option value="Wholesaler 1">Wholesaler 1</option>
                                            <option value="Wholesaler 2">Wholesaler 2</option>
                                            <option value="Wholesaler 3">Wholesaler 3</option>
                                            <option value="Consumer">Consumer</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-600">Price (₦)</label>
                                        <input type="number" step="0.01" min="0.01" name="sale_prices[]" placeholder="0.00"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <button type="button" onclick="removeSalePriceRow(this)"
                                            class="mt-1 px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 hidden remove-btn">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="addSalePriceRow()"
                                class="mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm">
                                + Add Another Price
                            </button>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes
                                (Optional)</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Profit Calculation -->
                        <div id="profitCalculation" class="bg-gray-50 p-4 rounded-md hidden">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Profit Calculation</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Expected Revenue:</span> ₦<span
                                        id="expectedRevenue">0.00</span>
                                </div>
                                <div>
                                    <span class="font-medium">Total Cost:</span> ₦<span id="totalCost">0.00</span>
                                </div>
                                <div>
                                    <span class="font-medium">Expected Profit:</span> ₦<span
                                        id="expectedProfit">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                    </div>
                    <div class="flex justify-between pt-6 w-ful; border-t border-gray-200">
                        <div>
                            <a href="{{ route('admin.assignments.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</a>
                        </div>
                        <div>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-6 rounded-md">Assign
                                Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateProductInfo() {
            const select = document.getElementById('purchase_id');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                const available = selectedOption.dataset.available;
                const cost = selectedOption.dataset.cost;
                const selling = selectedOption.dataset.selling;

                document.getElementById('availableQty').textContent = available;
                document.getElementById('costPrice').textContent = parseFloat(cost).toFixed(2);
                document.getElementById('suggestedPrice').textContent = parseFloat(selling).toFixed(2);
                document.getElementById('expected_selling_price').value = selling;
                document.getElementById('productInfo').classList.remove('hidden');

                calculateProfit();
            } else {
                document.getElementById('productInfo').classList.add('hidden');
                document.getElementById('profitCalculation').classList.add('hidden');
            }
        }

        function calculateProfit() {
            const quantity = parseFloat(document.getElementById('assigned_quantity').value) || 0;
            const sellingPrice = parseFloat(document.getElementById('expected_selling_price').value) || 0;
            const select = document.getElementById('purchase_id');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value && quantity > 0 && sellingPrice > 0) {
                const costPrice = parseFloat(selectedOption.dataset.cost) || 0;
                const expectedRevenue = quantity * sellingPrice;
                const totalCost = quantity * costPrice;
                const expectedProfit = expectedRevenue - totalCost;

                document.getElementById('expectedRevenue').textContent = expectedRevenue.toFixed(2);
                document.getElementById('totalCost').textContent = totalCost.toFixed(2);
                document.getElementById('expectedProfit').textContent = expectedProfit.toFixed(2);
                document.getElementById('profitCalculation').classList.remove('hidden');
            } else {
                document.getElementById('profitCalculation').classList.add('hidden');
            }
        }

        // Add event listeners for real-time calculation
        document.getElementById('assigned_quantity').addEventListener('input', calculateProfit);
        document.getElementById('expected_selling_price').addEventListener('input', calculateProfit);

        function addSalePriceRow() {
            const container = document.getElementById('salePricesContainer');
            const rows = container.querySelectorAll('.sale-price-row');
            const rowCount = rows.length;

            // Show remove button on all rows if there are more than 1
            if (rowCount >= 1) {
                rows.forEach(row => {
                    row.querySelector('.remove-btn').classList.remove('hidden');
                });
            }

            const newRow = document.createElement('div');
            newRow.className = 'sale-price-row flex gap-3 items-end';
            newRow.innerHTML = `
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600">Customer Type</label>
                    <select name="customer_types[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="Dealer">Dealer</option>
                        <option value="Wholesaler 1">Wholesaler 1</option>
                        <option value="Wholesaler 2">Wholesaler 2</option>
                        <option value="Wholesaler 3">Wholesaler 3</option>
                        <option value="Consumer">Consumer</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600">Price (₦)</label>
                    <input type="number" step="0.01" min="0.01" name="sale_prices[]" placeholder="0.00"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="button" onclick="removeSalePriceRow(this)"
                        class="mt-1 px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 remove-btn">
                        Remove
                    </button>
                </div>
            `;

            container.appendChild(newRow);
        }

        function removeSalePriceRow(button) {
            const container = document.getElementById('salePricesContainer');
            const row = button.closest('.sale-price-row');
            row.remove();

            const remainingRows = container.querySelectorAll('.sale-price-row');
            if (remainingRows.length === 1) {
                remainingRows[0].querySelector('.remove-btn').classList.add('hidden');
            }
        }
    </script>
</x-shop-layout>
