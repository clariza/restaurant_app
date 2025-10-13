@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Encabezado -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold mb-2 text-[var(--primary-color)] relative pb-2 section-title">
                Compras Realizadas
            </h1>
            <p class="text-sm text-[var(--text-light)]">Gestión y seguimiento de todas las compras</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('purchases.create') }}" 
               class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Nueva Compra</span>
            </a>
        @endif
    </div>

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 transition-all duration-500 ease-in-out">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="document.getElementById('success-alert').remove()">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    <!-- Filtros y Búsqueda -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 mb-6">
        <form method="GET" action="{{ route('purchases.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </label>
                    <input type="text" 
                           name="search" 
                           class="w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--primary-color)]" 
                           placeholder="Número de referencia, proveedor..."
                           value="{{ request('search') }}">
                </div>

                <!-- Filtro por Proveedor -->
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        <i class="fas fa-truck mr-1"></i>Proveedor
                    </label>
                    <select name="supplier_id" class="w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--primary-color)]">
                        <option value="">Todos los proveedores</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        <i class="fas fa-info-circle mr-1"></i>Estado
                    </label>
                    <select name="status" class="w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--primary-color)]">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Completado
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            Cancelado
                        </option>
                    </select>
                </div>

                <!-- Filtro por Fecha -->
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-light)] mb-1">
                        <i class="fas fa-calendar mr-1"></i>Fecha
                    </label>
                    <input type="date" 
                           name="date" 
                           class="w-full border border-[var(--gray-light)] rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[var(--primary-color)]"
                           value="{{ request('date') }}">
                </div>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="submit" class="bg-[var(--primary-color)] hover:bg-[var(--primary-light)] text-white px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-filter"></i>
                    <span>Filtrar</span>
                </button>
                <a href="{{ route('purchases.index') }}" class="bg-[var(--gray-light)] hover:bg-gray-300 text-[var(--text-color)] px-6 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                    <i class="fas fa-undo"></i>
                    <span>Limpiar</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Compras -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-[var(--primary-color)] to-[var(--primary-light)] text-white">
                        <th class="px-4 py-3 text-left font-semibold border-r border-white/20">Referencia</th>
                        <th class="px-4 py-3 text-left font-semibold border-r border-white/20">Proveedor</th>
                        <th class="px-4 py-3 text-left font-semibold border-r border-white/20">Fecha</th>
                        <th class="px-4 py-3 text-right font-semibold border-r border-white/20">Monto Total</th>
                        <th class="px-4 py-3 text-center font-semibold border-r border-white/20">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--gray-light)]">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 border-r border-[var(--gray-light)]">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-[var(--text-color)]">
                                    {{ $purchase->reference_number }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-r border-[var(--gray-light)]">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-[var(--primary-color)] text-white flex items-center justify-center font-bold text-xs mr-3 flex-shrink-0">
                                        {{ substr($purchase->supplier->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-[var(--text-color)]">{{ $purchase->supplier->name }}</div>
                                        <small class="text-[var(--text-light)] text-xs">
                                            NIT: {{ $purchase->supplier->nit ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-r border-[var(--gray-light)]">
                                <div class="flex items-center text-[var(--text-color)]">
                                    <i class="fas fa-calendar-alt text-[var(--text-light)] mr-2"></i>
                                    {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right border-r border-[var(--gray-light)]">
                                <span class="font-bold text-green-600">
                                    Bs. {{ number_format($purchase->total_amount, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center border-r border-[var(--gray-light)]">
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock', 'text' => 'Pendiente'],
                                        'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle', 'text' => 'Completado'],
                                        'cancelled' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'times-circle', 'text' => 'Cancelado']
                                    ];
                                    $config = $statusConfig[$purchase->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                                    <i class="fas fa-{{ $config['icon'] }} mr-1"></i>
                                    {{ $config['text'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-150"
                                       title="Ver detalles">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    
                                    @if(auth()->user()->role === 'admin')
                                        @if($purchase->status === 'pending')
                                            <a href="{{ route('purchases.edit', $purchase->id) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 rounded bg-yellow-500 hover:bg-yellow-600 text-white transition-colors duration-150"
                                               title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        @endif

                                        <form action="{{ route('purchases.destroy', $purchase->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar esta compra?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded bg-red-500 hover:bg-red-600 text-white transition-colors duration-150"
                                                    title="Eliminar">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="text-[var(--text-light)]">
                                    <i class="fas fa-inbox text-5xl mb-4 opacity-50"></i>
                                    <p class="text-lg">No se encontraron compras registradas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($purchases->hasPages())
            <div class="px-6 py-4 border-t border-[var(--gray-light)] bg-gray-50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-[var(--text-light)]">
                        Mostrando {{ $purchases->firstItem() }} a {{ $purchases->lastItem() }} 
                        de {{ $purchases->total() }} registros
                    </div>
                    <div>
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Estadísticas -->
    {{-- @if(isset($statistics))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 text-center">
                <i class="fas fa-shopping-cart text-4xl text-[var(--primary-color)] mb-3"></i>
                <h4 class="text-2xl font-bold text-[var(--text-color)] mb-1">{{ $statistics['total_purchases'] ?? 0 }}</h4>
                <small class="text-[var(--text-light)]">Total Compras</small>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 text-center">
                <i class="fas fa-dollar-sign text-4xl text-green-600 mb-3"></i>
                <h4 class="text-2xl font-bold text-[var(--text-color)] mb-1">Bs. {{ number_format($statistics['total_amount'] ?? 0, 2) }}</h4>
                <small class="text-[var(--text-light)]">Monto Total</small>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 text-center">
                <i class="fas fa-clock text-4xl text-yellow-500 mb-3"></i>
                <h4 class="text-2xl font-bold text-[var(--text-color)] mb-1">{{ $statistics['pending'] ?? 0 }}</h4>
                <small class="text-[var(--text-light)]">Pendientes</small>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-[var(--gray-light)] p-6 text-center">
                <i class="fas fa-check-circle text-4xl text-blue-500 mb-3"></i>
                <h4 class="text-2xl font-bold text-[var(--text-color)] mb-1">{{ $statistics['completed'] ?? 0 }}</h4>
                <small class="text-[var(--text-light)]">Completadas</small>
            </div>
        </div>
    @endif --}}
</div>
@endsection