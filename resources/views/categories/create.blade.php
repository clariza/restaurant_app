@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6">Crear Categoría</h1>
    <form action="{{ route('categorys.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="icon" class="block text-sm font-medium text-gray-700">Icono</label>
            <input type="text" name="icon" id="icon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Guardar</button>
    </form>
</div>
@endsection