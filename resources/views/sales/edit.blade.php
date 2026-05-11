<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Goods Dispatch #{{ $sale->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('sales.show', $sale) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    View Details
                </a>
                <a href="{{ auth()->user()->isAdmin() ? route('sales.index') : route('sales.my-sales') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Goods Dispatch
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6" id="saleForm">
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
                                        data-price="{{ $product->purchase_price }}" 
                                        data-stock="{{ $product->quantity }}"
                                        data-unit="{{ $product->product->unit_type }}"
                                        {{ old('product_id', $sale->purchase_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->product->name }} ({{ ucfirst($product->product->unit_type) }}) - {{ number_format($product->quantity, 1) }} available
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
                                <span class="text-gray-600">Purchase Price:</span>
                                <span id="sellingPrice" class="font-medium"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <x-input-label for="quantity" :value="__('Quantity')" />
                        <x-text-input id="quantity" 
                                      name="quantity" 
                                      type="number" 
                                      step="0.1" 
                                      min="0.1" 
                                      class="mt-1 block w-full" 
                                      :value="old('quantity', $sale->quantity)" 
                                      required 
                                      oninput="calculateTotal()" />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        <p class="text-xs text-gray-500 mt-1">Maximum available: <span id="maxQuantity">{{ $sale->purchase->quantity + $sale->quantity }}</span></p>
                    </div>

                    <!-- Selling Price per Unit -->
                    <div>
                        <x-input-label for="selling_price_per_unit" :value="__('Selling Price per Unit (₦)')" />
                        <x-text-input id="selling_price_per_unit" 
                                      name="selling_price_per_unit" 
                                      type="number" 
                                      step="0.01" 
                                      min="0" 
                                      class="mt-1 block w-full" 
                                      :value="old('selling_price_per_unit', $sale->selling_price_per_unit)" 
                                      required 
                                      oninput="calculateTotal()" />
                        <x-input-error class="mt-2" :messages="$errors->get('selling_price_per_unit')" />
                    </div>

                    <!-- Total Amount (Auto-calculated) -->
                    <div>
                        <x-input-label for="total_amount" :value="__('Total Amount (₦)')" />
                        <x-text-input id="total_amount" 
                                      name="total_amount" 
                                      type="number" 
                                      step="0.01" 
                                      class="mt-1 block w-full bg-gray-100" 
                                      :value="old('total_amount', $sale->total_amount)" 
                                      readonly />
                        <p class="text-xs text-gray-500 mt-1">This field is automatically calculated</p>
                    </div>

                    <!-- Receiver Name -->
                    <div>
                        <x-input-label for="customer_name" :value="__('Receiver Name (Optional)')" />
                        <x-text-input id="customer_name" 
                                      name="customer_name" 
                                      type="text" 
                                      class="mt-1 block w-full" 
                                      :value="old('customer_name', $sale->customer_name)" 
                                      placeholder="Enter receiver name (optional)" />
                        <x-input-error class="mt-2" :messages="$errors->get('customer_name')" />
                    </div>

                    <!-- Sale Date -->
                    <div>
                        <x-input-label for="sale_date" :value="__('Sale Date')" />
                        <x-text-input id="sale_date" 
                                      name="sale_date" 
                                      type="date" 
                                      class="mt-1 block w-full" 
                                      :value="old('sale_date', $sale->sale_date->format('Y-m-d'))" 
                                      required />
                        <x-input-error class="mt-2" :messages="$errors->get('sale_date')" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Any additional notes about this sale">{{ old('notes', $sale->notes) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <!-- Original Sale Info -->
                    <div class="bg-blue-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-blue-700 mb-2">Original Sale Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Original Product:</span>
                                <span class="font-medium">{{ $sale->purchase->product->name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Quantity:</span>
                                <span class="font-medium">{{ number_format($sale->quantity, 1) }} units</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Price:</span>
                                <span class="font-medium">₦{{ number_format($sale->selling_price_per_unit, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-blue-600">Original Total:</span>
                                <span class="font-medium">₦{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <x-primary-button>
                            {{ __('Update Sale') }}
                        </x-primary-button>
                    </div>
                </form>
                
                @if(auth()->user()->isAdmin())
                    <div class="mt-4">
                        <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this sale? This will restore the stock and cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">
                                {{ __('Delete Sale') }}
                            </x-danger-button>
                        </form>
                    </div>
                @endif
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
            
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const stock = selectedOption.getAttribute('data-stock');
                const unit = selectedOption.getAttribute('data-unit');
                
                document.getElementById('unitType').textContent = unit.charAt(0).toUpperCase() + unit.slice(1);
                document.getElementById('currentStock').textContent = parseFloat(stock).toFixed(1) + ' units';
                document.getElementById('sellingPrice').textContent = '₦' + parseFloat(price).toFixed(2);
                
                // Update selling price field
                document.getElementById('selling_price_per_unit').value = price;
                
                // Update max quantity (current stock + original quantity sold)
                const originalQuantity = {{ $sale->quantity }};
                const maxQuantity = parseFloat(stock) + originalQuantity;
                document.getElementById('maxQuantity').textContent = maxQuantity.toFixed(1);
                document.getElementById('quantity').setAttribute('max', maxQuantity);
                
                productInfo.style.display = 'block';
                calculateTotal();
            } else {
                productInfo.style.display = 'none';
            }
        }

        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const pricePerUnit = parseFloat(document.getElementById('selling_price_per_unit').value) || 0;
            const total = quantity * pricePerUnit;
            
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        // Validate quantity before form submission
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const maxQuantity = parseFloat(document.getElementById('maxQuantity').textContent) || 0;
            
            if (quantity > maxQuantity) {
                e.preventDefault();
                alert('Quantity cannot exceed available stock (' + maxQuantity.toFixed(1) + ' units)');
                return false;
            }
        });
    </script>
</x-shop-layout>
