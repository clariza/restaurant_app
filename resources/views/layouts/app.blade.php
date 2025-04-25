<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
     /* Estilos base */
    /* Estructura principal */
 body {
    font-family: 'Inter', sans-serif;
    background-color: #fafafa;
    /* margin: 0;
    padding: 0; */
    /* display: flex; */
    min-height: 100vh;
}

/* Sidebar fijo */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 16rem; /* 64/4 = 16rem */
    background-color: #203363;
    color: white;
    overflow-y: auto;
    z-index: 40;
    transition: transform 0.3s ease;
    transform: translateX(0);
}

/* Contenido principal con margen para el sidebar */
.main-content {
    margin-left: 16rem; /* Igual al ancho del sidebar */
    flex: 1;
    min-height: 100vh;
    position: relative;
} */

/* Header fijo */
.header {
    position: sticky;
    top: 0;
    z-index: 30;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Menú móvil */
#mobile-menu {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 16rem;
    background-color: #203363;
    z-index: 50;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

#mobile-menu.show {
    transform: translateX(0);
}

/* Overlay para menú móvil */
.mobile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 45;
    display: none;
}

.mobile-overlay.show {
    display: block;
}

/* Responsive */
@media (max-width: 767px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
        width: 100%;
    }
}
     body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text-color);
        background-color: var(--background-color);
        line-height: 1.5;
    }
     /* Transiciones suaves para interacciones */
     button, input, select, .card, .menu-item {
        transition: all 0.25s ease;
    }
      /* Efectos hover consistentes */
      button:hover, .card:hover, .menu-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
     /* Estilos para títulos y encabezados */
     .section-title {
        @apply text-xl font-bold mb-4 text-[var(--primary-color)];
        position: relative;
        padding-bottom: 8px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background-color: var(--primary-color);
        border-radius: 3px;
    }
       /* Filtros de órdenes - Estilo mejorado */
    .order-filters {
        @apply flex flex-wrap gap-3 mb-6;
    }

    .filter-btn {
        @apply px-4 py-2 rounded-lg font-medium border transition-colors flex items-center;
        min-width: 120px;
        transition: all 0.3s ease;
    }

    /* Estilo base para todos los botones de filtro */
    .filter-btn {
        background-color: var(--white);
        color: var(--primary-color);
        border-color: var(--tertiary-color);
    }

    .filter-btn:hover {
        @apply shadow-md;
        transform: translateY(-2px);
    }

    /* Badge de contador */
    .filter-badge {
        @apply w-6 h-6 rounded-full flex items-center justify-center text-xs mr-2;
    }

    /* Estado activo */
    .filter-btn.active {
        background-color: var(--primary-color);
        color: var(--white);
        border-color: var(--primary-color);
    }

    /* Colores específicos para cada tipo de filtro */
    .filter-all .filter-badge {
        background-color: var(--primary-color);
        color: var(--white);
    }

    .filter-dine-in .filter-badge {
        background-color: var(--yellow);
        color: var(--primary-color);
    }

    .filter-take-away .filter-badge {
        background-color: var(--green);
        color: var(--white);
    }

    .filter-pickup .filter-badge {
        background-color: var(--blue);
        color: var(--white);
    }

    .filter-proforma .filter-badge {
        background-color: var(--red);
        color: var(--white);
    }

    /* Efecto hover con colores específicos */
    .filter-dine-in:hover {
        background-color: rgba(255, 209, 102, 0.1);
        border-color: var(--yellow);
    }

    .filter-take-away:hover {
        background-color: rgba(6, 214, 160, 0.1);
        border-color: var(--green);
    }

    .filter-pickup:hover {
        background-color: rgba(17, 138, 178, 0.1);
        border-color: var(--blue);
    }

    .filter-proforma:hover {
        background-color: rgba(239, 71, 111, 0.1);
        border-color: var(--red);
    }

    /* Estado activo con colores específicos */
    .filter-dine-in.active {
        background-color: var(--yellow);
        color: var(--primary-color);
    }

    .filter-take-away.active {
        background-color: var(--green);
    }

    .filter-pickup.active {
        background-color: var(--blue);
    }

    .filter-proforma.active {
        background-color: var(--red);
    }
        /* Variables de color consistentes */
    :root {
        --primary-color: #203363;
        --primary-light: #47517c;
        --secondary-color: #6380a6;
        --tertiary-color: #a4b6ce;
        --background-color: #f8fafc;
        --text-color: #203363;
        --text-light: #6380a6;
        --text-gray: #7c7b90;
        --white: #ffffff;
        --gray-light: #e2e8f0;
        --yellow: #FFD166;
        --green: #06D6A0;
        --blue: #118AB2;
        --red: #EF476F;
        --success: #38a169;
        --error: #e53e3e;
        --warning: #dd6b20;
    }
    /* Estilos para los inputs del modal */
    .modal input[type="text"] {
    width: 100%;
    padding: 8px; /* Reducir el padding */
    font-size: 14px; /* Reducir el tamaño de la fuente */
    border: 2px solid var(--tertiary-color); /* Borde más suave */
    border-radius: 6px; /* Bordes redondeados */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Transición suave */
    outline: none; /* Eliminar el outline predeterminado */
    }

