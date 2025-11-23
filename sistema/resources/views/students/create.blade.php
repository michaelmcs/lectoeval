<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Nuevo Estudiante</h1>
                <p class="text-gray-600 mt-2">Agregar un nuevo estudiante al sistema</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- DNI -->
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI *</label>
                            <input type="text" name="dni" id="dni" required 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('dni') }}">
                            @error('dni')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombres -->
                        <div>
                            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres *</label>
                            <input type="text" name="nombres" id="nombres" required 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('nombres') }}">
                            @error('nombres')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos *</label>
                            <input type="text" name="apellidos" id="apellidos" required 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('apellidos') }}">
                            @error('apellidos')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Edad -->
                        <div>
                            <label for="edad" class="block text-sm font-medium text-gray-700">Edad</label>
                            <input type="number" name="edad" id="edad" min="3" max="20"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('edad') }}">
                            @error('edad')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grado -->
                        <div>
                            <label for="grado" class="block text-sm font-medium text-gray-700">Grado *</label>
                            <select name="grado" id="grado" required 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar grado</option>
                                @foreach(['1', '2', '3', '4', '5', '6'] as $grado)
                                    <option value="{{ $grado }}" {{ old('grado') == $grado ? 'selected' : '' }}>
                                        {{ $grado }}° Grado
                                    </option>
                                @endforeach
                            </select>
                            @error('grado')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sección -->
                        <div>
                            <label for="seccion" class="block text-sm font-medium text-gray-700">Sección</label>
                            <input type="text" name="seccion" id="seccion" maxlength="5"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('seccion') }}">
                            @error('seccion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Colegio -->
                        <div class="md:col-span-2">
                            <label for="colegio" class="block text-sm font-medium text-gray-700">Colegio</label>
                            <input type="text" name="colegio" id="colegio"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('colegio') }}">
                            @error('colegio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apoderado Nombre -->
                        <div class="md:col-span-2">
                            <label for="apoderado_nombre" class="block text-sm font-medium text-gray-700">Nombre del Apoderado</label>
                            <input type="text" name="apoderado_nombre" id="apoderado_nombre"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('apoderado_nombre') }}">
                            @error('apoderado_nombre')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apoderado Teléfono -->
                        <div class="md:col-span-2">
                            <label for="apoderado_telefono" class="block text-sm font-medium text-gray-700">Teléfono del Apoderado</label>
                            <input type="text" name="apoderado_telefono" id="apoderado_telefono"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('apoderado_telefono') }}">
                            @error('apoderado_telefono')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('students.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Guardar Estudiante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>