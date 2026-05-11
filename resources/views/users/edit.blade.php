<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Staff - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('admin.updateUser', $user) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Staff Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Staff Name</label>
                        <input type="text" name="name" id="name" required 
                               value="{{ old('name', $user->name) }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Staff Email</label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Staff Role</label>
                        <select name="role" id="role" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Role</option>
                            <option value="salesperson" {{ old('role', $user->role) == 'salesperson' ? 'selected' : '' }}>Salesperson</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Staff Status</label>
                        <select name="status" id="status" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Additional Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Created:</span> {{ $user->created_at->format('M d, Y \a\t g:i A') }}
                            </div>
                            <div>
                                <span class="font-medium">Last Updated:</span> {{ $user->updated_at->format('M d, Y \a\t g:i A') }}
                            </div>
                            @if($user->isSalesperson())
                            <div>
                                <span class="font-medium">Total Sales:</span> {{ $user->sales()->count() }}
                            </div>
                            <div>
                                <span class="font-medium">Total Revenue:</span> ₦{{ number_format($user->sales()->sum('total_amount'), 2) }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.showUser', $user) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-6 rounded-md">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
