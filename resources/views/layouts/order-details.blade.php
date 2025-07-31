<style>
.sale-number {
    font-weight: bold;
    font-size: 13px;
    text-align: right;
}
#order-panel.locked {
    opacity: 0.7;
    pointer-events: none;
}

.locked .btn-action {
    background-color: #cccccc !important;
    cursor: not-allowed;
}

.item-actions {
    display: inline-block;
}
    /* Estilos minimalistas para el enlace de configuración */
.table-config-minimal {
    transition: all 0.3s ease;
    text-decoration: none;
}

.table-config-minimal:hover {
    text-decoration: none;
}

/* Efecto sutil para pantallas grandes */
@media (min-width: 768px) {
    .table-config-minimal {
        opacity: 0.7;
    }
    
    .table-config-minimal:hover {
        opacity: 1;
    }
}
    @media (max-width: 768px) {
    .table-config-link {
        padding: 4px 8px;
        font-size: 14px;
    }
    
    .table-config-link i {
        font-size: 14px;
    }
}
/* Estilos para el botón de configuración de mesas */
.table-config-link {
    transition: all 0.2s ease;
    padding: 2px 6px;
    border-radius: 4px;
}

.table-config-link:hover {
    background-color: rgba(32, 51, 99, 0.1);
    transform: translateY(-1px);
}
/* Estilos para el botón de configuración de mesas */
.table-config-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 4px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.table-config-btn i {
    margin-right: 4px;
    font-size: 12px;
}

/* Estilo para pantallas pequeñas */
@media (max-width: 768px) {
    .table-config-btn {
        padding: 8px 14px;
        font-size: 13px;
    }
    
    .table-config-btn i {
        font-size: 13px;
    }
}
.table-config-link i {
    font-size: 12px;
}
.notes {
    margin-top: 2mm;
    font-size: 11px;
    line-height: 1.3;  /* Reduce espacio entre líneas */
    padding: 1mm 0;    /* Padding más compacto */
}
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
.proforma-notes { margin-top: 2mm; font-size: 11px; border-top: 1px dashed #000; padding-top: 2mm; }

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
<div id="print-preview-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[1000]">
    <div class="modal-container bg-white rounded-lg p-6 w-full max-w-md mx-auto my-8">
        <div class="modal-header flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#203363]">Vista previa de impresión</h3>
            <button onclick="closePrintPreview()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="print-preview-content" class="modal-body bg-white p-4 border border-gray-300 mb-4 max-h-[60vh] overflow-y-auto"></div>
        <div class="modal-footer flex justify-end space-x-2">
            <button onclick="closePrintPreview()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                Cancelar
            </button>
            <button onclick="confirmPrint()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>
</div>
<div id="order-panel" class="w-full md:w-1/5 bg-white p-4 rounded-lg shadow-lg fixed right-0 top-0 h-full">
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

      
    @if($settings->tables_enabled)
<div id="table-selection" class="mb-3">
    <div class="flex items-center justify-between mb-1">
        <label for="table-number" class="block text-sm text-[#203363] font-bold">Selecciona la Mesa:</label>
        <a href="{{ route('tables.index') }}" class="text-xs text-[#6380a6] hover:text-[#203363] transition-colors flex items-center group">
            <i class="fas fa-cog mr-1 text-[#a4b6ce] group-hover:text-[#203363] transition-colors"></i>
            <span class="border-b border-transparent group-hover:border-[#a4b6ce] transition-colors">Configurar</span>
        </a>
    </div>
    <select id="table-number" class="border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm">
        @foreach ($tables as $table)
            <option value="{{ $table->id }}">Mesa {{ $table->number }}</option>
        @endforeach
    </select>
</div>
@endif

            <!-- Selección de delivery (solo visible para "Para llevar" o "Recoger") -->
<div id="delivery-selection" class="mb-3 hidden">
    <label for="delivery-service" class="block text-sm text-[#203363] font-bold mb-1">Servicio de Delivery:</label>
    <select id="delivery-service" class="border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm">
        @foreach ($deliveryServices as $service)
            <option value="{{ $service->name }}">{{ $service->name }}</option>
        @endforeach
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
        <textarea id="order-notes" name="order_notes" class="notes-textarea" placeholder="Ej: Quiero una hamburguesa sin queso cheddar, salsa aparte..." maxlength="250" oninput="updateNotesCounter()"></textarea>
        <div class="notes-counter"><span id="notes-chars">0</span>/250 caracteres</div>
        <div class="notes-examples">Ejemplos: 
            <span>Sin cebolla</span>
            <span>Salsa aparte</span>
            <span>Bien cocido</span>
            <span>Poco sal</span>
        </div>
    </div>

    <!-- Reemplaza el div contenedor de los botones con este código -->
<div class="buttons-container">
    <div class="flex flex-row space-x-2 mb-2"> <!-- Contenedor flex en fila -->
         <!-- Nuevo botón: Limpiar Pedido -->
        <button id="btn-clear-order" 
                class="flex-1 bg-gray-500 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors transform hover:scale-105 text-sm flex items-center justify-center" 
                onclick="clearOrder()">
            <i class="fas fa-trash-alt mr-2"></i> Limpiar
        </button>
        <!-- Botón de Proforma -->
        <button id="btn-proforma" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-105 text-sm flex items-center justify-center" onclick="generateProforma()">
            <i class="fas fa-file-invoice mr-2"></i> Proforma
        </button>
        
       
    </div>
     <!-- Botón de Realizar Pago -->
        <button id="btn-multiple-payment" class="w-full flex-1 bg-[#203363] text-white py-2 px-3 rounded-lg hover:bg-[#47517c] transition-colors transform hover:scale-105 text-sm flex items-center justify-center" onclick="showPaymentModal()">
            Realizar Pago
        </button>
    
    <!-- Botón de Procesar Pedido (se mantiene debajo) -->
    <!-- <button id="btn-process-order" >
        Procesar Pedido
    </button> -->
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
// =============================================
// ====== INICIALIZACIÓN Y CONFIGURACIÓN ======
// =============================================

// Variables globales
let paymentProcessed = false;
let paymentRowCounter = 0;
let currentPrintContent = '';
 const tablesEnabled = @json($settings->tables_enabled ?? false);

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeOrderSystem();
    setupEventListeners();
    setupLogoutHandlers();
    setupTableSelectStyles();
    
    // Configurar listeners para botones de tipo de pedido
    document.getElementById('btn-comer-aqui').addEventListener('click', () => setOrderType('Comer aquí'));
    document.getElementById('btn-para-llevar').addEventListener('click', () => setOrderType('Para llevar'));
    document.getElementById('btn-recoger').addEventListener('click', () => setOrderType('Recoger'));
    // Mostrar el pedido actual al cargar
    updateOrderDetails();
      // Verificar si ya se procesó un pago anteriormente
    if (localStorage.getItem('paymentProcessed') === 'true') {
        paymentProcessed = true;
        lockOrderInterface();
    }
});

