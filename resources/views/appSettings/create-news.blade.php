<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Software News
        </h2>
    </x-slot>


    <div class="max-w-3xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">

                <!-- Expense Form -->
                <form method="POST" action="{{ route('appSetting.storeNews') }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf


                    <!-- Expense Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">News Title</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">News Content</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full px-3 py-2 border resize-none border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                            Save News
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
