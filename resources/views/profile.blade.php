<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Profile Information -->
            <div class="p-6 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 border rounded-2xl bg-white/5 backdrop-blur-sm border-white/10">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