// Función para bloquear la interfaz de pedido
function lockOrderInterface() {
    const orderPanel = document.getElementById('order-panel');
    if (orderPanel) {
        orderPanel.classList.add('locked');
    }
    
    // Deshabilitar botones de acciones
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.disabled = true;
    });
    
    // Ocultar botones de modificación en items
    document.querySelectorAll('.item-actions').forEach(actions => {
        actions.style.display = 'none';
    });
}
/**
 * Inicializa el sistema de pedidos
 */
function initializeOrderSystem() {
    // 1. Cargar o inicializar el pedido en localStorage
    if (!localStorage.getItem('order')) {
        localStorage.setItem('order', JSON.stringify([]));
    }
    
    // 2. Cargar o establecer valores por defecto
    const defaultValues = {
        'orderType': 'Comer aquí',
        'tableNumber': document.getElementById('table-number')?.options[0]?.value || '1',
        'orderNotes': ''
    };
    
    // 3. Sincronizar localStorage con DOM
    syncLocalStorageWithDOM(defaultValues);
    
    // 4. Actualizar la vista inicial
    updateOrderDetails();
}

/**
 * Sincroniza localStorage con elementos del DOM
 */
function syncLocalStorageWithDOM(defaults) {
    // Order Type
    const orderType = localStorage.getItem('orderType') || defaults.orderType;
    document.getElementById('order-type').value = orderType;
    setOrderType(orderType); // Actualiza la UI
    
    // Table Number
    const tableNumber = localStorage.getItem('tableNumber') || defaults.tableNumber;
    const tableSelect = document.getElementById('table-number');
    if (tableSelect) {
        tableSelect.value = tableNumber;
        tableSelect.addEventListener('change', function() {
            localStorage.setItem('tableNumber', this.value);
        });
    }
    
    // Order Notes
    const orderNotes = localStorage.getItem('orderNotes') || defaults.orderNotes;
    const notesTextarea = document.getElementById('order-notes');
    if (notesTextarea) {
        notesTextarea.value = localStorage.getItem('orderNotes') || '';
        notesTextarea.addEventListener('input', function() {
            localStorage.setItem('orderNotes', this.value);
            updateNotesCounter();
        });
    }
}

/**
 * Configura los event listeners principales
 */
function setupEventListeners() {
    // Notas del pedido
    const notesTextarea = document.getElementById('order-notes');
    if (notesTextarea) {
        notesTextarea.addEventListener('input', updateNotesCounter);
    }
    
    // Ejemplos de notas
    document.querySelectorAll('.notes-examples span').forEach(span => {
        span.addEventListener('click', function() {
            insertExample(this.textContent);
        });
    });
    
    // Botones de acciones
    document.getElementById('btn-proforma').addEventListener('click', generateProforma);
    document.getElementById('btn-multiple-payment').addEventListener('click', showPaymentModal);
    document.getElementById('btn-process-order').addEventListener('click', processOrder);
}

/**
 * Configura los manejadores para el logout
 */
function setupLogoutHandlers() {
    const logoutLinks = document.querySelectorAll('a[href*="logout"], form[action*="logout"]');
    
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            clearOrderOnLogout();
            
            if (link.tagName.toLowerCase() !== 'form') {
                e.preventDefault();
                window.location.href = link.href;
            }
        });
    });

    // Limpiar al cargar la página si el usuario no está autenticado
    @if(!auth()->check())
        clearOrderOnLogout();
    @endif
}

// =============================================
// ========== FUNCIONES PRINCIPALES ===========
// =============================================

/**
 * Establece el tipo de pedido y actualiza la UI
 */
