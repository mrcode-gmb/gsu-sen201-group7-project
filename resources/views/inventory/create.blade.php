<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Inventory Item
            </h2>
            <a href="{{ route('inventory.categories.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Manage Categories
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('inventory.store') }}" class="space-y-6">
                    @csrf

                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category
                                (Optional)</label>
                            <a href="{{ route('inventory.categories.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-500">Create category</a>
                        </div>
                        <select name="category_id" id="category_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">No category assigned</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU Code</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                            placeholder="e.g. WHS-PKO-001">
                        <p class="mt-1 text-xs text-gray-500">Use a unique stock-keeping code for this item.</p>
                        @error('sku')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Storage Location -->
                    <div>
                        <label for="storage_location" class="block text-sm font-medium text-gray-700">Storage
                            Location (Optional)</label>
                        <input type="text" name="storage_location" id="storage_location"
                            value="{{ old('storage_location') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                            placeholder="e.g. Rack A3 / Main Warehouse">
                        @error('storage_location')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Type -->
                    <div>
                        <label for="unit_type" class="block text-sm font-medium text-gray-700">Unit Type</label>
                        <select name="unit_type" id="unit_type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="" hidden></option>
                            <option value="Customize" {{ old('unit_type') === 'Customize' ? 'selected' : '' }}>Customize</option>
                            <option value="Uncustomize" {{ old('unit_type') === 'Uncustomize' ? 'selected' : '' }}>Uncustomize</option>
                        </select>
                        @error('unit_type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Low Stock Threshold -->
                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock
                            Threshold</label>
                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0"
                            max="100000" value="{{ old('low_stock_threshold', 10) }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        <p class="mt-1 text-xs text-gray-500">The system will flag this item once warehouse stock
                            falls to or below this level.</p>
                        @error('low_stock_threshold')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('inventory.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-6 rounded-md">Save Inventory Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
