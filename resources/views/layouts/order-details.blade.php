<style>
/* Estructura principal */
.category-header {
    margin-top: 30px !important; /* Ajusta este valor según necesites */
    padding-top: 15px;
    border-top: 1px solid #e2e8f0; /* Opcional: línea divisora */
}
.buttons-container {
    margin-top: auto; /* Empuja el contenedor hacia abajo */
    padding-top: 12px;
    background: white;
    position: sticky;
    bottom: 0;
    border-top: 1px solid #e2e8f0;
}

/* Ajuste para móviles */
@media (max-width: 768px) {
    .buttons-container {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 12px 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
}
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    /* overflow: hidden; */
}

/* Contenedor del panel derecho */
.w-full.md:w-1\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0 {
    height: 100vh;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    padding: 16px;
}

/* Contenedor de contenido principal */
.mb-4 {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    /* overflow: hidden; Evita que el contenido desborde */
}

/* Área de detalles del pedido con scroll */
#order-details {
    flex: 1;
    overflow-y: auto;  /* Cambiado de 'scroll' a 'auto' para que solo aparezca cuando sea necesario */
    scrollbar-width: thin;  /* Para Firefox */
    padding-right: 8px;  /* Espacio para la scrollbar */
    margin-bottom: 12px;
    max-height: calc(100vh - 530px);  /* Ajusta esta altura según necesites */
}

/* Personalización de la scrollbar para navegadores WebKit (Chrome, Safari) */
#order-details::-webkit-scrollbar {
    width: 6px;
}

#order-details::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#order-details::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#order-details::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
/* Contenedor de botones fijos en la parte inferior */
.buttons-container {
    margin-top: auto; /* Empuja el contenedor hacia abajo */
    padding-top: 12px;
    background: white;
    position: sticky;
    bottom: 0;
    border-top: 1px solid #e2e8f0; /* Línea separadora opcional */
}

/* Ajustes para móviles */
@media (max-width: 768px) {
    .w-full.md:w-1\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0 {
        position: relative;
        height: 70vh;
        width: 100%;
    }
    
    .buttons-container {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 12px 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
}

.notes-container {
    margin-top: 12px; /* Reduce el margen superior */
    margin-bottom: 12px;
}
.flex.flex-row.space-x-2.mb-2 {
    margin-bottom: 8px; /* Reduce el margen inferior */
}
.w-full.md:w-1\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0 {
    padding: 16px 12px; /* Ajusta el padding para ganar espacio */
}
@media (max-width: 768px) {
    .w-full.md:w-1\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0 {
        position: relative;
        height: auto;
        max-height: 70vh;
        width: 100%;
    }
    
    #order-details {
        max-height: 40vh;
    }
}

/* Contenedor de contenido principal */
.mb-4 {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.notes-container {
    margin-top: auto; /* Empuja las notas hacia arriba antes de los botones */
    margin-bottom: 16px;
}
    
    .notes-label {
        display: block;
        font-size: 12px;
        color: var(--table-data-color);
        margin-bottom: 4px;
        font-weight: 500;
    }
    
    .notes-textarea {
        width: 100%;
        min-height: 80px;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        transition: all 0.2s ease;
        outline: none;
        background-color: white;
        box-sizing: border-box;
        font-family: inherit;
        resize: vertical;
    }
    
    .notes-textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.1);
    }
    
    .notes-counter {
        font-size: 11px;
        color: #718096;
        text-align: right;
        margin-top: 4px;
    }
    
    .notes-examples {
        font-size: 11px;
        color: #718096;
        margin-top: 4px;
    }
    
    .notes-examples span {
        display: inline-block;
        margin-right: 6px;
        cursor: pointer;
        text-decoration: underline;
    }
    
    .notes-examples span:hover {
        color: var(--primary-color);
    }
    /* Estilos base para inputs */
    /* Estilos unificados para todos los inputs */
.payment-input, 
.payment-type, 
.transaction-number {
    width: 100%;
    height: 38px; /* Altura consistente */
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    transition: all 0.2s ease;
    outline: none;
    background-color: white;
    box-sizing: border-box;
    font-family: inherit;
}

   /* Focus state consistente */
.payment-input:focus, 
.payment-type:focus, 
.transaction-number:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.1);
}
/* Focus state consistente */
.payment-input:focus, 
.payment-type:focus, 
.transaction-number:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.1);
}
/* Estilos para selects para que coincidan con inputs
.payment-type {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    padding-right: 32px;
} */


/* Estilo mejorado para el select con mayor altura */
.payment-type {
    width: 100%;
    height: 48px; /* Aumentado de 38px a 48px */
    padding: 12px 40px 12px 16px; /* Más padding vertical */
    font-size: 15px; /* Texto ligeramente más grande */
    border: 1px solid #cbd5e0;
    border-radius: 8px; /* Bordes más redondeados */
    background-color: white;
    color: #203363;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center; /* Ajuste de posición del ícono */
    background-size: 18px; /* Ícono ligeramente más grande */
    line-height: 1.5; /* Mejor espaciado de línea */
}

/* Estilo para las opciones */
.payment-option {
    padding: 2px 12px;
    font-size: 14px;
    color: #203363;
    background-color: white;
}

/* Efecto hover en las opciones */
.payment-option:hover {
    background-color: #f8fafc;
}

/* Focus state */
.payment-type:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.1);
    outline: none;
}

/* Estilo para cuando está abierto (en navegadores que lo soportan) */
.payment-type:active, .payment-type:focus {
    border-color: var(--primary-color);
}

    /* Estados de validación mejorados */
.error-input {
    border-color: #e53e3e;
    background-color: #fff5f5;
    color: #c53030;
}

.success-input {
    border-color: #38a169;
    background-color: #f0fff4;
    color: #276749;
}

/* Feedback visual para inputs */
.input-feedback {
    font-size: 12px;
    margin-top: 4px;
    display: none;
}

