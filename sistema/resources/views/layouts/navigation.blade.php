<!-- resources/views/layouts/navigation.blade.php -->
<nav class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
Bienvenido
            </div>
            <div class="flex items-center justify-between sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0">
                    <x-application-logo class="block h-9 w-auto fill-current text-white"/>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <!-- Menú de usuario (Perfil, Logout, etc.) -->
                        <x-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">
                            {{ __('Perfil') }}
                        </x-nav-link>
                        <x-nav-link :href="route('logout')" :active="request()->routeIs('logout')">
                            {{ __('Cerrar sesión') }}
                        </x-nav-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
