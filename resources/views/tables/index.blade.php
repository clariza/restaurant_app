@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-[#203363]">Configuración de Mesas</h1>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <a href="{{ url()->previous() }}" class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200 inline-flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Atrás
            </a>
            @if($tablesEnabled)
                <button onclick="openModal()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200 inline-flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> Crear Mesa
                </button>
            @endif
        </div>
    </div>

    <!-- Configuración de gestión de mesas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-lg font-medium text-[#203363] mb-4">Habilitar Gestión de Mesas</h2>
            <form action="{{ route('settings.update') }}" method="POST" id="settings-form">
    @csrf
    <input type="hidden" name="tables_enabled" value="0">
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" name="tables_enabled" value="1" 
               class="sr-only peer" {{ $settings->tables_enabled ? 'checked' : '' }}>
        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#203363]/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#203363]"></div>
        <span class="ms-3 text-sm font-medium text-[#203363]">
            {{ $settings->tables_enabled ? 'Activado' : 'Desactivado' }}
        </span>
    </label>
    <button type="submit" class="ml-4 bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200">
        Guardar Configuración
    </button>
</form>
        </div>
    </div>

    @if($tablesEnabled)
    <!-- Modal para crear mesas (solo visible si la gestión está habilitada) -->
    <div id="createTableModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal content -->
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-[#203363]">Crear Nueva Mesa</h3>
                <form action="{{ route('tables.store') }}" method="POST" class="mt-2">
                    @csrf
                    <div class="mt-4">
                        <label for="number" class="block text-sm font-medium text-[#203363]">Número de Mesa</label>
                        <input type="text" name="number" id="number" class="mt-1 block w-full px-3 py-2 border border-[#a4b6ce] rounded-md shadow-sm focus:outline-none focus:ring-[#203363] focus:border-[#203363] sm:text-sm" required>
                    </div>
                    <div class="mt-4">
                        <label for="state" class="block text-sm font-medium text-[#203363]">Estado</label>
                        <select name="state" id="state" class="mt-1 block w-full px-3 py-2 border border-[#a4b6ce] rounded-md shadow-sm focus:outline-none focus:ring-[#203363] focus:border-[#203363] sm:text-sm" required>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                        </select>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600 transition duration-200">Cancelar</button>
                        <button type="submit" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition duration-200">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de mesas (solo visible si la gestión está habilitada) -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[#203363]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($tables as $table)
                <tr>
                    <td class="px-6 py-4 text-[#203363]">{{ $table->number }}</td>
                    <td class="px-6 py-4 text-[#203363]">
                        <span class="px-2 py-1 rounded-full text-xs 
                            @if($table->state == 'Disponible') bg-green-100 text-green-800
                            @elseif($table->state == 'Ocupada') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $table->state }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tables.edit', $table->id) }}" class="text-[#203363] hover:text-[#47517c] mr-2">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="{{ route('tables.destroy', $table->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro de que deseas eliminar esta mesa?')">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-[#203363]">La gestión de mesas está actualmente desactivada. Actívala para comenzar a gestionar las mesas de tu restaurante.</p>
    </div>
    @endif
</div>

<script>
    document.getElementById('settings-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Guardando...';
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error al guardar');
        }
        
        // Recargar la página para ver los cambios
        window.location.reload();
        
    } catch (error) {
        console.error('Error:', error);
        alert(error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
    function openModal() {
        document.getElementById('createTableModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('createTableModal').classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('createTableModal');
        if (event.target === modal) {
            closeModal();
        }
    });
</script>
@endsection