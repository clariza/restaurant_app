<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

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

</head>

<body class="bg-[#fafafa]">

    <!-- En la sección del header dentro de app.blade.php -->
     <header class="flex items-center justify-between bg-white shadow-sm sticky top-0 z-50">
        <!-- Logo y nombre de la aplicación -->
        <div class="flex items-center justify-between w-64 bg-[#203363] h-16 relative">
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

        <!-- Área de usuario -->
        <div class="flex items-center space-x-4">
            <button class="text-gray-600 hover:text-[#203363] relative">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
            </button>

            <!-- Menú de usuario -->
            <div class="relative ml-4 px-4">
                <button id="user-menu-button" class="flex items-center space-x-2 focus:outline-none py-2 px-3 rounded-md hover:bg-gray-100 transition-colors">
                    <span class="hidden md:inline text-sm font-medium text-gray-700">Hola, Usuario</span>
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border border-gray-300">
                        <img src="https://www.gravatar.com/avatar/default?s=200&d=mp" alt="User Avatar" class="h-full w-full object-cover">
                    </div>
                </button>

                <!-- Menú desplegable -->
                <div id="user-menu" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 hidden border border-gray-200" style="z-index: 1000;">
                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </button>
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
    <script src="/js/app.js"></script>
    @stack('scripts')
</body>

</html>