.error-input + .input-feedback.error,
.success-input + .input-feedback.success {
    display: block;
}
    /* Efecto hover para inputs con estado */
    .error-input:hover, .success-input:hover {
        box-shadow: 0 0 0 2px rgba(32, 51, 99, 0.1);
    }

    /* Labels mejorados */
    .input-label {
        font-size: 12px;
        color: var(--table-data-color);
        margin-bottom: 4px;
        display: block;
        font-weight: 500;
    }

    /* Contenedor más compacto */
    .input-with-icon {
        position: relative;
        margin-bottom: 8px;
    }

    /* Eliminamos los iconos de validación */
    .validation-icon {
        display: none !important;
    }

    .error-icon {
        color: #e53e3e;
    }

    .success-icon {
        color: #38a169;
    }

    /* Contenedor de input con icono */
    .input-with-icon {
        position: relative;
    }
    .payment-modal-lg {
    width: min(550px, 90vw); /* Más flexible para móviles */
    max-height: 90vh; /* Limitar altura máxima */
    display: flex;
    flex-direction: column;
}
.payment-row {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 12px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Encabezado de fila */
.payment-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Contenedor de íconos */
.payment-icons-container {
    display: flex;
    gap: 8px;
    align-items: center;
}


.payment-rows-scrollable {
    flex-grow: 1; /* Ocupa espacio disponible */
    overflow-y: auto;
    padding-right: 8px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
}

/* Scrollbar personalizada para WebKit */
.payment-rows-scrollable::-webkit-scrollbar {
    width: 6px;
}
.payment-rows-scrollable::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
    /* Mejoramos los inputs para el nuevo tamaño */
    .payment-row input, 
    .payment-row select {
        padding: 0.75rem; /* Más espacio interno */
        font-size: 0.9rem; /* Texto ligeramente más grande */
    }
    
    /* Ajustamos los botones de acción */
    .payment-actions {
        margin-top: 1.5rem; /* Más espacio sobre los botones */
    }
    /* Íconos de pago */
.payment-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Grupo de inputs de monto */
.payment-amount-group {
    display: flex;
    gap: 12px;
    width: 100%;
}

/* Inputs de monto con flexibilidad */
.payment-amount-input {
    flex: 1;
    min-width: 0; /* Previene problemas de desbordamiento */
}
/* Contenedor del select para mejor control */
.select-container {
    position: relative;
    display: flex;
    align-items: center;
}

/* Ajustes para las opciones del dropdown */
.payment-option {
    padding: 12px 16px; /* Más padding para mejor tacto */
    font-size: 15px;
    color: #203363;
    background-color: white;
}

/* Ajustes para móviles */
@media (max-width: 480px) {
    .payment-type {
        height: 52px; /* Aún más alto en móviles */
        font-size: 16px;
        padding: 14px 40px 14px 16px;
    }
    
    .payment-option {
        font-size: 16px;
        padding: 14px 16px;
    }
}
/* Ajustes para móviles */
@media (max-width: 480px) {
    .payment-modal-lg {
        width: 95vw;
        padding: 16px;
    }
    
    .payment-amount-group {
        flex-direction: column;
        gap: 8px;
    }
    
    .payment-row {
        padding: 12px;
    }
    
    .payment-input, 
    .payment-type, 
    .transaction-number {
        height: 42px; /* Más alto para mejor tacto en móviles */
    }
}
@media (max-width: 480px) {
    .payment-type {
        height: 42px;
        font-size: 15px; /* Un poco más grande en móviles */
    }
    
    .payment-option {
        font-size: 15px; /* Tamaño consistente en móviles */
    }
}
</style>

<!-- Agregar este modal para la vista previa -->
<div id="print-preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#203363]">Vista previa de impresión</h3>
            <button onclick="closePrintPreview()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="print-preview-content" class="bg-white p-4 border border-gray-300 mb-4"></div>
        <div class="flex justify-end space-x-2">
            <button onclick="closePrintPreview()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                Cancelar
            </button>
            <button onclick="confirmPrint()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>
</div>

<div class="w-full md:w-1/5 bg-white p-4 rounded-lg shadow-lg fixed right-0 top-0 h-full">
        <div class="mb-4">
            <h2 class="text-lg font-bold mb-2 text-[#203363]">Detalles del Pedido</h2>
        </div>
        <div class="mb-4">
            <!-- Botones para seleccionar el tipo de pedido -->
            <div class="flex flex-col space-y-2 mb-3">
                <button type="button" id="btn-comer-aqui" onclick="setOrderType('Comer aquí')" class="w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-105">Comer aquí</button>
                <button type="button" id="btn-para-llevar" onclick="setOrderType('Para llevar')" class="w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105">Para llevar</button>
                <button type="button" id="btn-recoger" onclick="setOrderType('Recoger')" class="w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105">Recoger</button>
            </div>
            <input type="hidden" name="order_type" id="order-type" value="Comer aquí">

            <!-- Selección de mesa (solo visible para "Comer aquí") -->
            <div id="table-selection" class="mb-3">
                <label for="table-number" class="block text-sm text-[#203363] font-bold mb-1">Selecciona la Mesa:</label>
                <select id="table-number" class="border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm">
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}">Mesa {{ $table->number }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selección de delivery (solo visible para "Para llevar" o "Recoger") -->
            <div id="delivery-selection" class="mb-3 hidden">
                <label for="delivery-service" class="block text-sm text-[#203363] font-bold mb-1">Servicio de Delivery:</label>
                <select id="delivery-service" class="border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm">
                    <option value="Yango">Yango</option>
                    <option value="Pedidos Ya">Pedidos Ya</option>
                </select>
            </div>

            <!-- Detalles del pedido -->
            <div id="order-details" class="mt-3 transition-all opacity-100 text-sm">
                <!-- Los ítems del pedido se agregarán aquí dinámicamente -->
            </div>
        </div>
         <!-- Agregar esto dentro del div principal (justo antes de los botones de acción) -->
    <div class="notes-container">
        <label for="order-notes" class="notes-label">Notas especiales para el pedido:</label>
        <textarea id="order-notes" class="notes-textarea" placeholder="Ej: Quiero una hamburguesa sin queso cheddar, salsa aparte..." maxlength="250" oninput="updateNotesCounter()"></textarea>
        <div class="notes-counter"><span id="notes-chars">0</span>/250 caracteres</div>
        <div class="notes-examples">Ejemplos: 
            <span onclick="insertExample('Sin cebolla')">Sin cebolla</span>
            <span onclick="insertExample('Salsa aparte')">Salsa aparte</span>
            <span onclick="insertExample('Bien cocido')">Bien cocido</span>
            <span onclick="insertExample('Poco sal')">Poco sal</span>
        </div>
    </div>

    <!-- Reemplaza el div contenedor de los botones con este código -->
<div class="buttons-container">
    <div class="flex flex-row space-x-2"> <!-- Contenedor flex en fila -->
        <!-- Botón de Proforma -->
        <button id="btn-proforma" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-105 text-sm flex items-center justify-center" onclick="generateProforma()">
            <i class="fas fa-file-invoice mr-2"></i> Proforma
        </button>
        
        <!-- Botón de Realizar Pago -->
        <button id="btn-multiple-payment" class="flex-1 bg-[#203363] text-white py-2 px-3 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-105 text-sm flex items-center justify-center" onclick="showPaymentModal()">
            Realizar Pago
        </button>
    </div>
    
    <!-- Botón de Procesar Pedido (se mantiene debajo) -->
    <button id="btn-process-order" class="w-full bg-gray-400 text-white py-2 px-3 rounded-lg cursor-not-allowed text-sm hidden mt-2" disabled onclick="processOrder()">
        Procesar Pedido
    </button>
