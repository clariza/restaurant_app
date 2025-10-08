@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-[#203363]">Historial de Movimientos de Inventario</h2>
    <a href="{{ route('inventory.index') }}" 
       class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Volver al inventario
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg p-4">
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <input type="date" id="filter-date" class="border rounded-lg p-2">
            <select id="filter-type" class="border rounded-lg p-2">
                <option value="all">Todos los tipos</option>
                <option value="addition">Ingresos</option>
                <option value="subtraction">Salidas</option>
            </select>
            <button id="apply-filters" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Fecha</th>
                    <th class="text-left py-2">Producto</th>
                    <th class="text-left py-2">Usuario</th>
                    <th class="text-left py-2">Tipo</th>
                    <th class="text-right py-2">Cantidad</th>
                    <th class="text-right py-2">Stock anterior</th>
                    <th class="text-right py-2">Nuevo stock</th>
                    <th class="text-left py-2">Notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2">{{ $movement->item->name }}</td>
                    <td class="py-2">{{ $movement->user->name }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $movement->movement_type === 'addition' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $movement->movement_type === 'addition' ? 'Ingreso' : 'Salida' }}
                        </span>
                    </td>
                    <td class="text-right py-2">{{ $movement->quantity }}</td>
                    <td class="text-right py-2">{{ $movement->previous_stock }}</td>
                    <td class="text-right py-2 font-bold">{{ $movement->new_stock }}</td>
                    <td class="py-2 text-sm text-gray-500">{{ $movement->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('apply-filters').addEventListener('click', function() {
        const date = document.getElementById('filter-date').value;
        const type = document.getElementById('filter-type').value;
        
        let url = new URL(window.location.href);
        let params = new URLSearchParams();
        
        if (date) params.append('date', date);
        if (type !== 'all') params.append('type', type);
        
        window.location.href = url.pathname + '?' + params.toString();
    });
});
</script>
@endsection