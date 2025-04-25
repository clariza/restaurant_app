@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Lista de Mesas</h1>
    
    <!-- Botón para abrir el modal -->
    <button onclick="openModal()" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-[var(--secondary-color)] transition duration-200">
        <i class="fas fa-plus mr-2"></i>Crear Mesa
    </button>

    <!-- Modal -->
    <div id="createTableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal content -->
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-[var(--primary-color)]">Crear Nueva Mesa</h3>
                <form action="{{ route('tables.store') }}" method="POST" class="mt-2">
                    @csrf
                    <div class="mt-4">
                        <label for="number" class="block text-sm font-medium text-[var(--table-data-color)]">Número de Mesa</label>
                        <input type="text" name="number" id="number" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                    </div>
                    <div class="mt-4">
                        <label for="state" class="block text-sm font-medium text-[var(--table-data-color)]">Estado</label>
                        <select name="state" id="state" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                        </select>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600 transition duration-200">Cancelar</button>
                        <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de mesas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($tables as $table)
                <tr>
                    <td class="px-6 py-4 text-[var(--table-data-color)]">{{ $table->number }}</td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]">{{ $table->state }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tables.edit', $table->id) }}" class="text-[var(--primary-color)] hover:text-[var(--secondary-color)] mr-2">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="{{ route('tables.destroy', $table->id) }}" method="POST" class="inline">
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

<script>
    function openModal() {
        document.getElementById('createTableModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('createTableModal').classList.add('hidden');
    }
</script>
@endsection