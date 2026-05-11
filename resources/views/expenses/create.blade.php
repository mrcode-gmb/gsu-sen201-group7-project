<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Record New Expense
        </h2>
    </x-slot>


    <div class="max-w-3xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                   
                    <!-- Expense Form -->
                    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
                        @csrf
                
                        <!-- Expense Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Expense Title</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount (₦)</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0"
                                value="{{ old('amount') }}" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('amount')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('date')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-between items-center pt-4">
                            <a href="{{ route('expenses.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition">
                                Save Expense
                            </button>
                        </div>
                    </form>
                
            </div>
        </div>
    </div>
</x-shop-layout>
