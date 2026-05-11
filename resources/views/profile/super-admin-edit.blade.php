@extends('layouts.super-admin')

@section('header', 'Profile Settings')

@section('slot')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Profile Information Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Profile Information') }}
                    </h3>
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Update Password') }}
                    </h3>
                </div>
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
