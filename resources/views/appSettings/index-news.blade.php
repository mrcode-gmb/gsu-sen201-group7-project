<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                News Record
            </h2>
            <a href="{{ route("appSetting.createNew") }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                Record News
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        
        <!-- News Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">News Title </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">News Content</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($news as $new)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $new->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $new->name_title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ $new->new_content }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{-- <a href="" class="text-blue-500 font-bold">Edit</a> --}}
                                <button onclick="confirmDelete('{{ route('appSetting.newDelete', $new) }}')" 
                                    class="text-red-500 font-bold ml-3">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No news found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($news->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $news->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-lg font-bold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-gray-600">Are you sure you want to delete this news? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end space-x-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('GET')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(action) {
            document.getElementById('deleteForm').action = action;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-shop-layout>
