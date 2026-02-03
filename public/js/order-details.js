console.log('📦 order-details.js iniciando carga...');

// ✅ Variables únicas para order-details
let paymentProcessed = false;
let currentPrintContent = '';

console.log('📋 order-details.js cargado');

// Verificar estado inicial de mesas
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        console.log('🔍 Estado inicial de mesas en order-details.js:', {
            tablesManagementEnabled: window.tablesManagementEnabled,
            tablesConfigState: window.tablesConfigState
        });
    }, 500);
});

console.log('📦 order-details.js: Funciones exportadas a window');
if (typeof updateOrderDetails === 'undefined') {
    window.updateOrderDetails = updateOrderDetails;
}

console.log('📦 order-details.js cargado COMPLETAMENTE');
console.log('   - updateOrderDetails:', typeof window.updateOrderDetails);
console.log('   - removeItem:', typeof window.removeItem);
console.log('   - increaseItemQuantity:', typeof window.increaseItemQuantity);

function checkOrderSystemReady() {
    const requiredFunctions = [
        'updateOrderDetails',
        'initializeOrderSystem',
        'removeItem',
        'increaseItemQuantity',
        'clearOrder'
    ];

    const missingFunctions = requiredFunctions.filter(fn => typeof window[fn] !== 'function');

    if (missingFunctions.length > 0) {
        console.error('❌ Funciones faltantes:', missingFunctions);
        return false;
    }

    console.log('✅ Sistema de pedidos listo');
    return true;
}
window.updateStockBadge = function (itemId, newStock, minStock, stockType, stockUnit, manageInventory = true) {
    if (!manageInventory) return;

    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    if (!itemElement) return;

    const stockBadge = itemElement.querySelector('.stock-badge');
    const addButton = itemElement.querySelector('button');
    if (!stockBadge || !addButton) return;

    itemElement.dataset.stock = newStock;

    if (stockType === 'discrete') {
        stockBadge.textContent = `${newStock} UNI`;
    } else {
        stockBadge.textContent = `${newStock} ${stockUnit.toUpperCase()}`;
    }

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

    stockBadge.classList.add('animate-pulse');
    setTimeout(() => {
        stockBadge.classList.remove('animate-pulse');
    }, 500);
};

function updateOrderDetails() {
    try {
        console.log('🔄 EJECUTANDO updateOrderDetails()');

        const order = JSON.parse(localStorage.getItem('order')) || [];
        const orderDetails = document.getElementById('order-details');
        const convertingProformaInfo = getConvertingProformaInfo();
        console.log('📦 Order items:', order.length);
        console.log('🎯 Order details element:', orderDetails);

        if (!orderDetails) {
            console.error('❌ Elemento order-details no encontrado');
            return;
        }

        orderDetails.innerHTML = '';

        if (order.length === 0) {
            orderDetails.innerHTML = `
                <div class="text-center py-4 text-gray-500 italic">
                    No hay ítems en el pedido
                </div>
            `;
            console.log('📭 Pedido vacío');
            return;
        }

        order.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex justify-between items-center mb-2 p-1.5 bg-gray-100 rounded-lg shadow-sm hover:shadow-md transition-shadow text-sm';
            itemElement.innerHTML = `
                <div class="flex items-center">
                    <button type="button" onclick="removeItem(${index})" class="text-red-600 font-bold text-sm hover:text-red-800 mr-2 transition-colors">-</button>
                    <button type="button" onclick="increaseItemQuantity(${index})" class="text-green-600 font-bold text-sm hover:text-green-800 mr-2 transition-colors">+</button>
                    <p class="text-[#203363]">${item.name} (x${item.quantity})</p>
                </div>
                <p class="text-[#203363]">$${(item.price * item.quantity).toFixed(2)}</p>
            `;
            orderDetails.appendChild(itemElement);
        });

        const subtotal = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = 0;
        const total = subtotal + tax;

        const totalsElement = document.createElement('div');
        totalsElement.className = 'mt-4 pt-4 border-t border-gray-300 text-sm';
        totalsElement.innerHTML = `
            <div class="flex justify-between items-center mb-1">
                <p class="text-gray-600">Subtotal:</p>
                <p class="text-gray-800">$${subtotal.toFixed(2)}</p>
            </div>
            <div class="flex justify-between items-center mb-1">
                <p class="text-gray-600">Impuesto:</p>
                <p class="text-gray-800">$${tax.toFixed(2)}</p>
            </div>
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-300">
                <p class="font-bold text-[#203363] text-base">Total:</p>
                <p class="font-bold text-[#203363] text-base">$${total.toFixed(2)}</p>
            </div>
        `;
        orderDetails.appendChild(totalsElement);

        console.log('✅ Order details actualizado correctamente');
    } catch (error) {
        console.error('❌ Error en updateOrderDetails:', error);
    }
}
function getConvertingProformaInfo() {
    const isConverting = localStorage.getItem('convertingProforma') === 'true';

    if (!isConverting) {
        return null;
    }

    return {
        proformaId: localStorage.getItem('proformaId'),
        notes: localStorage.getItem('proformaNotes')
    };
}
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        if (!checkOrderSystemReady()) {
            console.warn('⚠️ Recargando para inicializar sistema de pedidos...');
            setTimeout(() => location.reload(), 1000);
        }
    }, 500);
});
// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function () {
    initializeOrderSystem();
    setupEventListeners();
    setupLogoutHandlers();

    // Mostrar el pedido actual al cargar
    updateOrderDetails();

    // Verificar si ya se procesó un pago anteriormente
    if (localStorage.getItem('paymentProcessed') === 'true') {
        paymentProcessed = true;
        //lockOrderInterface();
    }
});

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function () {
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

/**
 * Inicializa el sistema de pedidos
 */


/**
 * Sincroniza localStorage con elementos del DOM
 */
function syncLocalStorageWithDOM(defaults) {
    // Order Type
    const orderType = localStorage.getItem('orderType') || defaults.orderType;
    document.getElementById('order-type').value = orderType;

    // Order Notes
    const orderNotes = localStorage.getItem('orderNotes') || defaults.orderNotes;
    const notesTextarea = document.getElementById('order-notes');
    if (notesTextarea) {
        notesTextarea.value = localStorage.getItem('orderNotes') || '';
        notesTextarea.addEventListener('input', function () {
            localStorage.setItem('orderNotes', this.value);
            updateNotesCounter();
        });
    }
}
function removeItem(index) {
    console.log('🗑️ Disminuyendo cantidad del item índice:', index);

    try {
        const order = JSON.parse(localStorage.getItem('order')) || [];

        if (index >= 0 && index < order.length) {
            const item = order[index];

            // Si la cantidad es mayor a 1, solo disminuir
            if (item.quantity > 1) {
                item.quantity -= 1;
                console.log(`📉 Cantidad reducida a: ${item.quantity}`);
            } else {
                // Si la cantidad es 1, eliminar el item completamente
                order.splice(index, 1);
                console.log('🗑️ Item eliminado completamente del pedido');
            }

            // Guardar cambios en localStorage
            localStorage.setItem('order', JSON.stringify(order));

            // Actualizar la interfaz
            if (typeof window.updateOrderDetails === 'function') {
                window.updateOrderDetails();
            } else {
                console.error('updateOrderDetails no disponible en removeItem');
                location.reload();
            }
        }
    } catch (error) {
        console.error('❌ Error en removeItem:', error);
    }
}
/**
 * Configura los event listeners principales
 */
function setupEventListeners() {
    const notesTextarea = document.getElementById('order-notes');
    if (notesTextarea) {
        // ✅ CRÍTICO: Guardar en localStorage cuando el usuario escribe
        notesTextarea.addEventListener('input', function () {
            updateNotesCounter();
            localStorage.setItem('orderNotes', this.value); // 👈 AGREGAR ESTA LÍNEA
        });
    }

    document.querySelectorAll('.notes-examples span').forEach(span => {
        span.addEventListener('click', function () {
            insertExample(this.textContent);
        });
    });

    document.getElementById('btn-proforma').addEventListener('click', generateProforma);
    document.getElementById('btn-multiple-payment').addEventListener('click', showPaymentModal);
}

// Función para bloquear la interfaz de pedido
function lockOrderInterface() {
    const orderPanel = document.getElementById('order-panel');
    if (orderPanel) {
        orderPanel.classList.add('opacity-50', 'pointer-events-none');
        orderPanel.style.transition = 'opacity 0.3s ease';
    }
}
function setupLogoutHandlers() {
    const logoutLinks = document.querySelectorAll('a[href*="logout"], form[action*="logout"]');

    logoutLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            clearOrderOnLogout();

            if (link.tagName.toLowerCase() !== 'form') {
                e.preventDefault();
                window.location.href = link.href;
            }
        });
    });
}
function unlockOrderInterface() {
    const orderPanel = document.getElementById('order-panel');
    if (orderPanel) {
        orderPanel.classList.remove('opacity-50', 'pointer-events-none');
        orderPanel.style.opacity = '1';
        orderPanel.style.pointerEvents = 'auto';
    }
}
function updateOrderPanelOpacity() {
    if (isCustomerDetailsVisible()) {
        lockOrderInterface();
    } else {
        unlockOrderInterface();
    }
}
function isCustomerDetailsVisible() {
    const mainContent = document.getElementById('main-content');
    if (!mainContent) return false;

    // Verificar si contiene elementos específicos de customer-details
    return mainContent.innerHTML.includes('customer-name') ||
        mainContent.querySelector('#customer-name') !== null ||
        mainContent.innerHTML.includes('Detalles de Pago') ||
        mainContent.querySelector('#payment-details-section') !== null;
}

function setOrderType(type) {
    console.log('🔧 Estableciendo tipo de pedido:', type);

    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) {
        orderTypeInput.value = type;
    }
    localStorage.setItem('orderType', type);

    if (type !== 'Comer aquí') {
        localStorage.removeItem('tableNumber');
        const tableSelect = document.getElementById('table-number');
        if (tableSelect) {
            tableSelect.value = '';
        }
    }

    if (type !== 'Para llevar') {
        localStorage.removeItem('deliveryService');
        const deliverySelect = document.getElementById('delivery-service');
        if (deliverySelect) {
            deliverySelect.value = '';
        }
    }

    updateOrderDetails();

    console.log('✅ Tipo de pedido establecido:', type);
}
async function showPaymentModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    console.log('🔧 Abriendo modal de pago...');
    const convertingFromProforma = localStorage.getItem('convertingProforma') === 'true';
    const proformaId = localStorage.getItem('proformaId');

    console.log('🔍 Estado al abrir modal:', {
        convertingFromProforma: convertingFromProforma,
        proformaId: proformaId
    });
    if (convertingFromProforma && !proformaId) {
        console.warn('⚠️ Limpiando banderas inconsistentes');
        clearProformaConversionFlags();
    }
    // 🔥 PRIMERO: Sincronizar estado de mesas ANTES de abrir el modal
    await syncInitialTablesState();

    // Abrir el modal
    if (typeof window.openPaymentModal === 'function') {
        window.openPaymentModal();
    } else if (typeof openPaymentModal === 'function') {
        openPaymentModal();
    } else {
        const modal = document.getElementById('payment-modal');
        if (modal) {
            modal.classList.remove('hidden');
        } else {
            alert('Error: No se puede abrir el modal de pago.');
            return;
        }
    }

    // Esperar un momento para que el modal se renderice
    await new Promise(resolve => setTimeout(resolve, 100));

    // Verificar y actualizar estado de mesas si "Comer aquí" está seleccionado
    const selectedButton = document.querySelector('.order-type-btn.selected');
    if (selectedButton && selectedButton.dataset.type === 'comer-aqui') {
        console.log('🔄 Modal abierto con tipo "Comer aquí", verificando estado de mesas...');

        // 🔥 Resetear locks antes de actualizar
        _handlingTableVisibility = false;
        _lastHandlingTime = 0;

        await handleTableSelectionVisibility(true);
    }
}

