<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Category: {{ $category->name }}
            </h2>
            <a href="{{ route('inventory.categories.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('inventory.categories.update', $category) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description
                            (Optional)</label>
                        <textarea id="description" name="description" rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Linked inventory items</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($category->products_count) }}</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md font-medium transition-colors duration-200">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
