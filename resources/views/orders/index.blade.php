@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Encabezado --}}
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
    {{-- Alertas --}}
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
    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-{{ ($isAdmin ?? false) && !empty($branches) ? '5' : '4' }} gap-4">
                {{-- Tipo --}}
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Tipo:</label>
                    <select name="type"
                            class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all"        {{ request('type') == 'all'        ? 'selected' : '' }}>Todos</option>
                        <option value="Comer aquí" {{ request('type') == 'Comer aquí' ? 'selected' : '' }}>Para la Mesa</option>
                        <option value="Para llevar"{{ request('type') == 'Para llevar'? 'selected' : '' }}>Para llevar</option>
                        <option value="Recoger"    {{ request('type') == 'Recoger'    ? 'selected' : '' }}>Recoger</option>
                        <option value="proforma"   {{ request('type') == 'proforma'   ? 'selected' : '' }}>Proformas Pendientes</option>
                    </select>
                </div>
                {{-- Desde --}}
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Desde:</label>
                    <input type="date" name="date_from"
                           value="{{ request('date_from') }}"
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                </div>
                {{-- Hasta --}}
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Hasta:</label>
                    <input type="date" name="date_to"
                           value="{{ request('date_to') }}"
                           class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                </div>
                {{-- Vendedor --}}
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">Vendedor:</label>
                    <select name="seller_id"
                            class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('seller_id', 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                        @foreach($sellers ?? [] as $seller)
                            <option value="{{ $seller->id }}"
                                {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Sucursal --}}
                @if(($isAdmin ?? false) && !empty($branches))
                <div>
                    <label class="block text-sm font-medium text-[#203363] mb-1">
                        <i class="fas fa-store mr-1"></i> Sucursal:
                    </label>
                    <select name="branch_id"
                            class="border rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#203363]">
                        <option value="all" {{ request('branch_id', 'all') === 'all' ? 'selected' : '' }}>
                            Todas las Sucursales
                        </option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}{{ $branch->is_main ? ' ⭐' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            {{-- Botones filtro --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <button type="submit"
                        class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Aplicar Filtros
                </button>
                <button type="button" onclick="clearFilters()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Limpiar Filtros
                </button>
            </div>
        </form>
    </div>
    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- 
            DISTRIBUCIÓN DE COLUMNAS (12 cols):
            Sin admin: ID(2) | Fecha(2) | Cliente(3) | Tipo(2) | Total(1) | Acciones(2)
            Con admin: ID(2) | Fecha(2) | Cliente(1) | Tipo(2) | Total(1) | Sucursal(2) | Acciones(2)
        --}}
        {{-- Header --}}
        @if($isAdmin ?? false)
        <div class="hidden md:grid md:grid-cols-12 bg-[#203363] text-white px-4 py-3 font-bold text-sm">
            <div class="col-span-2">ID</div>
            <div class="col-span-2">Fecha/Hora</div>
            <div class="col-span-1">Cliente</div>
            <div class="col-span-2">Tipo</div>
            <div class="col-span-1">Total</div>
            <div class="col-span-2">Sucursal</div>
            <div class="col-span-2">Acciones</div>
        </div>
        @else
        <div class="hidden md:grid md:grid-cols-12 bg-[#203363] text-white px-4 py-3 font-bold text-sm">
            <div class="col-span-2">ID</div>
            <div class="col-span-2">Fecha/Hora</div>
            <div class="col-span-3">Cliente</div>
            <div class="col-span-2">Tipo</div>
            <div class="col-span-1">Total</div>
            <div class="col-span-2">Acciones</div>
        </div>
        @endif
        {{-- Header móvil --}}
        <div class="grid grid-cols-12 bg-[#203363] text-white px-4 py-3 font-bold text-sm md:hidden">
            <div class="col-span-3">ID</div>
            <div class="col-span-4">Fecha</div>
            <div class="col-span-3">Tipo</div>
            <div class="col-span-2 text-right">Total</div>
        </div>
        {{-- Filas --}}
        @forelse($orders->merge($proformas)->sortByDesc('created_at') as $record)
            @php
                $isProforma = $record instanceof \App\Models\Proforma;
                // Saltar proformas ya convertidas
                if ($isProforma) {
                    $isConverted = ($record->converted_to_order == 1)
                        || (isset($record->is_converted) && $record->is_converted == 1)
                        || (!empty($record->converted_order_id));
                    if ($isConverted) { continue; }
                    if ($record->status === 'cancelled') { continue; }
                }
                $badgeColor = $isProforma ? 'bg-[#EF476F]' : 'bg-[#203363]';
                $typeColor  = [
                    'Comer aquí'  => 'bg-[#FFD166] text-[#203363]',
                    'Para llevar' => 'bg-[#06D6A0] text-white',
                    'Recoger'     => 'bg-[#118AB2] text-white',
                    'proforma'    => 'bg-[#EF476F] text-white',
                ][$isProforma ? 'proforma' : ($record->order_type ?? 'proforma')];
                $typeLabel = $isProforma
                    ? 'Proforma'
                    : ($record->order_type === 'Comer aquí' ? 'Para la Mesa' : $record->order_type);
                $tableLabel = (!$isProforma && ($record->order_type ?? '') === 'Comer aquí' && $record->table_number)
                    ? ' (Mesa '.$record->table_number.')'
                    : '';
            @endphp
            {{-- Fila desktop admin --}}
            @if($isAdmin ?? false)
            <div class="hidden md:grid md:grid-cols-12 px-4 py-3 border-b hover:bg-gray-50 items-center text-sm">
                {{-- ID (2) --}}
                <div class="col-span-2 font-medium">
                    <div class="flex items-center gap-1 min-w-0">
                        <span class="inline-flex w-6 h-6 rounded-full {{ $badgeColor }} text-white text-xs items-center justify-center flex-shrink-0">
                            {{ $isProforma ? 'P' : 'O' }}
                        </span>
                        <span class="truncate text-xs">
                            {{ $isProforma ? 'PROF-'.$record->id : $record->transaction_number }}
                        </span>
                    </div>
                </div>
                {{-- Fecha (2) --}}
                <div class="col-span-2">
                    <div>{{ $record->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $record->created_at->format('H:i') }}</div>
                </div>
                {{-- Cliente (1) --}}
                <div class="col-span-1 truncate text-gray-700">
                    {{ $record->customer_name ?? 'N/A' }}
                </div>
                {{-- Tipo (2) --}}
                <div class="col-span-2">
                    <span class="inline-block px-2 py-1 rounded-full text-xs {{ $typeColor }} whitespace-nowrap">
                        {{ $typeLabel }}{{ $tableLabel }}
                    </span>
                </div>
                {{-- Total (1) --}}
                <div class="col-span-1 font-bold text-[#203363] whitespace-nowrap">
                    Bs {{ number_format($record->total, 2) }}
                </div>
                {{-- Sucursal (2) --}}
                <div class="col-span-2 text-xs text-gray-600">
                    @if($record->branch)
                        <span class="flex items-center gap-1 min-w-0">
                            <i class="fas fa-building text-gray-400 flex-shrink-0"></i>
                            <span class="truncate">{{ Str::limit($record->branch->name, 30) }}</span>
                        </span>
                    {{-- @else
                        <span class="text-gray-400">—</span> --}}
                    @endif
                </div>
                {{-- Acciones (2) --}}
                <div class="col-span-2 flex items-center gap-1">
                    <a href="{{ $isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id) }}"
                       class="text-[#203363] hover:text-[#47517c] p-1" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="text-[#203363] hover:text-[#47517c] p-1"
                            onclick="printOrder('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $record->id }}')"
                            title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    @if($isProforma)
                        @if(method_exists($record, 'canBeConverted') && $record->canBeConverted())
                            <button class="text-green-600 hover:text-green-800 p-1"
                                    onclick="convertToOrder('{{ $record->id }}')"
                                    title="Convertir a orden">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        @endif
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteProforma('{{ $record->id }}')"
                                title="Cancelar proforma">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                    @if(!$isProforma && $hasOpenPettyCash && auth()->user()->role === 'admin')
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteOrder('{{ $record->id }}', '{{ $record->transaction_number }}')"
                                title="Eliminar orden">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>
            </div>
            @else
            {{-- Fila desktop no-admin --}}
            <div class="hidden md:grid md:grid-cols-12 px-4 py-3 border-b hover:bg-gray-50 items-center text-sm">
                {{-- ID (2) --}}
                <div class="col-span-2 font-medium">
                    <div class="flex items-center gap-1 min-w-0">
                        <span class="inline-flex w-6 h-6 rounded-full {{ $badgeColor }} text-white text-xs items-center justify-center flex-shrink-0">
                            {{ $isProforma ? 'P' : 'O' }}
                        </span>
                        <span class="truncate text-xs">
                            {{ $isProforma ? 'PROF-'.$record->id : $record->transaction_number }}
                        </span>
                    </div>
                </div>
                {{-- Fecha (2) --}}
                <div class="col-span-2">
                    <div>{{ $record->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $record->created_at->format('H:i') }}</div>
                </div>
                {{-- Cliente (3) --}}
                <div class="col-span-3 truncate text-gray-700">
                    {{ $record->customer_name ?? 'N/A' }}
                </div>
                {{-- Tipo (2) --}}
                <div class="col-span-2">
                    <span class="inline-block px-2 py-1 rounded-full text-xs {{ $typeColor }} whitespace-nowrap">
                        {{ $typeLabel }}{{ $tableLabel }}
                    </span>
                </div>
                {{-- Total (1) --}}
                <div class="col-span-1 font-bold text-[#203363] whitespace-nowrap">
                    Bs {{ number_format($record->total, 2) }}
                </div>
                {{-- Acciones (2) --}}
                <div class="col-span-2 flex items-center gap-1">
                    <a href="{{ $isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id) }}"
                       class="text-[#203363] hover:text-[#47517c] p-1" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="text-[#203363] hover:text-[#47517c] p-1"
                            onclick="printOrder('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $record->id }}')"
                            title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    @if($isProforma)
                        @if(method_exists($record, 'canBeConverted') && $record->canBeConverted())
                            <button class="text-green-600 hover:text-green-800 p-1"
                                    onclick="convertToOrder('{{ $record->id }}')"
                                    title="Convertir a orden">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        @endif
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteProforma('{{ $record->id }}')"
                                title="Cancelar proforma">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                    @if(!$isProforma && $hasOpenPettyCash && auth()->user()->role === 'admin')
                        <button class="text-red-600 hover:text-red-800 p-1"
                                onclick="deleteOrder('{{ $record->id }}', '{{ $record->transaction_number }}')"
                                title="Eliminar orden">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>
            </div>
            @endif
            {{-- Fila móvil --}}
            <div class="grid grid-cols-12 px-4 py-3 border-b hover:bg-gray-50 items-center text-sm md:hidden">
                {{-- ID (3) --}}
                <div class="col-span-3 font-medium">
                    <div class="flex items-center gap-1 min-w-0">
                        <span class="inline-flex w-5 h-5 rounded-full {{ $badgeColor }} text-white text-xs items-center justify-center flex-shrink-0">
                            {{ $isProforma ? 'P' : 'O' }}
                        </span>
                        <span class="truncate text-xs">
                            {{ $isProforma ? $record->id : $record->transaction_number }}
                        </span>
                    </div>
                </div>
                {{-- Fecha (4) --}}
                <div class="col-span-4">
                    <div class="text-xs">{{ $record->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $record->created_at->format('H:i') }}</div>
                </div>
                {{-- Tipo (3) --}}
                <div class="col-span-3">
                    <span class="inline-block px-1 py-0.5 rounded-full text-xs {{ $typeColor }}">
                        {{ $typeLabel }}
                    </span>
                </div>
                {{-- Total (2) --}}
                <div class="col-span-2 font-bold text-[#203363] text-xs text-right">
                    Bs {{ number_format($record->total, 2) }}
                </div>
                {{-- Acciones móvil expandidas --}}
                <div class="col-span-12 mt-2 pt-2 border-t flex justify-end gap-3 flex-wrap">
                    <a href="{{ $isProforma ? route('proformas.show', $record->id) : route('orders.show', $record->id) }}"
                       class="text-[#203363] hover:text-[#47517c] text-sm flex items-center">
                        <i class="fas fa-eye mr-1"></i> Ver
                    </a>
                    <button class="text-[#203363] hover:text-[#47517c] text-sm flex items-center"
                            onclick="printOrder('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $record->id }}')">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                    @if($isProforma)
                        @if(method_exists($record, 'canBeConverted') && $record->canBeConverted())
                            <button class="text-green-600 hover:text-green-800 text-sm flex items-center"
                                    onclick="convertToOrder('{{ $record->id }}')">
                                <i class="fas fa-exchange-alt mr-1"></i> Convertir
                            </button>
                        @endif
                        @if(method_exists($record, 'canBeCancelled') && $record->canBeCancelled())
                            <button class="text-red-600 hover:text-red-800 text-sm flex items-center"
                                    onclick="deleteProforma('{{ $record->id }}')">
                                <i class="fas fa-trash-alt mr-1"></i> Cancelar
                            </button>
                        @endif
                    @endif
                    @if(!$isProforma && $hasOpenPettyCash && auth()->user()->role === 'admin')
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
        {{-- Paginación --}}
        @if($orders->count() > 0 || $proformas->count() > 0)
            <div class="p-4 border-t">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Mostrando {{ ($orders->firstItem() ?? 0) + ($proformas->firstItem() ?? 0) }}
                        registros de
                        {{ $orders->total() + $proformas->total() }} totales
                    </div>
                    <div class="flex flex-wrap gap-2">
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
{{-- Form oculto para eliminación --}}
<form id="delete-order-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<script>
    // Aplicar filtros
    document.getElementById('filter-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this)).toString();
        window.location.href = "{{ route('orders.index') }}?" + params;
    });
    // Limpiar filtros
    function clearFilters() {
        window.location.href = "{{ route('orders.index') }}";
    }
    // Búsqueda con debounce
    let searchTimer;
    document.getElementById('search-input').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const url = new URL(window.location.href);
            this.value.trim()
                ? url.searchParams.set('search', this.value.trim())
                : url.searchParams.delete('search');
            window.location.href = url.toString();
        }, 500);
    });
    // Imprimir
    function printOrder(type, id) {
        const url = type === 'proforma' ? `/proformas/${id}/print` : `/orders/${id}/print`;
        window.open(url, '_blank');
    }
    // Eliminar orden
    function deleteOrder(orderId, orderNumber) {
        Swal.fire({
            title: '¿Eliminar orden?',
            html: `¿Estás seguro de eliminar la orden <strong>${orderNumber}</strong>?<br><br>
                   <span class="text-red-600">Esta acción revertirá el stock y no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => fetch(`/orders/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .catch(error => Swal.showValidationMessage(`Error: ${error.message || 'Error al eliminar'}`))
        }).then(result => {
           if (result.isConfirmed && result.value?.success) {
                const data = result.value.data;
                const proformasMsg = data.cancelled_proformas > 0
                ? `<p class="mt-2 text-sm text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Se cancelaron <strong>${data.cancelled_proformas}</strong> proforma(s) pendiente(s)
                    y se restauró su stock automáticamente.
                 </p>`: '';
                Swal.fire({
                title: '¡Orden Eliminada!',
                html: `${result.value.message}${proformasMsg}`,
                icon: 'success',
                }).then(() => window.location.reload());
            }
        });
    }
    // Convertir proforma a orden
    async function convertToOrder(proformaId) {
        localStorage.removeItem('convertingProforma');
        localStorage.removeItem('proformaId');
        localStorage.removeItem('proformaNotes');
        try {
            Swal.fire({
                title: 'Cargando proforma...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            const response = await fetch(`/proformas/${proformaId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!response.ok) throw new Error('Error al obtener la proforma');
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Error al cargar la proforma');
            const proforma = data.proforma;
            if (!data.can_convert) {
                Swal.close();
                const messages = {
                    already_converted:  ['Proforma ya convertida',   'Esta proforma ya fue convertida anteriormente.'],
                    no_open_petty_cash: ['Sin caja chica abierta',   'No hay una caja chica abierta.'],
                    insufficient_stock: ['Stock insuficiente',
                        'Productos sin stock:<br>' +
                        (data.stock_issues ?? []).map(i =>
                            `• <strong>${i.item_name}</strong>: necesita ${i.required}, hay ${i.available}`
                        ).join('<br>')
                    ],
                };
                const [title, html] = messages[data.reason] ?? ['No convertible', 'Esta proforma no puede convertirse.'];
                Swal.fire({ title, html, icon: 'warning', confirmButtonColor: '#203363' });
                return;
            }
            const confirm = await Swal.fire({
                title: '¿Convertir proforma a orden?',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Se cargará al sistema de pedidos:</p>
                        <div class="bg-gray-50 p-4 rounded-lg mb-3 text-sm">
                            <p><strong>ID:</strong> PROF-${proforma.id}</p>
                            <p><strong>Cliente:</strong> ${proforma.customer_name}</p>
                            <p><strong>Items:</strong> ${proforma.items.length}</p>
                            <p><strong>Total:</strong> Bs ${parseFloat(proforma.total).toFixed(2)}</p>
                        </div>
                        <p class="text-sm text-gray-600">Podrás revisar el pedido y proceder con el pago.</p>
                    </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#203363',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cargar al sistema',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'swal-wide' }
            });
            if (!confirm.isConfirmed) return;
            const orderItems = proforma.items.map(item => ({
                id: item.menu_item_id,
                name: item.name,
                price: parseFloat(item.price),
                quantity: item.quantity,
                menu_item_id: item.menu_item_id
            }));
            localStorage.setItem('order',             JSON.stringify(orderItems));
            localStorage.setItem('orderType',         proforma.order_type || 'Comer aquí');
            localStorage.setItem('orderNotes',        proforma.notes || '');
            localStorage.setItem('customerName',      proforma.customer_name || '');
            localStorage.setItem('customerPhone',     proforma.customer_phone || '');
            localStorage.setItem('convertingProforma','true');
            localStorage.setItem('proformaId',        proformaId);
            localStorage.setItem('proformaNotes',     proforma.notes || '');
            await Swal.fire({
                title: '¡Proforma Cargada!',
                html: `
                    <div class="text-center">
                        <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                        <p class="mb-3">Proforma cargada exitosamente.</p>
                        <div class="bg-blue-50 p-4 rounded-lg text-sm text-blue-800">
                            <p><strong>Cliente:</strong> ${proforma.customer_name}</p>
                            <p><strong>Items:</strong> ${orderItems.length}</p>
                            <p><strong>Total:</strong> Bs ${parseFloat(proforma.total).toFixed(2)}</p>
                        </div>
                    </div>`,
                icon: 'success',
                confirmButtonText: 'Ir al Menú',
                confirmButtonColor: '#203363',
                allowOutsideClick: false,
                timer: 3000,
                timerProgressBar: true
            });
            window.location.href = '{{ route("menu.index") }}';
        } catch (error) {
            localStorage.removeItem('convertingProforma');
            localStorage.removeItem('proformaId');
            localStorage.removeItem('proformaNotes');
            Swal.fire({
                title: 'Error',
                html: `<p class="mb-2">No se pudo cargar la proforma:</p>
                       <p class="text-sm text-red-600">${error.message}</p>`,
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        }
    }
    // Eliminar/Cancelar Proforma
    function deleteProforma(proformaId) {
        Swal.fire({
            title: '¿Cancelar proforma?',
            html: `
                <div class="text-center">
                    <p class="mb-3">¿Estás seguro de cancelar la proforma <strong>PROF-${proformaId}</strong>?</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                            <div class="text-left text-sm text-yellow-800">
                                <p class="font-semibold mb-1">El stock reservado será restaurado automáticamente</p>
                                <p class="text-xs">Los items volverán a estar disponibles en el menú</p>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i> Sí, cancelar proforma',
            cancelButtonText: '<i class="fas fa-times mr-2"></i> No, mantener',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/proformas/${proformaId}`, {
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
                        `Error: ${error.message || 'Error al cancelar la proforma'}`
                    );
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: '¡Proforma Cancelada!',
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-green-500 text-5xl"></i>
                                </div>
                                <p class="mb-3 text-lg">${result.value.message}</p>
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-box-open text-green-600 text-2xl mr-3"></i>
                                        <div class="text-left">
                                            <p class="text-sm font-semibold text-green-800">
                                                Stock restaurado correctamente
                                            </p>
                                            <p class="text-xs text-green-700">
                                                Los items están nuevamente disponibles
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i> Aceptar',
                        confirmButtonColor: '#203363',
                        timer: 5000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.value.message || 'Error desconocido',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            }
        });
    }
</script>
<style>
    .swal-wide { width: 600px !important; max-width: 90% !important; }
    .pagination { display: flex; list-style: none; padding: 0; }
    .pagination li { margin: 0 2px; }
    .pagination li a,
    .pagination li span { display: block; padding: 5px 10px; border-radius: 4px; border: 1px solid #e2e8f0; }
    .pagination li.active span { background-color: #203363; color: white; border-color: #203363; }
    .pagination li a:hover { background-color: #f8fafc; }
</style>
@endsection