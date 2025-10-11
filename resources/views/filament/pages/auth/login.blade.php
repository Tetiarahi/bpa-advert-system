<x-filament-panels::layout.base :livewire="$this">
    <div>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Login Form -->
        <div class="w-full max-w-2xl lg:max-w-4xl xl:max-w-5xl px-16 lg:px-24 xl:px-32 py-12">
            <div class="w-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-12 lg:p-16 xl:p-20">
                <!-- Logo and Company Name -->
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-4">
                        <img class="h-24 w-24 bpa-logo" src="{{ asset('images/bpa-logo.png') }}" alt="BPA Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="h-24 w-24 bg-gradient-to-br from-blue-600 to-red-600 rounded-full items-center justify-center text-white font-bold text-2xl shadow-lg" style="display: none;">
                            <div class="text-center">
                                <div class="text-lg font-bold">BPA</div>
                                <div class="text-xs opacity-80">LOGO</div>
                            </div>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        BPA
                    </h1>
                    {{-- <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">
                        Broadcasting and Publications Authority
                    </p> --}}
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                        Advert System Management
                    </p>
                </div>

                <!-- Login Form -->
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-12 text-center">
                        Sign in to your account
                    </h2>

                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <div class="mt-12">
                            <x-filament::button
                                type="submit"
                                size="lg"
                                class="w-full justify-center"
                            >
                                <x-filament::loading-indicator
                                    class="h-5 w-5"
                                    wire:loading
                                    wire:target="authenticate"
                                />

                                <span wire:loading.remove wire:target="authenticate">
                                    Sign in
                                </span>

                                <span wire:loading wire:target="authenticate">
                                    Signing in...
                                </span>
                            </x-filament::button>
                        </div>
                    </x-filament-panels::form>
                </div>

                <!-- Footer -->
                <div class="mt-16 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} Broadcasting and Publications Authority. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <script>
        // Debug logo loading
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.bpa-logo');
            if (logo) {
                logo.addEventListener('load', function() {
                    console.log('BPA Logo loaded successfully');
                });
                logo.addEventListener('error', function() {
                    console.log('BPA Logo failed to load, showing fallback');
                });
            }
        });
        </script>
    </div>
</x-filament-panels::layout.base>