async function setOrderType(type) {
    // 1. Almacenamiento y configuración básica
    document.getElementById('order-type').value = type;
    localStorage.setItem('orderType', type);
    
    // 2. Resetear estilos de todos los botones
    const buttons = ['btn-comer-aqui', 'btn-para-llevar', 'btn-recoger'];
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        btn.className = 'w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105';
    });
    
    // 3. Obtener elementos del DOM
    const tableSelection = document.getElementById('table-selection');
    const deliverySelection = document.getElementById('delivery-selection');
    const tableSelect = document.getElementById('table-number');
    
    // 4. Manejar según el tipo de pedido
    switch(type) {
        case 'Comer aquí':
            // Aplicar estilo al botón seleccionado
            document.getElementById('btn-comer-aqui').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            
            // Mostrar selección de mesa solo si tables_enabled es true
            if (tableSelection) {
                const tablesEnabled = @json($settings->tables_enabled ?? false);
                if (tablesEnabled) {
                    tableSelection.classList.remove('hidden');
                    // Cargar mesas disponibles
                    await loadAvailableTables();
                    
                    // Guardar la mesa seleccionada si existe
                    if (tableSelect) {
                        const savedTable = localStorage.getItem('tableNumber');
                        localStorage.setItem('tableNumber', tableSelect.value);
                        if (savedTable) {
                            tableSelect.value = savedTable;
                        }
                        
                        if (!tableSelect.hasListener) {
                            tableSelect.addEventListener('change', function() {
                                localStorage.setItem('tableNumber', this.value);
                            });
                            tableSelect.hasListener = true;
                        }
                    }
                } else {
                    tableSelection.classList.add('hidden');
                }
            }
            
            deliverySelection.classList.add('hidden');
            break;
            
        case 'Para llevar':
            // Aplicar estilo al botón seleccionado
            document.getElementById('btn-para-llevar').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            
            if (tableSelection) tableSelection.classList.add('hidden');
            deliverySelection.classList.remove('hidden');
            
            // Guardar el servicio de delivery seleccionado
            const deliverySelect = document.getElementById('delivery-service');
            if (deliverySelect) {
                localStorage.setItem('deliveryService', deliverySelect.value);
                deliverySelect.addEventListener('change', function() {
                    localStorage.setItem('deliveryService', this.value);
                });
            }
            
            localStorage.removeItem('tableNumber');
            break;
            
        case 'Recoger':
            // Aplicar estilo al botón seleccionado
            document.getElementById('btn-recoger').className = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';
            
            // Ocultar ambos selectores
            if (tableSelection) tableSelection.classList.add('hidden');
            deliverySelection.classList.add('hidden');
            
            // Limpiar mesa almacenada
            localStorage.removeItem('tableNumber');
            break;
    }
    
    // 5. Efecto de transición
    const orderDetails = document.getElementById('order-details');
    if (orderDetails) {
        orderDetails.classList.add('opacity-0');
        
        setTimeout(() => {
            updateOrderDetails();
            orderDetails.classList.remove('opacity-0');
            orderDetails.classList.add('opacity-100');
        }, 200);
    }
}
function checkTablesEnabled() {
    return @json($settings->tables_enabled ?? false);
}

/**
 * Actualiza los detalles del pedido en la UI
 */
function updateOrderDetails() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderDetails = document.getElementById('order-details');
    const processOrderBtn = document.getElementById('btn-process-order');

    if (orderDetails) {
        // Limpiar el contenido actual
        orderDetails.innerHTML = '';
        if (order.length === 0) {
            // Mostrar mensaje cuando no hay ítems
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'text-center py-4 text-gray-500 italic';
            emptyMessage.textContent = 'No hay ítems en el pedido';
            orderDetails.appendChild(emptyMessage);
            return;
        }
        // Agregar cada ítem al pedido
        order.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex justify-between items-center mb-2 p-1.5 bg-gray-100 rounded-lg shadow-sm hover:shadow-md transition-shadow text-sm';
              // Agregar contenedor para acciones (solo visible si no está procesado)
        const actionsHtml = paymentProcessed ? '' : `
            <div class="item-actions">
                <button onclick="removeItem(${index})" class="text-red-600 font-bold text-sm hover:text-red-800 mr-2 transition-colors">-</button>
                <button onclick="increaseItemQuantity(${index})" class="text-green-600 font-bold text-sm hover:text-green-800 mr-2 transition-colors">+</button>
            </div>
        `;
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
        if (order.length > 0) {
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
}

/**
 * Aumenta la cantidad de un ítem
 */
function increaseItemQuantity(index) {
    const order = JSON.parse(localStorage.getItem('order')) || [];

    if (index >= 0 && index < order.length) {
        const item = order[index];
        const menuItem = document.querySelector(`[data-item-id="${item.id}"]`);
        
        if (menuItem) {
            const currentStock = parseInt(menuItem.dataset.stock) || 0;
            
            if (currentStock <= 0) {
                alert(`No hay suficiente stock para ${item.name}`);
                return;
            }
            
            // Actualizar stock visualmente
            const newStock = currentStock - 1;
            const minStock = parseInt(menuItem.dataset.minStock) || 0;
            const stockType = menuItem.dataset.stockType;
            const stockUnit = menuItem.dataset.stockUnit;
            
            updateStockBadge(item.id, newStock, minStock, stockType, stockUnit);
        }
        item.quantity += 1;

        // Actualizar el localStorage y la vista
        localStorage.setItem('order', JSON.stringify(order));
        updateOrderDetails();
    } else {
        console.error('Índice no válido:', index);
    }
}

/**
 * Elimina o disminuye la cantidad de un ítem
 */
function removeItem(index) {
    const order = JSON.parse(localStorage.getItem('order')) || [];

    if (index >= 0 && index < order.length) {
        const item = order[index];
        
         // Encontrar el elemento del menú correspondiente
        const menuItem = document.querySelector(`[data-item-id="${item.id}"]`);
        
        if (menuItem) {
            const currentStock = parseInt(menuItem.dataset.stock) || 0;
            const minStock = parseInt(menuItem.dataset.minStock) || 0;
            const stockType = menuItem.dataset.stockType;
            const stockUnit = menuItem.dataset.stockUnit;
            
            // Revertir el stock visualmente
            const newStock = currentStock + 1;
            updateStockBadge(item.id, newStock, minStock, stockType, stockUnit);
        }

        if (item.quantity > 1) {
            item.quantity -= 1;
        } else {
            order.splice(index, 1);
        }

        // Actualizar el localStorage y la vista
        localStorage.setItem('order', JSON.stringify(order));
        updateOrderDetails();
    } else {
        console.error('Índice no válido:', index);
    }
}

// =============================================
// ============ FUNCIONES DE PAGO =============
// =============================================

/**
 * Muestra el modal de pago
 */
function showPaymentModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');

    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const scrollableContainer = document.getElementById('payment-rows-scrollable'); 

    // Limpiar contenido previo
    paymentRowsContainer.innerHTML = '';
    document.querySelectorAll('.total-display').forEach(el => el.remove());
    
    // Mostrar el total del pedido
    const totalAmount = calcularTotal();
    const totalDisplay = document.createElement('div');
    totalDisplay.className = 'text-sm font-bold text-[#203363] mb-4';
    totalDisplay.innerHTML = `Total del Pedido: $${totalAmount}`;
    
    paymentRowsContainer.parentNode.insertBefore(totalDisplay, paymentRowsContainer);

    // Inicializar contador y agregar primera fila
    paymentRowCounter = 0;
    addPaymentRow();
    
    // Asegurarse de que el scroll esté desactivado inicialmente
    scrollableContainer.classList.remove('has-scroll');
}

