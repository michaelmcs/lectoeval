<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Editar Perfil</h2>

                    <!-- Formulario de edición de perfil -->
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('POST')

                        <div class="mt-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full border rounded p-2">
                        </div>

                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full border rounded p-2">
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
