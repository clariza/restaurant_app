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
            <!-- ✅ CAMBIO: Botón que abre el modal -->
            <button onclick="openPettyCashModal()" class="flex-1 bg-[#EF476F] text-white py-2 px-3 rounded-lg hover:bg-accent-dark transition-colors text-sm flex items-center justify-center"
                title="Caja Chica">
                <i class="fas fa-cash-register"></i>
            </button>
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

<!-- ✅ NUEVO: Modal de Caja Chica -->
<div id="petty-cash-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="bg-white rounded-lg w-full max-w-7xl my-8 shadow-xl transform transition-all">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-[#203363]">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-cash-register mr-3"></i>
                    Gestión de Caja Chica
                </h2>
                <button onclick="closePettyCashModal()" class="text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Contenido del Modal -->
            <div id="petty-cash-content" class="p-6 max-h-[80vh] overflow-y-auto">
                <!-- El contenido se cargará dinámicamente aquí -->
                <div class="flex justify-center items-center py-12">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-[#203363] mb-4"></i>
                        <p class="text-gray-600">Cargando información de caja chica...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="closure-internal-overlay" class="closure-internal-overlay"></div>
<div id="modal-closure-internal" class="closure-internal-modal">
    <!-- El contenido del modal de cierre se cargará aquí dinámicamente -->
</div>
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
    menuIndex: "{{ route('menu.index') }}",
    pettyCashIndex: "{{ route('petty-cash.index') }}",
    pettyCashModalContent: "{{ route('petty-cash.modal-content') }}"
};
window.csrfToken = "{{ csrf_token() }}";
window.authUserName = "{{ Auth::user()->name ?? '' }}";
window.tablesEnabled = @json($settings->tables_enabled ?? false);

console.log('✅ Rutas de petty-cash cargadas:', window.routes);
</script>

<!-- Scripts DESPUÉS de las variables -->
<script src="{{ asset('js/payment-modal.js') }}"></script>
<script src="{{ asset('js/order-details.js') }}"></script>
<script src="{{ asset('js/petty-cash-modal.js') }}"></script> 