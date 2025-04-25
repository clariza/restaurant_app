@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Editar Producto</h1>
    <form action="{{ route('items.update', $item->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
            <input type="text" name="name" id="name" value="{{ $item->name }}" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-[var(--table-data-color)]">Descripción</label>
            <textarea name="description" id="description" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">{{ $item->description }}</textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-[var(--table-data-color)]">Precio</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ $item->price }}" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-[var(--table-data-color)]">Categoría</label>
            <select name="category_id" id="category_id" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $item->category_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-[var(--table-data-color)]">URL de la Imagen</label>
            <input type="text" name="image" id="image" value="{{ $item->image }}" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar
            </button>
        </div>
    </form>
</div>
@endsection