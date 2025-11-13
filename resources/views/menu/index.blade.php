@extends('layouts.app')

@section('content')
@php
    $isAdmin = auth()->user()->type === 'Admin';
    $showOrderDetails = true; // Asegurar que se muestre el panel
@endphp

<!-- Barra de búsqueda -->
<div class="flex justify-between items-center mb-6 mt-0">
    <div class="flex items-center w-full">
        <input id="menu-search" class="border rounded-lg bg-gray-200 py-2 pl-2 pr-4 w-full md:w-64 text-gray-700 focus:outline-none focus:bg-white focus:shadow-md" 
               placeholder="Buscar menú ..." type="text"
               oninput="searchMenuItems(this.value)"/>
        <button onclick="clearSearch()" class="ml-2 text-[#6380a6] hover:text-[#203363] hidden" id="clear-search-btn">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Línea de Órdenes - Sección desplegable -->
<!-- <div class="mb-4">
    <div class="flex justify-between items-center">
        <h2 class="section-title text-xl font-bold text-[#203363]">Lista de Órdenes</h2>
        <button onclick="toggleOrdersSection()" class="toggle-btn flex items-center space-x-1 text-[#6380a6] hover:text-[#203363] transition-colors group">
            <span class="text-sm font-medium">Mostrar/Ocultar</span>
            <i id="orders-arrow" class="fas fa-chevron-down text-xs transition-all duration-200 group-hover:text-[#203363]"></i>
        </button>
    </div>

    <div id="orders-container" class="hidden transition-all duration-300 ease-in-out">
        <div class="order-filters flex flex-wrap gap-3 my-4">
            <button onclick="filterOrders('all')" class="filter-btn filter-all px-4 py-2 rounded-lg bg-white text-[#203363] font-medium hover:bg-[#203363] hover:text-white transition-colors flex items-center">
                <span class="w-6 h-6 rounded-full bg-[#203363] flex items-center justify-center text-white text-xs mr-2">{{ $counts['all'] }}</span>
                Todos
            </button>
            <button onclick="filterOrders('Comer aquí')" class="filter-btn filter-dine-in px-4 py-2 rounded-lg bg-white text-[#203363] font-medium hover:bg-[#203363] hover:text-white transition-colors flex items-center">
                <span class="w-6 h-6 rounded-full bg-[#FFD166] flex items-center justify-center text-[#203363] text-xs mr-2">{{ $counts['dine_in'] }}</span>
                Comer Aquí
            </button>
            <button onclick="filterOrders('Para llevar')" class="filter-btn filter-take-away px-4 py-2 rounded-lg bg-white text-[#203363] font-medium border hover:bg-[#203363] hover:text-white transition-colors flex items-center">
                <span class="w-6 h-6 rounded-full bg-[#06D6A0] flex items-center justify-center text-white text-xs mr-2">{{ $counts['take_away'] }}</span>
                Para llevar
            </button>
            <button onclick="filterOrders('Recoger')" class="filter-btn filter-pickup px-4 py-2 rounded-lg bg-white text-[#203363] font-medium border hover:bg-[#203363] hover:text-white transition-colors flex items-center">
                <span class="w-6 h-6 rounded-full bg-[#118AB2] flex items-center justify-center text-white text-xs mr-2">{{ $counts['pickup'] }}</span>
                Recoger
            </button>
            <button onclick="filterOrders('proforma')" class="filter-btn filter-proforma px-4 py-2 rounded-lg bg-white text-[#203363] font-medium border hover:bg-[#203363] hover:text-white transition-colors flex items-center">
                <span class="w-6 h-6 rounded-full bg-[#EF476F] flex items-center justify-center text-white text-xs mr-2">{{ $counts['proforma'] }}</span>
                Proforma
            </button>
        </div>

        <div class="orders-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($orders as $order)
                @php
                    $borderClass = '';
                    $orderTypeText = '';
                    $isProforma = $order instanceof \App\Models\Proforma;
                    
                    if ($isProforma) {
                        $borderClass = 'border-[#EF476F] proforma-card';
                        $orderTypeText = 'Proforma';
                    } else {
                        switch($order->order_type) {
                            case 'Comer aquí':
                                $borderClass = 'border-[#FFD166] dine-in-card';
                                $orderTypeText = 'Mesa ' . $order->table_number;
                                break;
                            case 'Para llevar':
                                $borderClass = 'border-[#06D6A0] take-away-card';
                                $orderTypeText = 'Para llevar';
                                break;
                            case 'Recoger':
                                $borderClass = 'border-[#118AB2] pickup-card';
                                $orderTypeText = 'Recoger';
                                break;
                        }
                    }
                    
                    $createdAt = $order->created_at->diffForHumans();
                    $itemsCount = $order->items->count();
                    $totalAmount = number_format($order->total, 2);
                    
                    $statusBadge = '';
                    if ($isProforma) {
                        $statusBadge = '<span class="absolute top-2 right-2 bg-[#EF476F] text-white text-xs px-2 py-1 rounded-full">Reserva</span>';
                    } else {
                        $statusBadge = '<span class="absolute top-2 right-2 bg-[#203363] text-white text-xs px-2 py-1 rounded-full">Venta</span>';
                    }
                @endphp

                <div class="border-l-4 {{ $borderClass }} rounded-lg p-4 bg-white hover:shadow-md transition-shadow cursor-pointer relative" 
                     onclick="openOrderDetails('{{ $isProforma ? 'proforma' : 'order' }}', '{{ $order->id }}')">
                    {!! $statusBadge !!}
                    
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-[#203363]">#{{ $order->transaction_number ?? 'PROF-'.$order->id }}</p>
                            <p class="text-sm text-[#6380a6]">{{ $orderTypeText }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $createdAt }}</span>
                    </div>
                    
                    <div class="my-2">
                        @if($order->notes)
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                <i class="fas fa-sticky-note mr-1"></i> {{ $order->notes }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-sm font-medium text-[#203363]">
                            <i class="fas fa-list-ul mr-1"></i> {{ $itemsCount }} ítems
                        </p>
                        @if($isAdmin)
                        <p class="text-sm font-medium text-[#203363]">
                            <i class="fas fa-dollar-sign mr-1"></i> {{ $totalAmount }}
                        </p>
                        @endif
                    </div>
                    
                    @if($isProforma)
                        <div class="mt-3 flex justify-end">
                            @if(!$order->order)
                                <button onclick="event.stopPropagation(); convertProformaToOrder('{{ $order->id }}')" 
                                        class="bg-[#203363] text-white px-3 py-1 rounded text-xs hover:bg-[#47517c] transition-colors">
                                    Convertir a Orden
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div> -->
<div class="mb-8"></div> 
<div class="categories-container mb-6">
    <h2 class="section-title text-xl font-bold mb-4 text-[#203363]">Categorías</h2>

    <div class="block mobile-categories md:hidden">
        <select id="category-dropdown" onchange="filterItems(this.value)" class="w-full p-2 border rounded-lg bg-[#a4b6ce] text-[#203363] focus:outline-none">
            <option value="all">Todos</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="category-filters desktop-categories hidden md:grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <button onclick="filterItems('all')" class="category-btn flex flex-col items-center p-4 border rounded-lg bg-[#a4b6ce] hover:bg-[#203363] hover:text-white transition-colors">
            <i class="fas fa-th-list text-2xl mb-2"></i>
            <span>Todos</span>
        </button>
        @foreach ($categories as $category)
            <button onclick="filterItems('{{ $category->id }}')" class="flex flex-col items-center p-4 border rounded-lg bg-[#a4b6ce] hover:bg-[#203363] hover:text-white transition-colors">
                <i class="{{ $category->icon }} text-2xl mb-2"></i>
                <span>{{ $category->name }}</span>
            </button>
        @endforeach
    </div>
</div>

<div class="overflow-y-auto scrollbar-hidden" id="menu-container" style="max-height: calc(100vh - 300px);">
    <h2 class="section-title text-xl font-bold mb-4 text-[#203363]">Menú especial para ti</h2>
    <div id="menu-items">
        @foreach ($categories as $category)
    @if($category->menuItems->count() > 0)
        <div id="category-{{ $category->id }}" class="mb-8" style="display: none;">
            <h3 class="text-lg font-bold text-[#203363] mb-4">{{ $category->name }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($category->menuItems as $item)
                    <div class="border rounded-lg p-4 flex flex-col items-center hover:shadow-lg transition-shadow relative"
                         data-item-id="{{ $item->id }}"
                         data-stock-type="{{ $item->stock_type }}"
                         data-stock="{{ $item->stock }}"
                         data-stock-unit="{{ $item->stock_unit }}"
                         data-min-stock="{{ $item->min_stock }}">
                         
                        <!-- Label de stock en esquina superior izquierda -->
                        <div class="absolute top-2 left-2">
    @if($item->manage_inventory)
        <span class="stock-badge px-2 py-1 rounded text-xs font-medium 
            @if($item->stock <= 0) bg-gray-500 text-white
            @elseif($item->stock < $item->min_stock) bg-yellow-500 text-white
            @else bg-green-500 text-white @endif">
            @if($item->stock_type == 'discrete')
                {{ (int)$item->stock }} UNI
            @else
                {{ (int)$item->stock }} {{ strtoupper($item->stock_unit) }}
            @endif
        </span>
    @endif
</div>

                        <!-- Imagen responsiva -->
                        <img alt="{{ $item->name }}" 
     class="mb-4 w-full h-48 sm:h-56 md:h-40 lg:h-48 object-cover rounded-lg cursor-pointer mt-2" 
     src="{{ $item->image && filter_var($item->image, FILTER_VALIDATE_URL) 
            ? $item->image 
            : asset('storage/' . $item->image) }}" 
     onerror="this.src='{{ asset('images/placeholder.png') }}'"
     onclick="openItemModal({{ json_encode($item) }})"/>

                        <!-- Nombre del ítem -->
                        <p class="text-md font-semibold text-[#203363] mb-2 text-center">{{ $item->name }}</p>

                        <!-- Contenedor para precio y botón -->
                        <div class="w-full flex flex-col items-center mt-auto">
                            <!-- Precio del ítem -->
                            <p class="text-lg font-bold text-[#203363] mb-2">
                                ${{ number_format($item->price, 2) }}
                            </p>

                           <button type="button" 
        onclick="addToOrder({{ json_encode([
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'stock' => $item->stock,
            'stock_type' => $item->stock_type,
            'stock_unit' => $item->stock_unit,
            'min_stock' => $item->min_stock,
            'manage_inventory' => $item->manage_inventory
        ]) }}, event)" 
        class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c] transition-colors text-sm sm:text-base w-full max-w-[150px]
               @if($item->manage_inventory && $item->stock <= 0) opacity-50 cursor-not-allowed @endif"
        @if($item->manage_inventory && $item->stock <= 0) disabled @endif>
    Agregar
