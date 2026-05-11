<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Creditors
        </h2>
    </x-slot>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.creditors.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="phone" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.creditors.index') }}" class="mr-4 bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save Creditor</button>
            </div>
        </form>
    </div>
</x-shop-layout>
