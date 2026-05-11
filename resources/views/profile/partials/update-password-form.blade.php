<div class="space-y-6">
    <p class="text-sm text-gray-600 mb-6">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="space-y-6">
            <!-- Current Password -->
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="update_password_current_password" 
                        name="current_password" 
                        type="password" 
                        class="pl-10 w-full" 
                        autocomplete="current-password" 
                        placeholder="Enter your current password"
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
            </div>

            <!-- New Password -->
            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" class="mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="update_password_password" 
                        name="password" 
                        type="password" 
                        class="pl-10 w-full" 
                        autocomplete="new-password" 
                        placeholder="Create a new password"
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                <p class="mt-1 text-xs text-gray-500">Use 8 or more characters with a mix of letters, numbers & symbols</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm New Password')" class="mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="pl-10 w-full" 
                        autocomplete="new-password" 
                        placeholder="Confirm your new password"
                    />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-100 mt-8">
            <x-primary-button class="px-6 py-2.5">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Update Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="ml-4">
                    <div class="flex items-center px-4 py-2 bg-green-50 text-green-700 rounded-md">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('Password updated successfully!') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
