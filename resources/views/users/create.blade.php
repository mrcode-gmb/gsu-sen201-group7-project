<x-shop-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Staff
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
                <form method="POST" action="{{ route('admin.storeUser') }}" class="space-y-6">
                    @csrf

                    <!-- Staff Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Staff Name</label>
                        <input type="text" name="name" id="name" required 
                               value="{{ old('name') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Staff Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Staff Email</label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email') }}"
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
                            <option value="salesperson" {{ old('role') == 'salesperson' ? 'selected' : '' }}>Salesperson</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Form Actions -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.myStaff') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-6 rounded-md">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
