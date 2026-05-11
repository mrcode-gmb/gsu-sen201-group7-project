@extends('layouts.super-admin')

@section('header', 'System Settings')

@section('slot')
    <div class="space-y-6">
        <!-- System Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Total Businesses</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Business::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', '!=', 'super_admin')->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <p class="text-sm text-gray-600">Total Products</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Product::count() }}</p>
            </div>
        </div>

        <!-- Application Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                    <input type="text" value="{{ config('app.name') }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                    <input type="text" value="{{ config('app.env') }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PHP Version</label>
                    <input type="text" value="{{ PHP_VERSION }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Laravel Version</label>
                    <input type="text" value="{{ app()->version() }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50" />
                </div>
            </div>
        </div>

        <!-- Security Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md">
                    <div>
                        <p class="font-medium text-gray-900">Debug Mode</p>
                        <p class="text-sm text-gray-500">Application debug status</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md">
                    <div>
                        <p class="font-medium text-gray-900">HTTPS</p>
                        <p class="text-sm text-gray-500">Secure connection status</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ request()->secure() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ request()->secure() ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('super-admin.businesses.index') }}" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Manage Businesses
                </a>
                <a href="{{ route('super-admin.users.index') }}" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manage Users
                </a>
                <a href="{{ route('super-admin.reports') }}" class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    View Reports
                </a>
            </div>
        </div>
    </div>
@endsection
