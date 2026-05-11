<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Expense #{{ $expense->id }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Back to Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Expense Name -->
                            <div>
                                <x-input-label for="name" :value="__('Expense Title')" />
                                <x-text-input id="name" 
                                            name="name" 
                                            type="text" 
                                            class="mt-1 block w-full" 
                                            :value="old('name', $expense->name)" 
                                            required 
                                            autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Amount')" />
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">N</span>
                                    </div>
                                    <x-text-input id="amount" 
                                                name="amount" 
                                                type="number" 
                                                step="0.01" 
                                                min="0" 
                                                class="pl-7 block w-full" 
                                                :value="old('amount', $expense->amount)" 
                                                required />
                                </div>
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <!-- Date -->
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" 
                                            name="date" 
                                            type="date" 
                                            class="mt-1 block w-full" 
                                            :value="old('date', $expense->date->format('Y-m-d'))" 
                                            required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" 
                                    name="notes" 
                                    rows="3" 
                                    class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes', $expense->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Update Expense') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            {{ __('Once the expense is deleted, all of its resources and data will be permanently deleted.') }}
                        </div>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Are you sure you want to delete this expense? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">
                                    {{ __('Delete Expense') }}
                                </x-danger-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
