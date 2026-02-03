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

    <!-- Mensaje de éxito/error -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Tipo:</label>
                    <select name="type" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="Comer aquí" {{ request('type') == 'Comer aquí' ? 'selected' : '' }}>Comer aquí</option>
                        <option value="Para llevar" {{ request('type') == 'Para llevar' ? 'selected' : '' }}>Para llevar</option>
                        <option value="Recoger" {{ request('type') == 'Recoger' ? 'selected' : '' }}>Recoger</option>
                        <option value="proforma" {{ request('type') == 'proforma' ? 'selected' : '' }}>Proformas Pendientes</option>
                    </select>
                </div>
                
                <!-- Filtro por fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Desde:</label>
                    <input type="date" 
                           name="date_from" 
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]"
                           value="{{ request('date_from') }}">
                </div>
                
                <!-- Filtro por fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Hasta:</label>
                    <input type="date" 
                           name="date_to" 
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]"
                           value="{{ request('date_to') }}">
                </div>
                
                <!-- Filtro por vendedor -->
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Vendedor:</label>
                    <select name="seller_id" class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('seller_id') == 'all' ? 'selected' : '' }}>Todos</option>
                        @foreach($sellers ?? [] as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <button type="submit" 
                        class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Aplicar Filtros
                </button>
                
                <button type="button" 
                        onclick="clearFilters()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Limpiar Filtros
                </button>
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
                
                // ✅ VERIFICACIÓN ADICIONAL: Saltar si es proforma convertida
                if ($isProforma) {
                    $isConverted = ($record->converted_to_order == 1) || 
                                   (isset($record->is_converted) && $record->is_converted == 1) ||
                                   (!empty($record->converted_order_id));
                    
                    if ($isConverted) {
                        continue; // ✅ Saltar esta proforma
                    }
                }
                
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
                
                <!-- Items -->
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
                    
                    @if(!$isProforma && $hasOpenPettyCash)
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteOrder('{{ $record->id }}', '{{ $record->transaction_number }}')"
                                title="Eliminar orden">
                            <i class="fas fa-trash-alt"></i>
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
                    
                    @if(!$isProforma && $hasOpenPettyCash)
                        <button class="text-red-600 hover:text-red-800 text-sm flex items-center"
                                onclick="deleteOrder('{{ $record->id }}', '{{ $record->transaction_number }}')">
                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                <p class="text-lg">No se encontraron órdenes o proformas pendientes</p>
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

<!-- Formulario oculto para eliminación -->
<form id="delete-order-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Scripts para manejar interacciones -->
<script>
    // Aplicar filtros al enviar el formulario
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData).toString();
        window.location.href = "{{ route('orders.index') }}?" + params;
    });
    
    // Limpiar filtros
    function clearFilters() {
        window.location.href = "{{ route('orders.index') }}";
    }
    
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
    
    // Función para eliminar orden
    function deleteOrder(orderId, orderNumber) {
        Swal.fire({
            title: '¿Eliminar orden?',
            html: `¿Estás seguro de eliminar la orden <strong>${orderNumber}</strong>?<br><br>
                   <span class="text-red-600">Esta acción revertirá el stock de los productos y no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Error: ${error.message || 'Error al eliminar la orden'}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: '¡Eliminada!',
                        text: result.value.message,
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
    
    // Función para convertir proforma a orden
    async function convertToOrder(proformaId) {
        // Limpiar localStorage al inicio
        localStorage.removeItem('convertingProforma');
        localStorage.removeItem('proformaId');
        localStorage.removeItem('proformaNotes');
        
        try {
            // Mostrar loader inicial
            Swal.fire({
                title: 'Cargando proforma...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // 1. Obtener los datos de la proforma
            const response = await fetch(`/proformas/${proformaId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error('Error al obtener la proforma');
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Error al cargar la proforma');
            }

            const proforma = data.proforma;

            // 2. Validar que puede ser convertida
            if (!data.can_convert) {
                Swal.close();
                let errorMsg = 'Esta proforma no puede ser convertida';
                let errorDetail = '';
                
                if (data.is_converted) {
                    errorMsg = 'Proforma ya convertida';
                    errorDetail = 'Esta proforma ya fue convertida anteriormente a una orden de venta.';
                } else if (data.reason === 'insufficient_stock') {
                    errorMsg = 'Stock insuficiente';
                    errorDetail = 'Algunos productos no tienen stock disponible:<br><br>';
                    data.stock_issues.forEach(issue => {
                        errorDetail += `• <strong>${issue.item_name}</strong>: Requiere ${issue.required}, disponible ${issue.available}<br>`;
                    });
                } else if (data.reason === 'no_open_petty_cash') {
                    errorMsg = 'Sin caja chica abierta';
                    errorDetail = 'No hay una caja chica abierta para registrar la venta.';
                }
                
                Swal.fire({
                    title: errorMsg,
                    html: errorDetail,
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#203363'
                });
                return;
            }

            // 3. Confirmar conversión
            const confirmResult = await Swal.fire({
                title: '¿Convertir proforma a orden?',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Se cargará la siguiente proforma al sistema de pedidos:</p>
                        <div class="bg-gray-50 p-4 rounded-lg mb-3">
                            <p class="text-sm"><strong>ID:</strong> PROF-${proforma.id}</p>
                            <p class="text-sm"><strong>Cliente:</strong> ${proforma.customer_name}</p>
                            <p class="text-sm"><strong>Items:</strong> ${proforma.items.length}</p>
                            <p class="text-sm"><strong>Total:</strong> $${parseFloat(proforma.total).toFixed(2)}</p>
                        </div>
                        <p class="text-sm text-gray-600">Podrás revisar el pedido y proceder con el pago.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#203363',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cargar al sistema',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal-wide'
                }
            });

            // Verificar si el usuario canceló
            if (!confirmResult.isConfirmed) {
                return;
            }

            // 4. Cargar items al sistema de pedidos
            const orderItems = proforma.items.map(item => ({
                id: item.menu_item_id,
                name: item.name,
                price: parseFloat(item.price),
                quantity: item.quantity,
                menu_item_id: item.menu_item_id
            }));

            // Guardar en localStorage
            localStorage.setItem('order', JSON.stringify(orderItems));
            localStorage.setItem('orderType', proforma.order_type || 'Comer aquí');
            localStorage.setItem('orderNotes', proforma.notes || '');
            localStorage.setItem('customerName', proforma.customer_name || '');
            localStorage.setItem('customerPhone', proforma.customer_phone || '');
            
            // Marcar que estamos convirtiendo una proforma
            localStorage.setItem('convertingProforma', 'true');
            localStorage.setItem('proformaId', proformaId);
            localStorage.setItem('proformaNotes', proforma.notes || '');

            // 5. Mostrar notificación de éxito y redirigir
            Swal.fire({
                title: '¡Proforma Cargada!',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
                        </div>
                        <p class="mb-3">La proforma se ha cargado exitosamente al sistema de pedidos.</p>
                        <div class="bg-blue-50 p-4 rounded-lg mb-3">
                            <p class="text-sm text-blue-800"><strong>Cliente:</strong> ${proforma.customer_name}</p>
                            <p class="text-sm text-blue-800"><strong>Items cargados:</strong> ${orderItems.length}</p>
                            <p class="text-sm text-blue-800"><strong>Total:</strong> $${parseFloat(proforma.total).toFixed(2)}</p>
                        </div>
                        <p class="text-sm text-gray-600">Serás redirigido al menú para procesar el pago.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Ir al Menú',
                confirmButtonColor: '#203363',
                allowOutsideClick: false,
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '{{ route("menu.index") }}?open_payment=true';
            });

        } catch (error) {
            console.error('Error al convertir proforma:', error);
            localStorage.removeItem('convertingProforma');
            localStorage.removeItem('proformaId');
            localStorage.removeItem('proformaNotes');
            Swal.fire({
                title: 'Error',
                html: `
                    <p class="mb-2">No se pudo cargar la proforma:</p>
                    <p class="text-sm text-red-600">${error.message}</p>
                `,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc2626'
            });
        }
    }
</script>

<style>
    .swal-wide {
        width: 600px !important;
        max-width: 90% !important;
    }
    
    .swal2-html-container {
        margin: 1em 0 !important;
    }
    
    /* Animación para el ícono de éxito */
    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .swal2-icon.swal2-success .swal2-success-ring {
        animation: checkmark 0.8s ease-in-out;
    }
    
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