</div>
    </div>

    <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg payment-modal-lg">
        <h2 class="text-xl font-bold mb-5 text-[#203363]">Detalles del Pago</h2>

        <!-- Botón para agregar fila de pago -->
        <button onclick="addPaymentRow()" class="w-full flex items-center justify-center bg-[#203363] text-white py-3 px-4 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-[1.02] mb-5 text-base">
            <span class="mr-2">Agregar método de pago</span>
            <span class="text-xl font-bold">+</span>
        </button>

        <!-- Contenedor ampliado para las filas de pago -->
        <div id="payment-rows-scrollable" class="payment-rows-scrollable mb-5">
            <div id="payment-rows-container">
                <!-- Las filas de pago se agregarán aquí dinámicamente -->
            </div>
        </div>
   

        <!-- Botones de acción con más espacio -->
        <div class="payment-actions flex justify-end space-x-3">
            <button onclick="closePaymentModal()" class="bg-gray-400 text-white py-2.5 px-5 rounded-lg hover:bg-gray-500 transition-colors text-base">
                Cerrar
            </button>
            <button onclick="processPayment()" class="bg-[#203363] text-white py-2.5 px-5 rounded-lg hover:bg-[#47517c] transition-colors text-base">
                Procesar Pago
            </button>
        </div>
    </div>
</div>
<div id="proforma-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#203363]">Reserva de Pedido</h3>
            <button type="button" onclick="closeProformaModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="proforma-form" class="space-y-4" onsubmit="saveProforma(event)">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div>
                <label class="block text-sm font-medium text-[#203363] mb-1">Nombre del Cliente*</label>
                <input type="text" name="customer_name" id="proforma-customer-name" class="w-full border border-gray-300 rounded-md p-2 focus:border-[#203363] focus:ring-2 focus:ring-[#203363]" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-[#203363] mb-1">Notas Adicionales</label>
                <textarea name="notes" id="proforma-notes" class="w-full border border-gray-300 rounded-md p-2 focus:border-[#203363] focus:ring-2 focus:ring-[#203363]"></textarea>
            </div>
            
            <div id="proforma-summary" class="border-t border-gray-200 pt-4">
                <!-- Aquí se mostrará el resumen del pedido -->
            </div>
            
            <div class="flex justify-end space-x-2 pt-4">
                <button type="button" onclick="closeProformaModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                    <i class="fas fa-save mr-2"></i> Guardar Reserva
                </button>
            </div>
        </form>
    </div>
</div>

    <script>
        // Variable global para almacenar el contenido a imprimir
let currentPrintContent = '';
// Función para confirmar pedido e imprimir
function confirmAndPrint() {
    // Validar que hay items en el pedido
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para confirmar');
        return;
    }

    // Mostrar vista de impresión directamente
    printTicket();
    
    // También puedes mantener la funcionalidad original si es necesaria
    // loadCustomerDetails();
}
// Función mejorada para imprimir ticket (sin redirección)
function printTicket() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderType = document.getElementById('order-type').value;
    const tableNumber = document.getElementById('table-number').value;
    const orderNotes = document.getElementById('order-notes').value;
    const customerName = document.getElementById('customer-name')?.value || '';
    
    if (order.length === 0) {
        alert('No hay ítems en el pedido para imprimir');
        return;
    }

    // Calcular totales
    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0; // 0% de impuesto
    const total = subtotal + tax;

    // Generar contenido del ticket para impresora térmica (80mm)
   // Generar contenido del ticket
   currentPrintContent = `
        <style>
            @media print {
                @page { size: 80mm auto; margin: 0; }
                body { 
                    font-family: 'Courier New', monospace; 
                    font-size: 12px; 
                    width: 80mm;
                    margin: 0;
                    padding: 2mm;
                }
                .header { text-align: center; margin-bottom: 2mm; }
                .title { font-weight: bold; font-size: 14px; }
                .subtitle { font-size: 12px; }
                .divider { border-top: 1px dashed #000; margin: 2mm 0; }
                .item-row { display: flex; justify-content: space-between; margin: 1mm 0; }
                .total-row { font-weight: bold; margin-top: 2mm; }
                .footer { text-align: center; margin-top: 3mm; font-size: 10px; }
                .notes { margin-top: 2mm; font-style: italic; font-size: 11px; }
                .customer { margin-top: 2mm; font-weight: bold; font-size: 11px; }
            }
            @media screen {
                .ticket-preview {
                    font-family: 'Courier New', monospace;
                    font-size: 14px;
                    width: 100%;
                    max-width: 300px;
                    margin: 0 auto;
                    padding: 10px;
                    border: 1px dashed #ccc;
                    background-color: white;
                }
            }
        </style>
        <div class="ticket-preview">
            <div class="header">
                <div class="title">RESTAURANTE MIQUNA</div>
                <div class="subtitle">${new Date().toLocaleString()}</div>
                <div class="subtitle">${orderType} ${orderType === 'Comer aquí' ? 'Mesa: ' + tableNumber : ''}</div>
            </div>
            <div class="divider"></div>
            
            ${customerName ? `<div class="customer">Cliente: ${customerName}</div>` : ''}
            
            ${order.map(item => `
                <div class="item-row">
                    <span>${item.quantity}x ${item.name.substring(0, 20)}</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `).join('')}
            
            <div class="divider"></div>
            
            <div class="item-row">
                <span>Subtotal:</span>
                <span>$${subtotal.toFixed(2)}</span>
            </div>
            <div class="item-row">
                <span>Impuesto (0%):</span>
                <span>$${tax.toFixed(2)}</span>
            </div>
            <div class="item-row total-row">
                <span>TOTAL:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            
            ${orderNotes ? `
                <div class="divider"></div>
                <div class="notes">
                    <div>Notas:</div>
                    <div>${orderNotes.substring(0, 35)}</div>
                </div>
            ` : ''}
            
            <div class="divider"></div>
            <div class="footer">
                Gracias por su preferencia!
            </div>
        </div>
    `;

    // Mostrar vista previa
    showPrintPreview(currentPrintContent);
}
// Función para mostrar vista previa
function showPrintPreview(content) {
    const previewModal = document.getElementById('print-preview-modal');
    const previewContent = document.getElementById('print-preview-content');
    
    // Crear contenido de vista previa (versión más grande para visualización)
    const previewHtml = `
        <style>
            .ticket-preview {
                font-family: 'Courier New', monospace;
                font-size: 14px;
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
                padding: 10px;
                border: 1px dashed #ccc;
                background-color: white;
            }
            .header { text-align: center; margin-bottom: 10px; }
            .title { font-weight: bold; font-size: 16px; }
            .subtitle { font-size: 14px; }
            .divider { border-top: 1px dashed #000; margin: 8px 0; }
            .item-row { display: flex; justify-content: space-between; margin: 4px 0; }
            .total-row { font-weight: bold; margin-top: 8px; }
            .footer { text-align: center; margin-top: 10px; font-size: 12px; }
            .notes { margin-top: 8px; font-style: italic; font-size: 13px; }
        </style>
        ${content.replace('size: 80mm auto;', '').replace('width: 80mm;', '')}
    `;
    
    previewContent.innerHTML = previewHtml;
    previewModal.classList.remove('hidden');
}