</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
    </div>
</div>

<!-- Modal para detalles del ítem -->
<div id="item-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 invisible transition-opacity duration-300">
    <div id="item-modal-content" class="bg-white rounded-lg p-6 transform -translate-y-10 transition-transform duration-300 relative">
        <!-- Botón para cerrar el modal -->
        <button onclick="closeItemModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-lg">
            <i class="fas fa-times"></i>
        </button>

        <!-- Contenido del modal -->
        <div id="item-modal-content-detail" class="mt-4">
            <!-- Aquí se cargarán los detalles del ítem dinámicamente -->
        </div>
    </div>
</div>
    <style>
button[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #cccccc !important;
}        
        /* Estilos para la barra de búsqueda */
.search-highlight {
    background-color: #FFD166;
    padding: 0 2px;
    border-radius: 2px;
}

.search-no-results {
    text-align: center;
    padding: 20px;
    color: #6380a6;
    font-style: italic;
}

/* Animación para los resultados de búsqueda */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.search-result-item {
    animation: fadeIn 0.3s ease-out;
}
    /* Estilos minimalistas para el botón de toggle */
.toggle-btn {
    background: none;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.toggle-btn:hover {
    background-color: rgba(99, 128, 166, 0.1);
}

.toggle-btn:active {
    transform: scale(0.98);
}

/* Efecto sutil para pantallas grandes */
@media (min-width: 768px) {
    .toggle-btn {
        opacity: 0.8;
    }
    
    .toggle-btn:hover {
        opacity: 1;
    }
}
    #orders-container {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }

    #orders-container.show {
        max-height: 2000px; /* Valor suficientemente grande */
        opacity: 1;
        display: block;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }
          /* Colores de fondo para badges */
          .bg-primary {
            background-color: var(--primary-color);
        }

        .bg-yellow {
            background-color: var(--yellow);
            color: var(--primary-color);
        }

        .bg-green {
            background-color: var(--green);
        }

        .bg-blue {
            background-color: var(--blue);
        }

        .bg-red {
            background-color: var(--red);
        }
         /* Filtros */
         .filter-container {
            @apply flex flex-wrap gap-3 mb-6;
        }

        .filter-btn {
            @apply px-4 py-2 rounded-lg bg-white text-[var(--primary-color)] font-medium 
                   border border-[var(--tertiary-color)] hover:bg-[var(--primary-color)] 
                   hover:text-white transition-colors flex items-center;
        }

        .filter-badge {
            @apply w-6 h-6 rounded-full flex items-center justify-center text-white text-xs mr-2;
        }

        .active-filter {
            @apply bg-[var(--primary-color)] text-white;
        }

        /* Tarjetas de orden */
        .orders-grid {
            @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4;
        }

        /* .order-card {
            @apply rounded-lg p-4 bg-white hover:shadow-md transition-shadow cursor-pointer;
            border-left-width: 4px;
        } */
         /* Estilos unificados para las tarjetas de orden */
    .order-card {
        @apply rounded-lg p-4 bg-white hover:shadow-md transition-shadow cursor-pointer;
        border: 1px solid #e2e8f0; /* Borde sutil para todas las tarjetas */
    }
        .order-header {
            @apply flex justify-between items-start mb-2;
        }

        .order-id {
            @apply font-bold text-[var(--primary-color)];
        }

        .order-type {
            @apply text-sm text-[var(--text-light)];
        }

        .order-time {
            @apply text-xs text-gray-500;
        }

        .order-footer {
            @apply flex justify-between items-center mt-3;
        }

        .order-items {
            @apply text-sm font-medium text-[var(--primary-color)];
        }

         /* Filtros de órdenes - Estilo mejorado con fondos */
    .order-filters {
        @apply flex flex-wrap gap-3 mb-6;
    }

    .filter-btn {
        @apply px-4 py-2 rounded-lg font-medium border transition-colors flex items-center;
        min-width: 120px;
        transition: all 0.3s ease;
        border: none;
        color: white;
    }

    /* Badge de contador */
    .filter-badge {
        @apply w-6 h-6 rounded-full flex items-center justify-center text-xs mr-2;
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Estado activo - más oscuro */
    .filter-btn.active {
        filter: brightness(90%);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Colores de fondo para cada filtro */
    .filter-all {
        background-color: #203363; /* Color principal */
    }

    .filter-dine-in {
        background-color: #FF9F1C; /* Amarillo/naranja vibrante */
    }

    .filter-take-away {
        background-color: #2EC4B6; /* Verde azulado fresco */
    }

    .filter-pickup {
        background-color: #118AB2; /* Azul profesional */
    }

    .filter-proforma {
        background-color: #EF476F; /* Rosa/rojo llamativo */
    }

    /* Efecto hover */
    .filter-btn:hover {
        filter: brightness(110%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Efecto al hacer clic */
    .filter-btn:active {
        transform: translateY(0);
    }

        /* Categorías */
        .mobile-categories {
            @apply block md:hidden;
        }

        .categories-dropdown {
            @apply w-full p-2 border rounded-lg bg-white text-[var(--primary-color)] focus:outline-none;
        }

        .desktop-categories {
            @apply hidden md:grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4;
        }

        .category-btn {
            @apply flex flex-col items-center p-4 border rounded-lg bg-white text-[var(--primary-color)] 
                   hover:bg-[var(--primary-color)] hover:text-white transition-colors;
        }

        .category-icon {
            @apply text-2xl mb-2;
        }

        /* Menú */
        .menu-container {
            @apply overflow-y-auto;
            max-height: calc(100vh - 300px);
        }

        .category-section {
            @apply mb-8 hidden;
        }

        .category-name {
            @apply text-lg font-bold text-[var(--primary-color)] mb-4;
        }

        .menu-items-grid {
            @apply grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4;
        }

        .menu-item {
            @apply border rounded-lg p-4 flex flex-col items-center hover:shadow-lg transition-shadow;
        }

        .item-image {
            @apply mb-4 w-full h-48 sm:h-56 md:h-40 lg:h-48 object-cover rounded-lg cursor-pointer;
        }

        .item-price {
            @apply text-lg font-bold text-[var(--primary-color)] mb-2;
        }

        .add-btn {
            @apply bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg 
                   hover:bg-[var(--primary-light)] transition-colors text-sm sm:text-base;
        }
         /* Componentes reutilizables */
         .section-title {
            @apply text-xl font-bold mb-4 text-[var(--primary-color)];
        }
        .search-input {
            @apply border rounded-lg bg-gray-200 py-2 pl-2 pr-4 w-full md:w-64 text-gray-700 
                   focus:outline-none focus:bg-white focus:shadow-md;
        }
     /* Estilos mejorados para los filtros */
     .dine-in-filter {
        background-color: rgba(91, 77, 44, 0.2);
    }
    .take-away-filter {
        background-color: rgba(6, 214, 160, 0.2);
    }
    .pickup-filter {
        background-color: rgba(17, 138, 178, 0.2);
    }
    .proforma-filter {
        background-color: rgba(239, 71, 111, 0.2);
    }

    /* Estado activo para los filtros */
    .active-filter {
        background-color: var(--tertiary-color) !important;
        color: var(--primary-color);
    }

    /* Estilos para las tarjetas de orden */
    .dine-in-card {
        border-left-color: #FFD166;
        background-color: rgba(255, 209, 102, 0.05);
    }
    .take-away-card {
        border-left-color: #06D6A0;
        background-color: rgba(6, 214, 160, 0.05);
    }
    .pickup-card {
        border-left-color: #118AB2;
        background-color: rgba(17, 138, 178, 0.05);
    }
    .proforma-card {
        border-left-color: #EF476F;
        background-color: rgba(239, 71, 111, 0.05);
    }

    /* Efecto hover mejorado */
    [class$="-card"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
 
    
          /* Estilos personalizados */
          :root {
            --primary-color: #203363;
            --primary-light: #47517c;
            --secondary-color: #6380a6;
            --tertiary-color: #a4b6ce;
            --background-color: #fafafa;
            --text-color: #203363;
            --text-light: #6380a6;
            --text-gray: #7c7b90;
            --white: #ffffff;
            --gray-light: #e2e8f0;
            --yellow: #FFD166;
            --green: #06D6A0;
            --blue: #118AB2;
            --red: #EF476F;
        }
          /* Estilos base */
          body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            background-color: var(--background-color);
        }

        /* Estilos unificados para botones */
        button {
            transition: all 0.3s ease;
        }
        /* Estilos para el sistema de colores de tipos de orden */
        .dine-in-filter {
            border-color: #FFD166;
            background-color: #fff8e6;
        }
        .take-away-filter {
            border-color: #06D6A0;
            background-color: #e6f9f3;
        }
        .pickup-filter {
            border-color: #118AB2;
            background-color: #e6f4fb;
        }
        .proforma-filter {
            border-color: #EF476F;
            background-color: #fdebee;
        }

        /* Estado activo para los filtros */
        .active-filter {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
         /* Estilos para las tarjetas de orden */
         [class$="-card"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Estilos para ocultar la barra de desplazamiento */
        .scrollbar-hidden::-webkit-scrollbar {
            width: 0.5em;
            background-color: transparent;
        }

        /* Estilos para las tarjetas de orden */
        .dine-in-card {
            border-left-color: #FFD166;
        }
        .take-away-card {
            border-left-color: #06D6A0;
        }
        .pickup-card {
            border-left-color: #118AB2;
        }
        .proforma-card {
            border-left-color: #EF476F;
        }

        /* Estilos para los inputs del modal */
        .modal input[type="text"] {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 2px solid var(--tertiary-color);
            border-radius: 6px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        .modal input[type="text"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 6px rgba(32, 51, 99, 0.2);
        }

        /* Estilos para los labels */
        .modal label {
            font-size: 13px;
            color: var(--table-data-color);
            margin-bottom: 6px;
            display: block;
        }

        /* Estilos para el total de ventas en efectivo */
        .total-efectivo {
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Diseño de dos columnas para el modal */
        .modal-grid {
            display: grid;
            grid-template-columns: 60% 40%;
            gap: 20px;
        }

        /* Estilos para el contenedor de los inputs */
        .input-container {
            width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        /* Alinear horizontalmente los inputs y labels */
        .input-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        /* Estilos para los grupos de inputs */
        .input-group {
            flex: 1;
        }

        /* Alinear verticalmente el texto de las celdas de la tabla */
        table td {
            vertical-align: top;
        }
        
        .input-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .input-label {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 6px;
            width: 100%;
            text-align: start;
        }

        .modal-input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 2px solid #cbd5e0;
            border-radius: 6px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        .modal-input:focus {
            border-color: #4299e1;
            box-shadow: 0 0 6px rgba(66, 153, 225, 0.2);
        }
    

       
        /* Estilos para ocultar la barra de desplazamiento */
        .scrollbar-hidden::-webkit-scrollbar {
            width: 0.5em;
            background-color: transparent;
        }

        .scrollbar-hidden::-webkit-scrollbar-thumb {
            background-color: transparent;
        }

        .scrollbar-hidden:hover::-webkit-scrollbar-thumb {
            background-color: #203363;
        }

        .scrollbar-hidden {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        .scrollbar-hidden:hover {
            scrollbar-color: #203363 transparent;
        }

        /* Estilos para el modal con efecto de slide */
        #item-modal {
            z-index: 1000; /* Asegurar que esté por encima de otros elementos */
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        #item-modal.show {
            opacity: 1;
            visibility: visible;
        }

        #item-modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-50px);
            transition: transform 0.3s ease;
        }

        #item-modal.show #item-modal-content {
            transform: translateY(0);
        }

        /* Estilos para el botón de cerrar */
        #item-modal-content button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: #6b7280; /* Color gris */
            z-index: 1001; /* Asegurar que esté por encima de otros elementos */
        }

        #item-modal-content button:hover {
            color: #374151; /* Color gris más oscuro al hacer hover */
        }
        /* En la sección de estilos existente */

.stock-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-weight: 500;
    display: inline-block;
    transition: all 0.3s ease;
}

.stock-high {
    background-color: #10B981;
    color: white;
}

.stock-medium {
    background-color: #F59E0B;
    color: white;
}

.stock-low {
    background-color: #EF4444;
    color: white;
}

.stock-out {
    background-color: #6B7280;
    color: white;
    text-decoration: line-through;
}

/* Estilo para botones deshabilitados por falta de stock */
button[disabled].opacity-50 {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Animación para cambios de stock */
@keyframes stockUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.stock-updated {
    animation: stockUpdate 0.5s ease;
}

/* Animación para cambios de stock */
.animate-pulse {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Estilo para botones deshabilitados por falta de stock */
button[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #cccccc !important;
}

/* Efecto hover para los badges de stock */
.stock-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
/* Animación para el badge de stock */
.animate-pulse {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
    </style>

@push('scripts')
<script>

// Función mejorada para filtrar órdenes
    function filterOrders(type) {
        const isAdmin = {{ auth()->user()->type === 'Admin' ? 'true' : 'false' }};
        document.querySelectorAll('.order-filters button').forEach(btn => {
            btn.classList.remove('bg-[#203363]', 'text-white');
            btn.classList.add('bg-white', 'text-[#203363]');
        });
    
        document.querySelectorAll('.orders-grid > div .price-display').forEach(priceElement => {
            priceElement.style.display = isAdmin ? 'block' : 'none';
        });
    // Agregar clase active al botón seleccionado
    const activeButton = document.querySelector(`.order-filters button[onclick*="filterOrders('${type}')"]`);
    if (activeButton) {
        activeButton.classList.remove('bg-white', 'text-[#203363]');
        activeButton.classList.add('bg-[#203363]', 'text-white');
    }

    // Mostrar/ocultar órdenes según el filtro
    document.querySelectorAll('.orders-grid > div').forEach(card => {
        // Obtener el tipo de orden de la tarjeta
        const cardType = card.classList.contains('dine-in-card') ? 'Comer aquí' :
                         card.classList.contains('take-away-card') ? 'Para llevar' :
                         card.classList.contains('pickup-card') ? 'Recoger' :
                         card.classList.contains('proforma-card') ? 'proforma' : '';

        if (type === 'all') {
            card.style.display = 'block';
        } else {
            card.style.display = cardType.toLowerCase() === type.toLowerCase() ? 'block' : 'none';
        }
    });

    // Actualizar la URL para mantener el estado del filtro
    updateUrlParams({ filter: type });
}

// Función para actualizar parámetros de URL sin recargar la página
function updateUrlParams(params) {
    const url = new URL(window.location);
    Object.keys(params).forEach(key => {
        if (params[key] === null || params[key] === undefined) {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, params[key]);
        }
    });
    window.history.pushState({}, '', url);
}

// Función para aplicar filtro al cargar la página
function applyInitialFilter() {
    const urlParams = new URLSearchParams(window.location.search);
    const initialFilter = urlParams.get('filter') || 'all';
    filterOrders(initialFilter);
}

// Aplicar filtro inicial al cargar la página
document.addEventListener('DOMContentLoaded', applyInitialFilter);

// Función para abrir detalles de la orden
function openOrderDetails(type, id) {
    if (type === 'proforma') {
        window.location.href = `/proformas/${id}`;
    } else {
        window.location.href = `/orders/${id}`;
    }
}

// Función para convertir proforma a orden
function convertProformaToOrder(proformaId) {
    if (confirm('¿Estás seguro de convertir esta proforma en una orden?')) {
        fetch(`/proformas/${proformaId}/convert`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proforma convertida a orden exitosamente');
                window.location.reload();
            } else {
                alert('Error al convertir la proforma: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
        });
    }
}



        // Mostrar todas las órdenes al cargar
document.addEventListener('DOMContentLoaded', () => {
            filterOrders('all');
        });

        // Función para abrir el modal con los detalles del ítem
        function openItemModal(item) {
            const modal = document.getElementById('item-modal');
            const modalContentDetail = document.getElementById('item-modal-content-detail');

            // Convertir item.price a número si es una cadena
            const price = parseFloat(item.price);

            // Crear el contenido del modal dentro del div 'item-modal-content-detail'
            modalContentDetail.innerHTML = `
                <div class="flex flex-col items-center">
                    <!-- Imagen del ítem -->
                    <img src="${item.image}" alt="${item.name}" class="w-full h-48 object-cover rounded-lg mb-4">

                    <!-- Nombre del ítem -->
                    <h3 class="text-xl font-bold text-[#203363] mb-2">${item.name}</h3>

                    <!-- Descripción del ítem -->
                    <p class="text-sm text-[#6380a6] mb-4">${item.description}</p>

                    <!-- Precio del ítem -->
                    <p class="text-lg font-bold text-[#203363] mb-4">$${price.toFixed(2)}</p>
                </div>
            `;

            // Mostrar el modal con efecto de slide
            modal.classList.add('show');
        }

        // Función para cerrar el modal
        function closeItemModal() {
            const modal = document.getElementById('item-modal');
            const modalContent = document.getElementById('item-modal-content');

            // Ocultar el modal con efecto de slide
            modal.classList.remove('show');
            modalContent.classList.remove('show');
        }
    // Función para filtrar ítems por categoría (modificada)
function filterItems(categoryId) {
    // Ocultar todos los ítems
    document.querySelectorAll('#menu-items > div').forEach(div => {
        div.style.display = 'none';
    });

    // Quitar la selección de todas las categorías
    document.querySelectorAll('.category-filters button').forEach(button => {
        button.classList.remove('bg-[#203363]', 'text-white');
        button.classList.add('bg-[#a4b6ce]', 'text-black');
    });

    if (categoryId === 'all') {
        // Mostrar todos los ítems
        document.querySelectorAll('#menu-items > div').forEach(div => {
            div.style.display = 'block';
        });
        
        // Resaltar el botón "Todos"
        const allButton = document.querySelector('.category-filters button[onclick="filterItems(\'all\')"]');
        if (allButton) {
            allButton.classList.remove('bg-[#a4b6ce]', 'text-black');
            allButton.classList.add('bg-[#203363]', 'text-white');
        }
    } else {
        // Mostrar solo los ítems de la categoría seleccionada
        const categoryDiv = document.getElementById(`category-${categoryId}`);
        if (categoryDiv) {
            categoryDiv.style.display = 'block';
        }

        // Resaltar la categoría seleccionada
        const selectedCategoryButton = document.querySelector(`.category-filters button[onclick="filterItems('${categoryId}')"]`);
        if (selectedCategoryButton) {
            selectedCategoryButton.classList.remove('bg-[#a4b6ce]', 'text-black');
            selectedCategoryButton.classList.add('bg-[#203363]', 'text-white');
        }
    }

    // Actualizar el dropdown en móviles
    const dropdown = document.getElementById('category-dropdown');
    if (dropdown) {
        dropdown.value = categoryId;
    }
}
       // Mostrar todas las órdenes y todos los ítems al cargar
document.addEventListener('DOMContentLoaded', () => {
    
    filterOrders('all');
    filterItems('all');
    const isAdmin = {{ auth()->user()->type === 'Admin' ? 'true' : 'false' }};
    if (!isAdmin) {
        document.querySelectorAll('.price-display').forEach(el => {
            el.style.display = 'none';
        });
    }
});
        // Función para agregar ítems al pedido
function addToOrder(item, event) {
    // PREVENIR COMPORTAMIENTO POR DEFECTO Y REFRESCO
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
    }
    
    console.log('➕ AGREGANDO ITEM:', item);
    
    // Verificar stock
    if (item.manage_inventory) {
        const menuItem = document.querySelector(`[data-item-id="${item.id}"]`);
        if (!menuItem) return false;
        
        const currentStock = parseInt(menuItem.dataset.stock) || 0;
        if (currentStock <= 0) {
            alert('No hay suficiente stock disponible');
            return false;
        }
    }

    let order = JSON.parse(localStorage.getItem('order')) || [];
   
    // Buscar si el ítem ya existe
    const existingItem = order.find(i => i.id === item.id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        item.quantity = 1;
        order.push(item);
    }

    // Guardar en localStorage
    localStorage.setItem('order', JSON.stringify(order));
    console.log('💾 Order guardado:', order);

    // MÚLTIPLES ESTRATEGIAS para llamar updateOrderDetails
    let updateCalled = false;
    
    // Estrategia 1: Función directa
    if (typeof updateOrderDetails === 'function') {
        console.log('✅ Estrategia 1 - Llamando updateOrderDetails directamente');
        updateOrderDetails();
        updateCalled = true;
    }
    
    // Estrategia 2: Window function
    if (!updateCalled && typeof window.updateOrderDetails === 'function') {
        console.log('✅ Estrategia 2 - Llamando window.updateOrderDetails');
        window.updateOrderDetails();
        updateCalled = true;
    }
    
    // Estrategia 3: Timeout para esperar carga
    if (!updateCalled) {
        console.log('🕒 Estrategia 3 - Esperando carga de función...');
        setTimeout(() => {
            if (typeof window.updateOrderDetails === 'function') {
                console.log('✅ Función disponible después de timeout');
                window.updateOrderDetails();
            } else {
                console.error('❌ updateOrderDetails nunca estuvo disponible');
                // Último recurso: mostrar datos en consola
                console.log('📊 Estado actual del pedido:', order);
            }
        }, 500);
    }
    
    // IMPORTANTE: Retornar false para prevenir comportamiento por defecto
    return false;
}
function goBack() {
        // Aquí puedes implementar la lógica para volver atrás
        // Por ejemplo, recargar la página del menú:
    window.location.href = "{{ route('menu.index') }}";
        
        // O si estás usando un sistema de vistas dinámicas:
        // loadMenuView();
}
function toggleOrdersSection() {
        const container = document.getElementById('orders-container');
        const arrow = document.getElementById('orders-arrow');
        
        container.classList.toggle('show');
        arrow.classList.toggle('rotate-180');
    }

    // Asegurarse de que esté oculto al cargar la página
// Inicialización específica del menú
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando página del menú...');
    
    // Inicializar búsqueda
    if (typeof initializeSearch === 'function') {
        initializeSearch();
    }
    
    // Aplicar filtros iniciales
    filterOrders('all');
    filterItems('all');
    
    // Ocultar precios si no es admin
    const isAdmin = {{ auth()->user()->type === 'Admin' ? 'true' : 'false' }};
    if (!isAdmin) {
        document.querySelectorAll('.price-display').forEach(el => {
            el.style.display = 'none';
        });
    }
    
    // Verificar que el sistema de pedidos esté listo
    setTimeout(() => {
        if (typeof updateOrderDetails === 'function') {
            updateOrderDetails(); // Actualizar vista inicial
        }
    }, 500);
});
    // Variable para almacenar todos los ítems del menú
let allMenuItems = [];

// Función para inicializar la búsqueda
function initializeSearch() {
    allMenuItems = []; // Resetear el array
    
    // Recopilar todos los ítems del menú correctamente
    document.querySelectorAll('#menu-items .grid > div').forEach(item => {
        const nameElement = item.querySelector('p.text-md.font-semibold');
        const priceElement = item.querySelector('p.text-lg.font-bold');
        
        if (nameElement && priceElement) {
            const itemData = {
                element: item,
                name: nameElement.textContent.toLowerCase(),
                price: parseFloat(priceElement.textContent.replace('$', '')),
                category: item.closest('[id^="category-"]').id.replace('category-', ''),
                categoryName: item.closest('[id^="category-"]').querySelector('h3').textContent
            };
            allMenuItems.push(itemData);
        }
    });
}

// Función principal de búsqueda mejorada
function searchMenuItems(searchTerm) {
    const searchValue = searchTerm.toLowerCase().trim();
    const clearBtn = document.getElementById('clear-search-btn');
    
    // Mostrar/ocultar botón de limpiar
    if (searchValue.length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
        resetSearch();
        return;
    }

    // Ocultar todas las categorías primero
    document.querySelectorAll('#menu-items > div').forEach(div => {
        div.style.display = 'none';
    });

    // Filtrar ítems que coincidan con la búsqueda
    const results = allMenuItems.filter(item => 
        item.name.includes(searchValue) || 
        item.categoryName.toLowerCase().includes(searchValue)
    );

    // Mostrar resultados
    displaySearchResults(results, searchValue);
}

// Función para mostrar los resultados de búsqueda mejorada
function displaySearchResults(results, searchTerm) {
    const menuContainer = document.getElementById('menu-items');
    
    // Limpiar resultados anteriores
    const oldResults = document.getElementById('search-results-container');
    if (oldResults) oldResults.remove();

    if (results.length === 0) {
        // Mostrar mensaje de no resultados
        const noResults = document.createElement('div');
        noResults.className = 'search-no-results p-4 text-center';
        noResults.innerHTML = `
            <i class="fas fa-search mb-2 text-2xl text-[#6380a6]"></i>
            <p>No se encontraron resultados para "${searchTerm}"</p>
        `;
        menuContainer.appendChild(noResults);
        return;
    }

    // Crear contenedor para resultados
    const resultsContainer = document.createElement('div');
    resultsContainer.id = 'search-results-container';
    
    // Agrupar resultados por categoría
    const resultsByCategory = {};
    results.forEach(item => {
        if (!resultsByCategory[item.categoryName]) {
            resultsByCategory[item.categoryName] = [];
        }
        resultsByCategory[item.categoryName].push(item);
    });

    // Crear HTML para los resultados
    for (const [categoryName, items] of Object.entries(resultsByCategory)) {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'mb-8 search-result-item';
        
        const categoryTitle = document.createElement('h3');
        categoryTitle.className = 'text-lg font-bold text-[#203363] mb-4';
        categoryTitle.textContent = categoryName;
        categoryDiv.appendChild(categoryTitle);

        const itemsGrid = document.createElement('div');
        itemsGrid.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4';
        
        items.forEach(item => {
            // Clonar el elemento original para no afectar el DOM original
            const clonedItem = item.element.cloneNode(true);
            
            // Resaltar el texto coincidente en el nombre
            const itemName = clonedItem.querySelector('p.text-md.font-semibold');
            if (itemName) {
                const highlightedName = highlightText(itemName.textContent, searchTerm);
                itemName.innerHTML = highlightedName;
            }
            
            itemsGrid.appendChild(clonedItem);
        });
        
        categoryDiv.appendChild(itemsGrid);
        resultsContainer.appendChild(categoryDiv);
    }
    
    menuContainer.appendChild(resultsContainer);
}

// Función para resaltar texto coincidente
function highlightText(text, searchTerm) {
    if (!searchTerm) return text;
    
    const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
    return text.replace(regex, '<span class="search-highlight">$1</span>');
}

// Función para escapar caracteres especiales en regex
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Función para limpiar la búsqueda
function clearSearch() {
    document.getElementById('menu-search').value = '';
    document.getElementById('clear-search-btn').classList.add('hidden');
    resetSearch();
}

// Función para resetear la búsqueda
function resetSearch() {
    const menuContainer = document.getElementById('menu-items');
    const searchResults = document.getElementById('search-results-container');
    if (searchResults) searchResults.remove();
    
    // Mostrar todas las categorías nuevamente
    document.querySelectorAll('#menu-items > div').forEach(div => {
        div.style.display = 'none';
    });
    
    // Mostrar la categoría "Todos" por defecto
    const allCategory = document.querySelector('#menu-items > div:first-child');
    if (allCategory) allCategory.style.display = 'block';
}
function updateStockBadge(itemId, newStock, minStock, stockType, stockUnit, manageInventory = true) {
    if (!manageInventory) return;
    
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (!itemElement) return;

    const stockBadge = itemElement.querySelector('.stock-badge');
    const addButton = itemElement.querySelector('button');
    if (!stockBadge || !addButton) return;

    // Actualizar el valor del stock en el atributo data
    itemElement.dataset.stock = newStock;

    // Actualizar el texto del badge
    if (stockType === 'discrete') {
        stockBadge.textContent = `${newStock} UNI`;
    } else {
        stockBadge.textContent = `${newStock} ${stockUnit.toUpperCase()}`;
    }

    // Actualizar clases según el nivel de stock
    stockBadge.classList.remove('bg-gray-500', 'bg-yellow-500', 'bg-green-500', 'text-white');
    
    if (newStock <= 0) {
        stockBadge.classList.add('bg-gray-500', 'text-white');
        stockBadge.textContent = 'SIN STOCK';
        addButton.disabled = true;
        addButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else if (newStock < minStock) {
        stockBadge.classList.add('bg-yellow-500', 'text-white');
        addButton.disabled = false;
        addButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        stockBadge.classList.add('bg-green-500', 'text-white');
        addButton.disabled = false;
        addButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // Animación de feedback visual
    stockBadge.classList.add('animate-pulse');
    setTimeout(() => {
        stockBadge.classList.remove('animate-pulse');
    }, 500);
}

// Inicializar la búsqueda al cargar la página
function updateStockBadge(itemId, newStock, minStock, stockType, stockUnit, manageInventory = true) {
    if (!manageInventory) return;
    
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (!itemElement) return;

    const stockBadge = itemElement.querySelector('.stock-badge');
    const addButton = itemElement.querySelector('button');
    if (!stockBadge || !addButton) return;

    // Actualizar el valor del stock en el atributo data
    itemElement.dataset.stock = newStock;

    // Actualizar el texto del badge
    if (stockType === 'discrete') {
        stockBadge.textContent = `${newStock} UNI`;
    } else {
        stockBadge.textContent = `${newStock} ${stockUnit.toUpperCase()}`;
    }

    // Actualizar clases según el nivel de stock
    stockBadge.classList.remove('bg-gray-500', 'bg-yellow-500', 'bg-green-500', 'text-white');
    
    if (newStock <= 0) {
        stockBadge.classList.add('bg-gray-500', 'text-white');
        stockBadge.textContent = 'SIN STOCK';
        addButton.disabled = true;
        addButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else if (newStock < minStock) {
        stockBadge.classList.add('bg-yellow-500', 'text-white');
        addButton.disabled = false;
        addButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        stockBadge.classList.add('bg-green-500', 'text-white');
        addButton.disabled = false;
        addButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // Animación de feedback visual
    stockBadge.classList.add('animate-pulse');
    setTimeout(() => {
        stockBadge.classList.remove('animate-pulse');
    }, 500);
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando sistema de menú...');
    
    // Inicializar búsqueda
    initializeSearch();
    
    // Aplicar filtros iniciales
    filterOrders('all');
    filterItems('all');
    
    // Ocultar precios si no es admin
    const isAdmin = {{ auth()->user()->type === 'Admin' ? 'true' : 'false' }};
    if (!isAdmin) {
        document.querySelectorAll('.price-display').forEach(el => {
            el.style.display = 'none';
        });
    }
    
    // Verificar si el sistema de pedidos está inicializado
    if (typeof initializeOrderSystem === 'function') {
        initializeOrderSystem();
    } else {
        console.warn('⚠️ El sistema de pedidos no está completamente cargado');
        // Inicializar localStorage básico para el pedido
        if (!localStorage.getItem('order')) {
            localStorage.setItem('order', JSON.stringify([]));
        }
    }
    
    // Configurar tecla Escape para búsqueda
    const searchInput = document.getElementById('menu-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
    }
});
    </script>
   
@endpush
@endsection