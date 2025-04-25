@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título con estilo consistente -->
    <h1 class="text-xl font-bold mb-6 text-[var(--primary-color)] relative pb-2 section-title">
        Editar Gasto
        <span class="absolute bottom-0 left-0 w-10 h-1 bg-[var(--primary-color)] rounded"></span>
    </h1>
    
    <!-- Formulario con estilos del sistema -->
    <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <!-- Grid de 2 columnas para pantallas medianas/grandes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="input-label">Descripción</label>
                <input type="text" name="description" id="description" 
                       class="modal-input" 
                       placeholder="Ingrese la descripción del gasto"
                       value="{{ old('description', $expense->description) }}"
                       required>
            </div>
            
            <!-- Monto -->
            <div class="mb-4">
                <label for="amount" class="input-label">Monto (S/)</label>
                <input type="number" name="amount" id="amount" step="0.01" 
                       class="modal-input" 
                       placeholder="0.00"
                       value="{{ old('amount', $expense->amount) }}"
                       required>
            </div>
            
            <!-- Fecha -->
            <div class="mb-4">
                <label for="date" class="input-label">Fecha</label>
                <input type="date" name="date" id="date" 
                       class="modal-input"
                       value="{{ old('date', \Carbon\Carbon::parse($expense->date)->format('Y-m-d')) }}"
                       required>
            </div>
            
            <!-- Categoría -->
            <div class="mb-4">
                <label for="category" class="input-label">Categoría</label>
                <input type="text" name="category" id="category" 
                       class="modal-input"
                       placeholder="Ej. Operativos, Administrativos"
                       value="{{ old('category', $expense->category) }}">
            </div>
            
            <!-- Subcategoría -->
            <div class="mb-4">
                <label for="subcategory" class="input-label">Subcategoría</label>
                <input type="text" name="subcategory" id="subcategory" 
                       class="modal-input"
                       placeholder="Ej. Materiales, Servicios"
                       value="{{ old('subcategory', $expense->subcategory) }}">
            </div>
        </div>
        
        <!-- Botón de submit con estilo consistente -->
        <div class="flex justify-end mt-6">
            <button type="submit" 
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-light)] transition duration-200 inline-flex items-center">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection