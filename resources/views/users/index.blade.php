@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título y botón "Crear Usuario" -->
    <div class="mb-6">
        <!-- Título -->
        <div class="flex items-center mb-4">
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Usuarios</h1>
        </div>

        <!-- Botón "Crear Usuario" -->
        <div>
            <a href="{{ route('users.create') }}" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-plus mr-2"></i>Crear Usuario
            </a>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 text-[var(--table-data-color)]">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-[var(--table-data-color)]">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <!-- Icono de Editar -->
                            <a href="{{ route('users.edit', $user) }}" class="text-[var(--primary-color)] hover:text-[var(--secondary-color)] mr-2">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </a>
                            <!-- Icono de Eliminar -->
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection