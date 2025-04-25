@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Crear Proveedor</h1>

    <!-- Formulario para crear un proveedor -->
    <form action="{{ route('suppliers.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="contact" class="block text-sm font-medium text-[var(--table-data-color)]">Contacto</label>
            <input type="text" name="contact" id="contact" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-[var(--table-data-color)]">Telefono</label>
            <input type="text" name="phone" id="phone" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-[var(--table-data-color)]">Direccion</label>
            <input type="text" name="address" id="address" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Guardar
            </button>
        </div>
    </form>
</div>
@endsection