<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="p-8 border rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/30 backdrop-blur-sm border-white/10">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2
        class="mb-6 text-2xl font-bold text-center text-transparent bg-gradient-to-r from-purple-400 to-pink-600 bg-clip-text">
        {{ __('Autentificare') }}
    </h2>

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input wire:model="form.email" id="email"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Parola')" class="text-gray-300" />
            <x-text-input wire:model="form.password" id="password"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="text-purple-500 rounded border-white/10 bg-white/5 focus:ring-purple-500" name="remember">
                <span class="text-sm text-gray-400 ms-2">{{ __('Ține-mă minte') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-purple-400 transition-colors duration-200 hover:text-purple-300"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Ai uitat parola?') }}
                </a>
            @endif
        </div>

        <button type="submit"
            class="w-full px-6 py-3 font-medium text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 transform hover:scale-[1.02] focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-[#0A0A0F]">
            {{ __('Autentificare') }}
        </button>
    </form>
</div>
