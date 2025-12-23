<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Miquna</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Estilos propios -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="/css/components/buttons.css">
    <link rel="stylesheet" href="/css/components/filters.css">
    <link rel="stylesheet" href="/css/components/header.css">
    <link rel="stylesheet" href="/css/components/payment-modal.css">
    <link rel="stylesheet" href="/css/components/sidebar.css">
    <link rel="stylesheet" href="/css/layouts/app.css">
    <link rel="stylesheet" href="/css/layouts/login.css">
    <link rel="stylesheet" href="/css/layouts/order-details.css">
    <link rel="stylesheet" href="/css/utilities/animations.css">
    <link rel="stylesheet" href="/css/utilities/utilities.css">
    <link rel="stylesheet" href="/css/utilities/variables.css">
    <link rel="stylesheet" href="/css/app.css">
    
   <style>
    /* Estilos para botones de acción minimalistas */
.action-btn-minimal {
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}
.action-btn-expenses {
    background: linear-gradient(135deg, #FF9F1C 0%, #FFB84D 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 159, 28, 0.25);
}
.action-btn-expenses:hover {
    background: linear-gradient(135deg, #FFB84D 0%, #FF9F1C 100%);
    box-shadow: 0 4px 16px rgba(255, 159, 28, 0.4);
    transform: translateY(-2px);
}

/* Botón Historial - Azul */
.action-btn-history {
    background: linear-gradient(135deg, #118AB2 0%, #06D6A0 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(17, 138, 178, 0.25);
}

.action-btn-history:hover {
    background: linear-gradient(135deg, #06D6A0 0%, #118AB2 100%);
    box-shadow: 0 4px 16px rgba(17, 138, 178, 0.4);
    transform: translateY(-2px);
}

/* Botón Caja Chica - Rosa/Rojo */
.action-btn-cash {
    background: linear-gradient(135deg, #EF476F 0%, #FF6B9D 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(239, 71, 111, 0.25);
}

.action-btn-cash:hover {
    background: linear-gradient(135deg, #FF6B9D 0%, #EF476F 100%);
    box-shadow: 0 4px 16px rgba(239, 71, 111, 0.4);
    transform: translateY(-2px);
}
@keyframes subtlePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.action-btn-minimal::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.action-btn-minimal:hover::before {
    width: 120%;
    height: 120%;
}
.action-btn-minimal:active {
    transform: scale(0.95) translateY(-2px);
}

/* Animación de entrada para los botones */
@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.action-btn-minimal {
    animation: fadeInScale 0.3s ease-out;
}

.action-btn-minimal:nth-child(1) {
    animation-delay: 0.1s;
}

.action-btn-minimal:nth-child(2) {
    animation-delay: 0.2s;
}

.action-btn-minimal:nth-child(3) {
    animation-delay: 0.3s;
}

.action-btn-minimal::after {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    background: rgba(32, 51, 99, 0.95);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(8px);
}
.action-btn-expenses::after {
    background: rgba(255, 159, 28, 0.95);
}

.action-btn-history::after {
    background: rgba(17, 138, 178, 0.95);
}

.action-btn-cash::after {
    background: rgba(239, 71, 111, 0.95);
}
.action-btn-minimal:hover::after {
    opacity: 1;
    transform: translateX(-50%) scale(1);
    bottom: -38px;
}

/* Responsive: Menú móvil para botones */
@media (max-width: 768px) {
    .action-btn-minimal {
        display: none;
    }
}
/* Animación minimalista para el input de búsqueda */
#menu-search:focus {
    animation: searchFocusMinimal 0.2s ease-out;
    box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.08);
}

@keyframes searchFocusMinimal {
    0% { transform: scale(1); }
    50% { transform: scale(1.01); }
    100% { transform: scale(1); }
}

/* Efecto de resaltado en los resultados de búsqueda */
.search-highlight {
    background-color: #FFD166;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 500;
}

/* Responsive: En móviles, ajustar tamaños */
@media (max-width: 640px) {
    #menu-search {
        font-size: 13px;
        padding: 8px 30px 8px 32px;
    }
    
    #menu-search::placeholder {
        font-size: 12px;
    }
    
    /* Iconos más pequeños en móvil */
    #menu-search + div i,
    #clear-search-btn i {
        font-size: 12px;
    }
    
    @media (max-width: 380px) {
        #user-menu-button span {
            display: none;
        }
    }
}