// Función para cerrar vista previa
function closePrintPreview() {
    document.getElementById('print-preview-modal').classList.add('hidden');
}

// Función para confirmar e imprimir (sin redirección)
function confirmPrint() {
    closePrintPreview();
    
    // Intentar impresión directa si está disponible
    if (typeof window.printToThermal === 'function') {
        try {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = currentPrintContent;
            const textContent = tempDiv.textContent || tempDiv.innerText;
            window.printToThermal(textContent);
            
            // Llamar al callback con confirmación
            if (typeof window.confirmPrintCallback === 'function') {
                window.confirmPrintCallback(true);
            }
            return;
        } catch (e) {
            console.error("Error en impresión directa:", e);
        }
    }
    
    // Fallback a impresión en la misma ventana
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Impresión de Ticket</title>
            ${currentPrintContent}
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                            if (window.opener && typeof window.opener.confirmPrintCallback === 'function') {
                                window.opener.confirmPrintCallback(true);
                            }
                        }, 100);
                    }, 200);
                };
            <\/script>
        </head>
        <body>
            ${currentPrintContent}
        </body>
        </html>
    `);
    printWindow.document.close();
}
// Función para generar proforma (similar a imprimir pero sin cerrar ventana)

function generateProforma() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para generar una reserva');
        return;
    }

    // Mostrar el modal
    document.getElementById('proforma-modal').classList.remove('hidden');
    
    // Generar resumen del pedido
    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0;
    const total = subtotal + tax;
    
    const summaryContent = `
        <h4 class="font-bold text-[#203363] mb-2">Resumen del Pedido</h4>
        <div class="space-y-1 text-sm">
            ${order.map(item => `
                <div class="flex justify-between">
                    <span>${item.quantity}x ${item.name}</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `).join('')}
            <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between font-bold">
                <span>TOTAL:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('proforma-summary').innerHTML = summaryContent;
}
// Función para cerrar el modal de proforma

function closeProformaModal() {
    document.getElementById('proforma-modal').classList.add('hidden');
}


         // Función para actualizar el contador de caracteres
