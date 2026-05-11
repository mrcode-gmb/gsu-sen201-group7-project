<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Purchase #{{ $purchase->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('purchases.show', $purchase) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    View Details
                </a>
                <a href="{{ route('purchases.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Purchases
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('purchases.update', $purchase) }}" class="space-y-6" id="purchaseForm">
                    @csrf
                    @method('PUT')

                    <!-- Product Selection -->
                    <div>
                        <x-input-label for="product_id" :value="__('Product')" />
                        <select id="product_id" 
                                name="product_id" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                required 
                                onchange="updateProductInfo()">
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-price="{{ $product->selling_price }}" 
                                        data-stock="{{ $product->current_stock }}"
                                        data-unit="{{ $product->unit_type }}"
                                        {{ old('product_id', $purchase->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ ucfirst($product->unit_type) }}) - Current stock: {{ number_format($product->current_stock, 1) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                    </div>

                    <!-- Product Info Display -->
                    <div id="productInfo" class="bg-gray-50 p-4 rounded-md" style="display: none;">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Product Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Unit Type:</span>
                                <span id="unitType" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Current Stock:</span>
                                <span id="currentStock" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Selling Price:</span>
                                <span id="sellingPrice" class="font-medium"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="supplier_name" :value="__('Supplier Name')" />
                            <x-text-input id="supplier_name" 
                                          name="supplier_name" 
                                          type="text" 
                                          class="mt-1 block w-full" 
                                          :value="old('supplier_name', $purchase->supplier_name)" 
                                          required />
                            <x-input-error class="mt-2" :messages="$errors->get('supplier_name')" />
                        </div>

                        <div>
                            <x-input-label for="supplier_phone" :value="__('Supplier Phone (Optional)')" />
                            <x-text-input id="supplier_phone" 
                                          name="supplier_phone" 
                                          type="text" 
                                          class="mt-1 block w-full" 
                                          :value="old('supplier_phone', $purchase->supplier_phone)" 
                                          placeholder="e.g., +234 123 456 7890" />
                            <x-input-error class="mt-2" :messages="$errors->get('supplier_phone')" />
                        </div>
                    </div>

                    <!-- Purchase Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity Purchased')" />
                            <x-text-input id="quantity" 
                                          name="quantity" 
                                          type="number" 
                                          step="0.1" 
                                          min="0.1" 
                                          class="mt-1 block w-full" 
                                          :value="old('quantity', $purchase->quantity)" 
                                          required 
                                          oninput="calculateTotal()" />
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>

                        <div>
                            <x-input-label for="cost_price_per_unit" :value="__('Cost Price per Unit (₦)')" />
                            <x-text-input id="cost_price_per_unit" 
                                          name="cost_price_per_unit" 
                                          type="number" 
                                          step="0.01" 
                                          min="0" 
                                          class="mt-1 block w-full" 
                                          :value="old('cost_price_per_unit', $purchase->cost_price_per_unit)" 
                                          required 
                                          oninput="calculateTotal()" />
                            <x-input-error class="mt-2" :messages="$errors->get('cost_price_per_unit')" />
                        </div>
                    </div>

                    <!-- Total Cost (Auto-calculated) -->
                    <div>
                        <x-input-label for="total_cost" :value="__('Total Cost (₦)')" />
                        <x-text-input id="total_cost" 
                                      name="total_cost" 
                                      type="number" 
                                      step="0.01" 
                                      class="mt-1 block w-full bg-gray-100" 
                                      :value="old('total_cost', $purchase->total_cost)" 
                                      readonly />
                        <p class="text-xs text-gray-500 mt-1">This field is automatically calculated</p>
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <x-input-label for="purchase_date" :value="__('Purchase Date')" />
                        <x-text-input id="purchase_date" 
                                      name="purchase_date" 
                                      type="date" 
                                      class="mt-1 block w-full" 
                                      :value="old('purchase_date', $purchase->purchase_date->format('Y-m-d'))" 
                                      required />
                        <x-input-error class="mt-2" :messages="$errors->get('purchase_date')" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Any additional notes about this purchase">{{ old('notes', $purchase->notes) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <!-- Profit Calculation Display -->
                    <div id="profitCalculation" class="bg-green-50 p-4 rounded-md" style="display: none;">
                        <h3 class="text-sm font-medium text-green-700 mb-2">Profit Analysis</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-green-600">Cost per unit:</span>
                                <span id="displayCostPerUnit" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-green-600">Selling price:</span>
                                <span id="displaySellingPrice" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-green-600">Profit per unit:</span>
                                <span id="profitPerUnit" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-green-600">Total potential profit:</span>
                                <span id="totalPotentialProfit" class="font-medium"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Original Purchase Info -->
                    <div class="bg-blue-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-blue-700 mb-2">Original Purchase Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Original Product:</span>
                                <span class="font-medium">{{ $purchase->product->name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Supplier:</span>
                                <span class="font-medium">{{ $purchase->supplier_name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Quantity:</span>
                                <span class="font-medium">{{ number_format($purchase->quantity, 1) }} units</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Cost:</span>
                                <span class="font-medium">₦{{ number_format($purchase->cost_price_per_unit, 2) }} per unit</span>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 mt-2">
                            <strong>Note:</strong> Editing this purchase will adjust the product's stock level accordingly.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <x-primary-button>
                            {{ __('Update Purchase') }}
                        </x-primary-button>
                        
                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase? This will reduce the stock and cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">
                                {{ __('Delete Purchase') }}
                            </x-danger-button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateProductInfo();
            calculateTotal();
        });

        function updateProductInfo() {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productInfo = document.getElementById('productInfo');
            const profitCalculation = document.getElementById('profitCalculation');
            
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const stock = selectedOption.getAttribute('data-stock');
                const unit = selectedOption.getAttribute('data-unit');
                
                document.getElementById('unitType').textContent = unit.charAt(0).toUpperCase() + unit.slice(1);
                document.getElementById('currentStock').textContent = parseFloat(stock).toFixed(1) + ' units';
                document.getElementById('sellingPrice').textContent = '₦' + parseFloat(price).toFixed(2);
                
                productInfo.style.display = 'block';
                profitCalculation.style.display = 'block';
                calculateTotal();
            } else {
                productInfo.style.display = 'none';
                profitCalculation.style.display = 'none';
            }
        }

        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const costPerUnit = parseFloat(document.getElementById('cost_price_per_unit').value) || 0;
            const total = quantity * costPerUnit;
            
            document.getElementById('total_cost').value = total.toFixed(2);

            // Update profit calculation
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            
            if (selectedOption.value) {
                const sellingPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const profitPerUnit = sellingPrice - costPerUnit;
                const totalPotentialProfit = profitPerUnit * quantity;
                
                document.getElementById('displayCostPerUnit').textContent = '₦' + costPerUnit.toFixed(2);
                document.getElementById('displaySellingPrice').textContent = '₦' + sellingPrice.toFixed(2);
                document.getElementById('profitPerUnit').textContent = '₦' + profitPerUnit.toFixed(2);
                document.getElementById('totalPotentialProfit').textContent = '₦' + totalPotentialProfit.toFixed(2);
            }
        }
    </script>
</x-shop-layout>

