<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Software Gallery
        </h2>
    </x-slot>


    <div class="max-w-3xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
              
                <!-- Expense Form -->
                <form method="POST" action="{{ route('appSetting.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                   
                    <!-- Expense Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Image Title</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="image_file" class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <input type="file" id="image_file" name="image_file" step="0.01" min="0"
                            value="{{ old('image_file') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('image_file')
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
                            Save Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