function updateNotesCounter() {
        const textarea = document.getElementById('order-notes');
        const counter = document.getElementById('notes-chars');
        counter.textContent = textarea.value.length;
        
        // Cambiar color si se acerca al límite
        if (textarea.value.length > 200) {
            counter.style.color = '#e53e3e';
        } else {
            counter.style.color = '#718096';
        }
    }
    
    // Función para insertar ejemplos
    function insertExample(text) {
        const textarea = document.getElementById('order-notes');
        const currentText = textarea.value;
        
        if (currentText.length > 0 && !currentText.endsWith(', ') && !currentText.endsWith('. ')) {
            textarea.value += ', ' + text;
        } else {
            textarea.value += text;
        }
        
        textarea.focus();
        updateNotesCounter();
    }
    
    // Función para obtener las notas (usada al procesar el pedido)
    function getOrderNotes() {
        return document.getElementById('order-notes').value.trim();
    }
    
        let paymentRowCounter = 0; // Contador para identificar filas de pago
        function addPaymentRow() {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const scrollableContainer = document.getElementById('payment-rows-scrollable');
        const existingPaymentTypes = new Set();

        // Obtener los tipos de pago existentes
        paymentRowsContainer.querySelectorAll('.payment-type').forEach(selectElement => {
            existingPaymentTypes.add(selectElement.value);
        });

        paymentRowCounter++;

        // Obtener el cambio de la última fila de pago (si existe)
        const lastPaymentRow = paymentRowsContainer.querySelector('.payment-row:last-child');
        let lastChange = 0;
        if (lastPaymentRow) {
            const lastChangeInput = lastPaymentRow.querySelector('.change');
            lastChange = parseFloat(lastChangeInput.value) || 0;
        }

        // Calcular el total restante a pagar
        const totalAmount = calcularTotal();
        let totalPaid = 0;
        paymentRowsContainer.querySelectorAll('.total-paid').forEach(input => {
            totalPaid += parseFloat(input.value) || 0;
        });
        const remainingTotal = totalAmount - totalPaid;

        // El Total a Pagar en la nueva fila será el cambio de la fila anterior (si existe)
        const totalToPay = lastChange > 0 ? lastChange : remainingTotal;

        // Crear una nueva fila de pago
        const paymentRow = document.createElement('div');
        paymentRow.id = `payment-row-${paymentRowCounter}`;
        paymentRow.className = 'payment-row flex flex-col space-y-4 mb-4 p-4 bg-gray-100 rounded-lg border border-gray-300 shadow-sm';
        paymentRow.innerHTML = `
            <div class="flex justify-between items-center payment-row-header">
                <div class="flex items-center space-x-2 payment-icons-container">
                    <span class="payment-icon hidden">
                        <img src="{{ asset('images/codigo-qr.png') }}" alt="QR" class="w-5 h-5">
                    </span>
                    <span class="payment-icon hidden">
                        <img src="https://cdn-icons-png.flaticon.com/512/2704/2704714.png" alt="Efectivo" class="w-5 h-5">
                    </span>
                    <span class="payment-icon hidden">
                        <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" alt="Tarjeta" class="w-5 h-5">
                    </span>
                </div>
                <button onclick="removePaymentRow('${paymentRow.id}')" class="text-red-600 font-bold text-sm hover:text-red-800 transition-colors">✕</button>
            </div>
            <div class="flex-1">
                <label class="input-label">Tipo de Pago:</label>
                <div class="select-container"> <!-- Contenedor adicional para mejor control -->
                    <select class="payment-type" onchange="updatePaymentFields(this, '${paymentRow.id}')">
                    ${!existingPaymentTypes.has('QR') ? '<option value="QR" class="payment-option">QR</option>' : ''}
                    ${!existingPaymentTypes.has('Efectivo') ? '<option value="Efectivo" class="payment-option">Efectivo</option>' : ''}
                    ${!existingPaymentTypes.has('Tarjeta') ? '<option value="Tarjeta" class="payment-option">Tarjeta</option>' : ''}
                    </select>
                </div>
            </div>
            <div id="transaction-field-${paymentRowCounter}" class="hidden">
                <label class="block text-sm text-[#203363] font-bold mb-1">Nro Transacción:</label>
                <input type="text" class="transaction-number border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm" placeholder="Ingrese el número de transacción">
            </div>
             <div class="flex justify-between space-x-4 payment-amount-group">
        <div class="flex-1 payment-amount-input input-with-icon">
            <label class="input-label">Total a Pagar:</label>
            <input type="text" class="payment-input total-amount" value="${totalToPay.toFixed(2)}" readonly>
        </div>
        <div class="flex-1 payment-amount-input input-with-icon">
            <label class="input-label">Total Pagado:</label>
            <input type="text" class="payment-input total-paid" oninput="updateChange('${paymentRow.id}')">
        </div>
        <div class="flex-1 input-with-icon payment-amount-input">
            <label class="input-label">Cambio:</label>
            <input type="text" class="payment-input change" readonly>
        </div>
    </div>
        `;

        // Agregar la nueva fila al contenedor
        paymentRowsContainer.appendChild(paymentRow);

        // Actualizar clases de scroll según cantidad de filas
        updateScrollContainer();

        // Mostrar el ícono del tipo de pago inicial
        updatePaymentIcon(paymentRow.querySelector('.payment-type'), paymentRow.id);

        // Actualizar campos según el tipo de pago seleccionado
        updatePaymentFields(paymentRow.querySelector('.payment-type'), paymentRow.id);
    }
    // Función para actualizar el contenedor de scroll
     function updateScrollContainer() {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const scrollableContainer = document.getElementById('payment-rows-scrollable');
        
        // Contar filas de pago existentes
        const rowCount = paymentRowsContainer.querySelectorAll('.payment-row').length;
        
        // Aplicar scroll solo si hay 2 o más filas
        if (rowCount >=2) {
            scrollableContainer.classList.add('has-scroll');
        } else {
            scrollableContainer.classList.remove('has-scroll');
        }
    }

    // Función para actualizar los campos según el tipo de pago
    function updatePaymentFields(selectElement, rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const existingPaymentTypes = new Set(); // Usamos un Set para almacenar los tipos de pago existentes

        // Recorrer las filas de pago existentes para obtener los tipos de pago
        paymentRowsContainer.querySelectorAll('.payment-type').forEach(select => {
            if (select !== selectElement) { // Excluir el select actual
                existingPaymentTypes.add(select.value); // Agregar el tipo de pago al Set
            }
        });

        const selectedValue = selectElement.value;

        // Verificar si ya existe un pago del tipo seleccionado
        if (existingPaymentTypes.has(selectedValue)) {
            alert(`Ya existe un pago de tipo ${selectedValue}. Seleccione otro tipo de pago.`);
            selectElement.value = 'QR'; // Restablecer el valor por defecto
            updatePaymentIcon(selectElement, rowId); // Actualizar el ícono del tipo de pago
            return; // Detener la ejecución
        }

        const transactionField = row.querySelector(`#transaction-field-${rowId.split('-')[2]}`);

        // Mostrar u ocultar el campo "Nro Transacción" para "QR" y "Tarjeta"
        if (selectedValue === 'QR' || selectedValue === 'Tarjeta') {
            transactionField.classList.remove('hidden');
        } else {
            transactionField.classList.add('hidden');
        }

        // Actualizar el ícono del tipo de pago
        updatePaymentIcon(selectElement, rowId);
    }
}
        // Función para actualizar el ícono del tipo de pago
function updatePaymentIcon(selectElement, rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                const icons = row.querySelectorAll('.payment-icon');
                icons.forEach(icon => icon.classList.add('hidden')); // Ocultar todos los íconos

                const selectedValue = selectElement.value;
                if (selectedValue === 'QR') {
                    icons[0].classList.remove('hidden');
                } else if (selectedValue === 'Efectivo') {
                    icons[1].classList.remove('hidden');
                } else if (selectedValue === 'Tarjeta') {
                    icons[2].classList.remove('hidden');
                }
            }
        }

      // Función para eliminar una fila de pago (modificada)
    function removePaymentRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            const totalPaidInput = row.querySelector('.total-paid');
            const totalPaid = parseFloat(totalPaidInput.value) || 0;

            // Eliminar la fila
            row.remove();

            // Actualizar el contenedor de scroll
            updateScrollContainer();

            // Recalcular los totales
            updateAllPaymentRows();
        }
    }

        // Función para actualizar el cambio en una fila de pago
// Función para actualizar el cambio (versión simplificada)
function updateChange(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const totalAmountInput = row.querySelector('.total-amount');
        const totalPaidInput = row.querySelector('.total-paid');
        const changeInput = row.querySelector('.change');

        // Obtener los valores de los campos
        const totalAmount = parseFloat(totalAmountInput.value) || 0;
        const totalPaid = parseFloat(totalPaidInput.value) || 0;

        // Calcular el cambio
        const change = totalPaid - totalAmount;

        // Mostrar el cambio en el campo correspondiente
        if (!isNaN(change)) {
            changeInput.value = change.toFixed(2);
            
            // Aplicar estilos según el cambio (sin iconos)
            if (change < 0) {
                totalPaidInput.classList.add('error-input');
                totalPaidInput.classList.remove('success-input');
                changeInput.classList.add('error-input');
                changeInput.classList.remove('success-input');
            } else {
                totalPaidInput.classList.add('success-input');
                totalPaidInput.classList.remove('error-input');
                changeInput.classList.add('success-input');
                changeInput.classList.remove('error-input');
            }
        } else {
            changeInput.value = '0.00';
            // Limpiar estilos
            totalPaidInput.classList.remove('error-input', 'success-input');
            changeInput.classList.remove('error-input', 'success-input');
        }

        // Actualizar el Total a Pagar en las filas posteriores
        updateRemainingTotal(rowId);
    }
}
    // Función para mostrar el modal de pago (modificada para inicializar sin scroll)
