<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="p-8 border rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/30 backdrop-blur-sm border-white/10">
    <h2
        class="mb-6 text-2xl font-bold text-center text-transparent bg-gradient-to-r from-purple-400 to-pink-600 bg-clip-text">
        {{ __('Înregistrare') }}
    </h2>

    <form wire:submit="register" class="space-y-6">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nume')" class="text-gray-300" />
            <x-text-input wire:model="name" id="name"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input wire:model="email" id="email"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Parola')" class="text-gray-300" />
            <x-text-input wire:model="password" id="password"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmă Parola')" class="text-gray-300" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation"
                class="w-full px-4 py-3 mt-1 text-white transition-all duration-200 border bg-white/5 border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <a class="text-sm text-purple-400 transition-colors duration-200 hover:text-purple-300"
                href="{{ route('login') }}" wire:navigate>
                {{ __('Ai deja cont?') }}
            </a>

            <button type="submit"
                class="ms-4 px-6 py-3 font-medium text-white transition-all duration-300 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-pink-700 transform hover:scale-[1.02] focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-[#0A0A0F]">
                {{ __('Înregistrare') }}
            </button>
        </div>
    </form>
</div>
