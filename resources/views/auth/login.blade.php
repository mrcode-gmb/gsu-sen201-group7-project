<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div>
        <h4 class="text-xl uppercase">Sign in</h4>
        <p>Sign to continue selling product</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
    
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
            <div class="relative mt-1">
            
                <x-text-input id="email"
                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
        </div>
    
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
            <div class="relative mt-1">
                
                <x-text-input id="password"
                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    type="password" name="password" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
        </div>
    
        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center space-x-2">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-500">
                <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
    
            @if (Route::has('password.request'))
                <a class="text-sm text-blue-500 hover:text-blue-700" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    
        <!-- Submit Button -->
        <div>
            <x-primary-button
                class="w-full flex justify-center bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    
    </form>
    
</x-guest-layout>