function showPaymentModal() {
        console.log('Modal abierto');
        const modal = document.getElementById('payment-modal');
        modal.classList.remove('hidden');

        const paymentRowsContainer = document.getElementById('payment-rows-container');
        paymentRowsContainer.innerHTML = '';

        // Mostrar el total del pedido en el modal
        const totalAmount = calcularTotal();
        const totalDisplay = document.createElement('div');
        totalDisplay.className = 'text-sm font-bold text-[#203363] mb-4';
        totalDisplay.innerHTML = `Total del Pedido: $${totalAmount}`;
        paymentRowsContainer.parentNode.insertBefore(totalDisplay, paymentRowsContainer);

        paymentRowCounter = 0;
        addPaymentRow();
        
        // Asegurarse de que el scroll esté desactivado inicialmente
        document.getElementById('payment-rows-scrollable').classList.remove('has-scroll');
    }
        // Función para cerrar el modal de pago
        function closePaymentModal() {
            const modal = document.getElementById('payment-modal');
            modal.classList.add('hidden');
        }
        // Función para actualizar el Total a Pagar en las filas posteriores
    function updateRemainingTotal(currentRowId) {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const paymentRows = paymentRowsContainer.querySelectorAll('.payment-row');

        let totalAmount = calcularTotal(); // Obtener el total del pedido
        let totalPaid = 0;

        // Calcular el total pagado en todas las filas
        paymentRows.forEach(row => {
            const totalPaidInput = row.querySelector('.total-paid');
            totalPaid += parseFloat(totalPaidInput.value) || 0;
        });

        // Calcular el total restante a pagar
        const remainingTotal = totalAmount - totalPaid;

        // Actualizar el Total a Pagar solo en las filas posteriores a la fila actual
        let isCurrentRowFound = false;
        paymentRows.forEach(row => {
            if (row.id === currentRowId) {
                isCurrentRowFound = true; // Marcar que se encontró la fila actual
            }

            if (isCurrentRowFound && row.id !== currentRowId) {
                const totalAmountInput = row.querySelector('.total-amount');
                totalAmountInput.value = remainingTotal.toFixed(2); // Actualizar el valor con 2 decimales
            }
        });
    }
    function updateRemainingTotalAfterRemoval(removedAmount) {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const paymentRows = paymentRowsContainer.querySelectorAll('.payment-row');

        if (paymentRows.length > 0) {
            let totalAmount = calcularTotal(); // Obtener el total del pedido
            let totalPaid = 0;

            // Calcular el total pagado en todas las filas restantes
            paymentRows.forEach(row => {
                const totalPaidInput = row.querySelector('.total-paid');
                totalPaid += parseFloat(totalPaidInput.value) || 0;
            });

            // Calcular el total restante a pagar
            const remainingTotal = totalAmount - totalPaid;

            // Distribuir el total restante entre las filas restantes
            paymentRows.forEach((row, index) => {
                const totalAmountInput = row.querySelector('.total-amount');
                if (index === 0) {
                    // La primera fila debe mostrar el total restante
                    totalAmountInput.value = remainingTotal.toFixed(2);
                } else {
                    // Las filas posteriores deben mostrar 0, ya que el total restante ya se asignó a la primera fila
                    totalAmountInput.value = '0.00';
                }
            });
        }
    }

        function loadCustomerDetails(paymentDetails = []) {
        const order = JSON.parse(localStorage.getItem('order')) || [];
        if (order.length === 0) {
            alert('No hay ítems en el pedido. Agrega ítems antes de continuar.');
            return;
        }

        // Cambiar el contenido de la sección principal a la vista de detalles del cliente
        fetch("{{ route('customer.details') }}")
            .then(response => response.text())
            .then(html => {
                document.getElementById('main-content').innerHTML = html;

                // Mostrar los detalles del pago si existen
                if (paymentDetails.length > 0) {
                    showPaymentDetailsInCustomerDetails(paymentDetails);
                }

                // Ocultar el botón "Confirmar Pedido" y "Pago Múltiple", y mostrar el botón "Procesar Pedido"
                document.getElementById('btn-confirm-order').classList.add('hidden');
                document.getElementById('btn-multiple-payment').classList.add('hidden');
                document.getElementById('btn-process-order').classList.remove('hidden');
                document.getElementById('btn-process-order').disabled = false;
            });
    }
    function setOrderType(type) {
        document.getElementById('order-type').value = type;
        localStorage.setItem('orderType', type);

        // Resetear estilos de todos los botones
        document.getElementById('btn-comer-aqui').className = 'w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105';
        document.getElementById('btn-para-llevar').className = 'w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105';
        document.getElementById('btn-recoger').className = 'w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105';

        // Aplicar estilo seleccionado
        if (type === 'Comer aquí') {
            document.getElementById('btn-comer-aqui').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            document.getElementById('table-selection').classList.remove('hidden'); // Mostrar selección de mesa
            document.getElementById('delivery-selection').classList.add('hidden'); // Ocultar selección de delivery
        } else if (type === 'Para llevar') {
            document.getElementById('btn-para-llevar').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            document.getElementById('table-selection').classList.add('hidden'); // Ocultar selección de mesa
            document.getElementById('delivery-selection').classList.remove('hidden'); // Mostrar selección de delivery
        } else if (type === 'Recoger') {
            document.getElementById('btn-recoger').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            document.getElementById('table-selection').classList.add('hidden'); // Ocultar selección de mesa
            document.getElementById('delivery-selection').classList.add('hidden'); // Ocultar selección de delivery
        }

        // Aplicar efecto de fade-in al cambiar de opción
        const orderDetails = document.getElementById('order-details');
        orderDetails.classList.add('opacity-0');

        setTimeout(() => {
            updateOrderDetails();
            orderDetails.classList.remove('opacity-0');
            orderDetails.classList.add('opacity-100');
        }, 200);
    }
        // Función para inicializar el estado predeterminado
        function initializeDefaultOrderType() {
            const defaultOrderType = 'Comer aquí';
            setOrderType(defaultOrderType); // Establecer "Comer aquí" como predeterminado
        }

        // Llamar a la función de inicialización al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            initializeDefaultOrderType();
            updateOrderDetails(); // Actualizar detalles del pedido
        });

        function showPaymentModal() {
        console.log('Modal abierto'); // Verifica que esta línea se ejecute
        const modal = document.getElementById('payment-modal');
        modal.classList.remove('hidden');

        const paymentRowsContainer = document.getElementById('payment-rows-container');
        paymentRowsContainer.innerHTML = '';

        paymentRowCounter = 0;
        addPaymentRow();
    }
        // Función para cerrar el modal de pago
        function closePaymentModal() {
            const modal = document.getElementById('payment-modal');
            modal.classList.add('hidden');
        }

        // Función para procesar el pago
