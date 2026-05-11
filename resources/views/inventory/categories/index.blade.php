<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory Categories
            </h2>
            <a href="{{ route('inventory.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Back to Inventory
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <p class="font-medium">Please correct the highlighted issue before continuing.</p>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create Category</h3>
                    <form method="POST" action="{{ route('inventory.categories.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description
                                (Optional)</label>
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition-colors duration-200">
                            Save Category
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Existing Categories</h3>
                        <span class="text-sm text-gray-500">{{ $categories->count() }} total</span>
                    </div>

                    @if ($categories->isEmpty())
                        <div class="text-center py-10">
                            <h4 class="text-sm font-medium text-gray-900">No categories created yet</h4>
                            <p class="mt-1 text-sm text-gray-500">Create your first category to organize inventory by
                                item type or warehouse group.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Items</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $category->description ?: 'No description provided' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($category->products_count) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('inventory.categories.edit', $category) }}"
                                                        class="text-blue-600 hover:text-blue-500 font-medium">Edit</a>
                                                    <form method="POST"
                                                        action="{{ route('inventory.categories.destroy', $category) }}"
                                                        onsubmit="return confirm('Delete this category?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-500 font-medium">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
