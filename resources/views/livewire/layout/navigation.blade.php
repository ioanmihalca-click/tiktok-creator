<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block w-auto text-purple-500 fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate
                        class="text-gray-300 hover:text-white hover:border-purple-500">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tiktoks.create')" :active="request()->routeIs('tiktoks.create')"
                        class="text-gray-300 hover:text-white hover:border-purple-500">
                        Crează TikTok
                    </x-nav-link>
                    <x-nav-link :href="route('tiktoks.list')" :active="request()->routeIs('tiktoks.list')"
                        class="text-gray-300 hover:text-white hover:border-purple-500">
                        Listă TikTok-uri
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-300 transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:text-white hover:bg-gray-700 focus:outline-none">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-gray-800 border border-gray-700">
                            <x-dropdown-link :href="route('profile')" wire:navigate
                                class="text-gray-300 hover:text-white hover:bg-gray-700">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="text-gray-300 hover:text-white hover:bg-gray-700">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-white hover:bg-gray-700 focus:outline-none">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 border-t bg-gray-900/95 backdrop-blur-sm border-white/10">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate
                class="text-gray-300 hover:text-white hover:bg-white/5">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tiktoks.create')" :active="request()->routeIs('tiktoks.create')" wire:navigate
                class="text-gray-300 hover:text-white hover:bg-white/5">
                Crează TikTok
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tiktoks.list')" :active="request()->routeIs('tiktoks.list')" wire:navigate
                class="text-gray-300 hover:text-white hover:bg-white/5">
                Listă TikTok-uri
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t bg-gray-900/95 backdrop-blur-sm border-white/10">
            <div class="px-4">
                <div class="text-base font-medium text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600"
                    x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name">
                </div>
                <div class="text-sm font-medium text-gray-400">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate
                    class="text-gray-300 hover:text-white hover:bg-white/5">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="text-gray-300 hover:text-white hover:bg-white/5">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