/* Estilo cuando el input está enfocado */
    .modal input[type="text"]:focus {
    border-color: var(--primary-color); /* Cambiar el color del borde al enfocar */
    box-shadow: 0 0 6px rgba(32, 51, 99, 0.2); /* Sombra suave al enfocar */
    }

/* Estilos para los labels */
.modal label {
    font-size: 13px; /* Reducir el tamaño de fuente para los labels */
    color: var(--table-data-color); /* Color del texto */
    margin-bottom: 6px; /* Reducir el espaciado inferior */
    display: block; /* Asegurar que estén en una línea separada */
}
 /* Estilos para el total de ventas en efectivo */
 .total-efectivo {
        font-size: 16px;
        font-weight: bold;
        color: var(--primary-color);
    }
    /* Estilos para los labels */
    .modal label {
        font-size: 13px; /* Reducir el tamaño de fuente para los labels */
        color: var(--table-data-color); /* Color del texto */
        margin-bottom: 6px; /* Reducir el espaciado inferior */
        display: block; /* Asegurar que estén en una línea separada */
        text-align: start; /* Alinear texto al inicio */
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
        grid-template-columns: 60% 40%; /* Tabla más grande, inputs más pequeños */
        gap: 20px;
    }

    /* Estilos para el contenedor de los inputs */
    .input-container {
        width: 90%; /* 10% más pequeño que la tabla de denominaciones */
        margin-left: auto; /* Centrar el contenedor */
        margin-right: auto; /* Centrar el contenedor */
    }

    /* Alinear horizontalmente los inputs y labels */
    .input-row {
        display: flex;
        gap: 10px; /* Espaciado entre elementos */
        align-items: flex-start; /* Alinear verticalmente al inicio */
        margin-bottom: 12px; /* Espaciado entre filas */
    }

    /* Estilos para los grupos de inputs */
    .input-group {
        flex: 1; /* Distribuir el espacio equitativamente */
    }

    /* Alinear verticalmente el texto de las celdas de la tabla */
    table td {
        vertical-align: top; /* Alinear contenido al inicio */
    }
    .input-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Alinea los elementos al inicio */
    width: 100%; /* Asegura que el input-group ocupe todo el ancho disponible */
}

.input-label {
    font-size: 14px; /* Tamaño de fuente uniforme */
    color: #4a5568; /* Color de texto uniforme */
    margin-bottom: 6px; /* Espaciado entre el label y el input */
    width: 100%; /* Asegura que todos los labels tengan el mismo ancho */
    text-align: start; /* Alinea el texto al inicio */
}

