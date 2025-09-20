<div id="order-panel" class="w-full md:w-1/5 bg-white p-4 rounded-lg shadow-lg fixed right-0 top-16 h-[calc(100vh-4rem)] flex flex-col z-40">
    <!-- Encabezado con tipo de pedido -->
    <div class="mb-4">
        <div class="flex flex-col space-y-2">
            <button type="button" id="btn-comer-aqui" onclick="setOrderType('Comer aquí')" 
                class="w-full bg-primary text-white px-3 py-1.5 rounded-lg hover:bg-primary-dark transition-colors">
                Comer aquí
            </button>
            <button type="button" id="btn-para-llevar" onclick="setOrderType('Para llevar')" 
                class="w-full border border-primary text-primary px-3 py-1.5 rounded-lg hover:bg-primary hover:text-white transition-colors">
                Para llevar
            </button>
            <button type="button" id="btn-recoger" onclick="setOrderType('Recoger')" 
                class="w-full border border-primary text-primary px-3 py-1.5 rounded-lg hover:bg-primary hover:text-white transition-colors">
                Recoger
            </button>
        </div>
        <input type="hidden" name="order_type" id="order-type" value="Comer aquí">
    </div>

    <!-- Selección de mesa (si está habilitado) -->
    @if($settings->tables_enabled)
    <div id="table-selection" class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <label for="table-number" class="block text-sm font-bold text-primary">Selecciona la Mesa:</label>
            <a href="{{ route('tables.index') }}" class="text-xs text-secondary hover:text-primary transition-colors flex items-center group table-config-link">
                <i class="fas fa-cog mr-1 group-hover:text-primary transition-colors"></i>
                <span class="border-b border-transparent group-hover:border-secondary transition-colors">Configurar</span>
            </a>
        </div>
    
        <select id="table-number" class="w-full p-2 border border-gray-300 rounded-md focus:border-primary focus:ring-2 focus:ring-primary transition-colors text-sm">
            @foreach ($tables as $table)
                <option value="{{ $table->id }}" data-state="{{ $table->state }}">
                    Mesa {{ $table->number }} - {{ $table->state }}
                </option>
            @endforeach
        </select>

        <!-- Selector para el nuevo estado de todas las mesas -->
        <div class="mt-3">
            <label for="bulk-state-selector" class="block text-xs font-bold text-primary mb-1">
                Cambiar estado de todas las mesas a:
            </label>
            <select id="bulk-state-selector" class="w-full p-2 border border-gray-300 rounded-md focus:border-primary focus:ring-2 focus:ring-primary transition-colors text-xs">
                <option value="Disponible">Disponible</option>
                <option value="No Disponible">No Disponible</option>
                <option value="Ocupada">Ocupada</option>
                <option value="Reservada">Reservada</option>
            </select>
        </div>

        <!-- Botón para cambiar estado de todas las mesas -->
        <button id="change-all-tables-availability" class="w-full mt-2 py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center"
            onclick="changeAllTablesAvailability()">
            <i class="fas fa-sync-alt mr-2"></i>
            <span id="bulk-availability-text">Cambiar Estado de Todas las Mesas</span>
        </button>
    </div>
    @endif

    <!-- Selección de delivery -->
    <div id="delivery-selection" class="mb-4 hidden">
        <label for="delivery-service" class="block text-sm font-bold text-primary mb-1">Servicio de Delivery:</label>
        <select id="delivery-service" class="w-full p-2 border border-gray-300 rounded-md focus:border-primary focus:ring-2 focus:ring-primary transition-colors text-sm">
            @foreach ($deliveryServices as $service)
                <option value="{{ $service->name }}">{{ $service->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Contenedor scrollable para detalles del pedido -->
    <div class="scroll-container flex-1">
        <div id="order-details" class="mb-4">
            <!-- Los ítems del pedido se agregarán aquí dinámicamente -->
        </div>

        <!-- Notas del pedido -->
        <div class="notes-container">
            <label for="order-notes" class="notes-label">Notas especiales para el pedido:</label>
            <textarea id="order-notes" name="order_notes" class="notes-textarea" 
                placeholder="Ej: Quiero una hamburguesa sin queso cheddar, salsa aparte..." 
                maxlength="250" oninput="updateNotesCounter()"></textarea>
            <div class="notes-counter"><span id="notes-chars">0</span>/250 caracteres</div>
            <div class="notes-examples">Ejemplos: 
                <span onclick="insertExample('Sin cebolla')">Sin cebolla</span>
                <span onclick="insertExample('Salsa aparte')">Salsa aparte</span>
                <span onclick="insertExample('Bien cocido')">Bien cocido</span>
                <span onclick="insertExample('Poco sal')">Poco sal</span>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="buttons-container mt-auto">
        <div class="flex space-x-2 mb-2">
            
            <button id="btn-clear-order" class="flex-1 bg-gray-500 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center" 
                onclick="clearOrder()">
                <i class="fas fa-trash-alt mr-2"></i> Limpiar
            </button>
            <button id="btn-proforma" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center" 
                onclick="generateProforma()">
                <i class="fas fa-file-invoice mr-2"></i> Proforma
            </button>
        </div>

        
        <button id="btn-multiple-payment" class="w-full bg-primary text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center mb-2" 
            onclick="showPaymentModal()">
            Realizar Pago
        </button>
    
        <div class="flex space-x-2">
            <a href="{{ route('expenses.index') }}" class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center"
                title="Gastos">
                <i class="fas fa-receipt"></i>
            </a>
            <a href="{{ route('orders.index') }}" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center"
                title="Historial">
                <i class="fas fa-history"></i>
            </a>
            <a href="{{ route('petty-cash.index') }}" class="flex-1 bg-[#EF476F] text-white py-2 px-3 rounded-lg hover:bg-accent-dark transition-colors text-sm flex items-center justify-center"
                title="Caja Chica">
                <i class="fas fa-cash-register"></i>
            </a>
        </div>
    </div>

    
</div>
<!-- Modal de Pago -->
@include('partials.payment-modal')

<!-- Modal de Proforma -->
@include('partials.proforma-modal')

<!-- Modal de Vista Previa de Impresión -->
@include('partials.print-preview-modal')
<script>
    const tablesEnabled = @json($settings->tables_enabled ?? false);
       // Limpiar al cargar la página si el usuario no está autenticado
    @if(!auth()->check())
        clearOrderOnLogout();
    @endif
    
</script>
<script src="{{ asset('js/order-details.js') }}"></script>
<script src="{{ asset('js/payment-modal.js') }}"></script>