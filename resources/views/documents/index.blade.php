<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Document Management
        </h2>
    </x-slot>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Document Management</h2>
        <a href="{{ route('documents.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload Document</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $document->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($document->size / 1024, 2) }} KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ Storage::url($document->path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">View</a>
                                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No documents uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>
</x-shop-layout>
