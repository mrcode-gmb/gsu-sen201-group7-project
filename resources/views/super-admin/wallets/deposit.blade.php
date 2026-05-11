@extends('layouts.super-admin')

@section('header', 'Add Funds to Wallet')

@section('slot')
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Funds to {{ $business->name }}'s Wallet</h3>

        <form action="{{ route('super-admin.wallets.deposit.store', $business) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount ({{ $business->wallet->currency }})</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₦</span>
                        </div>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                    </div>
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <input type="text" id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g. Initial deposit">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('super-admin.businesses.show', $business) }}" class="mr-4 bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Add Funds</button>
            </div>
        </form>
    </div>
@endsection
