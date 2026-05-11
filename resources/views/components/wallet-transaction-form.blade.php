<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4 py-6 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        </div>
        
        <!-- Form -->
        <form wire:submit.prevent="{{ $action }}" class="p-6">
            @csrf
            
            <!-- Amount -->
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Amount ({{ $wallet->currency }})
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₦</span>
                    </div>
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           name="amount" 
                           id="amount" 
                           wire:model.defer="amount"
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" 
                           placeholder="0.00">
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description (Optional)
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          wire:model.defer="description"
                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        wire:click="$emit('closeModal')" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ $buttonText }}
                </button>
            </div>
        </form>
    </div>
</div>