/**
 * Cierra el modal de pago
 */
function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');
    
    // Limpiar el total display al cerrar
    const totalDisplay = document.querySelector('.total-display');
    if (totalDisplay) {
        totalDisplay.remove();
    }
}

/**
 * Agrega una fila de pago
 */
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
            <div class="select-container">
                <select class="payment-type" onchange="updatePaymentFields(this, '${paymentRow.id}')">
                    ${!existingPaymentTypes.has('Efectivo') ? '<option value="Efectivo" class="payment-option" selected>Efectivo</option>' : ''}
                    ${!existingPaymentTypes.has('QR') ? '<option value="QR" class="payment-option">QR</option>' : ''}
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

/**
 * Actualiza el contenedor de scroll
 */
function updateScrollContainer() {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const scrollableContainer = document.getElementById('payment-rows-scrollable');
    
    // Contar filas de pago existentes
    const rowCount = paymentRowsContainer.querySelectorAll('.payment-row').length;
    
    // Aplicar scroll solo si hay 2 o más filas
    if (rowCount >= 2) {
        scrollableContainer.classList.add('has-scroll');
    } else {
        scrollableContainer.classList.remove('has-scroll');
    }
}

/**
 * Actualiza los campos según el tipo de pago
 */
function updatePaymentFields(selectElement, rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const paymentRowsContainer = document.getElementById('payment-rows-container');
        const existingPaymentTypes = new Set();

        // Recorrer las filas de pago existentes para obtener los tipos de pago
        paymentRowsContainer.querySelectorAll('.payment-type').forEach(select => {
            if (select !== selectElement) {
                existingPaymentTypes.add(select.value);
            }
        });

        const selectedValue = selectElement.value;

        // Verificar si ya existe un pago del tipo seleccionado
        if (existingPaymentTypes.has(selectedValue)) {
            alert(`Ya existe un pago de tipo ${selectedValue}. Seleccione otro tipo de pago.`);
            selectElement.value = 'QR'; // Restablecer el valor por defecto
            updatePaymentIcon(selectElement, rowId);
            return;
        }

        const transactionField = row.querySelector(`#transaction-field-${rowId.split('-')[2]}`);

        // Mostrar u ocultar el campo "Nro Transacción"
        if (selectedValue === 'QR' || selectedValue === 'Tarjeta') {
            transactionField.classList.remove('hidden');
        } else {
            transactionField.classList.add('hidden');
        }

        // Actualizar el ícono del tipo de pago
        updatePaymentIcon(selectElement, rowId);
    }
}

/**
 * Actualiza el ícono del tipo de pago
 */
function updatePaymentIcon(selectElement, rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const icons = row.querySelectorAll('.payment-icon');
        icons.forEach(icon => icon.classList.add('hidden'));

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

/**
 * Elimina una fila de pago
 */
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

/**
 * Actualiza el cambio en una fila de pago
 */
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
            
            // Aplicar estilos según el cambio
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

/**
 * Actualiza el Total a Pagar en las filas posteriores
 */
function updateRemainingTotal(currentRowId) {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const paymentRows = paymentRowsContainer.querySelectorAll('.payment-row');

    let totalAmount = calcularTotal();
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
            isCurrentRowFound = true;
        }

        if (isCurrentRowFound && row.id !== currentRowId) {
            const totalAmountInput = row.querySelector('.total-amount');
            totalAmountInput.value = remainingTotal.toFixed(2);
        }
    });
}

/**
 * Valida el pago antes de procesarlo
 */
function validatePayment() {
    const paymentRows = document.querySelectorAll('.payment-row');
    let totalPaid = 0;

    paymentRows.forEach(row => {
        const totalPaidInput = row.querySelector('.total-paid');
        const paidValue = parseFloat(totalPaidInput.value);

        if (isNaN(paidValue) || paidValue <= 0) {
            alert('Por favor, ingrese un monto válido en todos los campos de "Total Pagado".');
            return false;
        }

        totalPaid += paidValue;
    });

    const totalAmount = parseFloat(calcularTotal());

    if (totalPaid < totalAmount) {
        alert(`El total pagado ($${totalPaid.toFixed(2)}) es menor al total del pedido ($${totalAmount.toFixed(2)}).`);
        return false;
    }

    return true;
}



// =============================================
// ========== FUNCIONES DE IMPRESIÓN ==========
// =============================================

/**
 * Genera el contenido del ticket
 */
function generateTicketContent(dailyOrderNumber) {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderType = document.getElementById('order-type')?.value || 'Comer aquí';
    const tableNumber = orderType === 'Comer aquí' ? 
        (document.getElementById('table-number')?.value || '1') : '';
    
    // Obtener servicio de delivery si el tipo es "Para llevar"
    const deliveryService = orderType === 'Para llevar' ? 
        (document.getElementById('delivery-service')?.value || '') : '';
    
    // Obtener todas las notas del contenedor
    const orderNotes = document.getElementById('order-notes')?.value || '';
    const proformaNotes = document.getElementById('proforma-notes')?.value || '';
    
    const customerName = document.getElementById('customer-name')?.value || '';
    const sellerName = "{{ Auth::user()->name }}";
    
    // Calcular totales
    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0;
    const total = subtotal + tax;

    // Formatear fecha y hora
    const now = new Date();
    const dateStr = `${now.getDate()}/${now.getMonth()+1}/${now.getFullYear()}`;
    const timeStr = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;

    // Combinar todas las notas si existen
    let allNotes = '';
    if (orderNotes) allNotes += `Notas del pedido: ${orderNotes}\n`;
    if (proformaNotes) allNotes += `Notas de reserva: ${proformaNotes}`;

    return `
        <div class="header">
            <div class="title">RESTAURANTE MIQUNA</div>
            <div class="subtitle">${dateStr} ${timeStr}</div>
        </div>
        <div class="divider"></div>
        
        <div class="item-row">
            <span>Vendedor:</span>
            <span>${sellerName}</span>
        </div>
        <div class="item-row">
            <span>Pedido:</span>
            <span>${dailyOrderNumber}</span>
        </div>
        <div class="divider"></div>
        
        ${orderType ? `<div class="item-row"><span>Tipo:</span><span>${orderType} ${orderType === 'Comer aquí' && tableNumber ? 'Mesa ' + tableNumber : ''}${orderType === 'Para llevar' && deliveryService ? ' - ' + deliveryService : ''}</span></div>` : ''}
        
        ${customerName ? `<div class="item-row"><span>Cliente:</span><span>${customerName}</span></div>` : ''}
        
        <div class="divider"></div>
        
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
            <span>Impuesto:</span>
            <span>$${tax.toFixed(2)}</span>
        </div>
        <div class="item-row total-row">
            <span>TOTAL:</span>
            <span>$${total.toFixed(2)}</span>
        </div>
        
        ${allNotes ? `
            <div class="divider"></div>
            <div class="notes">${allNotes}</div>
        ` : ''}
        
        <div class="divider"></div>
        <div class="footer">
            ¡Gracias por su preferencia!
        </div>
    `;
}
/**
 * Muestra la vista previa de impresión
 */
