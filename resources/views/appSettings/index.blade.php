<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gallery Record
            </h2>
            <a href="{{ route("appSetting.create") }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Record New Image
            </a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ openModal: false, deleteUrl: '' }">
        <!-- Expenses Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image Title </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($galleries as $gallery)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $gallery->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $gallery->name }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <img src="{{ $gallery->image_url }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $gallery->name }}">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap space-x-3">
                                {{-- <a href="#" class="text-blue-500 font-bold">Edit</a> --}}
                                <button @click="openModal = true; deleteUrl = '{{ route('appSetting.galleryDelete', $gallery) }}'" 
                                        class="text-red-500 font-bold">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No gallery found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($galleries->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $galleries->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="openModal" 
             class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50"
             x-cloak>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-lg font-semibold text-gray-800">Confirm Deletion</h2>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this gallery record? This action cannot be undone.</p>
                
                <div class="mt-4 flex justify-end space-x-3">
                    <button @click="openModal = false" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('GET')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
