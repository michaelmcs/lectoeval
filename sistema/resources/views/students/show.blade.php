<x-app-layout>
  @include('layouts.nav')
  <div class="max-w-3xl mx-auto p-4 bg-white border rounded">
    <h1 class="text-xl font-bold mb-4">Detalle del estudiante</h1>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div><dt class="font-semibold">DNI</dt><dd>{{ $student->dni }}</dd></div>
      <div><dt class="font-semibold">Edad</dt><dd>{{ $student->edad }}</dd></div>
      <div><dt class="font-semibold">Nombres</dt><dd>{{ $student->nombres }}</dd></div>
      <div><dt class="font-semibold">Apellidos</dt><dd>{{ $student->apellidos }}</dd></div>
      <div><dt class="font-semibold">Grado</dt><dd>{{ $student->grado }}°</dd></div>
      <div><dt class="font-semibold">Sección</dt><dd>{{ $student->seccion }}</dd></div>
      <div class="md:col-span-2"><dt class="font-semibold">Colegio</dt><dd>{{ $student->colegio }}</dd></div>
      <div><dt class="font-semibold">Apoderado</dt><dd>{{ $student->apoderado_nombre }}</dd></div>
      <div><dt class="font-semibold">Tel. apoderado</dt><dd>{{ $student->apoderado_telefono }}</dd></div>
      <div class="md:col-span-2"><dt class="font-semibold">Observaciones</dt><dd class="whitespace-pre-wrap">{{ $student->observaciones }}</dd></div>
    </dl>
    <div class="mt-4">
      <a href="{{ route('students.edit', $student) }}" class="text-indigo-600">Editar</a> ·
      <a href="{{ route('students.index') }}" class="text-gray-700">Volver</a>
    </div>
  </div>
</x-app-layout>