function syncOrderTypeWithModal(orderType) {
    console.log('🔄 Sincronizando tipo de pedido con modal:', orderType);

    // Mapear tipos de pedido del sistema principal al modal
    let modalType = 'comer-aqui';
    switch (orderType) {
        case 'Comer aquí':
            modalType = 'comer-aqui';
            break;
        case 'Para llevar':
            modalType = 'para-llevar';
            break;
        case 'Recoger':
            modalType = 'recoger';
            break;
    }

    // Actualizar la variable global del modal
    if (typeof window.selectedOrderType !== 'undefined') {
        window.selectedOrderType = modalType;
    }

    // Seleccionar el botón correspondiente en el modal
    document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
        btn.classList.remove('selected');
        if (btn.dataset.type === modalType) {
            btn.classList.add('selected');
        }
    });
}
// Nueva función para sincronizar botones sin efectos secundarios
function syncOrderTypeButtons(type) {
    // Resetear estilos de todos los botones del modal únicamente
    const modalButtons = [
        'modal-btn-comer-aqui', 'modal-btn-para-llevar', 'modal-btn-recoger'
    ];

    modalButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.className = 'w-full border border-[#203363] text-[#203363] px-3 py-1.5 rounded-lg hover:bg-[#203363] hover:text-white transition-colors transform hover:scale-105';
        }
    });

    // Aplicar estilo al botón seleccionado
    const selectedStyle = 'w-full bg-[#203363] text-white px-3 py-1.5 rounded-lg hover:bg-[#47517c] transition-colors transform scale-105';

    let selectedBtnId = '';
    switch (type) {
        case 'Comer aquí':
            selectedBtnId = 'modal-btn-comer-aqui';
            break;
        case 'Para llevar':
            selectedBtnId = 'modal-btn-para-llevar';
            break;
        case 'Recoger':
            selectedBtnId = 'modal-btn-recoger';
            break;
    }

    if (selectedBtnId) {
        const selectedBtn = document.getElementById(selectedBtnId);
        if (selectedBtn) {
            selectedBtn.className = selectedStyle;
        }
    }
}
function loadOrderDataInModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const total = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const totalElement = document.getElementById('order-total');
    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
}

function checkTablesEnabled() {
    return tablesEnabled;
}
/**
 * Aumenta la cantidad de un ítem
 */
function increaseItemQuantity(index) {
    console.log('➕ Aumentando cantidad índice:', index);

    try {
        const order = JSON.parse(localStorage.getItem('order')) || [];

        if (index >= 0 && index < order.length) {
            order[index].quantity += 1;
            console.log('📈 Nueva cantidad:', order[index].quantity);

            localStorage.setItem('order', JSON.stringify(order));

            if (typeof window.updateOrderDetails === 'function') {
                window.updateOrderDetails();
            } else {
                console.error('updateOrderDetails no disponible en increaseItemQuantity');
                location.reload();
            }
        }
    } catch (error) {
        console.error('Error en increaseItemQuantity:', error);
    }
}

// Función para cerrar el modal de pago
function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');
    currentStep = 1;

    // ✅ LIMPIAR window.paymentRows correctamente
    window.paymentRows = [];
    selectedTable = null;
    selectedDeliveryService = null;
    if (modal) {
        modal.classList.add('hidden');
    }

    const paymentContainer = document.getElementById('payment-rows-container');
    if (paymentContainer) {
        paymentContainer.innerHTML = '';
    }

    if (typeof window.currentStep !== 'undefined') {
        window.currentStep = 1;
    }
    if (typeof window.paymentRows !== 'undefined') {
        window.paymentRows = [];
    }
    // ✅ Limpiar flags
    window._orderTypeButtonsConfigured = false;
    window._tableVisibilityChecked = false;
    const orderWasProcessed = localStorage.getItem('orderProcessed') === 'true';
    if (!orderWasProcessed) {
        console.log('⚠️ Modal cerrado sin confirmar pedido, limpiando banderas');
        clearProformaConversionFlags();
    }

    console.log('✅ Modal cerrado y paymentRows limpiado');

    // ✅ NUEVO: Limpiar bandera para permitir reconfiguración
    window._orderTypeButtonsConfigured = false;

    console.log('✅ Modal de pago cerrado y limpiado');
}

function processPaymentFromModal() {
    console.log('💳 Iniciando proceso de pago desde modal...');

    // Validar que hay métodos de pago
    const paymentRows = document.querySelectorAll('#payment-modal .payment-row');
    if (paymentRows.length === 0) {
        alert('Debe agregar al menos un método de pago');
        return;
    }
    // Recopilar información del pedido
    const paymentDetails = [];
    const paymentMethods = [];
    let totalPaid = 0;
    let isValid = true;

    paymentRows.forEach(row => {
        const paymentType = row.querySelector('.payment-type')?.value || '';
        const totalAmount = parseFloat(row.querySelector('.total-amount')?.value) || 0;
        const totalPaidValue = parseFloat(row.querySelector('.total-paid')?.value) || 0;
        const change = parseFloat(row.querySelector('.change')?.value) || 0;
        const transactionNumber = row.querySelector('.transaction-number')?.value || '';

        if (totalPaidValue <= 0) {
            isValid = false;
            return;
        }

        totalPaid += totalPaidValue;

        paymentDetails.push({
            paymentType,
            totalAmount,
            totalPaid: totalPaidValue,
            change,
            transactionNumber: (paymentType === 'tarjeta' || paymentType === 'transferencia' || paymentType === 'qr') ? transactionNumber : null
        });

        paymentMethods.push({
            method: paymentType,
            amount: totalPaidValue,
            transaction_number: (paymentType === 'tarjeta' || paymentType === 'transferencia' || paymentType === 'qr') ? transactionNumber : null
        });
    });

    if (!isValid) {
        alert('Por favor, ingrese montos válidos en todos los métodos de pago');
        return;
    }

    // Validar que el total pagado cubra el monto del pedido
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderTotal = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    if (totalPaid < orderTotal) {
        alert(`El total pagado (${totalPaid.toFixed(2)}) es menor al total del pedido (${orderTotal.toFixed(2)})`);
        return;
    }

    // Guardar detalles del pago
    localStorage.setItem('paymentDetails', JSON.stringify(paymentDetails));
    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));
    localStorage.setItem('paymentProcessed', 'true');

    // Cerrar modal
    closePaymentModal();

    // 🔥 REMOVER lockOrderInterface() y llamar directamente a loadCustomerDetails
    if (typeof window.loadCustomerDetails === 'function') {
        window.loadCustomerDetails(paymentDetails);
    } else {
        alert('Pago procesado correctamente. Continuando con los detalles del cliente...');
    }
}

/**
 * Agrega una fila de pago
 */
