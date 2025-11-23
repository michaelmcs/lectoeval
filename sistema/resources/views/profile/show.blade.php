<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Perfil del usuario</h2>
                    <p>Nombre: {{ auth()->user()->name }}</p>
                    <p>Email: {{ auth()->user()->email }}</p>
                    <p>Fecha de creación: {{ auth()->user()->created_at }}</p>

                    <div class="mt-6">
                        <a href="{{ route('profile.edit') }}" class="text-blue-600">Editar perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
