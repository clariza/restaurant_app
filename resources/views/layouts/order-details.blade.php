<div id="order-panel" class="w-full md:w-1/5 bg-white p-4 rounded-lg shadow-lg fixed right-0 top-16 h-[calc(100vh-4rem)] flex flex-col z-40">
    
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

    <!-- Input oculto para el tipo de pedido -->
    <input type="hidden" name="order_type" id="order-type" value="Comer aquí">
</div>

<!-- Modal de Pago -->
@include('partials.payment-modal')

<!-- Modal de Proforma -->
@include('partials.proforma-modal')

<!-- Modal de Vista Previa de Impresión -->
@include('partials.print-preview-modal')

<script>
    const tablesEnabled = @json($settings->tables_enabled ?? false);
    @if(!auth()->check())
        clearOrderOnLogout();
    @endif
</script>

<!-- Variables globales PRIMERO -->
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
</script>

<!-- Scripts DESPUÉS de las variables -->
<script src="{{ asset('js/payment-modal.js') }}"></script>
<script src="{{ asset('js/order-details.js') }}"></script>