// Función para procesar el pago
function processPayment() {
    // Validar el pago antes de continuar
    if (!validatePayment()) {
        return; // Detener la función si la validación falla
    }

    const paymentRows = document.querySelectorAll('.payment-row');
    const paymentDetails = [];

    paymentRows.forEach(row => {
        const paymentType = row.querySelector('.payment-type').value;
        const totalAmount = row.querySelector('.total-amount').value;
        const totalPaid = row.querySelector('.total-paid').value;
        const change = row.querySelector('.change').value;
        const transactionNumber = (paymentType === 'QR' || paymentType === 'Tarjeta') ? row.querySelector('.transaction-number').value : null;

        paymentDetails.push({
            paymentType,
            totalAmount,
            totalPaid,
            change,
            transactionNumber
        });
    });

    // Cerrar el modal de pago
    closePaymentModal();

    // Llamar a loadCustomerDetails y pasar paymentDetails como argumento
    loadCustomerDetails(paymentDetails);
}
function showPaymentDetailsInCustomerDetails(paymentDetails) {
    const paymentDetailsSection = document.getElementById('payment-details-section');
    if (!paymentDetailsSection) {
        console.error('El elemento payment-details-section no existe en el DOM.');
        return;
    }

    // Obtener el tipo de pedido y la opción de delivery
    const orderType = localStorage.getItem('orderType');
    const deliveryService = localStorage.getItem('deliveryService');

    // Limpiar el contenido actual de la sección de detalles de pago
    paymentDetailsSection.innerHTML = `
        <h3 class="text-lg font-bold mb-4 text-[#203363]">Detalles de Pago</h3>
        <div id="payment-details-list">
            ${paymentDetails.map((payment) => `
                <div class="mb-4 p-4 bg-gray-100 rounded-lg border border-gray-300 shadow-sm">
                    <p class="text-sm text-[#203363]"><strong>Tipo de Pago:</strong> ${payment.paymentType}</p>
                    <p class="text-sm text-[#203363]"><strong>Total a Pagar:</strong> $${payment.totalAmount}</p>
                    <p class="text-sm text-[#203363]"><strong>Total Pagado:</strong> $${payment.totalPaid}</p>
                    <p class="text-sm text-[#203363]"><strong>Cambio:</strong> $${payment.change}</p>
                    ${(payment.paymentType === 'QR' || payment.paymentType === 'Tarjeta') && payment.transactionNumber ? `<p class="text-sm text-[#203363]"><strong>Nro Transacción:</strong> ${payment.transactionNumber}</p>` : ''}
                </div>
            `).join('')}
        </div>
        ${orderType === 'Para llevar' || orderType === 'Recoger' ? `
            <div class="mb-4 p-4 bg-gray-100 rounded-lg border border-gray-300 shadow-sm">
                <p class="text-sm text-[#203363]"><strong>Servicio de Delivery:</strong> ${deliveryService}</p>
            </div>
        ` : ''}
    `;
}
        // Función para calcular el total del pedido (debes implementar la lógica según tu aplicación)
        function calcularTotal() {
        const order = JSON.parse(localStorage.getItem('order')) || [];
        const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
        const taxRate = 0; // 0% de impuesto
        const tax = subtotal * taxRate;
        const total = subtotal + tax;
        return total.toFixed(2); // Retorna el total con 2 decimales
    }

        // Función para actualizar la vista de order-details
    function updateOrderDetails() {
        const order = JSON.parse(localStorage.getItem('order')) || [];
        const orderDetails = document.getElementById('order-details');

        if (orderDetails) {
            // Limpiar el contenido actual
            orderDetails.innerHTML = '';

            // Agregar cada ítem al pedido
            order.forEach((item, index) => {
                const itemElement = document.createElement('div');
                itemElement.className = 'flex justify-between items-center mb-2 p-1.5 bg-gray-100 rounded-lg shadow-sm hover:shadow-md transition-shadow text-sm';
                itemElement.innerHTML = `
                    <div class="flex items-center">
                        <button onclick="removeItem(${index})" class="text-red-600 font-bold text-sm hover:text-red-800 mr-2 transition-colors">-</button>
                        <button onclick="increaseItemQuantity(${index})" class="text-green-600 font-bold text-sm hover:text-green-800 mr-2 transition-colors">+</button>
                        <p class="text-[#203363]">${item.name} (x${item.quantity})</p>
                    </div>
                    <p class="text-[#203363]">$${(item.price * item.quantity).toFixed(2)}</p>
                `;
                orderDetails.appendChild(itemElement);
            });

            // Calcular y mostrar el subtotal, impuesto y total
            const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
            const taxRate = 0; // 0% de impuesto
            const tax = subtotal * taxRate;
            const total = subtotal + tax;

            const totalsElement = document.createElement('div');
            totalsElement.className = 'text-sm';
            totalsElement.innerHTML = `
                <div class="flex justify-between items-center">
                    <p>Subtotal</p>
                    <p>$${subtotal.toFixed(2)}</p>
                </div>
                <div class="flex justify-between items-center">
                    <p>Impuesto</p>
                    <p>$${tax.toFixed(2)}</p>
                </div>
                <div class="flex justify-between items-center font-bold text-[#203363]">
                    <p>Total</p>
                    <p>$${total.toFixed(2)}</p>
                </div>
            `;
            orderDetails.appendChild(totalsElement);
        }
    }

    // Función para aumentar la cantidad de un ítem
    function increaseItemQuantity(index) {
        const order = JSON.parse(localStorage.getItem('order')) || [];

        // Verificar que el índice sea válido
        if (index >= 0 && index < order.length) {
            const item = order[index];
            item.quantity += 1; // Aumentar la cantidad en 1

            // Actualizar el localStorage
            localStorage.setItem('order', JSON.stringify(order));

            // Actualizar la vista
            updateOrderDetails();
        } else {
            console.error('Índice no válido:', index);
        }
    }

    // Función para eliminar o disminuir la cantidad de un ítem
    function removeItem(index) {
        const order = JSON.parse(localStorage.getItem('order')) || [];

        // Verificar que el índice sea válido
        if (index >= 0 && index < order.length) {
            const item = order[index];

            if (item.quantity > 1) {
                // Si hay más de un ítem, reducir la cantidad en 1
                item.quantity -= 1;
            } else {
                // Si solo hay un ítem, eliminarlo completamente
                order.splice(index, 1);
            }

            // Actualizar el localStorage
            localStorage.setItem('order', JSON.stringify(order));

            // Actualizar la vista
            updateOrderDetails();
        } else {
            console.error('Índice no válido:', index);
        }
    }
    // Función para validar si el total pagado es suficiente
    function validatePayment() {
    const paymentRows = document.querySelectorAll('.payment-row');
    let totalPaid = 0;

    paymentRows.forEach(row => {
        const totalPaidInput = row.querySelector('.total-paid');
        const paidValue = parseFloat(totalPaidInput.value);

        if (isNaN(paidValue) || paidValue <= 0) {
            alert('Por favor, ingrese un monto válido en todos los campos de "Total Pagado".');
            return false; // Validación fallida
        }

        totalPaid += paidValue;
    });

    const totalAmount = parseFloat(calcularTotal());

    if (totalPaid < totalAmount) {
        alert(`El total pagado ($${totalPaid.toFixed(2)}) es menor al total del pedido ($${totalAmount.toFixed(2)}).`);
        return false; // Validación fallida
    }

    return true; // Validación exitosa
}
        // Función para procesar el pedido
