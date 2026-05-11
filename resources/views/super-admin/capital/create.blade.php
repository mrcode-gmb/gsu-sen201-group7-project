@extends('layouts.super-admin')

@section('header', ucfirst($type) . ' Capital')

@section('slot')
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">{{ ucfirst($type) }} Capital for {{ $business->name }}</h2>

        <form action="{{ route('super-admin.capital.store', $business) }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" name="amount" id="amount" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" step="0.01" min="0.01" required>
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <a href="{{ route('super-admin.businesses.show', $business) }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 mr-4">Cancel</a>
                <button type="submit" class="px-4 py-2 {{ $type === 'deposit' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg">{{ ucfirst($type) }}</button>
            </div>
        </form>
    </div>
@endsection
