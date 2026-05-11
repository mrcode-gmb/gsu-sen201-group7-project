@extends('layouts.super-admin')

@section('header', $business->name . ' - Analytics')

@section('slot')
    <div class="mb-6">
        <a href="{{ route('super-admin.businesses.show', $business) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Business Details
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Business Analytics</h3>
        <p class="text-gray-600 mb-6">Coming soon in Module 2</p>
        <p class="text-sm text-gray-500">This section will include detailed analytics for {{ $business->name }} including:</p>
        <ul class="text-sm text-gray-500 mt-4 space-y-2">
            <li>• Sales trends and forecasting</li>
            <li>• Product performance analysis</li>
            <li>• Staff performance metrics</li>
            <li>• Profit margin analysis</li>
            <li>• Customer insights</li>
        </ul>
    </div>
@endsection
