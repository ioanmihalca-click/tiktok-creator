<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-gray-300" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password"
                type="password"
                class="block w-full mt-1 text-white placeholder-gray-400 bg-white/5 border-white/10 focus:border-purple-500 focus:ring-purple-500"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-gray-300" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password"
                class="block w-full mt-1 text-white placeholder-gray-400 bg-white/5 border-white/10 focus:border-purple-500 focus:ring-purple-500"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-gray-300" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation"
                name="password_confirmation" type="password"
                class="block w-full mt-1 text-white placeholder-gray-400 bg-white/5 border-white/10 focus:border-purple-500 focus:ring-purple-500"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-purple-500 hover:bg-purple-600 focus:bg-purple-600 active:bg-purple-700">
                {{ __('Save') }}
            </x-primary-button>

            <x-action-message class="text-green-400 me-3" on="password-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