function addPaymentRow() {
    console.log('➕ Agregando nueva fila de pago...');

    // ✅ ASEGURAR que window.paymentRows existe
    if (!window.paymentRows) {
        window.paymentRows = [];
        console.log('📦 window.paymentRows inicializado');
    }

    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const scrollableContainer = document.getElementById('payment-rows-scrollable');
    const existingPaymentTypes = new Set();

    // Obtener los tipos de pago existentes
    paymentRowsContainer.querySelectorAll('.payment-type').forEach(selectElement => {
        existingPaymentTypes.add(selectElement.value);
    });

    paymentRowCounter++;

    // 🔥 CREAR ID ÚNICO
    const rowId = Date.now() + paymentRowCounter;

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

    // Obtener el tipo de pedido actual
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    const isPickupOrder = orderType === 'Recoger';

    // Generar opciones de pago según el tipo de pedido
    let paymentOptions = '';
    let defaultSelected = false;

    if (isPickupOrder) {
        if (!existingPaymentTypes.has('QR')) {
            paymentOptions += `<option value="QR" class="payment-option" ${!defaultSelected ? 'selected' : ''}>QR</option>`;
            defaultSelected = true;
        }
        if (!existingPaymentTypes.has('Transferencia')) {
            paymentOptions += `<option value="Transferencia" class="payment-option" ${!defaultSelected ? 'selected' : ''}>Transferencia Bancaria</option>`;
            defaultSelected = true;
        }
    } else {
        if (!existingPaymentTypes.has('Efectivo')) {
            paymentOptions += `<option value="Efectivo" class="payment-option" ${!defaultSelected ? 'selected' : ''}>Efectivo</option>`;
            defaultSelected = true;
        }
        if (!existingPaymentTypes.has('QR')) {
            paymentOptions += `<option value="QR" class="payment-option" ${!defaultSelected ? 'selected' : ''}>QR</option>`;
            if (!defaultSelected) defaultSelected = true;
        }
        if (!existingPaymentTypes.has('Tarjeta')) {
            paymentOptions += `<option value="Tarjeta" class="payment-option" ${!defaultSelected ? 'selected' : ''}>Tarjeta</option>`;
            if (!defaultSelected) defaultSelected = true;
        }
        if (!existingPaymentTypes.has('Transferencia')) {
            paymentOptions += `<option value="Transferencia" class="payment-option" ${!defaultSelected ? 'selected' : ''}>Transferencia Bancaria</option>`;
            if (!defaultSelected) defaultSelected = true;
        }
    }

    if (!paymentOptions) {
        const availableMethods = isPickupOrder
            ? 'QR y Transferencia Bancaria'
            : 'Efectivo, QR, Tarjeta y Transferencia Bancaria';
        alert(`No hay más métodos de pago disponibles. Métodos permitidos: ${availableMethods}`);
        return;
    }

    const firstPaymentType = paymentOptions.match(/value="([^"]+)"/)[1];
    const showTransactionField = firstPaymentType === 'QR' ||
        firstPaymentType === 'Tarjeta' ||
        firstPaymentType === 'Transferencia';

    // 🔥 CREAR FILA CON data-row-id
    const paymentRow = document.createElement('div');
    paymentRow.id = `payment-row-${paymentRowCounter}`;
    paymentRow.className = 'payment-row flex flex-col space-y-4 mb-4 p-4 bg-gray-100 rounded-lg border border-gray-300 shadow-sm';

    // 🔥 CRÍTICO: AGREGAR data-row-id
    paymentRow.dataset.rowId = rowId;

    paymentRow.innerHTML = `
        <div class="flex justify-between items-center payment-row-header">
            <div class="flex items-center space-x-2 payment-icons-container">
                <span class="payment-icon hidden" data-type="QR">
                    <img src="/images/codigo-qr.png" alt="QR" class="w-5 h-5">
                </span>
                <span class="payment-icon hidden" data-type="Efectivo">
                    <img src="https://cdn-icons-png.flaticon.com/512/2704/2704714.png" alt="Efectivo" class="w-5 h-5">
                </span>
                <span class="payment-icon hidden" data-type="Tarjeta">
                    <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" alt="Tarjeta" class="w-5 h-5">
                </span>
                <span class="payment-icon hidden" data-type="Transferencia">
                    <i class="fas fa-university text-blue-600 text-lg"></i>
                </span>
            </div>
            <button onclick="removePaymentRow('${paymentRow.id}')" class="text-red-600 font-bold text-sm hover:text-red-800 transition-colors">✕</button>
        </div>
        <div class="flex-1">
            <label class="input-label">Tipo de Pago:</label>
            <div class="select-container">
                <select class="payment-type" data-row-id="${rowId}" onchange="updatePaymentFields(this, '${paymentRow.id}'); updatePaymentRowField(${rowId}, 'method', this.value);">
                    ${paymentOptions}
                </select>
            </div>
        </div>
        <div id="transaction-field-${paymentRowCounter}" class="${showTransactionField ? '' : 'hidden'}">
            <label class="block text-sm text-[#203363] font-bold mb-1">Nro Transacción:</label>
            <input type="text" 
                class="transaction-number border border-gray-300 rounded-md p-1.5 w-full focus:border-[#203363] focus:ring-2 focus:ring-[#203363] transition-colors text-sm" 
                placeholder="Ingrese el número de transacción" 
                data-row-id="${rowId}"
                onchange="updatePaymentRowField(${rowId}, 'reference', this.value);"
                ${isPickupOrder ? 'required' : ''}>
        </div>
        <div class="flex justify-between space-x-4 payment-amount-group">
            <div class="flex-1 payment-amount-input input-with-icon">
                <label class="input-label">Total a Pagar:</label>
                <input type="text" class="payment-input total-amount" value="${totalToPay.toFixed(2)}" readonly>
            </div>
            <div class="flex-1 payment-amount-input input-with-icon">
                <label class="input-label">Total Pagado:</label>
                <input type="text" 
                    class="payment-input total-paid" 
                    data-row-id="${rowId}"
                    oninput="updateChange('${paymentRow.id}'); updatePaymentRowField(${rowId}, 'amount', parseFloat(this.value) || 0);">
            </div>
            <div class="flex-1 input-with-icon payment-amount-input">
                <label class="input-label">Cambio:</label>
                <input type="text" class="payment-input change" readonly>
            </div>
        </div>
    `;

    // 🔥 AGREGAR AL ARRAY ANTES DE AGREGAR AL DOM
    const method = firstPaymentType;
    const row = {
        id: rowId,
        method: method,
        reference: '',
        amount: 0
    };

    window.paymentRows.push(row);
    console.log('✅ Fila agregada al array:', row);
    console.log('📦 Total en array:', window.paymentRows.length);

    // Agregar la nueva fila al contenedor
    paymentRowsContainer.appendChild(paymentRow);

    // Actualizar clases de scroll según cantidad de filas
    updateScrollContainer();

    // Mostrar el ícono del tipo de pago inicial
    updatePaymentIcon(paymentRow.querySelector('.payment-type'), paymentRow.id);

    // Actualizar campos según el tipo de pago seleccionado
    updatePaymentFields(paymentRow.querySelector('.payment-type'), paymentRow.id);

    console.log('✅ Fila agregada al DOM');
    console.log('🎯 Verificación:');
    console.log('   - Array:', window.paymentRows.length);
    console.log('   - DOM:', document.querySelectorAll('.payment-row').length);
}
function updateScrollContainer() {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const scrollableContainer = document.getElementById('payment-rows-scrollable');

    // Contar filas de pago existentes
    const rowCount = paymentRowsContainer.querySelectorAll('.payment-row').length;

    // Aplicar scroll solo si hay 2 o más filas
    if (rowCount >= 2) {
        scrollableContainer.classList.add('has-scroll');
    } else {
        // scrollableContainer.classList.remove('has-scroll');
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

            // Restablecer al primer valor disponible
            const firstOption = selectElement.querySelector('option:not([disabled])');
            if (firstOption) {
                selectElement.value = firstOption.value;
            }
            updatePaymentIcon(selectElement, rowId);
            return;
        }

        const transactionField = row.querySelector(`#transaction-field-${rowId.split('-')[2]}`);
        const transactionInput = transactionField ? transactionField.querySelector('.transaction-number') : null;

        // Mostrar campo de transacción para QR, Tarjeta y Transferencia
        if (selectedValue === 'QR' || selectedValue === 'Tarjeta' || selectedValue === 'Transferencia') {
            if (transactionField) {
                transactionField.classList.remove('hidden');
            }

            // Hacer obligatorio el campo si es un pedido "Recoger"
            const orderType = localStorage.getItem('orderType') || 'Comer aquí';
            if (transactionInput) {
                transactionInput.required = orderType === 'Recoger';
            }
        } else {
            if (transactionField) {
                transactionField.classList.add('hidden');
            }
            if (transactionInput) {
                transactionInput.required = false;
                transactionInput.value = ''; // Limpiar el valor
            }
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

        // Buscar el ícono por el atributo data-type
        const iconToShow = row.querySelector(`.payment-icon[data-type="${selectedValue}"]`);
        if (iconToShow) {
            iconToShow.classList.remove('hidden');
        }
    }
}
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
function updateRemainingTotalAfterRemoval(removedAmount) {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const paymentRows = paymentRowsContainer.querySelectorAll('.payment-row');

    if (paymentRows.length > 0) {
        let totalAmount = calcularTotal();
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
/**
 * Valida el pago antes de procesarlo
 */
function validatePayment() {
    const paymentRows = document.querySelectorAll('.payment-row');
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    const isPickupOrder = orderType === 'Recoger';

    let totalPaid = 0;
    const allowedMethods = isPickupOrder ? ['QR', 'Transferencia'] : ['Efectivo', 'QR', 'Tarjeta', 'Transferencia'];

    for (let row of paymentRows) {
        const paymentTypeSelect = row.querySelector('.payment-type');
        const paymentType = paymentTypeSelect ? paymentTypeSelect.value : '';
        const totalPaidInput = row.querySelector('.total-paid');
        const paidValue = parseFloat(totalPaidInput.value);
        const transactionInput = row.querySelector('.transaction-number');

        // 🔥 VALIDACIÓN ESTRICTA: Verificar métodos permitidos según tipo de pedido
        if (!allowedMethods.includes(paymentType)) {
            const methodsList = allowedMethods.join(', ');
            alert(`❌ Método de pago no permitido.\n\nPara pedidos "${orderType}" solo se permiten:\n${methodsList}`);
            paymentTypeSelect.focus();
            return false;
        }

        // Validar monto
        if (isNaN(paidValue) || paidValue <= 0) {
            alert('Por favor, ingrese un monto válido en todos los campos de "Total Pagado".');
            totalPaidInput.focus();
            return false;
        }

        // 🔥 VALIDACIÓN: Número de transacción obligatorio para pedidos "Recoger"
        if (isPickupOrder && (paymentType === 'QR' || paymentType === 'Transferencia')) {
            if (transactionInput) {
                const transactionValue = transactionInput.value.trim();
                if (!transactionValue) {
                    alert(`❌ El número de transacción es OBLIGATORIO para pagos con ${paymentType} en pedidos "Recoger"`);
                    transactionInput.focus();
                    return false;
                }

                // Validar longitud mínima del número de transacción
                if (transactionValue.length < 4) {
                    alert(`❌ El número de transacción debe tener al menos 4 caracteres`);
                    transactionInput.focus();
                    return false;
                }
            }
        }

        totalPaid += paidValue;
    }

    const totalAmount = parseFloat(calcularTotal());

    if (totalPaid < totalAmount) {
        alert(`❌ El total pagado ($${totalPaid.toFixed(2)}) es menor al total del pedido ($${totalAmount.toFixed(2)}).`);
        return false;
    }

    return true;
}
function showPickupPaymentWarning() {
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';

    // Eliminar advertencia existente primero
    const existingWarning = document.getElementById('pickup-warning');
    if (existingWarning) {
        existingWarning.remove();
    }

    if (orderType === 'Recoger') {
        const paymentSummary = document.querySelector('#payment-modal .payment-summary');
        if (paymentSummary) {
            const warningDiv = document.createElement('div');
            warningDiv.id = 'pickup-warning';
            warningDiv.style.cssText = `
                background-color: #fef3c7;
                border-left: 4px solid #f59e0b;
                padding: 12px;
                margin-bottom: 16px;
                border-radius: 6px;
                animation: slideInDown 0.4s ease-out;
            `;
            warningDiv.innerHTML = `
                <div style="display: flex; align-items: start;">
                    <i class="fas fa-exclamation-triangle" style="color: #d97706; margin-top: 2px; margin-right: 8px; font-size: 18px;"></i>
                    <div>
                        <p style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px 0;">
                            Restricción de pago para "Recoger"
                        </p>
                        <p style="font-size: 12px; color: #b45309; margin: 0; line-height: 1.4;">
                            Solo se permiten pagos mediante <strong>QR</strong> o <strong>Transferencia Bancaria</strong> para este tipo de pedido.
                        </p>
                        <p style="font-size: 11px; color: #b45309; margin: 6px 0 0 0; font-style: italic;">
                            ℹ️ El número de transacción es obligatorio
                        </p>
                    </div>
                </div>
            `;

            // Insertar después del título pero antes del total
            const summaryTitle = paymentSummary.querySelector('h3');
            if (summaryTitle) {
                summaryTitle.after(warningDiv);
            } else {
                paymentSummary.insertBefore(warningDiv, paymentSummary.firstChild);
            }
        }
    }
}
function clearPaymentRestrictions() {
    const warning = document.getElementById('pickup-warning');
    if (warning) {
        warning.remove();
    }

    // Limpiar todas las filas de pago existentes
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    if (paymentRowsContainer) {
        paymentRowsContainer.innerHTML = '';
    }

    paymentRowCounter = 0;
}
function loadCustomerDetails(paymentDetails = []) {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido. Agrega ítems antes de continuar.');
        return;
    }

    // Cambiar el contenido de la sección principal a la vista de detalles del cliente
    fetch(window.routes.customerDetails)
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

            // 🔥 AQUÍ ESTÁ EL CAMBIO PRINCIPAL: Aplicar opacidad solo cuando customer-details es visible
            setTimeout(() => {
                updateOrderPanelOpacity();
            }, 100);
        });
}

// =============================================
// ========== FUNCIONES DE IMPRESIÓN ==========
// =============================================

