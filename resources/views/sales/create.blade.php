<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Record Goods Dispatch
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <!-- Session Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Please fix the following errors:</strong>
                        <ul class="mt-3 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
                    @csrf

                    <!-- Products Section -->
                    <div class="space-y-4" id="products-container">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Goods for Dispatch</h3>
                        <!-- Product Row Template -->
                        <div class="product-row grid grid-cols-12 gap-4 items-center p-3 bg-gray-50 rounded-lg" data-row-id="0">
                            <!-- Inventory Select -->
                            <div class="col-span-12 md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Inventory Item</label>
                                <select name="products[0][product_id]" class="product-select mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select an inventory item</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-price="{{ $product->product->selling_price > 0 ? $product->product->selling_price : $product->purchase_price }}"
                                            data-stock="{{ $product->quantity }}"
                                            data-category="{{ $product->product->category?->name ?: 'Uncategorized' }}"
                                            data-sku="{{ $product->product->sku ?: 'Not assigned' }}"
                                            data-location="{{ $product->product->storage_location ?: 'No location assigned' }}"
                                            {{ (string) request('product') === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->product->name }} - SKU: {{ $product->product->sku ?: 'N/A' }} - {{ $product->product->storage_location ?: 'No location' }} (Stock: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12">
                                <p class="product-meta text-xs text-gray-500">Select an inventory item to view SKU, category, and storage location.</p>
                            </div>

                            <!-- Selling Price -->
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Price (₦)</label>
                                <input type="number" name="products[0][selling_price]" step="0.01" min="0.01" required class="selling-price-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" name="products[0][quantity]"  required class="quantity-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Subtotal -->
                            <div class="col-span-10 md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                <p class="subtotal-text mt-2 font-semibold text-gray-800">₦0.00</p>
                            </div>

                            <!-- Remove Button -->
                            <div class="col-span-2 md:col-span-1 flex items-end">
                                <button type="button" class="remove-product-btn text-red-600 hover:text-red-800" style="display: none;">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Product Button -->
                    <div class="flex justify-start">
                        <button type="button" id="add-product-btn" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">Add Another Item</button>
                    </div>

                    <!-- Grand Total Display -->
                    <div class="mt-6 pt-4 border-t">
                        <div class="flex justify-end items-center">
                            <span class="text-lg font-medium text-gray-700">Grand Total:</span>
                            <span id="grand-total" class="text-2xl font-bold text-gray-900 ml-4">₦0.00</span>
                        </div>
                    </div>

                    <!-- Receiver Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Receiver Name (Optional)</label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="{{ old('customer_name') }}" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700">Receiver Phone (Optional)</label>
                            <input type="tel" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   value="{{ old('customer_phone') }}" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select id="payment_type" name="payment_type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select payment method</option>
                            <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="pos" {{ old('payment_type') == 'pos' ? 'selected' : '' }}>POS</option>
                            <option value="mobile_money" {{ old('payment_type') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('payment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('sales.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200">
                            Record Goods Dispatch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('products-container');
        const grandTotalEl = document.getElementById('grand-total');
        let rowCounter = 1;

        // Function to initialize a new row's event listeners
        function initializeRow(row) {
            const productSelect = row.querySelector('.product-select');
            const priceInput = row.querySelector('.selling-price-input');
            const quantityInput = row.querySelector('.quantity-input');
            const subtotalEl = row.querySelector('.subtotal-text');
            const removeBtn = row.querySelector('.remove-product-btn');
            const metaText = row.querySelector('.product-meta');

            function updateRow() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    quantityInput.setCustomValidity('');
                    subtotalEl.textContent = '₦0.00';
                    if (metaText) {
                        metaText.textContent = 'Select an inventory item to view SKU, category, and storage location.';
                    }
                    updateGrandTotal();
                    return;
                }

                const price = parseFloat(priceInput.value) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                const stock = parseFloat(selectedOption.dataset.stock) || 0;

                // Validate quantity against stock
                if (quantity > stock) {
                    quantityInput.setCustomValidity(`Max quantity is ${stock}`);
                    quantityInput.reportValidity();
                } else {
                    quantityInput.setCustomValidity('');
                }

                const subtotal = price * quantity;
                subtotalEl.textContent = `₦${subtotal.toLocaleString('en-NG', { minimumFractionDigits: 2 })}`;
                updateGrandTotal();
            }

            function onProductSelect() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    updateRow();
                    return;
                }

                priceInput.value = selectedOption.dataset.price || '';
                if (metaText) {
                    metaText.textContent = `Category: ${selectedOption.dataset.category || 'Uncategorized'} • SKU: ${selectedOption.dataset.sku || 'Not assigned'} • Location: ${selectedOption.dataset.location || 'No location assigned'} • Available: ${selectedOption.dataset.stock || 0}`;
                }
                updateRow();
            }

            productSelect.addEventListener('change', onProductSelect);
            priceInput.addEventListener('input', updateRow);
            quantityInput.addEventListener('input', updateRow);

            removeBtn.addEventListener('click', () => {
                row.remove();
                updateGrandTotal();
                toggleRemoveButtons();
            });

            if (productSelect.value) {
                onProductSelect();
            }
        }

        // Function to update the grand total
        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                const subtotalText = row.querySelector('.subtotal-text').textContent;
                total += parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;
            });
            grandTotalEl.textContent = `₦${total.toLocaleString('en-NG', { minimumFractionDigits: 2 })}`;
        }

        // Function to show/hide remove buttons
        function toggleRemoveButtons() {
            const rows = document.querySelectorAll('.product-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-product-btn');
                removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
            });
        }

        // Add new product row
        document.getElementById('add-product-btn').addEventListener('click', () => {
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);
            const newRowId = rowCounter++;
            
            newRow.dataset.rowId = newRowId;
            newRow.querySelectorAll('select, input').forEach(input => {
                input.name = input.name.replace(/products\[\d+\]/, `products[${newRowId}]`);
                input.value = '';
            });
            newRow.querySelector('.subtotal-text').textContent = '₦0.00';
            const metaText = newRow.querySelector('.product-meta');
            if (metaText) {
                metaText.textContent = 'Select an inventory item to view SKU, category, and storage location.';
            }
            
            container.appendChild(newRow);
            initializeRow(newRow);
            toggleRemoveButtons();
        });

        // Initialize the first row
        initializeRow(container.querySelector('.product-row'));
        toggleRemoveButtons();

    });
    </script>
</x-shop-layout>