.modal-input {
    width: 100%; /* Asegura que los inputs ocupen todo el ancho disponible */
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

    </style>
</head>
<body class="bg-[#fafafa]">
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar (Visible en tablets y pantallas más grandes) -->
        <div class="bg-[#203363] w-64 min-h-screen hidden sm:block shadow-md sidebar">
            <div class="p-4">
                <div class="flex items-center justify-center h-16 border-b border-gray-800">
                    <img alt="Skydash logo" class="mr-2" height="40" src="https://static.vecteezy.com/system/resources/previews/000/656/554/original/restaurant-badge-and-logo-good-for-print-vector.jpg" width="40"/>
                    <span class="text-xl font-bold text-[#b6e0f6]">Miquna</span>
                </div>
                <nav class="mt-6 space-y-1">
                    <!-- Dashboard -->
                    <a class="flex items-center text-[#ffffff] bg-[#47517c] p-2 rounded-md" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                    <!-- Ventas (Menú con submenús) -->
                    <div class="relative">
                        <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md cursor-pointer menu-toggle" data-menu="ventas">
                            <i class="fas fa-cube mr-3"></i>
                            <span>Ventas</span>
                            <i class="fas fa-chevron-down ml-auto transition-transform duration-300 arrow"></i>
                        </a>
                        <div class="submenu ml-4 mt-2 hidden" id="ventas-submenu">
                            <a class="flex items-center p-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('menu.index') }}">
                                <i class="fas fa-bars mr-3"></i>
                                <span>Menu</span>
                            </a>
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('sales.index') }}">
                                <i class="fas fa-list mr-3"></i>
                                <span>Lista de Ventas</span>
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
                            <a class="flex items-center p-2 mt-2 text-[#b6e0f6] hover:bg-[#47517c] rounded-md" href="{{ route('tables.index') }}">
                                <i class="fas fa-table mr-3"></i>
                                <span>Mesas</span>
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
            <header class="flex items-center justify-between">
                <!-- Botón del menú móvil a la izquierda -->
                <div class="flex items-center sm:hidden">
                    <button id="menu-toggle" class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
                <!-- User Avatar e iconos de notificaciones en la esquina superior derecha -->
                <div class="flex items-center ml-auto"> <!-- ml-auto para alinear a la derecha -->
                    <div class="relative">
                        <!-- Menú desplegable del User Avatar -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center focus:outline-none">
                                <!-- Avatar genérico -->
                                <img alt="User Avatar" class="w-10 h-10 rounded-full mr-2" height="40" src="https://www.gravatar.com/avatar/default?s=200&d=mp" width="40"/>
                                <!-- Saludo dinámico -->
                                <span class="text-gray-700">Hola, {{ Auth::user()->name }}</span>
                            </button>
                            <!-- Menú desplegable -->
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <!-- Formulario para logout -->
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf <!-- Token CSRF -->
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
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
            <img alt="Logo" class="mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/wdVhKpjxoPtLv5IwtcNKZTtND5y2hoPfIUEZqQaGIhQ.jpg" width="40"/>
            <span class="text-xl font-bold">kaiadmin</span>
        </div>
        <nav class="mt-10">
            <a class="flex items-center py-2 px-8 bg-gray-800 text-gray-200" href="#">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
        </nav>
    </div>

    <script>
        // Toggle para el menú móvil
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('-translate-x-full');
        });

        // Toggle para el menú desplegable del User Avatar
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        userMenuButton.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Ocultar el menú desplegable al hacer clic fuera de él
        document.addEventListener('click', (event) => {
            if (!userMenuButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Toggle para los submenús - Versión mejorada para mantener hermanos abiertos
    const menuToggles = document.querySelectorAll('.menu-toggle');
    menuToggles.forEach((toggle) => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const menuId = toggle.getAttribute('data-menu');
            const submenu = document.getElementById(`${menuId}-submenu`);
            const arrow = toggle.querySelector('.arrow');

            // Alternar visibilidad del submenú actual
            submenu.classList.toggle('hidden');

            // Rotar la flecha del menú actual
            arrow.classList.toggle('rotate-180');
        });
    });

       // Mantener abiertos todos los submenús hermanos al hacer clic en cualquier enlace
       document.querySelectorAll('.submenu a').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Obtener el menú padre (el div.relative que contiene todos los submenús hermanos)
            const menuParent = link.closest('.relative');
            if (menuParent) {
                // Mantener abiertos todos los submenús de este menú padre
                const allSubmenus = menuParent.querySelectorAll('.submenu');
                allSubmenus.forEach((submenu) => {
                    submenu.classList.remove('hidden');
                });
                
                // Asegurar que todas las flechas apunten hacia arriba (indicando abierto)
                const allArrows = menuParent.querySelectorAll('.arrow');
                allArrows.forEach((arrow) => {
                    arrow.classList.add('rotate-180');
                });
            }
        });
    });
    // Verificar estado de caja chica al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        @if(!$hasOpenPettyCash && Route::currentRouteName() != 'petty-cash.create')
            // Bloquear todos los enlaces excepto logout y apertura de caja
            const allLinks = document.querySelectorAll('a');
            allLinks.forEach(link => {
                if (!link.href.includes('logout') && !link.href.includes('petty-cash/create')) {
                    link.addEventListener('click', function(e) {
                        if (this.href !== window.location.href) {
                            e.preventDefault();
                            window.location.href = "{{ route('petty-cash.create') }}";
                        }
                    });
                }
            });
            
            // Mostrar mensaje si se intenta acceder a otra ruta
            @if(Route::currentRouteName() != 'petty-cash.create' && Route::currentRouteName() != 'login')
                alert('Debe abrir una caja chica antes de continuar.');
                window.location.href = "{{ route('petty-cash.create') }}";
            @endif
        @endif
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>