/**
 * Genera el contenido del ticket
 */
function generateTicketContent(dailyOrderNumber) {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';

    // Obtener información de mesa
    let tableNumber = '';
    let tableDisplayText = '';

    if (orderType === 'Comer aquí') {
        const tableId = localStorage.getItem('tableNumber');
        console.log('🔍 DEBUG Ticket - Generando ticket:', {
            orderType,
            tableId
        });

        if (tableId) {
            if (window.paymentModalState?.selectedTable?.number) {
                tableNumber = window.paymentModalState.selectedTable.number;
                tableDisplayText = `Mesa ${tableNumber}`;
                console.log('   - Table Number (paymentModalState):', tableNumber);
            } else {
                tableNumber = tableId;
                tableDisplayText = `Mesa ${tableId}`;
                console.log('   - Usando Table ID como fallback:', tableId);
            }
        } else {
            console.warn('⚠️ No se encontró tableNumber en localStorage');
            tableDisplayText = '';
        }
    }

    // Obtener servicio de delivery si el tipo es "Para llevar"
    const deliveryService = orderType === 'Para llevar' ?
        (localStorage.getItem('deliveryService') || '') : '';

    // Obtener notas de "Recoger" si el tipo es "Recoger"
    const pickupNotes = orderType === 'Recoger' ?
        (localStorage.getItem('pickupNotes') || '') : '';

    // Obtener notas generales y de proforma
    const orderNotes = localStorage.getItem('orderNotes') || '';
    const proformaNotes = localStorage.getItem('proformaNotes') || '';

    // ✅ CRÍTICO: Obtener nombre del cliente desde múltiples fuentes
    let customerName = '';

    // Opción 1: Desde el modal (Paso 3)
    customerName = document.getElementById('modal-customer-name')?.value?.trim();

    // Opción 2: Desde la vista customer-details
    if (!customerName) {
        customerName = document.getElementById('customer-name')?.value?.trim();
    }

    // Opción 3: Desde localStorage
    if (!customerName) {
        customerName = localStorage.getItem('customerName') || '';
    }

    const sellerName = window.authUserName || 'Usuario';

    // Calcular totales
    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0;
    const total = subtotal + tax;

    // Formatear fecha y hora
    const now = new Date();
    const dateStr = `${now.getDate()}/${now.getMonth() + 1}/${now.getFullYear()}`;
    const timeStr = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;

    // Combinar TODAS las notas según el tipo de pedido
    let allNotes = '';
    if (orderNotes) allNotes += `Notas del pedido: ${orderNotes}\n`;
    if (proformaNotes) allNotes += `Notas de reserva: ${proformaNotes}\n`;
    if (pickupNotes && orderType === 'Recoger') {
        allNotes += `Notas: ${pickupNotes}`;
    }

    console.log('✅ Ticket generado con:', {
        orderType,
        tableId: localStorage.getItem('tableNumber'),
        tableDisplay: tableDisplayText,
        deliveryService,
        pickupNotes: pickupNotes || 'Sin notas',
        customerName: customerName || 'Sin nombre' // ✅ LOG del cliente
    });

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
        
        ${orderType ? `
            <div class="item-row">
                <span>Tipo:</span>
                <span>${orderType}${tableDisplayText ? ' ' + tableDisplayText : ''}${deliveryService ? ' - ' + deliveryService : ''}</span>
            </div>
        ` : ''}
        
        ${customerName ? `
            <div class="item-row">
                <span>Cliente:</span>
                <span>${customerName}</span>
            </div>
        ` : ''}
        
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
            <div class="modal-container bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <!-- Header del Modal -->
                <div class="bg-[#203363] text-white px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-receipt text-xl"></i>
                        <h3 class="text-lg font-bold">Vista Previa del Ticket</h3>
                    </div>
                    <button onclick="closePrintPreview()" class="text-white hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenido del Ticket -->
                <div id="print-preview-content" class="bg-white p-6 border-y border-gray-200 max-h-[60vh] overflow-y-auto">
                    <!-- El contenido del ticket se insertará aquí -->
                </div>

                <!-- Footer con Botones -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button onclick="closePrintPreview()" class="bg-gray-400 text-white px-5 py-2.5 rounded-lg hover:bg-gray-500 transition-colors font-medium flex items-center space-x-2">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </button>
                    <button onclick="confirmPrint()" class="bg-[#203363] text-white px-5 py-2.5 rounded-lg hover:bg-[#47517c] transition-colors font-medium flex items-center space-x-2 shadow-md">
                        <i class="fas fa-print"></i>
                        <span>Imprimir</span>
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
async function getTableNumberFromId(tableId) {
    try {
        const response = await fetch(`/tables/${tableId}/status`);
        const data = await response.json();

        if (data.success && data.table) {
            return data.table.number;
        }
    } catch (error) {
        console.error('Error al obtener número de mesa:', error);
    }
    return tableId; // Fallback al ID
}
async function generateTicketContentAsync(dailyOrderNumber) {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';

    let tableNumber = '';
    let tableDisplayText = '';

    if (orderType === 'Comer aquí') {
        const tableId = localStorage.getItem('tableNumber');

        if (tableId) {
            if (window.paymentModalState?.selectedTable?.number) {
                tableNumber = window.paymentModalState.selectedTable.number;
            } else {
                tableNumber = await getTableNumberFromId(tableId);
            }

            tableDisplayText = `Mesa ${tableNumber}`;
            console.log('✅ Mesa para ticket:', tableDisplayText);
        }
    }

    const deliveryService = orderType === 'Para llevar' ?
        (localStorage.getItem('deliveryService') || '') : '';

    // Obtener notas de "Recoger"
    const pickupNotes = orderType === 'Recoger' ?
        (localStorage.getItem('pickupNotes') || '') : '';

    const orderNotes = localStorage.getItem('orderNotes') || '';
    const proformaNotes = localStorage.getItem('proformaNotes') || '';

    // ✅ CRÍTICO: Obtener nombre del cliente desde múltiples fuentes
    let customerName = '';

    // Opción 1: Desde el modal (Paso 3)
    customerName = document.getElementById('modal-customer-name')?.value?.trim();

    // Opción 2: Desde la vista customer-details
    if (!customerName) {
        customerName = document.getElementById('customer-name')?.value?.trim();
    }

    // Opción 3: Desde localStorage
    if (!customerName) {
        customerName = localStorage.getItem('customerName') || '';
    }

    const sellerName = window.authUserName || 'Usuario';

    const subtotal = order.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const tax = 0;
    const total = subtotal + tax;

    const now = new Date();
    const dateStr = `${now.getDate()}/${now.getMonth() + 1}/${now.getFullYear()}`;
    const timeStr = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;

    // Incluir notas de Recoger
    let allNotes = '';
    if (orderNotes) allNotes += `Notas del pedido: ${orderNotes}\n`;
    if (proformaNotes) allNotes += `Notas de reserva: ${proformaNotes}\n`;
    if (pickupNotes && orderType === 'Recoger') {
        allNotes += `Notas de Recoger: ${pickupNotes}`;
    }

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
        
        ${orderType ? `
            <div class="item-row">
                <span>Tipo:</span>
                <span>${orderType}${tableDisplayText ? ' ' + tableDisplayText : ''}${deliveryService ? ' - ' + deliveryService : ''}</span>
            </div>
        ` : ''}
        
        ${customerName ? `
            <div class="item-row">
                <span>Cliente:</span>
                <span>${customerName}</span>
            </div>
        ` : ''}
        
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
 * Cierra la vista previa de impresión
 */
function closePrintPreview(confirmed = false) {
    if (typeof window.handlePrintClose === 'function') {
        window.handlePrintClose(confirmed);
    }
}
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
                 {
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
async function showPrintConfirmation(dailyOrderNumber) {
    return new Promise(async (resolve) => {
        // Generar el contenido del ticket (usar versión async si quieres)
        const printContent = await generateTicketContentAsync(dailyOrderNumber);

        // O usar la versión síncrona si paymentModalState siempre tiene el número
        // const printContent = generateTicketContent(dailyOrderNumber);

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

    // 🔥 REMOVER la llamada a lockOrderInterface() de aquí
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
// Función para calcular el total del pedido
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
    const convertingFromProforma = localStorage.getItem('convertingProforma') === 'true';
    const proformaId = localStorage.getItem('proformaId');
    console.log('🔍 Estado de conversión al procesar:', {
        convertingFromProforma: convertingFromProforma,
        proformaId: proformaId,
        hasProformaId: !!proformaId
    });
    if (convertingFromProforma && !proformaId) {
        console.warn('⚠️ Inconsistencia detectada: convertingFromProforma es true pero no hay proformaId');
        localStorage.removeItem('convertingFromProforma');
        localStorage.removeItem('proformaNotes');
    }
    if (typeof syncPaymentRowsFromDOM === 'function') {
        syncPaymentRowsFromDOM();
    }
    try {
        console.log('🚀 Iniciando processOrder...');

        // ============================================
        // 1. OBTENER DATOS DEL CLIENTE
        // ============================================

        // Primero intentar desde el modal (Paso 3)
        let customerName = document.getElementById('modal-customer-name')?.value?.trim();
        let customerEmail = document.getElementById('modal-customer-email')?.value?.trim() || '';
        let customerPhone = document.getElementById('modal-customer-phone')?.value?.trim() || '';
        let customerNotes = document.getElementById('modal-customer-notes')?.value?.trim() || '';

        // Si no existe en el modal, intentar desde la vista customer-details
        if (!customerName) {
            customerName = document.getElementById('customer-name')?.value?.trim();
            customerEmail = document.getElementById('customer-email')?.value?.trim() || '';
            customerPhone = document.getElementById('customer-phone')?.value?.trim() || '';
        }

        // Fallback: intentar desde localStorage
        if (!customerName) {
            customerName = localStorage.getItem('customerName');
            customerEmail = localStorage.getItem('customerEmail') || '';
            customerPhone = localStorage.getItem('customerPhone') || '';
            customerNotes = localStorage.getItem('customerNotes') || '';
        }

        console.log('📋 Datos del cliente obtenidos:', {
            name: customerName,
            email: customerEmail,
            phone: customerPhone
        });

        // Validar nombre del cliente
        if (!customerName) {
            alert('El nombre del cliente es obligatorio');
            return;
        }

        // ============================================
        // 2. OBTENER ITEMS DEL PEDIDO
        // ============================================

        const order = JSON.parse(localStorage.getItem('order')) || [];

        if (order.length === 0) {
            alert('No hay ítems en el pedido');
            return;
        }

        console.log('📦 Items del pedido:', order.length);

        // Convertir items al formato esperado por el backend
        const orderItems = order.map(item => ({
            menu_item_id: item.id || item.menu_item_id,
            name: item.name,
            price: item.price,
            quantity: item.quantity
        }));

        // ============================================
        // 3. OBTENER MÉTODOS DE PAGO
        // ============================================

        let paymentMethods = [];

        // Opción 1: Desde window.paymentRows (modal de 3 pasos)
        if (window.paymentRows && window.paymentRows.length > 0) {
            console.log('✅ Obteniendo métodos de pago desde window.paymentRows');
            paymentMethods = window.paymentRows.map(row => ({
                method: row.method,
                amount: parseFloat(row.amount) || 0,
                transaction_number: row.reference || null
            }));
        }
        // Opción 2: Desde localStorage
        else if (localStorage.getItem('paymentMethods')) {
            console.log('✅ Obteniendo métodos de pago desde localStorage');
            paymentMethods = JSON.parse(localStorage.getItem('paymentMethods'));
        }
        // Opción 3: Desde paymentDetails (modal antiguo)
        else if (localStorage.getItem('paymentDetails')) {
            console.log('✅ Obteniendo métodos de pago desde paymentDetails');
            const paymentDetails = JSON.parse(localStorage.getItem('paymentDetails'));
            paymentMethods = paymentDetails.map(p => ({
                method: p.paymentType,
                amount: parseFloat(p.totalPaid) || 0,
                transaction_number: p.transactionNumber || null
            }));
        }

        console.log('💳 Métodos de pago obtenidos:', paymentMethods);

        // Validar que existan métodos de pago
        if (!paymentMethods || paymentMethods.length === 0) {
            alert('Debe registrar al menos un método de pago');
            console.error('❌ No se encontraron métodos de pago');
            return;
        }

        // Validar que los montos sean válidos
        const totalPaid = paymentMethods.reduce((sum, method) => sum + (method.amount || 0), 0);
        if (totalPaid <= 0) {
            alert('Los montos de pago deben ser mayores a 0');
            return;
        }

        // ============================================
        // 4. OBTENER TIPO DE PEDIDO Y DETALLES
        // ============================================

        const orderType = localStorage.getItem('orderType') || 'Comer aquí';
        const orderNotes = localStorage.getItem('orderNotes') || '';

        console.log('📝 Tipo de pedido:', orderType);

        // ============================================
        // 5. 🔥 OBTENER ID DE PROFORMA SI ESTÁ CONVIRTIENDO
        // ============================================



        console.log('🔍 Datos de conversión de proforma:', {
            isConverting: convertingFromProforma,
            proformaId: proformaId
        });

        // ============================================
        // 6. OBTENER MESA (SI APLICA)
        // ============================================

        let tableNumber = null;

        if (orderType === 'Comer aquí') {
            // ✅ VERIFICAR SI LAS MESAS ESTÁN HABILITADAS
            const tablesEnabled = window.tablesConfigState?.tablesEnabled ||
                window.tablesManagementEnabled ||
                false;

            console.log('🪑 Estado de mesas:', {
                tablesEnabled: tablesEnabled,
                orderType: orderType
            });

            if (tablesEnabled) {
                // Intentar obtener desde diferentes fuentes
                tableNumber = localStorage.getItem('tableNumber');

                // Fallback: desde window.selectedTable (modal)
                if (!tableNumber && window.selectedTable?.id) {
                    tableNumber = window.selectedTable.id;
                    console.log('📍 Mesa obtenida desde window.selectedTable:', tableNumber);
                }

                // Fallback: desde paymentModalState
                if (!tableNumber && window.paymentModalState?.selectedTable?.id) {
                    tableNumber = window.paymentModalState.selectedTable.id;
                    console.log('📍 Mesa obtenida desde paymentModalState:', tableNumber);
                }

                console.log('🪑 Mesa seleccionada:', tableNumber);

                // Validar que se haya seleccionado una mesa SOLO si están habilitadas
                if (!tableNumber) {
                    throw new Error('Debe seleccionar una mesa para "Comer aquí"');
                }

                // Actualizar estado de la mesa a "Ocupada"
                try {
                    const result = await updateTableState(tableNumber, 'Ocupada');
                    if (!result.success) {
                        throw new Error(result.error || 'Error al actualizar estado de mesa');
                    }
                    console.log('✅ Estado de mesa actualizado correctamente');
                } catch (error) {
                    console.error('Error al actualizar mesa:', error);
                    throw new Error(`No se pudo ocupar la mesa. ${error.message}`);
                }
            } else {
                // ✅ Mesas deshabilitadas - permitir continuar sin mesa
                console.log('⚠️ Mesas deshabilitadas - continuando sin asignación de mesa');
                tableNumber = null;
            }
        }

        // ============================================
        // 7. OBTENER SERVICIO DE DELIVERY (SI APLICA)
        // ============================================

        let deliveryService = null;
        if (orderType === 'Recojo por Delivery' || orderType === 'Para llevar') {
            deliveryService = localStorage.getItem('deliveryService') ||
                window.selectedDeliveryService || null;
            console.log('🚚 Servicio de delivery:', deliveryService);
        }

        // ============================================
        // 8. OBTENER NOTAS DE RECOGER (SI APLICA)
        // ============================================

        let pickupNotes = '';
        if (orderType === 'Recoger') {
            pickupNotes = localStorage.getItem('pickupNotes') ||
                document.getElementById('modal-pickup-notes-text')?.value?.trim() || '';
            console.log('📝 Notas de recoger:', pickupNotes);
        }

        // ============================================
        // 9. 🔥 PREPARAR DATOS PARA EL BACKEND (INCLUIR PROFORMA ID)
        // ============================================

        const requestData = {
            // Datos del cliente
            customer_name: customerName,
            customer_email: customerEmail || null,
            customer_phone: customerPhone || null,

            // Tipo de pedido y ubicación
            order_type: orderType,
            table_number: tableNumber,
            delivery_service: deliveryService,

            // Notas
            order_notes: orderNotes,
            customer_notes: customerNotes,
            pickup_notes: pickupNotes,

            // Items del pedido (como JSON string)
            order: JSON.stringify(orderItems),

            // Método de pago principal (el primero)
            payment_method: paymentMethods[0]?.method || 'Efectivo',
            transaction_number: paymentMethods[0]?.transaction_number || null,

            // Todos los métodos de pago (para registro completo)
            payment_methods: paymentMethods,

            // 🔥 CRÍTICO: Agregar ID de proforma si está convirtiendo
            converting_from_proforma: (convertingFromProforma && proformaId) ? parseInt(proformaId) : null
        };
        console.log('📤 Datos a enviar (incluyendo proforma):', {
            converting_from_proforma: requestData.converting_from_proforma,
            convertingFromProforma: convertingFromProforma,
            proformaId: proformaId
        });
        console.log('📤 Datos a enviar al servidor (incluyendo proforma):', requestData);

        // ============================================
        // 10. ENVIAR AL SERVIDOR
        // ============================================

        const response = await fetch(window.routes.salesStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `Error HTTP: ${response.status}`);
        }

        const data = await response.json();

        console.log('✅ Respuesta del servidor:', data);

        // ============================================
        // 11. MANEJAR RESPUESTA EXITOSA
        // ============================================

        if (data.success) {
            const dailyOrderNumber = data.daily_order_number;
            const orderId = data.order_id;

            // Mostrar vista previa de impresión
            const printConfirmed = await showPrintConfirmation(dailyOrderNumber);

            if (!printConfirmed) {
                console.log('ℹ️ Impresión cancelada por el usuario');
            }

            // Limpiar todos los datos
            unlockOrderInterface();
            localStorage.removeItem('paymentProcessed');
            localStorage.removeItem('order');
            localStorage.removeItem('orderType');
            localStorage.removeItem('tableNumber');
            localStorage.removeItem('orderNotes');
            localStorage.removeItem('customerData');
            localStorage.removeItem('customerName');
            localStorage.removeItem('customerEmail');
            localStorage.removeItem('customerPhone');
            localStorage.removeItem('customerNotes');
            localStorage.removeItem('paymentMethods');
            localStorage.removeItem('paymentDetails');
            localStorage.removeItem('deliveryService');
            localStorage.removeItem('pickupNotes');

            localStorage.removeItem('convertingProforma');
            localStorage.removeItem('proformaId');
            localStorage.removeItem('proformaNotes');
            console.log('🧹 Datos de conversión de proforma limpiados');
            // 🔥 IMPORTANTE: Limpiar datos de conversión de proforma
            // if (convertingFromProforma) {
            //     localStorage.removeItem('convertingProforma');
            //     localStorage.removeItem('proformaId');
            //     localStorage.removeItem('proformaNotes');
            //     console.log('🧹 Datos de conversión de proforma limpiados');
            // }

            // Limpiar variables globales
            if (window.paymentRows) {
                window.paymentRows = [];
            }
            if (window.selectedTable) {
                window.selectedTable = null;
            }
            if (window.selectedDeliveryService) {
                window.selectedDeliveryService = null;
            }

            // Cerrar modal si está abierto
            const paymentModal = document.getElementById('payment-modal');
            if (paymentModal && !paymentModal.classList.contains('hidden')) {
                paymentModal.classList.add('hidden');
            }

            // Redirigir al menú
            console.log('✅ Pedido procesado correctamente, redirigiendo...');
            window.location.href = window.routes.menuIndex;

        } else {
            throw new Error(data.message || 'Error al procesar el pedido');
        }

    } catch (error) {
        console.error('❌ Error en processOrder:', error);
        // 🔥 En caso de error, limpiar banderas de conversión
        localStorage.removeItem('convertingProforma');
        localStorage.removeItem('proformaId');
        localStorage.removeItem('proformaNotes');

        alert(`Error al procesar el pedido:\n${error.message}`);

        // Rehabilitar interfaz si hay error
        const confirmBtn = document.querySelector('.step-btn.confirm');
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Pedido';
        }
    }
}
async function checkStockAvailability(order) {
    try {
        const response = await fetch('/api/check-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
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
async function saveProforma(event) {
    event.preventDefault();

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

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('No se encontró el token CSRF');
        }

        const proformaData = {
            customer_name: formData.get('customer_name'),
            customer_phone: formData.get('customer_phone') || null, // ✅ Usar null si no existe
            notes: formData.get('notes'),
            order_type: orderType,
            items: order,
            subtotal: order.reduce((sum, item) => sum + item.price * item.quantity, 0),
            tax: 0,
            total: order.reduce((sum, item) => sum + item.price * item.quantity, 0),
            status: 'reservado'
        };

        console.log('Enviando datos:', proformaData);

        const response = await fetch('/proformas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(proformaData)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `Error HTTP: ${response.status}`);
        }

        const data = await response.json();

        // alert('Reserva guardada correctamente con ID: ' + data.id);
        closeProformaModal();

        // Opcional: Limpiar el formulario
        document.getElementById('proforma-form').reset();

    } catch (error) {
        console.error('Error al guardar proforma:', error);
        alert('Error al guardar la reserva: ' + error.message);
    } finally {
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
        const response = await fetch(window.routes.tablesAvailable);
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
            switch (table.state) {
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
    const tablesEnabled = window.tablesConfigState?.tablesEnabled || window.tablesEnabled || false;

    if (!tablesEnabled) {
        console.log('ℹ️ Mesas deshabilitadas, saltando actualización de estado');
        return { success: true, message: 'Mesas deshabilitadas' };
    }
    // ✅ Si tableId es null o vacío, devolver éxito
    if (!tableId) {
        console.log('ℹ️ No hay tableId, saltando actualización');
        return { success: true, message: 'Sin mesa asignada' };
    }

    try {
        console.log(`🔄 Actualizando mesa ${tableId} a estado: ${newState}`);

        const response = await fetch(`/tables/${tableId}/state`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ state: newState })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Error al actualizar estado de mesa');
        }

        console.log('✅ Mesa actualizada correctamente');
        return data;

    } catch (error) {
        console.error('❌ Error updating table state:', error);
        throw error;
    }
}
function setupTableSelectStyles() {
    const tableSelect = document.getElementById('table-number');
    if (!tableSelect) return;

    // Aplicar estilo al select según la opción seleccionada
    tableSelect.addEventListener('change', function () {
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
// Función para mostrar el panel de pedido
function showOrderPanel() {
    const orderPanel = document.querySelector('.w-full.md\\:w-1\\/5.bg-white.p-4.rounded-lg.shadow-lg.fixed.right-0.top-0');
    if (orderPanel) {
        orderPanel.classList.remove('hidden');
        orderPanel.classList.add('block');
    }
}
function clearOrder() {
    console.log('🧹 Limpiando todo el pedido');

    if (!confirm('¿Estás seguro de que deseas limpiar todo el pedido?')) {
        return;
    }

    localStorage.setItem('order', JSON.stringify([]));

    // Llamar a updateOrderDetails de forma segura
    if (typeof window.updateOrderDetails === 'function') {
        window.updateOrderDetails();
    } else {
        console.error('updateOrderDetails no disponible en clearOrder');
        location.reload();
    }

    alert('Pedido limpiado correctamente');
}
/**
 * Función para cambiar la disponibilidad de una mesa
 */
async function changeTableAvailability() {
    try {
        const tableSelect = document.getElementById('table-number');
        const tableId = tableSelect.value;
        const button = document.getElementById('change-table-availability');

        if (!tableId) {
            alert('Por favor, seleccione una mesa primero');
            return;
        }

        // Mostrar estado de carga
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        button.disabled = true;

        // Realizar la petición al servidor
        const response = await fetch(`/tables/${tableId}/change-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Actualizar la interfaz
            updateTableStateIndicator(data.new_state);

            // Actualizar el texto del option en el select
            const option = tableSelect.options[tableSelect.selectedIndex];
            option.text = `Mesa ${option.text.split(' - ')[0].split(' ')[1]} - ${data.new_state}`;
            option.dataset.state = data.new_state;

            // Mostrar mensaje de éxito
            alert(data.message);
        } else {
            throw new Error(data.message);
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    } finally {
        // Restaurar el botón
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sync-alt mr-2"></i><span id="availability-text">Cambiar Disponibilidad</span>';
        }, 500);
    }
}
/**
 * Actualizar el indicador de estado de la mesa
 */
function updateTableStateIndicator(state) {
    const stateElement = document.getElementById('current-state');
    const indicator = document.getElementById('table-state-indicator');

    if (stateElement && indicator) {
        stateElement.textContent = state;

        // Remover clases anteriores
        indicator.classList.remove(
            'state-available',
            'state-unavailable',
            'state-occupied',
            'state-reserved'
        );

        // Agregar clase según el estado
        switch (state) {
            case 'Disponible':
                indicator.classList.add('state-available');
                break;
            case 'No Disponible':
                indicator.classList.add('state-unavailable');
                break;
            case 'Ocupada':
                indicator.classList.add('state-occupied');
                break;
            case 'Reservada':
                indicator.classList.add('state-reserved');
                break;
        }
    }
}
/**
 * Cargar el estado de la mesa seleccionada
 */
async function loadTableState() {
    try {
        const tableSelect = document.getElementById('table-number');
        const tableId = tableSelect.value;

        if (!tableId) return;

        const response = await fetch(`/tables/${tableId}/status`);
        const data = await response.json();

        if (data.success) {
            updateTableStateIndicator(data.state);
        }
    } catch (error) {
        console.error('Error al cargar estado de mesa:', error);
    }
}
async function changeAllTablesAvailability() {
    try {
        const stateSelector = document.getElementById('bulk-state-selector');
        const newState = stateSelector.value;
        const button = document.getElementById('change-all-tables-availability');

        if (!newState) {
            // alert('Por favor, seleccione un estado');
            return;
        }

        // Confirmar la acción
        const confirmMessage = `¿Está seguro de que desea cambiar TODAS las mesas al estado "${newState}"?`;
        if (!confirm(confirmMessage)) {
            return;
        }

        // Mostrar estado de carga
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        button.disabled = true;

        // Realizar la petición al servidor para cambiar todas las mesas
        const response = await fetch('/tables/bulk-change-state', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                state: newState
            })
        });

        const data = await response.json();

        if (data.success) {
            // Actualizar todas las opciones del select de mesas
            updateAllTableOptions(newState);

            // Actualizar el indicador de estado si hay una mesa seleccionada
            updateTableStateIndicator(newState);

            // Mostrar mensaje de éxito
            alert(`${data.updated_count} mesa(s) actualizadas al estado "${newState}"`);

            // Recargar las mesas disponibles
            await loadAvailableTables();

        } else {
            throw new Error(data.message || 'Error al actualizar las mesas');
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    } finally {
        // Restaurar el botón
        setTimeout(() => {
            const button = document.getElementById('change-all-tables-availability');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sync-alt mr-2"></i><span id="bulk-availability-text">Cambiar Estado de Todas las Mesas</span>';
        }, 500);
    }
}
// Función auxiliar para actualizar todas las opciones del select
function updateAllTableOptions(newState) {
    const tableSelect = document.getElementById('table-number');
    if (!tableSelect) return;

    // Actualizar todas las opciones excepto la primera (que es el placeholder)
    for (let i = 1; i < tableSelect.options.length; i++) {
        const option = tableSelect.options[i];
        const tableNumber = option.text.split(' - ')[0].split(' ')[1];

        option.text = `Mesa ${tableNumber} - ${newState}`;
        option.dataset.state = newState;

        // Habilitar o deshabilitar según el estado
        if (newState === 'Ocupada' || newState === 'Reservada') {
            option.disabled = true;
        } else {
            option.disabled = false;
        }

        // Actualizar clases CSS
        option.classList.remove('text-green-600', 'text-red-600', 'text-yellow-600', 'font-medium');

        switch (newState) {
            case 'Disponible':
                option.classList.add('text-green-600', 'font-medium');
                break;
            case 'Ocupada':
            case 'No Disponible':
                option.classList.add('text-red-600', 'font-medium');
                break;
            case 'Reservada':
                option.classList.add('text-yellow-600', 'font-medium');
                break;
        }
    }
}
function initializePaymentModal() {
    console.log('🔧 Inicializando modal de pago...');

    // Configurar botones de tipo de pedido en el modal
    const orderTypeButtons = document.querySelectorAll('#payment-modal .order-type-btn');

    orderTypeButtons.forEach(btn => {
        // Remover listeners anteriores clonando el elemento
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        // Agregar el nuevo listener
        newBtn.addEventListener('click', function () {
            handleOrderTypeChange(this);
        });
    });

    // Configurar navegación por steps
    const stepItems = document.querySelectorAll('#payment-modal .step-item');
    stepItems.forEach(item => {
        const newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);

        newItem.addEventListener('click', function () {
            const step = parseInt(this.getAttribute('data-step'));
            if (window.goToStep) {
                window.goToStep(step);
            }
        });
    });

    // Sincronizar estado inicial
    const currentOrderType = localStorage.getItem('orderType') || 'Comer aquí';
    syncOrderTypeWithModal(currentOrderType);

    // Actualizar visibilidad inicial
    if (typeof window.updateModalSectionsVisibility === 'function') {
        window.updateModalSectionsVisibility();
    }

    console.log('✅ Modal de pago inicializado correctamente');
}
function handleOrderTypeChange(btnElement) {
    console.log('📝 Cambiando tipo de pedido en modal...');

    // Deseleccionar botón anterior
    document.querySelectorAll('#payment-modal .order-type-btn').forEach(b => {
        b.classList.remove('selected');
    });

    // Seleccionar nuevo botón
    btnElement.classList.add('selected');
    const selectedType = btnElement.dataset.type;

    // Actualizar variable global del modal
    if (typeof window.selectedOrderType !== 'undefined') {
        window.selectedOrderType = selectedType;
    }

    console.log('📋 Tipo seleccionado:', selectedType);

    // Convertir el tipo a formato del sistema principal
    let orderTypeName = '';
    switch (selectedType) {
        case 'comer-aqui':
            orderTypeName = 'Comer aquí';
            break;
        case 'para-llevar':
            orderTypeName = 'Para llevar';
            break;
        case 'recoger':
            orderTypeName = 'Recoger';
            break;
    }

    // Actualizar el sistema principal (sin efectos secundarios)
    updateOrderTypeWithoutSideEffects(orderTypeName);

    // Actualizar visibilidad de secciones del modal
    if (typeof window.updateModalSectionsVisibility === 'function') {
        window.updateModalSectionsVisibility();
    }
}
function updateOrderTypeWithoutSideEffects(orderType) {
    // Solo actualizar localStorage y el input oculto
    localStorage.setItem('orderType', orderType);

    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) {
        orderTypeInput.value = orderType;
    }

    // Limpiar datos irrelevantes según el tipo
    if (orderType !== 'Comer aquí') {
        localStorage.removeItem('tableNumber');
    }

    if (orderType !== 'Para llevar') {
        localStorage.removeItem('deliveryService');
    }

    console.log('✅ Tipo de pedido actualizado:', orderType);
}

// NUEVA FUNCIÓN: Actualizar visibilidad de mesas en el modal
function updateModalTableVisibility(orderType) {
    const tableSelection = document.getElementById('table-selection');
    if (!tableSelection) return;

    console.log('🔄 Actualizando visibilidad de mesas en modal, tipo:', orderType);

    if (orderType === 'comer-aqui') {
        console.log('✅ Mostrando selección de mesas en modal');
        tableSelection.classList.remove('hidden');
        loadModalTables();
    } else {
        console.log('❌ Ocultando selección de mesas en modal');
        tableSelection.classList.add('hidden');
        // Limpiar selección
        document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
            btn.classList.remove('selected');
        });
    }
}
function updateModalSectionsVisibility() {

    const tableSelection = document.getElementById('modal-table-selection');
    const deliverySelection = document.getElementById('modal-delivery-selection');
    const pickupNotes = document.getElementById('modal-pickup-notes');

    console.log('🔄 Actualizando visibilidad en modal, tipo:', window.paymentModalState.selectedOrderType);

    // Ocultar todas las secciones primero
    if (tableSelection) tableSelection.classList.add('hidden');
    if (deliverySelection) deliverySelection.classList.add('hidden');
    if (pickupNotes) pickupNotes.classList.add('hidden');

    // Mostrar secciones según el tipo de pedido
    switch (window.paymentModalState.selectedOrderType) {
        case 'comer-aqui':
            if (tableSelection) {
                console.log('✅ Mostrando selección de mesas en modal');
                tableSelection.classList.remove('hidden');

                _handlingTableVisibility = false;
                _lastHandlingTime = 0;
                handleTableSelectionVisibility(true);
            }
            break;

        case 'para-llevar':
            console.log('Para llevar');
            if (tableSelection) tableSelection.classList.add('hidden');
            if (deliverySelection) {
                deliverySelection.classList.remove('hidden');
                loadDeliveryServices();
            }
            break;

        case 'recoger':
            if (tableSelection) tableSelection.classList.add('hidden');
            if (pickupNotes) {
                console.log('✅ Mostrando notas para recoger en modal');
                pickupNotes.classList.remove('hidden');
                loadPickupNotes();
            }
            break;
    }
}
async function loadDeliveryServices() {
    const deliverySelect = document.getElementById('modal-delivery-service');
    if (!deliverySelect) {
        console.error('❌ No se encontró el select de delivery en el modal');
        return;
    }

    console.log('🚚 Cargando servicios de delivery desde la base de datos...');

    // Mostrar loader mientras carga
    deliverySelect.innerHTML = '<option value="">Cargando servicios...</option>';
    deliverySelect.disabled = true;

    try {
        // Hacer petición a la API
        const response = await fetch('/api/delivery-services', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success || !data.data) {
            throw new Error(data.message || 'No se pudieron obtener los servicios');
        }

        const deliveryServices = data.data;
        console.log('✅ Servicios obtenidos:', deliveryServices);

        // Limpiar y agregar opción por defecto
        deliverySelect.innerHTML = '<option value="">Seleccione un servicio de delivery</option>';

        // Verificar si hay servicios disponibles
        if (deliveryServices.length === 0) {
            deliverySelect.innerHTML = '<option value="">No hay servicios disponibles</option>';
            console.warn('⚠️ No se encontraron servicios de delivery activos');
            return;
        }

        // Agregar opciones de servicios
        deliveryServices.forEach(service => {
            const option = document.createElement('option');
            option.value = service.name;
            option.textContent = service.name;

            // Agregar descripción como tooltip si existe
            if (service.description) {
                option.title = service.description;
            }

            deliverySelect.appendChild(option);
        });

        // Habilitar el select
        deliverySelect.disabled = false;

        // Seleccionar el servicio guardado previamente si existe
        const savedService = localStorage.getItem('deliveryService');
        if (savedService) {
            const optionExists = deliverySelect.querySelector(`option[value="${savedService}"]`);
            if (optionExists) {
                deliverySelect.value = savedService;
                console.log('📋 Servicio de delivery restaurado:', savedService);
            } else {
                console.warn('⚠️ Servicio guardado no encontrado:', savedService);
                localStorage.removeItem('deliveryService');
            }
        }

        // Configurar evento change (reemplazar elemento para evitar múltiples listeners)
        const newDeliverySelect = deliverySelect.cloneNode(true);
        deliverySelect.parentNode.replaceChild(newDeliverySelect, deliverySelect);

        newDeliverySelect.addEventListener('change', function () {
            const selectedService = this.value;
            console.log('🚚 Servicio de delivery seleccionado:', selectedService);

            // Guardar en localStorage
            if (selectedService) {
                localStorage.setItem('deliveryService', selectedService);
            } else {
                localStorage.removeItem('deliveryService');
            }

            // Sincronizar con el sistema principal si existe
            const mainDeliverySelect = document.getElementById('delivery-service');
            if (mainDeliverySelect) {
                mainDeliverySelect.value = selectedService;
            }

            // Actualizar detalles del pedido
            if (typeof window.updateOrderDetails === 'function') {
                window.updateOrderDetails();
            }
        });

        console.log('✅ Servicios de delivery cargados correctamente');

    } catch (error) {
        console.error('❌ Error al cargar servicios de delivery:', error);

        // Mostrar error en el select
        deliverySelect.innerHTML = '<option value="">Error al cargar servicios</option>';
        deliverySelect.disabled = true;

        // Mostrar alerta al usuario
        alert('Error al cargar los servicios de delivery. Por favor, recargue la página.');
    }
}
function validateStep1() {
    const selectedBtn = document.querySelector('#payment-modal .order-type-btn.selected');
    if (!selectedBtn) {
        alert('Por favor, selecciona un tipo de pedido');
        return false;
    }

    const orderType = selectedBtn.dataset.type;

    // Validaciones específicas según el tipo de pedido
    switch (orderType) {
        case 'comer-aqui':
            // ✅ VERIFICAR SI LAS MESAS ESTÁN HABILITADAS ANTES DE VALIDAR
            const tablesEnabled = window.tablesConfigState?.tablesEnabled ||
                window.tablesManagementEnabled ||
                false;

            console.log('🔍 validateStep1 - Estado de mesas:', {
                tablesEnabled: tablesEnabled,
                orderType: 'comer-aqui'
            });

            // Solo validar mesa si las mesas están habilitadas
            if (tablesEnabled) {
                const selectedTable = document.querySelector('#payment-modal .table-btn.selected');
                if (!selectedTable) {
                    alert('Por favor, selecciona una mesa para "Comer aquí"');
                    return false;
                }
                console.log('✅ Mesa seleccionada:', selectedTable.dataset.tableId);
            } else {
                console.log('⚠️ Mesas deshabilitadas - omitiendo validación de mesa en validateStep1');
            }
            break;

        case 'para-llevar':
            // Validar que se haya seleccionado un servicio de delivery
            const deliverySelect = document.getElementById('modal-delivery-service');
            if (!deliverySelect || !deliverySelect.value) {
                alert('Por favor, selecciona un servicio de delivery para "Para llevar"');
                return false;
            }
            break;

        case 'recoger':
            // No hay validaciones adicionales para "Recoger"
            console.log('✅ Tipo "Recoger" - sin validaciones adicionales');
            break;

        default:
            alert('Tipo de pedido no válido');
            return false;
    }

    return true;
}

async function loadModalTables(forceReload = false) {
    const tableGrid = document.getElementById('table-grid');
    const loadingElement = document.getElementById('table-loading');
    const errorElement = document.getElementById('table-error');
    const errorMessage = document.getElementById('table-error-message');
    // ✅ Si ya se cargaron y no es forzado, saltar
    if (window._modalTablesLoaded && !forceReload) {
        console.log('ℹ️ Mesas ya cargadas, usando caché');
        return;
    }
    // ✅ Si ya se está cargando, saltar
    if (window._modalTablesLoading) {
        console.log('⏸️ Ya se están cargando las mesas, saltando...');
        return;
    }
    if (!tableGrid) {
        console.error('❌ No se encontró table-grid');
        return;
    }

    // Mostrar loading
    if (loadingElement) loadingElement.classList.remove('hidden');
    if (errorElement) errorElement.classList.add('hidden');
    window._modalTablesLoading = true;
    if (!tableGrid) {
        console.error('❌ No se encontró table-grid');
        window._modalTablesLoading = false;
        return;
    }

    // Mostrar loading
    if (loadingElement) loadingElement.classList.remove('hidden');
    if (errorElement) errorElement.classList.add('hidden');
    tableGrid.innerHTML = '';
    tableGrid.innerHTML = '';

    try {
        console.log('🔄 Cargando mesas desde el servidor para el modal...');

        // Usar la misma función que ya existe en el sistema
        const response = await fetch(window.routes.tablesAvailable);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success || !data.data) {
            throw new Error(data.message || 'No se pudieron obtener las mesas');
        }

        const tables = data.data;
        console.log('✅ Mesas obtenidas para modal:', tables);

        // Limpiar el grid
        tableGrid.innerHTML = '';

        if (tables.length === 0) {
            tableGrid.innerHTML = '<div class="col-span-full text-center text-gray-500">No hay mesas configuradas</div>';
            window._modalTablesLoaded = true;
            return;
        }

        // Crear botones para cada mesa
        tables.forEach(table => {
            const button = document.createElement('button');
            button.className = 'table-btn';
            button.dataset.tableId = table.id;
            button.dataset.tableNumber = table.number;
            button.dataset.status = table.state.toLowerCase().replace(' ', '-');
            button.textContent = `Mesa ${table.number}`;

            // Aplicar estilos según el estado
            switch (table.state) {
                case 'Disponible':
                    button.addEventListener('click', function () {
                        selectModalTable(this);
                    });
                    break;
                case 'Ocupada':
                    button.classList.add('occupied');
                    button.disabled = true;
                    button.title = 'Mesa ocupada';
                    break;
                case 'Reservada':
                    button.classList.add('reserved');
                    button.disabled = true;
                    button.title = 'Mesa reservada';
                    break;
                case 'No Disponible':
                    button.classList.add('occupied');
                    button.disabled = true;
                    button.title = 'Mesa no disponible';
                    break;
                default:
                    button.classList.add('occupied');
                    button.disabled = true;
                    button.title = `Estado: ${table.state}`;
                    break;
            }

            tableGrid.appendChild(button);
        });

        console.log(`✅ ${tables.length} mesas cargadas en el modal`);

    } catch (error) {
        console.error('❌ Error al cargar mesas:', error);
        if (errorMessage) errorMessage.textContent = error.message;
        if (errorElement) errorElement.classList.remove('hidden');
        tableGrid.innerHTML = '<div class="col-span-full text-center text-red-500">Error al cargar las mesas</div>';
        window._modalTablesLoaded = false;
    } finally {
        // Ocultar loading
        if (loadingElement) loadingElement.classList.add('hidden');
        window._modalTablesLoading = false;
    }
}
function setupCustomerDetailsObserver() {
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === 'childList') {
                updateOrderPanelOpacity();
            }
        });
    });

    const mainContent = document.getElementById('main-content');
    if (mainContent) {
        observer.observe(mainContent, {
            childList: true,
            subtree: true
        });
    }
}

function debugTableSelection() {
    console.log('🔍 DEBUG - Estado de selección de mesa:');
    console.log('   - localStorage.tableNumber:', localStorage.getItem('tableNumber'));
    console.log('   - paymentModalState:', window.paymentModalState?.selectedTable);
    console.log('   - Mesa seleccionada en modal:', document.querySelector('#payment-modal .table-btn.selected')?.dataset);
    console.log('   - Select principal:', document.getElementById('table-number')?.value);
}

function selectModalTable(tableElement) {
    console.log('✅ Mesa seleccionada en modal:', tableElement.dataset.tableNumber);

    // Deseleccionar mesa anterior
    document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });

    // Seleccionar nueva mesa
    tableElement.classList.add('selected');

    // CRÍTICO: Obtener el ID correcto de la mesa
    const tableId = tableElement.dataset.tableId;
    const tableNumber = tableElement.dataset.tableNumber;

    console.log('🔍 Datos de la mesa:', {
        tableId: tableId,
        tableNumber: tableNumber
    });

    // Actualizar localStorage con el ID correcto
    localStorage.setItem('tableNumber', tableId);

    // Actualizar el select principal si existe
    const tableSelect = document.getElementById('table-number');
    if (tableSelect) {
        tableSelect.value = tableId;
        console.log('✅ Select principal actualizado a:', tableId);
    }

    // Actualizar window.paymentModalState si existe
    if (window.paymentModalState) {
        window.paymentModalState.selectedTable = {
            id: tableId,
            number: tableNumber
        };
    }

    console.log('📋 Mesa guardada - ID:', tableId, 'Número:', tableNumber);
}
function initializeOrderSystem() {
    console.log('🚀 Inicializando sistema de pedidos...');

    try {
        // Verificar que estamos en la página correcta
        const orderPanel = document.getElementById('order-panel');
        if (!orderPanel) {
            console.log('ℹ️ No se encontró order-panel, saltando inicialización');
            return;
        }

        // Inicializar localStorage si no existe
        if (!localStorage.getItem('order')) {
            localStorage.setItem('order', JSON.stringify([]));
            console.log('📦 localStorage inicializado');
        }

        // Actualizar vista inicial
        updateOrderDetails();

        console.log('✅ Sistema de pedidos inicializado correctamente');
        return true;
    } catch (error) {
        console.error('❌ Error inicializando sistema de pedidos:', error);
        return false;
    }
}
function checkProformaConversion() {
    console.log('🔍 Verificando conversión de proforma...');

    const urlParams = new URLSearchParams(window.location.search);
    const openPayment = urlParams.get('open_payment');

    if (openPayment === 'true') {
        console.log('✅ Parámetro open_payment detectado');

        const order = JSON.parse(localStorage.getItem('order')) || [];
        const isConverting = localStorage.getItem('convertingProforma') === 'true';
        const proformaId = localStorage.getItem('proformaId');
        if (!isConverting || !proformaId) {
            console.warn('⚠️ Banderas de conversión inválidas, limpiando');
            clearProformaConversionFlags();
            return;
        }

        if (order.length > 0 && isConverting && proformaId) {
            console.log('✅ Abriendo modal automáticamente...');

            setTimeout(() => {
                if (typeof updateOrderDetails === 'function') {
                    updateOrderDetails();
                }

                if (typeof window.openPaymentModal === 'function') {
                    window.openPaymentModal();
                } else if (typeof showPaymentModal === 'function') {
                    showPaymentModal();
                }

                setTimeout(() => {
                    const proformaId = localStorage.getItem('proformaId');
                    if (proformaId && typeof showProformaConversionBanner === 'function') {
                        showProformaConversionBanner(proformaId);
                    }
                }, 500);

            }, 1000);
        }

        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 2000);
    }
}
function showProformaConversionBanner(proformaId) {
    const modalContent = document.querySelector('#payment-modal .payment-modal-content');
    if (!modalContent || document.querySelector('.proforma-conversion-banner')) {
        return;
    }

    const infoBanner = document.createElement('div');
    infoBanner.className = 'proforma-conversion-banner';
    infoBanner.style.cssText = `
        background: linear-gradient(135deg, #EF476F, #ff6b8a);
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 12px rgba(239, 71, 111, 0.3);
        animation: slideInDown 0.5s ease-out;
    `;

    const convertingNotes = localStorage.getItem('proformaNotes') || '';

    infoBanner.innerHTML = `
        <i class="fas fa-file-invoice" style="font-size: 1.8rem;"></i>
        <div style="flex: 1;">
            <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 4px;">
                🔄 Convirtiendo Proforma a Orden
            </div>
            <div style="font-size: 0.9rem; opacity: 0.95;">
                Proforma ID: PROF-${proformaId}
                ${convertingNotes ? ` • ${convertingNotes}` : ''}
            </div>
        </div>
        <i class="fas fa-arrow-right" style="font-size: 1.3rem;"></i>
    `;

    modalContent.insertBefore(infoBanner, modalContent.firstChild);
}


// Cargar el estado al cambiar de mesa
document.addEventListener('DOMContentLoaded', function () {
    const tableSelect = document.getElementById('table-number');
    if (tableSelect) {
        // Cargar estado inicial
        loadTableState();

        // Actualizar estado cuando cambia la selección
        tableSelect.addEventListener('change', loadTableState);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Solo ejecutar si estamos en la página del menú
    if (document.getElementById('order-panel')) {
        initializeOrderSystem();
        setupEventListeners();
        setupLogoutHandlers();
        setupTableSelectStyles();
        setupCustomerDetailsObserver();
        setupOrderTypeButtons();
        // Solo establecer valores iniciales sin llamar funciones complejas
        const orderTypeInput = document.getElementById('order-type');
        if (orderTypeInput) {
            orderTypeInput.value = 'Comer aquí';
        }
        localStorage.setItem('orderType', 'Comer aquí');

        // Mostrar el pedido actual al cargar
        updateOrderDetails();

        // Verificar si ya se procesó un pago anteriormente
        if (localStorage.getItem('paymentProcessed') === 'true') {
            paymentProcessed = true;
            //lockOrderInterface();
        }
    }
});
// Event listeners adicionales para el modal
document.addEventListener('click', function (e) {
    // Cerrar modal al hacer click en el overlay
    if (e.target && e.target.classList.contains('payment-modal-overlay')) {
        closePaymentModal();
    }

    // Cerrar modal con el botón X
    if (e.target && e.target.classList.contains('payment-modal-close')) {
        closePaymentModal();
    }
});

// Cerrar modal con la tecla Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('payment-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closePaymentModal();
        }
    }
});
const originalOpenPaymentModal = window.openPaymentModal;
if (typeof originalOpenPaymentModal === 'function') {
    window.openPaymentModal = function () {
        // Limpiar restricciones anteriores
        clearPaymentRestrictions();

        // Llamar a la función original
        originalOpenPaymentModal();

        // Mostrar advertencia después de un pequeño delay
        setTimeout(() => {
            showPickupPaymentWarning();
        }, 100);
    };
}

// Escuchar cambios en el tipo de pedido para actualizar restricciones
document.addEventListener('DOMContentLoaded', function () {
    // Observar cambios en localStorage para tipo de pedido
    const originalSetItem = localStorage.setItem;
    localStorage.setItem = function (key, value) {
        if (key === 'orderType') {
            clearPaymentRestrictions();
            console.log('🔄 Tipo de pedido cambiado a:', value);
        }
        originalSetItem.apply(this, arguments);
    };
});
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        checkProformaConversion();
    }, 500);
});

window.checkProformaConversion = checkProformaConversion;
window.showProformaConversionBanner = showProformaConversionBanner;
// ========== ESTILOS PARA LA ANIMACIÓN ==========
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #pickup-warning {
        animation: slideInDown 0.4s ease-out;
    }
    
    .payment-type option:disabled {
        color: #9ca3af;
        font-style: italic;
    }
    
    .transaction-number:required {
        border-color: #f59e0b !important;
    }
    
    .transaction-number:required:focus {
        border-color: #d97706 !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
    }
`;
document.head.appendChild(style);
function debugTablesState() {
    console.log('🔍 DEBUG - Estado actual de mesas:', {
        tablesManagementEnabled: window.tablesManagementEnabled,
        tablesConfigState: window.tablesConfigState,
        modalTableSelection: {
            hidden: document.getElementById('modal-table-selection')?.classList.contains('hidden'),
            exists: !!document.getElementById('modal-table-selection')
        },
        tableGrid: {
            hidden: document.getElementById('table-grid')?.classList.contains('hidden'),
            exists: !!document.getElementById('table-grid')
        },
        tablesDisabledMessage: {
            hidden: document.getElementById('tables-disabled-message')?.classList.contains('hidden'),
            exists: !!document.getElementById('tables-disabled-message')
        }
    });
}
async function syncInitialTablesState() {
    try {
        console.log('🔄 Sincronizando estado inicial de mesas...');

        // Obtener estado fresco del servidor
        const tablesEnabled = await checkTablesEnabledStatus();

        // Sincronizar TODAS las variables globales
        window.tablesManagementEnabled = tablesEnabled;

        if (!window.tablesConfigState) {
            window.tablesConfigState = {};
        }
        window.tablesConfigState.tablesEnabled = tablesEnabled;

        console.log('✅ Estado inicial sincronizado:', {
            tablesManagementEnabled: window.tablesManagementEnabled,
            tablesConfigState: window.tablesConfigState.tablesEnabled
        });

    } catch (error) {
        console.error('❌ Error sincronizando estado inicial:', error);
    }
}
document.addEventListener('DOMContentLoaded', async function () {
    // Solo ejecutar si estamos en la página del menú
    if (document.getElementById('order-panel')) {
        // 🔥 PRIMERO: Sincronizar estado de mesas
        await syncInitialTablesState();

        // LUEGO: Inicializar todo lo demás
        initializeOrderSystem();
        setupEventListeners();
        setupLogoutHandlers();
        setupTableSelectStyles();
        setupCustomerDetailsObserver();
        setupOrderTypeButtons();

        // Establecer valores iniciales
        const orderTypeInput = document.getElementById('order-type');
        if (orderTypeInput) {
            orderTypeInput.value = 'Comer aquí';
        }
        localStorage.setItem('orderType', 'Comer aquí');

        // Mostrar el pedido actual al cargar
        updateOrderDetails();

        // Verificar si ya se procesó un pago anteriormente
        if (localStorage.getItem('paymentProcessed') === 'true') {
            paymentProcessed = true;
        }
    }
});
function clearProformaConversionFlags() {
    console.log('🧹 Limpiando banderas de conversión de proforma');
    localStorage.removeItem('convertingProforma');
    localStorage.removeItem('proformaId');
    localStorage.removeItem('proformaNotes');
}
function debugProformaConversion() {
    console.log('🔍 === DEBUG CONVERSIÓN DE PROFORMA ===');
    console.log('convertingProforma:', localStorage.getItem('convertingProforma'));
    console.log('proformaId:', localStorage.getItem('proformaId'));
    console.log('proformaNotes:', localStorage.getItem('proformaNotes'));
    console.log('order:', JSON.parse(localStorage.getItem('order') || '[]').length, 'items');
    console.log('===================================');
}

// Exportar
window.debugProformaConversion = debugProformaConversion;

// Exportar la función
window.clearProformaConversionFlags = clearProformaConversionFlags;
// Exportar función de debug
window.debugTablesState = debugTablesState;
// ✅ Exportar funciones al scope global
window.updateOrderDetails = updateOrderDetails;
window.removeItem = removeItem;
window.increaseItemQuantity = increaseItemQuantity;
window.clearOrder = clearOrder;
window.initializeOrderSystem = initializeOrderSystem;
window.processOrder = processOrder;
window.checkStockAvailability = checkStockAvailability;
window.updateTableState = updateTableState;
window.handleTableSelectionVisibility = handleTableSelectionVisibility;
window.showPaymentModal = showPaymentModal;
window.saveTablesConfig = saveTablesConfig;
window.checkTablesEnabledStatus = checkTablesEnabledStatus
window.setupOrderTypeButtons = setupOrderTypeButtons;
window.checkTablesEnabledStatus = checkTablesEnabledStatus;