/* Estado cuando hay texto en el input */
#menu-search:not(:placeholder-shown) {
    border-color: #203363;
    background-color: white;
    font-weight: 500;
}

/* Efecto hover minimalista */
#menu-search:hover:not(:focus) {
    background-color: rgba(32, 51, 99, 0.02);
}

/* Efecto hover en el botón de limpiar */
#clear-search-btn:hover {
    transform: translateY(-50%) scale(1.2) rotate(90deg);
}

/* Transición suave para mostrar/ocultar el botón de limpiar */
#clear-search-btn {
    transition: all 0.25s ease;
}

#clear-search-btn.hidden {
    opacity: 0;
    pointer-events: none;
    transform: translateY(-50%) scale(0.8);
}

/* Placeholder minimalista */
#menu-search::placeholder {
    font-weight: 300;
    letter-spacing: 0.3px;
}

/* Animación sutil al escribir */
@keyframes subtlePulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.7; }
}

#menu-search:focus + div i {
    animation: subtlePulse 2s ease-in-out infinite;
    color: #203363;
}

/* Sombra sutil al hacer hover */
#menu-search {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

#menu-search:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

/* Transición suave del fondo */
#menu-search {
    backdrop-filter: blur(8px);
}
/* Animación de entrada para el search bar */
@keyframes slideInSearch {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.search-container-animated {
    animation: slideInSearch 0.3s ease-out;
}
/* Tooltip mejorado */
.action-btn-minimal::after {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    background: #203363;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.action-btn-minimal:hover::after {
    opacity: 1;
    transform: translateX(-50%) scale(1);
}

/* Responsive: Menú móvil para botones */
@media (max-width: 768px) {
    .action-btn-minimal {
        display: none;
    }
}
</style>
 {{-- @php
    // ✅ Obtener la última caja chica abierta
    $openPettyCash = \App\Models\PettyCash::where('status', 'open')
        ->where('user_id', auth()->id())
        ->latest()
        ->first();
    
    // Debug (opcional, eliminar en producción)
    \Log::info('Verificando caja abierta:', [
        'user_id' => auth()->id(),
        'tiene_caja_abierta' => !is_null($openPettyCash),
        'petty_cash_id' => $openPettyCash?->id ?? 'ninguna'
    ]);
@endphp --}}
</head>

<body class="bg-[#fafafa]">

    <!-- En la sección del header dentro de app.blade.php -->
    <header class="flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
    <!-- Logo y nombre de la aplicación -->
    <div class="flex items-center justify-between w-64 bg-[#203363] h-16 relative flex-shrink-0">
        <!-- Botón del menú móvil -->
        <button id="menu-toggle" class="text-[#b6e0f6] focus:outline-none absolute left-4 sm:hidden">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <!-- Logo centrado -->
        <div class="flex items-center justify-center w-full">
            <img alt="Logo" class="h-10 w-10" src="https://static.vecteezy.com/system/resources/previews/000/656/554/original/restaurant-badge-and-logo-good-for-print-vector.jpg" />
            <span class="text-xl font-bold text-[#b6e0f6] hidden sm:block ml-2">Miquna</span>
        </div>
    </div>

    <!-- Barra de búsqueda minimalista alineada a la izquierda -->
    @if(isset($showOrderDetails) && $showOrderDetails)
    <div class="flex-1 flex items-center justify-between px-4 search-container-animated">
        <!-- Contenedor de búsqueda -->
        <div class="relative w-full max-w-xs">
            <div class="relative pl-5">
                <input 
                    id="menu-search" 
                    class="w-full border border-gray-200 rounded-full bg-gray-50/50 py-1.5 pl-8 pr-8 text-gray-700 
                           text-sm placeholder-gray-400
                           focus:outline-none focus:border-[#203363] focus:bg-white
                           transition-all duration-200 hover:border-gray-300" 
                    placeholder="Buscar productos..." 
                    type="text"
                    oninput="searchMenuItems(this.value)"
                />
                
                <!-- Ícono de búsqueda minimalista -->
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm pl-4">
                    <i class="fas fa-search"></i>
                </div>

                <!-- Botón para limpiar búsqueda -->
                <button 
                    onclick="clearSearch()" 
                    class="absolute right-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#203363] 
                           transition-colors duration-200 hidden text-xs" 
                    id="clear-search-btn"
                    title="Limpiar"
                >
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </div>
     
        <!-- Botones de Acciones Rápidas - Minimalistas con Color -->
        <div class="hidden md:flex items-center gap-2 ml-4 mr-20 pr-5">
            <!-- Botón Gastos - Naranja -->
            <button 
                onclick="openExpensesModal()" 
                class="action-btn-minimal action-btn-expenses
                       w-10 h-10 rounded-full flex items-center justify-center 
                       transition-all duration-200 hover:shadow-lg group"
                title="Gestión de Gastos"
            >
                <i class="fas fa-receipt text-base transition-transform duration-200 group-hover:scale-110"></i>
            </button>
            
            <!-- Botón Historial - Azul -->
            <a 
                href="{{ route('orders.index') }}" 
                class="action-btn-minimal action-btn-history
                       w-10 h-10 rounded-full flex items-center justify-center 
                       transition-all duration-200 hover:shadow-lg group"
                title="Historial de Órdenes"
            >
                <i class="fas fa-history text-base transition-transform duration-200 group-hover:scale-110"></i>
            </a>
            
            <!-- Botón Caja Chica - Rosa/Rojo -->
           <button 
    onclick="openPettyCashModal()" 
    class="action-btn-minimal action-btn-cash
           w-10 h-10 rounded-full flex items-center justify-center 
           transition-all duration-200 hover:shadow-lg group"
    title="Gestión de Caja Chica"
>
    <i class="fas fa-cash-register text-base transition-transform duration-200 group-hover:scale-110"></i>
</button>
        </div>
    </div>
    @else
    <!-- Espacio vacío cuando no hay búsqueda -->
    <div class="flex-1"></div>
    @endif

    <!-- Área de usuario y notificaciones -->
    <div class="flex items-center space-x-3 pr-4 flex-shrink-0">
        <!-- Botón de notificaciones -->
        <button class="text-gray-600 hover:text-[#203363] relative transition-colors duration-200 hidden sm:block">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
        </button>

        <!-- Menú de usuario -->
        <div class="relative">
            <button 
                id="user-menu-button" 
                class="flex items-center space-x-2 focus:outline-none py-2 px-3 rounded-md 
                       hover:bg-gray-100 transition-colors duration-200"
            >
                <span class="hidden md:inline text-sm font-medium text-gray-700">
                    Hola, {{ Auth::user()->name ?? 'Usuario' }}
                </span>
                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center 
                            overflow-hidden border border-gray-300">
                    <img 
                        src="https://www.gravatar.com/avatar/default?s=200&d=mp" 
                        alt="User Avatar" 
                        class="h-full w-full object-cover"
                    >
                </div>
            </button>

            <!-- Menú desplegable -->
            <div 
                id="user-menu" 
                class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden 
                       border border-gray-200" 
                style="z-index: 1000;"
            >
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button 
                        type="submit" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 
                               hover:bg-gray-50 transition-colors flex items-center"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>



    <div class="flex flex-col md:flex-row">
        <!-- Sidebar (Visible en tablets y pantallas más grandes) -->
        <div class="bg-[#203363] w-64 min-h-screen hidden sm:block shadow-md sidebar">
            <div class="sidebar-divider"></div>
  <!-- Contenido del sidebar -->
            <div class="sidebar-content">                
                <nav class="mt-4 space-y-1">
                    <!-- Dashboard -->
                    <a class="flex items-center text-[#ffffff] bg-[#47517c] p-2 rounded-md" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                    <!-- Ventas (Menú con submenús) -->
                    <div class="relative">
                        <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md cursor-pointer menu-toggle"
                            data-menu="ventas" href="#" onclick="return false;">
                            <i class="fas fa-cube mr-3"></i>
                            <span>Ventas</span>
                            <i class="fas fa-chevron-down ml-auto transition-transform duration-300 arrow"></i>
                        </a>
                        <div class="submenu ml-4 mt-2 hidden" id="ventas-submenu">
                            <a class="flex items-center p-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('menu.index') }}">
                                <i class="fas fa-bars mr-3"></i>
                                <span>Menu</span>
                            </a>
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('orders.index') }}">
                                <i class="fas fa-list mr-3"></i>
                                <span>Lista de Ventas</span>
                            </a>
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('tables.index') }}">
                                <i class="fas fa-table mr-3"></i>
                                <span>Mesas</span>
                            </a>
                        </div>
                    </div>

                    @unless(auth()->user()->role === 'vendedor')
                    <!-- Gastos (Menú con submenús) - Solo visible para no vendedores -->
                    <div class="relative">
                        <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md cursor-pointer menu-toggle" data-menu="gastos">
                            <i class="fas fa-chart-bar mr-3"></i>
                            <span>Compras</span>
                            <i class="fas fa-chevron-down ml-auto transition-transform duration-300 arrow"></i>
                        </a>
                        <div class="submenu ml-4 mt-2 hidden" id="gastos-submenu">
                            <a class="flex items-center p-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('purchases.index') }}">
                                <i class="fas fa-chart-line mr-3"></i>
                                <span>Lista de Compras</span>
                            </a>
                            <a class="flex items-center p-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('purchases.create') }}">
                                <i class="fas fa-chart-line mr-3"></i>
                                <span>Realizar Compra</span>
                            </a>
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('suppliers.index') }}">
                                <i class="fas fa-file-invoice-dollar mr-3"></i>
                                <span>Proveedores</span>
                            </a>
                        </div>
                    </div>
                    @endunless

                    <!-- Proveedor -->
                    <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('expenses.index') }}">
                        <i class="fas fa-table mr-3"></i>
                        <span>Gastos</span>
                    </a>
                    <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('petty-cash.index') }}">
                        <i class="fas fa-cash-register mr-3"></i>
                        <span>Cierre de Caja</span>
                    </a>

                    @unless(auth()->user()->role === 'vendedor')
                    <!-- Configuración - Solo visible para no vendedores -->
                    <div class="relative">
                        <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md cursor-pointer menu-toggle" data-menu="configuracion">
                            <i class="fas fa-chart-bar mr-3"></i>
                            <span>Configuracion</span>
                            <i class="fas fa-chevron-down ml-auto transition-transform duration-300 arrow"></i>
                        </a>
                        <div class="submenu ml-4 mt-2 hidden" id="configuracion-submenu">
                            <!-- Nuevo ítem para Inventario -->
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('inventory.index') }}">
                                <i class="fas fa-boxes mr-3"></i>
                                <span>Inventario</span>
                            </a>

                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('items.index') }}">
                                <i class="fas fa-cube mr-3"></i>
                                <span>Productos</span>
                            </a>
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('categories.index') }}">
                                <i class="fas fa-list mr-3"></i>
                                <span>Categorías</span>
                            </a>
                            <!-- Usuarios -->
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('users.index') }}">
                                <i class="fas fa-edit mr-3"></i>
                                <span>Usuarios</span>
                            </a>
                            <!-- Nuevo submenú para Delivery -->
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('deliveries.index') }}">
                                <i class="fas fa-truck mr-3"></i>
                                <span>Delivery</span>
                            </a>
                        </div>
                    </div>
                    @endunless
                </nav>
            
            </div>
        </div>
        <!-- Overlay para menú móvil -->
        <div id="mobile-overlay" class="mobile-overlay"></div>
        <!-- Main Content -->
        <div class="flex-1 p-6 pb-24 sm:pb-6 main-content" id="main-content"> <!-- Ajustar padding-bottom para móviles -->
            <!-- <header class="flex items-center justify-between">
             
                <div class="flex items-center sm:hidden">
                    <button id="menu-toggle" class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
              
                <div class="flex items-center ml-auto"> 
                    <div class="relative">
                      
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center focus:outline-none">
                                
                                <img alt="User Avatar" class="w-10 h-10 rounded-full mr-2" height="40" src="https://www.gravatar.com/avatar/default?s=200&d=mp" width="40"/>
                               
                                <span class="text-gray-700">Hola, {{ Auth::user()->name }}</span>
                            </button>
                           
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                             
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                               
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header> -->
            <div class="flex-1 p-6 pb-24 sm:pb-6 @if(isset($showOrderDetails) && $showOrderDetails) mr-0 md:mr-[25%] @endif" id="main-content">
                @yield('content')
            </div>
        </div>

        <!-- Order Details -->
        @if(isset($showOrderDetails) && $showOrderDetails)
        @include('layouts.order-details')
        @endif
    </div>

    <!-- Mobile Sidebar -->
    <div id="mobile-menu" class="bg-gray-900 text-white w-64 min-h-screen fixed top-0 left-0 transform -translate-x-full transition-transform duration-300 sm:hidden">
        <div class="flex items-center justify-center h-16 border-b border-gray-800">
            <img alt="Logo" class="mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/wdVhKpjxoPtLv5IwtcNKZTtND5y2hoPfIUEZqQaGIhQ.jpg" width="40" />
            <span class="text-xl font-bold">kaiadmin</span>
        </div>
        <nav class="mt-10">
            <a class="flex items-center py-2 px-8 bg-gray-800 text-gray-200" href="#">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
        </nav>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Variables globales PRIMERO -->
