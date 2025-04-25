@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título -->
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Editar Usuario</h1>

    <!-- Formulario -->
    <form action="{{ route('users.update', $user) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <!-- Campo Nombre -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>

        <!-- Campo Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-[var(--table-data-color)]">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>

        <!-- Campo Contraseña -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-[var(--table-data-color)]">Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
        </div>

        <!-- Botones Atrás y Actualizar -->
        <div class="flex justify-end gap-4">
            <!-- Botón Atrás -->
            <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Atrás
            </a>

            <!-- Botón Actualizar -->
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar
            </button>
        </div>
    </form>
</div>
@endsection