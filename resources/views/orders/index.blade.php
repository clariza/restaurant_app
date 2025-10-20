@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado y controles -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-[#203363]">
            <i class="fas fa-list-alt mr-2"></i> Historial de Ventas
        </h1>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('menu.index') }}" 
               class="bg-[#6380a6] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Menú
            </a>
            
            <div class="relative">
                <input type="text" id="search-input" placeholder="Buscar..." 
                       class="border rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-[#203363]"
                       value="{{ request('search') }}">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Tipo:</label>
                    <select name="type" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="Comer aquí" {{ request('type') == 'Comer aquí' ? 'selected' : '' }}>Comer aquí</option>
                        <option value="Para llevar" {{ request('type') == 'Para llevar' ? 'selected' : '' }}>Para llevar</option>
                        <option value="Recoger" {{ request('type') == 'Recoger' ? 'selected' : '' }}>Recoger</option>
                        <option value="proforma" {{ request('type') == 'proforma' ? 'selected' : '' }}>Proformas</option>
                    </select>
                </div>
                
                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Estado:</label>
                    <select name="status" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completadas</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                    </select>
                </div>
                
                <!-- Botón de aplicar filtros -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors w-full">
                        <i class="fas fa-filter mr-2"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Listado de órdenes y proformas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Encabezados de la tabla -->
        <div class="grid grid-cols-12 bg-[#203363] text-white p-4 font-bold text-sm">
            <div class="col-span-2 md:col-span-1">ID</div>
            <div class="col-span-4 md:col-span-2">Fecha/Hora</div>
            <div class="hidden md:block md:col-span-2">Cliente</div>
            <div class="col-span-3 md:col-span-2">Tipo</div>
            <div class="hidden md:block md:col-span-1">Items</div>
            <div class="col-span-3 md:col-span-2">Total</div>
            <div class="hidden md:block md:col-span-2">Acciones</div>
        </div>
        
        <!-- Cuerpo de la tabla - ORDEN ASCENDENTE -->
        @forelse($orders->merge($proformas)->sortBy('created_at') as $record)
            @php
                $isProforma = $record instanceof \App\Models\Proforma;
                $badgeColor = $isProforma ? 'bg-[#EF476F]' : 'bg-[#203363]';
                $typeColor = [
                    'Comer aquí' => 'bg-[#FFD166] text-[#203363]',
                    'Para llevar' => 'bg-[#06D6A0] text-white',
                    'Recoger' => 'bg-[#118AB2] text-white',
                    'proforma' => 'bg-[#EF476F] text-white'
                ][$isProforma ? 'proforma' : $record->order_type];
            @endphp
            
            <div class="grid grid-cols-12 p-4 border-b hover:bg-gray-50 items-center text-sm">
                <!-- ID -->
                <div class="col-span-2 md:col-span-1 font-medium">
                    <span class="inline-block w-6 h-6 rounded-full {{ $badgeColor }} text-white text-xs flex items-center justify-center mr-1">
                        {{ $isProforma ? 'P' : 'O' }}
                    </span>
                    {{ $isProforma ? 'PROF-'.$record->id : $record->transaction_number }}
                </div>
                
                <!-- Fecha -->
                <div class="col-span-4 md:col-span-2">
                    <div>{{ $record->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $record->created_at->format('H:i') }}</div>
                </div>
                
                <!-- Cliente (solo desktop) -->
                <div class="hidden md:block md:col-span-2 truncate">
                    {{ $record->customer_name ?? 'N/A' }}
                </div>
                
                <!-- Tipo -->
                <div class="col-span-3 md:col-span-2">
                    <span class="px-2 py-1 rounded-full text-xs {{ $typeColor }}">
                        {{ $isProforma ? 'Proforma' : $record->order_type }}
                        @if(!$isProforma && $record->order_type == 'Comer aquí' && $record->table_number)
                            (Mesa {{ $record->table_number }})
                        @endif
                    </span>
                </div>
                
                <!-- Items (solo desktop) -->
                <div class="hidden md:block md:col-span-1 text-center">
                    {{ $record->items->count() }}
                </div>
                
                <!-- Total -->
                <div class="col-span-3 md:col-span-2 font-bold">
                    ${{ number_format($record->total, 2) }}
                </div>
                
                <!-- Acciones (solo desktop) -->
                <div class="hidden md:flex md:col-span-2 space-x-2">
                    <a href="{{ $isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id) }}" 
                       class="text-[#203363] hover:text-[#47517c] p-1"
                       title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    
                    <button class="text-[#203363] hover:text-[#47517c] p-1"
                            onclick="printOrder('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $record->id }}')"
                            title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    
                    @if($isProforma && method_exists($record, 'canBeConverted') && $record->canBeConverted())
                        <button class="text-green-600 hover:text-green-800 p-1"
                                onclick="convertToOrder('{{ $record->id }}')"
                                title="Convertir a orden">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    @endif
                </div>

                <!-- Acciones móvil -->
                <div class="md:hidden col-span-12 mt-2 pt-2 border-t flex justify-end space-x-3">
                    <a href="{{ $isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id) }}" 
                       class="text-[#203363] hover:text-[#47517c] text-sm flex items-center">
                        <i class="fas fa-eye mr-1"></i> Ver
                    </a>
                    
                    <button class="text-[#203363] hover:text-[#47517c] text-sm flex items-center"
                            onclick="printOrder('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $record->id }}')">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                    
                    @if($isProforma && method_exists($record, 'canBeConverted') && $record->canBeConverted())
                        <button class="text-green-600 hover:text-green-800 text-sm flex items-center"
                                onclick="convertToOrder('{{ $record->id }}')">
                            <i class="fas fa-exchange-alt mr-1"></i> Convertir
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                <p class="text-lg">No se encontraron órdenes o proformas</p>
                <p class="text-sm">Intenta con otros criterios de búsqueda</p>
            </div>
        @endforelse
        
        <!-- Paginación -->
        @if($orders->count() > 0 || $proformas->count() > 0)
            <div class="p-4 border-t">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-2 md:mb-0">
                        Mostrando {{ $orders->firstItem() ?? $proformas->firstItem() ?? 0 }} a 
                        {{ $orders->lastItem() ?? $proformas->lastItem() ?? 0 }} de 
                        {{ $orders->total() + $proformas->total() }} registros
                    </div>
                    <div class="flex space-x-1">
                        {{ $orders->withQueryString()->links() }}
                        @if(request('type') === 'all' || request('type') === 'proforma')
                            {{ $proformas->withQueryString()->links() }}
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Scripts para manejar interacciones -->
<script>
    // Aplicar filtros al enviar el formulario
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData).toString();
        window.location.href = "{{ route('orders.index') }}?" + params;
    });
    
    // Búsqueda en tiempo real con debounce
    let searchTimer;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const searchValue = this.value.trim();
            const url = new URL(window.location.href);
            
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            
            window.location.href = url.toString();
        }, 500);
    });
    
    // Función para imprimir orden/proforma
    function printOrder(type, id) {
        const url = type === 'proforma' 
            ? `/proformas/${id}/print` 
            : `/orders/${id}/print`;
            
        window.open(url, '_blank');
    }
    
    // Función para convertir proforma a orden
    function convertToOrder(proformaId) {
        Swal.fire({
            title: '¿Convertir proforma a orden?',
            text: "Esta acción registrará la venta en la caja chica actual",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#203363',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, convertir',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/proformas/${proformaId}/convert`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Error: ${error.message || 'Error en la solicitud'}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: '¡Conversión exitosa!',
                        html: `Orden creada: <strong>${result.value.order_number}</strong>`,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', result.value.message, 'error');
                }
            }
        });
    }
</script>

<style>
    /* Estilos personalizados para la paginación */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination li a,
    .pagination li span {
        display: block;
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    
    .pagination li.active span {
        background-color: #203363;
        color: white;
        border-color: #203363;
    }
    
    .pagination li a:hover {
        background-color: #f8fafc;
    }
    
    /* Asegurar que la tabla sea responsive */
    @media (max-width: 768px) {
        .grid-cols-12 > div {
            padding: 8px 4px;
        }
    }
</style>
@endsection