<script>
   window.routes = {
        tablesAvailable: "{{ route('tables.available') }}",
        salesStore: "{{ route('sales.store') }}",
        customerDetails: "{{ route('customer.details') }}",
        menuIndex: "{{ route('menu.index') }}"
    };
    window.csrfToken = "{{ csrf_token() }}";
    window.authUserName = "{{ Auth::user()->name ?? '' }}";
    window.tablesEnabled = @json($settings->tables_enabled ?? false);
    
    console.log('🌍 Variables globales configuradas');
</script>

<script>
    // 🔥 Función principal para abrir el modal de caja chica
    window.openPettyCashModal = async function() {
        console.log('🔓 Abriendo modal de caja chica desde botón header...');

        const modal = document.getElementById('petty-cash-modal');
        const content = document.getElementById('petty-cash-content');

        if (!modal || !content) {
            console.error('❌ Modal de caja chica no encontrado en app.blade.php');
            // Si el modal no existe, redirigir a la página de caja chica
            window.location.href = '{{ route("petty-cash.index") }}';
            return;
        }

        // Mostrar modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Mostrar loading
        content.innerHTML = `
            <div class="flex items-center justify-center p-12">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                    <p class="text-gray-600">Verificando estado de caja chica...</p>
                </div>
            </div>
        `;

        try {
            // Verificar si hay una caja abierta
            const checkResponse = await fetch('/petty-cash/get-open', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const checkData = await checkResponse.json();
            console.log('📦 Estado de caja:', checkData);

            if (checkData.success && checkData.petty_cash_id) {
                // Si hay caja abierta, cargar el modal de cierre
                await loadClosureModal(checkData.petty_cash_id, content);
            } else {
                // Si no hay caja abierta, preguntar si desea crear una
                showCreatePettyCashOption(content);
            }

        } catch (error) {
            console.error('❌ Error al verificar caja chica:', error);
            showErrorContent(content);
        }
    };

    // Función para cargar el modal de cierre con la caja específica
    async function loadClosureModal(pettyCashId, content) {
        console.log('📥 Cargando modal de cierre para caja:', pettyCashId);
        
        content.innerHTML = `
            <div class="flex items-center justify-center p-12">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-green-500 mb-4"></i>
                    <p class="text-gray-600">Cargando datos de cierre...</p>
                </div>
            </div>
        `;

        try {
            // Cargar el contenido del modal de cierre
            const response = await fetch(`/petty-cash/modal-closure/${pettyCashId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            content.innerHTML = html;

            // Inicializar funciones del modal (si están en el HTML cargado)
            if (typeof window.initializeClosureModal === 'function') {
                window.initializeClosureModal(pettyCashId);
            }

            console.log('✅ Modal de cierre cargado correctamente');

        } catch (error) {
            console.error('❌ Error al cargar modal de cierre:', error);
            showErrorContent(content);
        }
    }

    // Función para mostrar opción de crear caja chica
    function showCreatePettyCashOption(content) {
        content.innerHTML = `
            <div class="p-8 text-center">
                <div class="mb-6">
                    <i class="fas fa-info-circle text-6xl text-blue-500 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                        No hay caja chica abierta
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Para realizar un cierre, primero debes tener una caja chica abierta.
                    </p>
                </div>
                
                <div class="flex justify-center gap-4">
                    <button onclick="createNewPettyCash()" 
                            class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 
                                   transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Crear Caja Chica</span>
                    </button>
                    
                    <button onclick="closePettyCashModal()" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 
                                   transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('petty-cash.index') }}" 
                       class="text-blue-500 hover:text-blue-700 underline">
                        <i class="fas fa-list"></i>
                        Ver lista de cajas chicas
                    </a>
                </div>
            </div>
        `;
    }

    // Función para crear una nueva caja chica
    window.createNewPettyCash = async function() {
        const content = document.getElementById('petty-cash-content');
        
        content.innerHTML = `
            <div class="flex items-center justify-center p-12">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-green-500 mb-4"></i>
                    <p class="text-gray-600">Creando nueva caja chica...</p>
                </div>
            </div>
        `;

        try {
            const response = await fetch('/petty-cash', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: new Date().toISOString().split('T')[0],
                    initial_amount: 0
                })
            });

            const data = await response.json();

            if (data.success) {
                console.log('✅ Caja chica creada:', data.petty_cash_id);
                
                // Mostrar mensaje de éxito
                content.innerHTML = `
                    <div class="p-8 text-center">
                        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                            ¡Caja chica creada!
                        </h3>
                        <p class="text-gray-600 mb-6">
                            La caja chica ha sido creada correctamente. Cargando modal de cierre...
                        </p>
                    </div>
                `;
                
                // Esperar 1 segundo y cargar el modal de cierre
                setTimeout(() => {
                    loadClosureModal(data.petty_cash_id, content);
                }, 1000);
                
            } else {
                throw new Error(data.message || 'Error al crear la caja chica');
            }

        } catch (error) {
            console.error('❌ Error al crear caja chica:', error);
            content.innerHTML = `
                <div class="p-8 text-center">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                        Error al crear caja chica
                    </h3>
                    <p class="text-gray-600 mb-6">
                        ${error.message}
                    </p>
                    <button onclick="openPettyCashModal()" 
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 
                                   transition-colors duration-200">
                        <i class="fas fa-redo"></i>
                        Reintentar
                    </button>
                </div>
            `;
        }
    };

    // Función para mostrar error
    function showErrorContent(content) {
        content.innerHTML = `
            <div class="p-8 text-center">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                    Error de conexión
                </h3>
                <p class="text-gray-600 mb-6">
                    No se pudo cargar el contenido. Por favor, intenta de nuevo.
                </p>
                <div class="flex justify-center gap-4">
                    <button onclick="openPettyCashModal()" 
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 
                                   transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-redo"></i>
                        <span>Reintentar</span>
                    </button>
                    
                    <a href="{{ route('petty-cash.index') }}" 
                       class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 
                              transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Ir a Lista de Cajas</span>
                    </a>
                </div>
            </div>
        `;
    }

    // 🔥 Función placeholder para abrir modal de gastos
    window.openExpensesModal = function() {
        alert('Funcionalidad de gastos en desarrollo');
    };

    console.log('✅ Sistema de modal de caja chica configurado desde app.blade.php');
</script>
<!-- ✅ Cargar scripts en orden correcto -->
<script src="{{ asset('js/order-details.js') }}" defer></script>
<script src="{{ asset('js/payment-modal.js') }}" defer></script>
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/init.js') }}" defer></script>
<script src="{{ asset('js/petty-cash-modal.js') }}" defer></script>
 @if(Request::is('petty-cash*'))
        <script src="{{ asset('js/petty-cash-index.js') }}" defer></script>
    @endif
    
<script>
(function() {
    console.log('🚀 Inicializando sistema de logout...');
    
    function initLogout() {
        const logoutForm = document.getElementById('logout-form');
        
        if (!logoutForm) {
            console.warn('⚠️ Formulario de logout no encontrado');
            return;
        }
        
        console.log('✅ Formulario de logout encontrado');
        
        // Prevenir múltiples listeners
        logoutForm.removeEventListener('submit', handleLogoutSubmit);
        logoutForm.addEventListener('submit', handleLogoutSubmit);
    }
    
    function handleLogoutSubmit(e) {
        console.log('🔴 Logout submit detectado');
        
        // Limpiar datos locales
        try {
            localStorage.removeItem('order');
            localStorage.removeItem('orderType');
            console.log('✅ localStorage limpiado');
        } catch (error) {
            console.warn('⚠️ Error al limpiar localStorage:', error);
        }
        
        // Permitir que el formulario se envíe normalmente
        console.log('📤 Enviando formulario de logout...');
        // No hacer e.preventDefault() - dejar que el form se envíe
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLogout);
    } else {
        initLogout();
    }
})();
</script>
<!-- ✅ Inicialización sin recargas -->
<script>
    let initAttempts = 0;
    const MAX_ATTEMPTS = 3;

    function ensureOrderSystemReady() {
        console.log('🔍 Verificando sistema de pedidos... Intento:', initAttempts + 1);
        
        if (typeof window.updateOrderDetails === 'function') {
            console.log('✅ updateOrderDetails está disponible');
            window.updateOrderDetails();
            return true;
        } else {
            initAttempts++;
            
            if (initAttempts < MAX_ATTEMPTS) {
                console.warn('⚠️ updateOrderDetails NO disponible, reintentando...');
                setTimeout(ensureOrderSystemReady, 500);
            } else {
                console.error('❌ Sistema de pedidos no disponible después de', MAX_ATTEMPTS, 'intentos');
                // NO recargar - solo mostrar error
                console.error('Por favor, verifica que order-details.js esté cargado correctamente');
            }
            return false;
        }
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureOrderSystemReady);
    } else {
        ensureOrderSystemReady();
    }
</script>
<div id="petty-cash-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0, 0, 0, 0.5);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
            <!-- Header del Modal -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-cash-register mr-2"></i>
                    Gestión de Caja Chica
                </h2>
                <button onclick="closePettyCashModal()" 
                        class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Contenido del Modal -->
            <div id="petty-cash-content" class="overflow-y-auto" style="max-height: calc(90vh - 70px);">
                <!-- El contenido se cargará aquí dinámicamente -->
                <div class="flex items-center justify-center p-12">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                        <p class="text-gray-600">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stack('scripts')
</body>

</html>