function processOrder() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido. Agrega ítems antes de continuar.');
        return;
    }

    // Mostrar vista de impresión primero
    printTicket();
    window.confirmPrintCallback = function(confirmed) {
        if (confirmed) {
            // Obtener datos del pedido
            const order = JSON.parse(localStorage.getItem('order')) || [];
            const orderType = localStorage.getItem('orderType') || 'Comer aquí';
            const customerName = document.getElementById('customer-name').value;
            const customerEmail = document.getElementById('customer-email').value;
            const customerPhone = document.getElementById('customer-phone').value;
            const tableNumber = document.getElementById('table-number').value;
            const orderNotes = getOrderNotes(); // Obtener las notas del pedido
                // Obtener el tipo de pago seleccionado
    const paymentRows = document.querySelectorAll('.payment-row');
    let paymentMethod = null;

    paymentRows.forEach(row => {
        const paymentType = row.querySelector('.payment-type').value;
        if (paymentType === 'QR' || paymentType === 'Efectivo' || paymentType === 'Tarjeta') {
            paymentMethod = paymentType; // Guardar el tipo de pago
        }
    });

    // Validar que el nombre del cliente esté presente
    if (!customerName) {
        alert('El nombre del cliente es obligatorio.');
        return;
    }

    // Verificar que el pedido no esté vacío
    if (order.length === 0) {
        alert('No hay ítems en el pedido. Agrega ítems antes de continuar.');
        return;
    }

    // Convertir el pedido a una cadena JSON
    const orderJSON = JSON.stringify(order);
            // Enviar los datos al servidor
            fetch("{{ route('sales.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    order: orderJSON, // Enviar el pedido como una cadena JSON
                    order_type: orderType,
                    customer_name: customerName,
                    customer_email: customerEmail,
                    customer_phone: customerPhone,
                    table_number: tableNumber,
                    payment_method: paymentMethod, // Enviar el tipo de pago
                    order_notes: orderNotes,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido procesado correctamente.');
                    localStorage.removeItem('order');
                    localStorage.removeItem('orderType');
                    window.location.href = "{{ route('menu.index') }}";
                } else {
                    alert(data.message || 'Hubo un error al procesar el pedido.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al procesar el pedido.');
            });
        }
    };
}

function generateProforma() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para generar una reserva');
        return;
    }

    // Mostrar el modal
    document.getElementById('proforma-modal').classList.remove('hidden');
    
    // Resetear el formulario
    document.getElementById('proforma-form').reset();
    
    // Generar resumen del pedido
    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0;
    const total = subtotal + tax;
    
    const summaryContent = `
        <h4 class="font-bold text-[#203363] mb-2">Resumen del Pedido</h4>
        <div class="space-y-1 text-sm">
            ${order.map(item => `
                <div class="flex justify-between">
                    <span>${item.quantity}x ${item.name}</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `).join('')}
            <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between font-bold">
                <span>TOTAL:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('proforma-summary').innerHTML = summaryContent;
}
async function saveProforma(event) {
    event.preventDefault();
    
    // Mostrar loader o estado de carga
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;

    try {
        const order = JSON.parse(localStorage.getItem('order')) || [];
        if (order.length === 0) {
            throw new Error('No hay ítems en el pedido para guardar');
        }

        const formData = new FormData(document.getElementById('proforma-form'));
        const orderType = document.getElementById('order-type').value;
        const tableNumber = orderType === 'Comer aquí' ? document.getElementById('table-number').value : null;
        
        // Obtener el token CSRF del meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('No se encontró el token CSRF');
        }

        // Crear objeto con los datos de la proforma
        const proformaData = {
            customer_name: formData.get('customer_name'),
            customer_phone: formData.get('customer_phone'),
            notes: formData.get('notes'),
            order_type: orderType,
            table_number: tableNumber,
            items: order,
            subtotal: order.reduce((sum, item) => sum + item.price * item.quantity, 0),
            tax: 0,
            total: order.reduce((sum, item) => sum + item.price * item.quantity, 0),
            status: 'reservado'
        };

        console.log('Enviando datos:', proformaData); // Para depuración

        // Enviar datos al servidor
        const response = await fetch('/proformas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(proformaData)
        });

        console.log('Respuesta recibida:', response); // Para depuración

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        
        alert('Proforma guardada correctamente con ID: ' + data.id);
        closeProformaModal();
        
    } catch (error) {
        console.error('Error al guardar proforma:', error);
        alert('Error al guardar la proforma: ' + error.message);
    } finally {
        // Restaurar el botón a su estado original
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
}

// Manejar el envío del formulario de reserva

// document.getElementById('proforma-form').addEventListener('submit', function(e) {
//     e.preventDefault();
    
//     const order = JSON.parse(localStorage.getItem('order')) || [];
//     const formData = new FormData(this);
//     const reservationData = {
//         customerName: formData.get('customerName'),
//         notes: formData.get('notes'),
//         order: order,
//         total: order.reduce((sum, item) => sum + item.price * item.quantity, 0),
//         createdAt: new Date().toISOString()
//     };
    
//     // Aquí deberías enviar los datos al servidor
//     console.log('Datos de reserva:', reservationData);
    
//     // Ejemplo de guardado en localStorage (simulación)
//     const reservations = JSON.parse(localStorage.getItem('reservations') || '[]');
//     reservations.push(reservationData);
//     localStorage.setItem('reservations', JSON.stringify(reservations));
    
//     alert('Reserva guardada exitosamente');
//     closeProformaModal();
// });
    </script>