function showPrintPreview(content) {
    let previewModal = document.getElementById('print-preview-modal');
    let previewContent = document.getElementById('print-preview-content');
    
    if (!previewModal) {
        // Crear el modal dinámicamente si no existe
        previewModal = document.createElement('div');
        previewModal.id = 'print-preview-modal';
        previewModal.className = 'fixed inset-0 bg-black bg-opacity-50 hidden z-[1000] flex items-center justify-center';
        previewModal.innerHTML = `
        <div class="modal-container bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-2">
                    <h3 class="text-lg font-bold text-[#203363]">Vista previa de impresión</h3>
                </div>
            <div class="flex items-center space-x-2 bg-black">
                <button onclick="closePrintPreview()" class="bg-gray-400 text-white px-2 py-2 rounded-lg hover:bg-gray-500 text-sm">
                    Cancelar
                </button>
                <button onclick="closePrintPreview()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
    </div>
    <div id="print-preview-content" class="bg-white p-4 border border-gray-300 mb-4 max-h-[60vh] overflow-y-auto"></div>
    <div class="flex justify-end">
        <button onclick="confirmPrint()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>
</div>
        `;
        document.body.appendChild(previewModal);
    }

    if (!previewContent) {
        previewContent = document.getElementById('print-preview-content');
    }
    
    // Asignar el contenido y mostrar el modal
    previewContent.innerHTML = content;
    previewModal.classList.remove('hidden');
    previewModal.style.display = 'flex';
    
    // Bloquear el scroll del body cuando el modal está abierto
    document.body.style.overflow = 'hidden';
}

/**
 * Cierra la vista previa de impresión
 */
function closePrintPreview(confirmed = false) {
    if (typeof window.handlePrintClose === 'function') {
        window.handlePrintClose(confirmed);
    }
}

/**
 * Confirma la impresión
 */
function confirmPrint() {
    const printContent = document.getElementById('print-preview-content').innerHTML;
    
    const printWindow = window.open();
    printWindow.document.open();
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket de Venta</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    font-size: 12px;
                    width: 72mm;
                    margin: 0;
                    padding: 2mm;
                    -webkit-print-color-adjust: exact;
                }
                .header { text-align: center; margin-bottom: 3px; }
                .title { font-weight: bold; font-size: 14px; }
                .subtitle { font-size: 11px; }
                .divider { border-top: 1px dashed #000; margin: 3px 0; }
                .item-row { display: flex; justify-content: space-between; margin: 2px 0; }
                .total-row { font-weight: bold; margin-top: 4px; }
                .footer { text-align: center; margin-top: 5px; font-size: 10px; }
                .notes { 
                    margin-top: 4px; 
                    font-size: 11px;
                    white-space: pre-wrap; /* Para mantener los saltos de línea */
                }
                @page {
                    size: 72mm auto;
                    margin: 0;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body onload="window.print(); setTimeout(function(){ window.close(); }, 100);">
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    
    closePrintPreview(true);
}

/**
 * Muestra la confirmación de impresión y devuelve una promesa
 */
function showPrintConfirmation() {
    return new Promise((resolve) => {
        // Generar el contenido del ticket
        const printContent = generateTicketContent();
        
        // Mostrar el modal de vista previa
        showPrintPreview(printContent);
        
        // Configurar el manejador de cierre
        window.handlePrintClose = (confirmed) => {
            const previewModal = document.getElementById('print-preview-modal');
            if (previewModal) {
                previewModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            resolve(confirmed);
        };
    });
}

// =============================================
// ========== FUNCIONES DE PROFORMA ===========
// =============================================

/**
 * Genera una proforma
 */
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

/**
 * Cierra el modal de proforma
 */
function closeProformaModal() {
    document.getElementById('proforma-modal').classList.add('hidden');
}


/**
 * Actualiza el contador de caracteres para las notas
 */
    function updateNotesCounter() {
        const textarea = document.getElementById('order-notes');
        const counter = document.getElementById('notes-chars');
        if (textarea && counter) {
            counter.textContent = textarea.value.length;
            counter.style.color = textarea.value.length > 200 ? '#e53e3e' : '#718096';
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
    function addPaymentRow() {
            const paymentRowsContainer = document.getElementById('payment-rows-container');
            const scrollableContainer = document.getElementById('payment-rows-scrollable');
            const existingPaymentTypes = new Set();
             // Verificar si ya hay un display de total
           
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
                ${!existingPaymentTypes.has('Efectivo') ? '<option value="Efectivo" class="payment-option" selected>Efectivo</option>' : ''}
                ${!existingPaymentTypes.has('QR') ? '<option value="QR" class="payment-option">QR</option>' : ''}
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
        const scrollableContainer = document.getElementById('payment-rows-scrollable'); 

        paymentRowsContainer.innerHTML = '';
         // Eliminar TODOS los total-display existentes
        document.querySelectorAll('.total-display').forEach(el => el.remove());
         // Eliminar cualquier total-display previo (si existe)
    const existingTotalDisplay = document.querySelector('.total-display');
    if (existingTotalDisplay) {
        existingTotalDisplay.remove();
    }
        // Mostrar el total del pedido en el modal
        const totalAmount = calcularTotal();
        const totalDisplay = document.createElement('div');
        totalDisplay.className = 'text-sm font-bold text-[#203363] mb-4';
        totalDisplay.innerHTML = `Total del Pedido: $${totalAmount}`;
        
        
        //paymentRowsContainer.parentNode.insertBefore(totalDisplay, paymentRowsContainer);

        paymentRowCounter = 0;
        addPaymentRow();
        
        // Asegurarse de que el scroll esté desactivado inicialmente
       // document.getElementById('payment-rows-scrollable').classList.remove('has-scroll');
        scrollableContainer.classList.remove('has-scroll');
    }
    // Función para cerrar el modal de pago
    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.classList.add('hidden');
    
        // Limpiar el total display al cerrar
        const totalDisplay = document.querySelector('.total-display');
        if (totalDisplay) {
            totalDisplay.remove();
        }
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

     // Función para inicializar el estado predeterminado
    function initializeDefaultOrderType() {
        const defaultOrderType = 'Comer aquí';
        setOrderType(defaultOrderType); // Establecer "Comer aquí" como predeterminado
    }
    // Función para procesar el pago
    function processPayment() {
       
    // Validar el pago antes de continuar
        if (!validatePayment()) {
            return;
        }
    
        const paymentRows = document.querySelectorAll('.payment-row');
        const paymentDetails = [];
        const paymentMethods = [];

        paymentRows.forEach(row => {
            const paymentType = row.querySelector('.payment-type').value;
            const totalAmount = parseFloat(row.querySelector('.total-amount').value) || 0;
            const totalPaid = parseFloat(row.querySelector('.total-paid').value) || 0;
            const change = parseFloat(row.querySelector('.change').value) || 0;
            const transactionNumber = (paymentType === 'QR' || paymentType === 'Tarjeta') ? 
                                row.querySelector('.transaction-number').value : null;

            paymentDetails.push({
                paymentType,
                totalAmount,
                totalPaid,
                change,
                transactionNumber
            });

            paymentMethods.push({
                method: paymentType,
                amount: totalPaid,
                transaction_number: transactionNumber
            });
    });

    // Guardar en localStorage para usar en processOrder
    localStorage.setItem('paymentDetails', JSON.stringify(paymentDetails));
    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));

    // Cerrar el modal de pago
    closePaymentModal();
    paymentProcessed = true;
    localStorage.setItem('paymentProcessed', 'true');
    lockOrderInterface();
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
    
// Función para procesar el pedido
async function processOrder() {
    try {
        // Verificar stock antes de procesar
        // const stockCheck = await checkStockAvailability(order);
        // if (!stockCheck.available) {
        //     alert(`No hay suficiente stock para: ${stockCheck.itemName}`);
        //     return;
        // }
        
         // Obtener paymentMethods de localStorage
        let paymentMethods = JSON.parse(localStorage.getItem('paymentMethods')) || [];
        // Validaciones iniciales  
        const customerName = document.getElementById('customer-name')?.value;
        if (!customerName) {
            alert('El nombre del cliente es obligatorio');
            return;
        }

        const order = JSON.parse(localStorage.getItem('order')) || [];
        if (order.length === 0) {
            alert('No hay ítems en el pedido');
            return;
        }

        

        // Convertir items al formato esperado
        const orderItems = order.map(item => ({
            name: item.name,
            price: item.price,
            quantity: item.quantity
        }));
        
        if (paymentMethods.length === 0) {
            
            paymentMethods = paymentDetails.map(p => ({
                method: p.paymentType,
                amount: parseFloat(p.totalPaid) || 0,
                transaction_number: p.transactionNumber || null
            }));
            
            if (paymentMethods.length === 0) {
                alert('Debe registrar al menos un método de pago');
                return;
            }
        }

        // Obtener datos del formulario
        const orderType = localStorage.getItem('orderType') || 'Comer aquí';
        const customerEmail = document.getElementById('customer-email')?.value || '';
        const customerPhone = document.getElementById('customer-phone')?.value || '';
        const orderNotes = localStorage.getItem('orderNotes') || '';
        

        let tableNumber = '';
        if (orderType === 'Comer aquí') {
              // Solo validar mesa si tablesEnabled es true
            if (tablesEnabled) {
                tableNumber = localStorage.getItem('tableNumber') || 
                             document.getElementById('table-number')?.value || '';
            
                if (!tableNumber) {
                    throw new Error('Debe seleccionar una mesa para "Comer aquí"');
                }
                
                // Actualizar estado de la mesa solo si está habilitado
                try {
                    const result = await updateTableState(tableNumber, 'Ocupada');
                    if (!result.success) {
                        throw new Error(result.error || 'Error al actualizar estado de mesa');
                    }
                } catch (error) {
                    console.error('Error al actualizar mesa:', error);
                    throw new Error(`No se pudo ocupar la mesa. ${error.message}`);
                }
            }else{

            }
            
        }

        // Preparar datos para enviar al servidor
        const requestData = {
            order_type: orderType,
            customer_name: customerName,
            customer_email: customerEmail,
            customer_phone: customerPhone,
            table_number: tableNumber,
            order_notes: orderNotes,
            order: JSON.stringify(orderItems), 
            payment_method: paymentMethods[0]?.method || 'Efectivo',
            transaction_number: paymentMethods[0]?.transaction_number || null
        };

        // Enviar al servidor
        const response = await fetch("{{ route('sales.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || 'Error al procesar el pedido');
        }

        const data = await response.json();

        const dailyOrderNumber = data.daily_order_number;
        // Mostrar vista previa y esperar confirmación
        const printConfirmed = await showPrintConfirmation(dailyOrderNumber);

        if (!printConfirmed) {
            console.log('Impresión cancelada por el usuario');
            return;
        }

        // Éxito - limpiar y redirigir
        if (data.success) {
            localStorage.removeItem('paymentProcessed');
            paymentProcessed = false;
            localStorage.removeItem('order');
            localStorage.removeItem('orderType');
            localStorage.removeItem('tableNumber');
            localStorage.removeItem('orderNotes');
            localStorage.removeItem('customerData');
            localStorage.removeItem('paymentMethods');
            localStorage.removeItem('paymentDetails');
            
            window.location.href = "{{ route('menu.index') }}";
        } else {
            throw new Error(data.message || 'Error al procesar el pedido');
        }

    } catch (error) {
        console.error('Error en processOrder:', error);
        alert(`Error: ${error.message}`);
    }
}
async function checkStockAvailability(order) {
    try {
        const response = await fetch('/api/check-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items: order })
        });
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error al verificar stock:', error);
        return { available: false, itemName: 'Error al verificar stock' };
    }
}
// Función para mostrar confirmación de impresión
function showPrintConfirmation(dailyOrderNumber) {
    const printContent = generateTicketContent(dailyOrderNumber);
    return new Promise((resolve) => {
        // Crear el modal dinámicamente si no existe
        let previewModal = document.getElementById('print-preview-modal');
        let previewContent = document.getElementById('print-preview-content');
        
        if (!previewModal) {
            previewModal = document.createElement('div');
            previewModal.id = 'print-preview-modal';
            previewModal.className = 'fixed inset-0 bg-black bg-opacity-50 hidden z-[1000] flex items-center justify-center';
            previewModal.innerHTML = `
                <div class="modal-container bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-[#203363]">Vista previa de impresión</h3>
                        <button onclick="closePrintPreview(false)" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="print-preview-content" class="bg-white p-4 border border-gray-300 mb-4 max-h-[60vh] overflow-y-auto"></div>
                    <div class="flex justify-end space-x-2">
                        <button onclick="closePrintPreview(false)" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                            Cancelar
                        </button>
                        <button onclick="confirmPrint()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                            <i class="fas fa-print mr-2"></i> Imprimir
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(previewModal);
            previewContent = document.getElementById('print-preview-content');
        }
        
        // Obtener datos adicionales para el ticket
        const order = JSON.parse(localStorage.getItem('order')) || [];
        const orderType = document.getElementById('order-type')?.value || 'Comer aquí';
        const tableNumber = orderType === 'Comer aquí' ? 
            (document.getElementById('table-number')?.value || '1') : '';
        
        // Obtener servicio de delivery si el tipo es "Para llevar"
        const deliveryService = orderType === 'Para llevar' ? 
            (document.getElementById('delivery-service')?.value || '') : '';
            
        const orderNotes = localStorage.getItem('orderNotes') || '';
        const customerName = document.getElementById('customer-name')?.value || '';

        // Calcular totales
        const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
        const tax = 0;
        const total = subtotal + tax;
        
        const sellerName = "{{ Auth::user()->name }}";

        // Generar contenido del ticket (con servicio de delivery si aplica)
        const printContent = `
            <style>
                .ticket-preview {
                    font-family: 'Courier New', monospace;
                    font-size: 14px;
                    width: 100%;
                    max-width: 300px;
                    margin: 0 auto;
                    padding: 10px;
                    background-color: white;
                }
                .header { text-align: center; margin-bottom: 10px; }
                .title { font-weight: bold; font-size: 16px; }
                .subtitle { font-size: 14px; }
                .divider { border-top: 1px dashed #000; margin: 8px 0; }
                .item-row { display: flex; justify-content: space-between; margin: 4px 0; }
                .total-row { font-weight: bold; margin-top: 8px; }
                .footer { text-align: center; margin-top: 10px; font-size: 12px; }
                .notes { margin-top: 8px; font-style: italic; font-size: 13px; white-space: pre-wrap; }
                .customer { margin-top: 8px; font-weight: bold; font-size: 13px; }
                .sale-info { 
                    display: flex;
                    justify-content: space-between;
                    margin: 8px 0;
                    font-size: 13px;
                }
                .seller-info { font-weight: bold; }
                .sale-number { font-weight: bold; }
                .delivery-info { margin-top: 4px; font-size: 13px; }
            </style>
            <div class="ticket-preview">
                <div class="header">
                    <div class="title">RESTAURANTE MIQUNA</div>
                    <div class="subtitle">${new Date().toLocaleString()}</div>
                    ${orderType ? `<div class="subtitle">${orderType} ${orderType === 'Comer aquí' && tableNumber ? 'Mesa: ' + tableNumber : ''}</div>` : ''}
                </div>
                <div class="divider"></div>
                
                <div class="sale-info">
                    <div class="seller-info">Vendedor: ${sellerName}</div>
                    <div class="sale-number"> ${dailyOrderNumber}</div>
                </div>
                <div class="divider"></div>
                
                ${customerName ? `<div class="customer">Cliente: ${customerName}</div>` : ''}
                
                ${orderType === 'Para llevar' && deliveryService ? `
                    <div class="delivery-info">
                        <div>Servicio Delivery: ${deliveryService}</div>
                    </div>
                    <div class="divider"></div>
                ` : ''}
                
                ${order.map(item => `
                    <div class="item-row">
                        <span>${item.quantity}x ${(item.name || '').substring(0, 20)}</span>
                        <span>$${((item.price || 0) * (item.quantity || 0)).toFixed(2)}</span>
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
                    <div>
                        <div class="notes">Notas:</div>
                        <div>${orderNotes}</div>
                    </div>
                ` : ''}
                
                <div class="divider"></div>
                <div class="footer">
                    Gracias por su preferencia!
                </div>
            </div>
        `;
      
        // Asignar el contenido y mostrar el modal
        if (previewContent) {
            previewContent.innerHTML = printContent;
        }
        previewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Configurar el manejador de cierre
        window.handlePrintClose = (confirmed) => {
            previewModal.classList.add('hidden');
            document.body.style.overflow = '';
            resolve(confirmed);
        };
    });
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
    function clearOrderOnLogout() {
    // Limpiar los items del pedido
        localStorage.removeItem('order');
        localStorage.removeItem('orderType');    
    }
    // Función para cargar mesas disponibles
async function loadAvailableTables() {
    // Verificar si las mesas están habilitadas
    if (!checkTablesEnabled()) {
        console.log('La gestión de mesas está desactivada');
        return;
    }
    try {
        const response = await fetch("{{ route('tables.available') }}");
        if (!response.ok) throw new Error('Error al cargar mesas');
        
        const tables = await response.json();
        const tableSelect = document.getElementById('table-number');
        
        // Limpiar opciones existentes
        tableSelect.innerHTML = '';
        
        // Agregar opción por defecto
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Seleccione una mesa';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        tableSelect.appendChild(defaultOption);
        
        // Agregar mesas con colores según estado
        tables.data.forEach(table => {
            const option = document.createElement('option');
            option.value = table.id;
            option.textContent = `Mesa ${table.number} - ${table.state}`;
            option.dataset.state = table.state;
            
            // Asignar clase según estado
            switch(table.state) {
                case 'Disponible':
                    option.classList.add('text-green-600', 'font-medium');
                    break;
                case 'Ocupada':
                    option.classList.add('text-red-600', 'font-medium');
                    option.disabled = true; // Deshabilitar mesas ocupadas
                    break;
                case 'Reservada':
                    option.classList.add('text-yellow-600', 'font-medium');
                    option.disabled = true; // Deshabilitar mesas reservadas
                    break;
            }
            
            tableSelect.appendChild(option);
        });
        
        // Seleccionar la mesa guardada si existe
        const savedTable = localStorage.getItem('tableNumber');
        if (savedTable) {
            tableSelect.value = savedTable;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('No se pudieron cargar las mesas disponibles');
    }
}
// Función para actualizar el estado de una mesa
async function updateTableState(tableId, newState) {
    if (!tablesEnabled) {
        return { success: true };
    }
    try {
        const response = await fetch(`/tables/${tableId}/state`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ state: newState })
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Error al actualizar estado de mesa');
        }
        
        return data;
    } catch (error) {
        console.error('Error updating table state:', error);
        throw error; // Re-lanzar el error para que lo capture processOrder
    }
}
// Función para actualizar dinámicamente los colores al cambiar selección
function setupTableSelectStyles() {
    const tableSelect = document.getElementById('table-number');
    if (!tableSelect) return;
    
    // Aplicar estilo al select según la opción seleccionada
    tableSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption) {
            // Resetear clases
            this.classList.remove(
                'text-green-600', 
                'text-red-600', 
                'text-yellow-600',
                'bg-green-100',
                'bg-red-100',
                'bg-yellow-100'
            );
            
            // Aplicar clases según estado
            const state = selectedOption.dataset.state;
            if (state === 'Disponible') {
                this.classList.add('text-green-600', 'bg-green-100');
            } else if (state === 'Ocupada') {
                this.classList.add('text-red-600', 'bg-red-100');
            } else if (state === 'Reservada') {
                this.classList.add('text-yellow-600', 'bg-yellow-100');
            }
        }
    });
    
    // Disparar evento change para aplicar estilos iniciales
    tableSelect.dispatchEvent(new Event('change'));
}
// Función para agregar ítems al pedido
function addToOrder(item) {
      // Bloquear si ya se procesó el pago
    if (paymentProcessed) {
        alert('No se pueden agregar ítems después de procesar el pago');
        return;
    }
    // Convertir item.price a número si es una cadena
    item.price = parseFloat(item.price);

    // Obtener el pedido actual del localStorage
    let order = JSON.parse(localStorage.getItem('order')) || [];

    // Verificar si el ítem ya está en el pedido
    const existingItem = order.find(i => i.id === item.id);
    if (existingItem) {
        existingItem.quantity += 1; // Incrementar la cantidad si ya existe
    } else {
        item.quantity = 1; // Agregar el ítem con cantidad 1 si no existe
        order.push(item);
    }

    // Guardar el pedido actualizado en el localStorage
    localStorage.setItem('order', JSON.stringify(order));

    // Actualizar la vista de order-details
    updateOrderDetails();
    
    // Mostrar automáticamente el panel lateral si está oculto (para móviles)
    showOrderPanel();
}
// Función para mostrar el panel de pedido
function showOrderPanel() {
    const orderPanel = document.querySelector('.w-full.md\\:w-1\\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0');
    if (orderPanel) {
        orderPanel.classList.remove('hidden');
        orderPanel.classList.add('block');
    }
}
function clearOrder() {
    // Confirmar con el usuario
    if (!confirm('¿Estás seguro de que deseas limpiar todo el pedido? Esta acción no se puede deshacer.')) {
        return;
    }
    const order = JSON.parse(localStorage.getItem('order')) || [];
    // Revertir el stock de todos los ítems
    order.forEach(item => {
        const menuItem = document.querySelector(`[data-item-id="${item.id}"]`);
        if (menuItem) {
            const currentStock = parseInt(menuItem.dataset.stock) || 0;
            const minStock = parseInt(menuItem.dataset.minStock) || 0;
            const stockType = menuItem.dataset.stockType;
            const stockUnit = menuItem.dataset.stockUnit;
            
            const newStock = currentStock + item.quantity;
            updateStockBadge(item.id, newStock, minStock, stockType, stockUnit);
        }
    });
    // Limpiar el pedido del localStorage
    localStorage.setItem('order', JSON.stringify([]));
    updateOrderDetails();
    // Limpiar las notas
    const notesTextarea = document.getElementById('order-notes');
    if (notesTextarea) {
        notesTextarea.value = '';
        updateNotesCounter();
        localStorage.removeItem('orderNotes');
    }
    
    // Actualizar los detalles del pedido
    updateOrderDetails();
    
    // Mostrar mensaje de éxito
    alert('El pedido ha sido limpiado correctamente.');
}



</script>   


