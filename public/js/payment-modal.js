// ============================================
// VARIABLES GLOBALES
// ============================================
window.paymentRows = [];

let paymentRowCounter = 0;
let originalTablesEnabled = false;
let tables = [];

let currentStep = 1;
const totalSteps = 3;
let selectedTable = null;
let selectedDeliveryService = null;

// API Routes
const TABLES_API = {
    list: '/tables',
    store: '/tables',
    update: (id) => `/tables/${id}`,
    delete: (id) => `/tables/${id}`,
    bulkState: '/tables/bulk-state',
    stats: '/tables/stats',
    available: '/tables/available'
};

// Estado global del modal - ACTUALIZADO PARA 3 PASOS
window.paymentModalState = {
    currentStep: 1,
    maxSteps: 3, // Ahora son 3 pasos
    selectedOrderType: 'comer-aqui',
    selectedTable: null,
    paymentRows: [],
    customerData: {} // Nuevo: Datos del cliente
};

// Estado de configuración de mesas
window.tablesConfigState = {
    tables: [],
    isLoading: false,
    tablesEnabled: false
};

// ============================================
// MODAL DE PAGO - FUNCIONES PRINCIPALES
// ============================================

function showPaymentModal() {
    const modal = document.getElementById('payment-modal');
    const orderTotal = calculateOrderTotal();

    if (orderTotal <= 0) {
        alert('No hay items en el pedido');
        return;
    }

    modal.classList.remove('hidden');
    currentStep = 1;
    updateStepDisplay();
    updateOrderTotal();

    // ✅ LIMPIAR Y REINICIAR paymentRows
    window.paymentRows = [];
    console.log('🧹 window.paymentRows limpiado');

    // Limpiar selecciones previas
    selectedTable = null;
    selectedDeliveryService = null;

    // Limpiar contenedor de filas
    const container = document.getElementById('payment-rows-container');
    if (container) {
        container.innerHTML = '';
    }

    // ❌ ELIMINAR ESTAS LÍNEAS - No agregar fila automáticamente
    /*
    setTimeout(() => {
        console.log('➕ Agregando primera fila automáticamente...');
        addPaymentRow();

        setTimeout(() => {
            console.log('🔍 Verificación post-agregar:');
            console.log('   - Array:', window.paymentRows.length);
            console.log('   - DOM:', document.querySelectorAll('.payment-row').length);
        }, 100);
    }, 100);
    */

    // Resetear formulario del paso 3
    if (document.getElementById('modal-customer-details-form')) {
        document.getElementById('modal-customer-details-form').reset();
    }

    // Cargar mesas si está habilitado
    const tablesEnabled = window.tablesConfigState?.tablesEnabled || false;
    console.log('🔍 Tables enabled:', tablesEnabled);

    if (tablesEnabled) {
        loadModalTables();
    }

    // Establecer tipo de pedido por defecto
    const defaultType = tablesEnabled ? 'comer-aqui' : 'para-llevar';
    selectOrderType(defaultType);

    console.log('✅ Modal abierto correctamente');
}
function calculateOrderTotal() {
    const orderDetails = document.getElementById('order-details');
    if (!orderDetails) return 0;

    let total = 0;
    orderDetails.querySelectorAll('.order-item').forEach(item => {
        const priceText = item.querySelector('.order-item-price')?.textContent || '0';
        const price = parseFloat(priceText.replace('$', '').trim());
        total += price;
    });

    return total;
}

function openPaymentModal() {
    console.log('🚀 Abriendo modal de pagos (3 pasos)...');

    const modal = document.getElementById('payment-modal');
    if (!modal) {
        console.error('❌ No se encontró el modal');
        return;
    }

    modal.classList.remove('hidden');
    loadOrderData();

    setTimeout(() => {
        initializeModal();
        if (typeof showPickupPaymentWarning === 'function') {
            showPickupPaymentWarning();
        }
    }, 50);
}
// function addPaymentRow() {
//     console.log('➕ === AGREGANDO NUEVA FILA ===');

//     if (!window.paymentRows) {
//         window.paymentRows = [];
//         console.log('📦 window.paymentRows inicializado');
//     }

//     const rowId = Date.now();

//     const row = {
//         id: rowId,
//         method: '',
//         reference: '',
//         amount: 0
//     };

//     window.paymentRows.push(row);
//     console.log(`✅ Fila agregada al array (ID: ${rowId})`);
//     console.log(`📦 Total filas en array: ${window.paymentRows.length}`);

//     renderPaymentRows();
//     updateNoPaymentsMessage();
//     // Verificar después de renderizar
//     setTimeout(() => {
//         const domCount = document.querySelectorAll('.payment-row').length;
//         console.log(`🎯 Verificación post-agregar:`);
//         console.log(`   - Array: ${window.paymentRows.length}`);
//         console.log(`   - DOM: ${domCount}`);

//         if (domCount !== window.paymentRows.length) {
//             console.error(`❌ DESINCRONIZACIÓN después de agregar`);
//         }
//     }, 100);
// }
function closePaymentModal() {
    console.log('🔒 Cerrando modal de pago...');

    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');

    currentStep = 1;

    // ✅ LIMPIAR window.paymentRows correctamente
    window.paymentRows = [];

    selectedTable = null;
    selectedDeliveryService = null;

    // Limpiar contenedor
    const container = document.getElementById('payment-rows-container');
    if (container) {
        container.innerHTML = '';
    }

    console.log('✅ Modal cerrado y paymentRows limpiado');
}
function renderPaymentRows() {
    const container = document.getElementById('payment-rows-container');

    if (!container) {
        console.error('❌ No se encontró payment-rows-container');
        return;
    }

    if (!window.paymentRows) {
        window.paymentRows = [];
    }

    console.log(`🎨 Renderizando ${window.paymentRows.length} filas`);

    container.innerHTML = '';

    if (window.paymentRows.length === 0) {
        console.warn('⚠️ No hay filas para renderizar');
        return;
    }

    window.paymentRows.forEach((row, index) => {
        const rowElement = createPaymentRowElement(row, index);
        container.appendChild(rowElement);
    });

    console.log(`✅ ${window.paymentRows.length} filas renderizadas`);

    // Verificar sincronización
    const domCount = document.querySelectorAll('.payment-row').length;
    if (domCount !== window.paymentRows.length) {
        console.error(`❌ DESINCRONIZACIÓN después de renderizar:`);
        console.error(`   - Array: ${window.paymentRows.length}`);
        console.error(`   - DOM: ${domCount}`);
    }
}
function createPaymentRowElement(row, index) {
    const div = document.createElement('div');
    div.className = 'payment-row';
    div.dataset.rowId = row.id; // ✅ CRÍTICO: Agregar identificador único

    div.innerHTML = `
        <div class="payment-row-header">
            <strong>Método de Pago ${index + 1}</strong>
            <button type="button" class="payment-row-remove" onclick="removePaymentRow(${row.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="payment-form">
            <div class="form-group">
                <label class="form-label">Método</label>
                <select class="form-select" data-row-id="${row.id}" onchange="updatePaymentRowFromSelect(${row.id}, 'method', this.value)">
                    <option value="">Seleccionar...</option>
                    <option value="Efectivo" ${row.method === 'Efectivo' ? 'selected' : ''}>Efectivo</option>
                    <option value="Tarjeta" ${row.method === 'Tarjeta' ? 'selected' : ''}>Tarjeta</option>
                    <option value="QR" ${row.method === 'QR' ? 'selected' : ''}>QR</option>
                    <option value="Transferencia" ${row.method === 'Transferencia' ? 'selected' : ''}>Transferencia</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Referencia</label>
                <input type="text" 
                    class="form-input" 
                    placeholder="Número de referencia" 
                    value="${row.reference || ''}"
                    data-row-id="${row.id}"
                    onchange="updatePaymentRowFromInput(${row.id}, 'reference', this.value)">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Monto</label>
                <input type="number" 
                    class="form-input" 
                    step="0.01" 
                    min="0" 
                    placeholder="0.00"
                    value="${row.amount || ''}"
                    data-row-id="${row.id}"
                    onchange="updatePaymentRowFromInput(${row.id}, 'amount', parseFloat(this.value) || 0)">
            </div>
        </div>
    `;
    return div;
}
function updatePaymentRowFromInput(rowId, field, value) {
    console.log(`🔄 Actualizando ${field} de fila ${rowId} desde INPUT:`, value);

    if (!window.paymentRows) {
        window.paymentRows = [];
    }

    const row = window.paymentRows.find(r => r.id === rowId);
    if (row) {
        row[field] = value;
        console.log(`✅ ${field} actualizado en array:`, row);
    } else {
        console.error(`❌ No se encontró fila con ID ${rowId} en el array`);
    }
}
function updateOrderTotal() {
    const total = calculateOrderTotal();
    document.querySelectorAll('#order-total, #step3-order-total').forEach(el => {
        el.textContent = total.toFixed(2);
    });
}
function updatePaymentRow(id, field, value) {
    // Actualizar en window.paymentRows si existe
    if (window.paymentRows && window.paymentRows.length > 0) {
        const row = window.paymentRows.find(r => r.id === id);
        if (row) {
            row[field] = value;
            console.log(`✅ Actualizado ${field} de fila ${id}:`, value);
        }
    }

    // ✅ TAMBIÉN sincronizar desde el DOM a window.paymentRows
    syncPaymentRowsFromDOM();
}
function updatePaymentRowFromSelect(rowId, field, value) {
    console.log(`🔄 Actualizando ${field} de fila ${rowId} desde SELECT:`, value);

    if (!window.paymentRows) {
        window.paymentRows = [];
    }

    const row = window.paymentRows.find(r => r.id === rowId);
    if (row) {
        row[field] = value;
        console.log(`✅ ${field} actualizado en array:`, row);
    } else {
        console.error(`❌ No se encontró fila con ID ${rowId} en el array`);
    }
}

function syncPaymentRowsFromDOM() {
    console.log('🔄 === INICIANDO SINCRONIZACIÓN DESDE DOM ===');

    if (!window.paymentRows) {
        window.paymentRows = [];
    }

    const paymentRowElements = document.querySelectorAll('#payment-rows-container .payment-row');
    console.log(`🔍 Filas encontradas en DOM: ${paymentRowElements.length}`);

    // Si no hay filas, no hay nada que sincronizar
    if (paymentRowElements.length === 0) {
        console.warn('⚠️ No hay filas de pago en el DOM para sincronizar');
        window.paymentRows = [];
        return;
    }

    const tempRows = [];

    paymentRowElements.forEach((rowElement, index) => {
        console.log(`\n📝 === Procesando fila ${index} ===`);

        // Obtener rowId
        const rowId = parseInt(rowElement.dataset.rowId);
        console.log(`   1️⃣ Row ID: ${rowId}`);

        if (!rowId) {
            console.error(`   ❌ No se encontró rowId para la fila ${index}`);
            return;
        }

        // ESTRATEGIA MÚLTIPLE: Intentar varios selectores para encontrar los elementos

        // Método 1: Buscar por clase específica
        let methodSelect = rowElement.querySelector('.payment-type') ||
            rowElement.querySelector('select.form-select') ||
            rowElement.querySelector('select');

        let referenceInput = rowElement.querySelector('.transaction-number') ||
            rowElement.querySelector('input[type="text"]');

        let amountInput = rowElement.querySelector('.total-paid') ||
            rowElement.querySelector('input[type="number"]');

        console.log(`   2️⃣ Elementos encontrados:`, {
            methodSelect: !!methodSelect,
            referenceInput: !!referenceInput,
            amountInput: !!amountInput
        });

        // Si no encontramos el select de método, la fila es inválida
        if (!methodSelect) {
            console.error(`   ❌ No se encontró SELECT de método de pago en fila ${index}`);
            console.log(`   🔍 Diagnóstico de fila:`, rowElement.innerHTML.substring(0, 300));
            return;
        }

        // Si no encontramos el input de monto, la fila es inválida
        if (!amountInput) {
            console.error(`   ❌ No se encontró INPUT de monto en fila ${index}`);
            console.log(`   🔍 Diagnóstico de fila:`, rowElement.innerHTML.substring(0, 300));
            return;
        }

        // Extraer valores
        const method = methodSelect.value;
        const reference = referenceInput ? referenceInput.value : '';
        const amount = parseFloat(amountInput.value) || 0;

        console.log(`   3️⃣ Valores extraídos:`, {
            method,
            reference,
            amount
        });

        // Validar que al menos tengamos método o monto
        if (!method && amount === 0) {
            console.warn(`   ⚠️ Fila ${index} sin datos válidos (método vacío y monto 0)`);
        }

        // Crear objeto de fila
        const row = {
            id: rowId,
            method: method,
            reference: reference,
            amount: amount
        };

        tempRows.push(row);
        console.log(`   ✅ Fila ${index} agregada al array temporal:`, row);
    });

    // Reemplazar array global
    window.paymentRows = tempRows;

    console.log(`\n✅ === SINCRONIZACIÓN COMPLETA ===`);
    console.log(`📦 Total filas sincronizadas: ${window.paymentRows.length}`);
    console.log(`📦 window.paymentRows:`, window.paymentRows);

    // Verificar sincronización
    const domRows = paymentRowElements.length;
    if (domRows !== window.paymentRows.length) {
        console.error(`\n❌ === DESINCRONIZACIÓN DETECTADA ===`);
        console.error(`   - Filas en DOM: ${domRows}`);
        console.error(`   - Filas en Array: ${window.paymentRows.length}`);
        console.error(`   - Diferencia: ${domRows - window.paymentRows.length} filas perdidas`);

        // Ejecutar diagnóstico automático
        diagnosePaymentRowStructure();
    } else {
        console.log(`✅ Sincronización exitosa: DOM y Array coinciden`);
    }

    return window.paymentRows;
}
function createPaymentRowElement(row, index) {
    const div = document.createElement('div');
    div.className = 'payment-row';
    div.dataset.rowId = row.id;

    // IMPORTANTE: Usar clases específicas que coincidan con syncPaymentRowsFromDOM
    div.innerHTML = `
        <div class="payment-row-header">
            <strong>Método de Pago ${index + 1}</strong>
            <button type="button" class="payment-row-remove" onclick="removePaymentRow(${row.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="payment-form">
            <div class="form-group">
                <label class="form-label">Método</label>
                <select class="form-select payment-type" data-row-id="${row.id}" onchange="updatePaymentRowField(${row.id}, 'method', this.value)">
                    <option value="">Seleccionar...</option>
                    <option value="Efectivo" ${row.method === 'Efectivo' ? 'selected' : ''}>Efectivo</option>
                    <option value="Tarjeta" ${row.method === 'Tarjeta' ? 'selected' : ''}>Tarjeta</option>
                    <option value="QR" ${row.method === 'QR' ? 'selected' : ''}>QR</option>
                    <option value="Transferencia" ${row.method === 'Transferencia' ? 'selected' : ''}>Transferencia</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Referencia</label>
                <input type="text" 
                    class="form-input transaction-number" 
                    placeholder="Número de referencia" 
                    value="${row.reference || ''}"
                    data-row-id="${row.id}"
                    onchange="updatePaymentRowField(${row.id}, 'reference', this.value)">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Monto</label>
                <input type="number" 
                    class="form-input total-paid" 
                    step="0.01" 
                    min="0" 
                    placeholder="0.00"
                    value="${row.amount || ''}"
                    data-row-id="${row.id}"
                    onchange="updatePaymentRowField(${row.id}, 'amount', parseFloat(this.value) || 0)">
            </div>
        </div>
    `;

    return div;
}
function updatePaymentRowField(rowId, field, value) {
    console.log(`🔄 Actualizando campo "${field}" de fila ${rowId}:`, value);

    if (!window.paymentRows) {
        console.warn('⚠️ window.paymentRows no existe, inicializando...');
        window.paymentRows = [];
    }

    const row = window.paymentRows.find(r => r.id === rowId);

    if (row) {
        row[field] = value;
        console.log(`✅ Campo "${field}" actualizado en array:`, row);
    } else {
        console.error(`❌ No se encontró fila con ID ${rowId} en el array`);
        console.log('📦 Array actual:', window.paymentRows);

        // Intentar recuperar sincronizando desde DOM
        console.log('🔄 Intentando recuperar sincronizando desde DOM...');
        syncPaymentRowsFromDOM();
    }
}
function removePaymentRow(id) {
    console.log(`🗑️ Eliminando fila ID: ${id}`);

    if (!window.paymentRows) {
        window.paymentRows = [];
    }

    const lengthBefore = window.paymentRows.length;
    window.paymentRows = window.paymentRows.filter(row => row.id !== id);

    console.log(`📦 Filas: ${lengthBefore} → ${window.paymentRows.length}`);

    renderPaymentRows();
    updateNoPaymentsMessage();
}
function updateStepDisplay() {
    // Actualizar indicadores de paso
    document.querySelectorAll('.step-item').forEach((item, index) => {
        const stepNum = index + 1;
        item.classList.remove('active', 'completed');

        if (stepNum === currentStep) {
            item.classList.add('active');
        } else if (stepNum < currentStep) {
            item.classList.add('completed');
        }
    });

    // Mostrar/ocultar contenido de pasos
    document.querySelectorAll('.step-content').forEach((content, index) => {
        const stepNum = index + 1;
        content.classList.toggle('active', stepNum === currentStep);
    });

    // Actualizar botones
    updateNavigationButtons();
}
function updateNavigationButtons() {
    const prevButtons = document.querySelectorAll('.step-btn.prev');
    const nextButtons = document.querySelectorAll('.step-btn.next');

    prevButtons.forEach(btn => {
        btn.disabled = currentStep === 1;
    });

    nextButtons.forEach(btn => {
        btn.style.display = currentStep === totalSteps ? 'none' : 'inline-block';
    });
}
function loadOrderData() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const total = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const totalElement = document.getElementById('order-total');
    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }

    // También actualizar el total del paso 3
    const step3Total = document.getElementById('step3-order-total');
    if (step3Total) {
        step3Total.textContent = total.toFixed(2);
    }
}

function initializeModal() {
    console.log('🔧 Inicializando modal...');

    syncWithMainSystem();

    // Botones de tipo de pedido
    document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.addEventListener('click', function () {
            handleOrderTypeSelection(this);
        });
    });

    // Navegación de pasos
    document.querySelectorAll('#payment-modal .step-item').forEach(item => {
        const newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);

        newItem.addEventListener('click', function () {
            const step = parseInt(this.getAttribute('data-step'));
            goToStep(step);
        });
    });

    console.log('✅ Modal inicializado correctamente');
}

function syncWithMainSystem() {
    const currentOrderType = localStorage.getItem('orderType') || 'Comer aquí';

    let modalType = 'comer-aqui';
    switch (currentOrderType) {
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

    window.paymentModalState.selectedOrderType = modalType;

    document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
        btn.classList.remove('selected');
        if (btn.dataset.type === modalType) {
            btn.classList.add('selected');
        }
    });

    updateModalSectionsVisibility();
}

function handleOrderTypeSelection(btnElement) {
    console.log('📝 Tipo de pedido seleccionado en modal...');

    document.querySelectorAll('#payment-modal .order-type-btn').forEach(b => {
        b.classList.remove('selected');
    });

    btnElement.classList.add('selected');

    const selectedType = btnElement.dataset.type;
    window.paymentModalState.selectedOrderType = selectedType;

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

    localStorage.setItem('orderType', orderTypeName);
    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) {
        orderTypeInput.value = orderTypeName;
    }

    if (selectedType !== 'comer-aqui') {
        localStorage.removeItem('tableNumber');
        window.paymentModalState.selectedTable = null;
    }

    if (selectedType !== 'para-llevar') {
        localStorage.removeItem('deliveryService');
    }

    if (selectedType !== 'recoger') {
        localStorage.removeItem('pickupNotes');
    }

    updateModalSectionsVisibility();
}

function updateModalSectionsVisibility() {
    const tableSelection = document.getElementById('modal-table-selection');
    const deliverySelection = document.getElementById('modal-delivery-selection');
    const pickupNotes = document.getElementById('modal-pickup-notes');

    if (tableSelection) tableSelection.classList.add('hidden');
    if (deliverySelection) deliverySelection.classList.add('hidden');
    if (pickupNotes) pickupNotes.classList.add('hidden');

    switch (window.paymentModalState.selectedOrderType) {
        case 'comer-aqui':
            if (tableSelection) {
                tableSelection.classList.remove('hidden');
                loadModalTables();
            }
            break;

        case 'para-llevar':
            if (deliverySelection) {
                deliverySelection.classList.remove('hidden');
                loadDeliveryServices();
            }
            break;

        case 'recoger':
            if (pickupNotes) {
                pickupNotes.classList.remove('hidden');
                loadPickupNotes();
            }
            break;
    }
}

// ============================================
// NAVEGACIÓN ENTRE PASOS (ACTUALIZADO PARA 3 PASOS)
// ============================================

function goToStep(step) {
    console.log(`🔄 Navegando al paso ${step}`);

    // Validaciones antes de cambiar de paso
    if (step > window.paymentModalState.currentStep) {
        // Validar paso 1 antes de ir al paso 2
        if (window.paymentModalState.currentStep === 1 && step >= 2) {
            if (!validateStep1()) {
                return;
            }
        }

        // Validar paso 2 antes de ir al paso 3
        if (window.paymentModalState.currentStep === 2 && step === 3) {
            if (!validateStep2()) {
                return;
            }
            // Cargar resumen en el paso 3
            loadStep3Summary();
        }
    }

    // Ocultar todos los contenidos de pasos
    document.querySelectorAll('#payment-modal .step-content').forEach(content => {
        content.classList.remove('active');
    });

    // Desactivar todos los items de navegación
    document.querySelectorAll('#payment-modal .step-item').forEach(item => {
        item.classList.remove('active');
        item.classList.remove('completed');
    });

    // Marcar pasos completados
    for (let i = 1; i < step; i++) {
        const completedItem = document.querySelector(`#payment-modal .step-item[data-step="${i}"]`);
        if (completedItem) {
            completedItem.classList.add('completed');
        }
    }

    // Activar paso actual
    const stepContent = document.getElementById(`step-${step}`);
    const stepItem = document.querySelector(`#payment-modal .step-item[data-step="${step}"]`);

    if (stepContent) stepContent.classList.add('active');
    if (stepItem) stepItem.classList.add('active');

    window.paymentModalState.currentStep = step;
    updateStepNavigation();
}

function nextStep() {
    console.log(`🔄 Intentando avanzar del paso ${currentStep} al paso ${currentStep + 1}`);

    // ✅ Sincronizar SOLO si estamos SALIENDO del paso 2
    if (currentStep === 2) {
        console.log('🔄 Paso 2 detectado, sincronizando datos de pago...');
        syncPaymentRowsFromDOM();
    }

    // ✅ Validar el paso ACTUAL antes de avanzar
    if (!validateCurrentStep()) {
        console.warn('⚠️ Validación fallida, no se avanza al siguiente paso');
        return;
    }

    // ✅ Avanzar al siguiente paso
    if (currentStep < totalSteps) {
        currentStep++;
        updateStepDisplay();

        console.log(`✅ Avanzando al paso ${currentStep}`);

        // ✅ AHORA SÍ: Si acabamos de LLEGAR al paso 2, agregar fila automáticamente
        if (currentStep === 2) {
            setTimeout(() => {
                const rowsContainer = document.getElementById('payment-rows-container');
                if (rowsContainer && rowsContainer.children.length === 0) {
                    console.log('📝 Llegamos al Paso 2 sin filas, agregando una automáticamente...');
                    addPaymentRow();
                }
            }, 100);
        }

        // ✅ Si llegamos al paso 3, actualizar el resumen
        if (currentStep === 3) {
            syncPaymentRowsFromDOM();
            updateStep3Summary();
        }
    }
}
function updateNoPaymentsMessage() {
    const container = document.getElementById('payment-rows-container');
    const message = document.getElementById('no-payments-message');

    if (!container || !message) return;

    if (container.children.length === 0) {
        message.style.display = 'block';
    } else {
        message.style.display = 'none';
    }
}
function debugPaymentRows() {
    console.log('=== DEBUG PAYMENT ROWS ===');
    console.log('window.paymentRows existe:', typeof window.paymentRows !== 'undefined');
    console.log('window.paymentRows:', window.paymentRows);
    console.log('Cantidad de filas:', window.paymentRows?.length || 0);
    console.log('Contenedor DOM:', document.getElementById('payment-rows-container'));
    console.log('Filas en DOM:', document.querySelectorAll('.payment-row').length);
    console.log('========================');
}
window.debugPaymentRows = debugPaymentRows;
// 7. MODIFICAR updateStep3Summary() para usar window.paymentRows
function updateStep3Summary() {
    console.log('📋 Actualizando resumen del paso 3...');
    syncPaymentRowsFromDOM();
    console.log('💳 Datos de pago para resumen:', window.paymentRows);
    // Actualizar resumen del pedido

    const orderSummary = document.getElementById('step3-order-summary');
    const orderDetails = document.getElementById('order-details');

    if (orderSummary && orderDetails) {
        const items = orderDetails.querySelectorAll('.order-item');
        let summaryHTML = '';

        items.forEach(item => {
            const name = item.querySelector('.order-item-name')?.textContent || '';
            const quantity = item.querySelector('.order-item-quantity')?.textContent || '1';
            const price = item.querySelector('.order-item-price')?.textContent || '$0.00';

            summaryHTML += `
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                    <span>${quantity} × ${name}</span>
                    <span style="font-weight: 600;">${price}</span>
                </div>
            `;
        });

        orderSummary.innerHTML = summaryHTML;
    }

    // Actualizar detalles de pago - ✅ USAR window.paymentRows
    const paymentDetails = document.getElementById('step3-payment-methods');
    if (paymentDetails) {
        let paymentHTML = '';

        if (window.paymentRows.length === 0) {
            paymentHTML = '<p style="color: #666; text-align: center; padding: 20px;">No hay métodos de pago registrados</p>';
        } else {
            window.paymentRows.forEach((row, index) => {
                paymentHTML += `
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <strong>${row.method || 'Sin método'}</strong>
                            ${row.reference ? `<br><small style="color: var(--text-secondary);">Ref: ${row.reference}</small>` : ''}
                        </div>
                        <span style="font-weight: 600; color: var(--success-color);">$${parseFloat(row.amount || 0).toFixed(2)}</span>
                    </div>
                `;
            });
        }

        paymentDetails.innerHTML = paymentHTML;
    }


    // Actualizar total
    updateOrderTotal();
}


function validateCurrentStep() {
    if (currentStep === 1) {
        return validateStep1();
    } else if (currentStep === 2) {
        return validateStep2();
    }
    return true;
}
function selectOrderType(type) {
    // Actualizar botones
    document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    document.querySelector(`[data-type="${type}"]`).classList.add('selected');

    // Actualizar input oculto
    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) {
        const typeMap = {
            'comer-aqui': 'Comer aquí',
            'para-llevar': 'Recojo por Delivery',
            'recoger': 'Recoger'
        };
        orderTypeInput.value = typeMap[type] || 'Comer aquí';
    }

    // Mostrar/ocultar secciones según el tipo
    const tableSelection = document.getElementById('modal-table-selection');
    const deliverySelection = document.getElementById('modal-delivery-selection');
    const pickupNotes = document.getElementById('modal-pickup-notes');

    tableSelection.classList.add('hidden');
    deliverySelection.classList.add('hidden');
    pickupNotes.classList.add('hidden');

    if (type === 'comer-aqui') {
        tableSelection.classList.remove('hidden');
    } else if (type === 'para-llevar') {
        deliverySelection.classList.remove('hidden');
        loadDeliveryServices();
    } else if (type === 'recoger') {
        pickupNotes.classList.remove('hidden');
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}


function updateStepNavigation() {
    const prevButton = document.querySelector('#payment-modal .step-btn.prev');
    const nextButton = document.querySelector('#payment-modal .step-btn.next');
    const confirmButton = document.querySelector('#payment-modal .step-btn.confirm');

    if (prevButton) {
        prevButton.disabled = window.paymentModalState.currentStep === 1;
    }

    if (nextButton && confirmButton) {
        // Mostrar botón "Siguiente" en pasos 1 y 2
        if (window.paymentModalState.currentStep < 3) {
            nextButton.style.display = 'block';
            confirmButton.style.display = 'none';
        } else {
            // Mostrar botón "Confirmar" en paso 3
            nextButton.style.display = 'none';
            confirmButton.style.display = 'block';
        }
    }
}

// ============================================
// VALIDACIONES POR PASO
// ============================================
function validateStep1() {
    const orderType = document.getElementById('order-type')?.value || 'Comer aquí';

    if (orderType === 'Comer aquí') {
        const tablesEnabled = window.tablesConfigState?.tablesEnabled || false;

        if (tablesEnabled && !selectedTable) {
            alert('Por favor selecciona una mesa');
            return false;
        }
    } else if (orderType === 'Recojo por Delivery') {
        const deliverySelect = document.getElementById('modal-delivery-service');
        selectedDeliveryService = deliverySelect?.value;

        if (!selectedDeliveryService) {
            alert('Por favor selecciona un servicio de delivery');
            return false;
        }
    }

    return true;
}
function validateStep2() {
    console.log('🔍 Validando paso 2 (leyendo del DOM)...');

    // ✅ Leer directamente del DOM en lugar de window.paymentRows
    const paymentRowElements = document.querySelectorAll('#payment-rows-container .payment-row');

    console.log('📦 Filas de pago en DOM:', paymentRowElements.length);

    if (paymentRowElements.length === 0) {
        alert('Por favor agrega al menos un método de pago');
        console.error('❌ No hay filas de pago en el DOM');
        return false;
    }

    let totalPaid = 0;
    const orderTotal = calculateOrderTotal();

    // Validar cada fila
    for (let rowElement of paymentRowElements) {
        const methodSelect = rowElement.querySelector('.form-select');
        const amountInput = rowElement.querySelector('input[type="number"]');

        if (!methodSelect || !amountInput) {
            console.error('❌ No se encontraron elementos en la fila');
            continue;
        }

        const method = methodSelect.value;
        const amount = parseFloat(amountInput.value) || 0;

        console.log(`💳 Método: ${method}, Monto: ${amount}`);

        if (!method) {
            alert('Por favor selecciona un método de pago para todos los métodos agregados');
            return false;
        }

        if (amount <= 0) {
            alert('Por favor ingresa un monto válido para todos los métodos de pago');
            return false;
        }

        totalPaid += amount;
    }

    console.log('💰 Total del pedido:', orderTotal);
    console.log('💳 Total pagado:', totalPaid);

    if (totalPaid < orderTotal) {
        alert(`El total de pagos ($${totalPaid.toFixed(2)}) es menor al total del pedido ($${orderTotal.toFixed(2)})`);
        return false;
    }

    console.log('✅ Validación del paso 2 exitosa');
    return true;
}
// ============================================
// CARGAR RESUMEN EN EL PASO 3
// ============================================

function loadStep3Summary() {
    console.log('📋 Cargando resumen en el paso 3...');

    // Cargar resumen del pedido
    loadStep3OrderSummary();

    // Cargar detalles de pago
    loadStep3PaymentDetails();

    // Cargar datos del cliente si existen
    loadStep3CustomerData();
}

function loadStep3OrderSummary() {
    const summaryContainer = document.getElementById('step3-order-summary');
    const totalElement = document.getElementById('step3-order-total');

    if (!summaryContainer) return;

    const order = JSON.parse(localStorage.getItem('order')) || [];

    if (order.length === 0) {
        summaryContainer.innerHTML = '<p class="text-gray-500 text-center">No hay ítems en el pedido</p>';
        return;
    }

    const total = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    summaryContainer.innerHTML = order.map(item => `
        <div class="summary-item">
            <span class="summary-item-name">${item.quantity}x ${item.name}</span>
            <span class="summary-item-price">${(item.price * item.quantity).toFixed(2)}</span>
        </div>
    `).join('');

    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
}

function loadStep3PaymentDetails() {
    const paymentContainer = document.getElementById('step3-payment-methods');

    if (!paymentContainer) return;

    const paymentMethods = JSON.parse(localStorage.getItem('paymentMethods')) || [];

    if (paymentMethods.length === 0) {
        paymentContainer.innerHTML = '<p class="text-gray-500 text-center">No hay métodos de pago registrados</p>';
        return;
    }

    paymentContainer.innerHTML = paymentMethods.map(method => {
        const icon = getPaymentIcon(method.method);
        return `
            <div class="payment-method-item">
                <div class="payment-method-type">
                    ${icon}
                    <span>${method.method}</span>
                    ${method.transaction_number ? `<span class="text-xs text-gray-500">(Trans: ${method.transaction_number})</span>` : ''}
                </div>
                <div class="payment-method-amount">${method.amount.toFixed(2)}</div>
            </div>
        `;
    }).join('');
}

function getPaymentIcon(method) {
    const icons = {
        'Efectivo': '<i class="fas fa-money-bill-wave payment-method-icon"></i>',
        'QR': '<i class="fas fa-qrcode payment-method-icon"></i>',
        'Tarjeta': '<i class="fas fa-credit-card payment-method-icon"></i>',
        'Transferencia': '<i class="fas fa-exchange-alt payment-method-icon"></i>'
    };

    return icons[method] || '<i class="fas fa-dollar-sign payment-method-icon"></i>';
}

function loadStep3CustomerData() {
    // Cargar datos del cliente desde localStorage si existen
    const savedCustomerName = localStorage.getItem('customerName');
    const savedCustomerEmail = localStorage.getItem('customerEmail');
    const savedCustomerPhone = localStorage.getItem('customerPhone');

    if (savedCustomerName) {
        const nameInput = document.getElementById('modal-customer-name');
        if (nameInput) nameInput.value = savedCustomerName;
    }

    if (savedCustomerEmail) {
        const emailInput = document.getElementById('modal-customer-email');
        if (emailInput) emailInput.value = savedCustomerEmail;
    }

    if (savedCustomerPhone) {
        const phoneInput = document.getElementById('modal-customer-phone');
        if (phoneInput) phoneInput.value = savedCustomerPhone;
    }
}

// ============================================
// PROCESAR PAGO (ACTUALIZADO)
// ============================================

async function processPayment() {
    console.log('🚀 Iniciando processPayment...');
    console.log('📦 window.paymentRows:', window.paymentRows);

    // Validar formulario de cliente
    const customerName = document.getElementById('modal-customer-name')?.value?.trim();

    if (!customerName) {
        alert('Por favor ingresa el nombre del cliente');
        return;
    }

    // Validar que haya métodos de pago
    if (!window.paymentRows || window.paymentRows.length === 0) {
        alert('No hay métodos de pago registrados');
        console.error('❌ window.paymentRows está vacío');
        return;
    }

    // Recopilar datos del cliente
    const customerData = {
        name: customerName,
        email: document.getElementById('modal-customer-email')?.value?.trim() || null,
        phone: document.getElementById('modal-customer-phone')?.value?.trim() || null,
        notes: document.getElementById('modal-customer-notes')?.value?.trim() || null
    };

    // Recopilar items del pedido
    const order = JSON.parse(localStorage.getItem('order')) || [];

    if (order.length === 0) {
        alert('No hay items en el pedido');
        return;
    }

    // Preparar métodos de pago
    const paymentMethods = window.paymentRows.map(row => ({
        method: row.method,
        amount: parseFloat(row.amount),
        transaction_number: row.reference || null
    }));

    console.log('💳 Métodos de pago preparados:', paymentMethods);

    // Guardar en localStorage
    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));
    localStorage.setItem('customerName', customerData.name);
    localStorage.setItem('customerEmail', customerData.email || '');
    localStorage.setItem('customerPhone', customerData.phone || '');
    localStorage.setItem('customerNotes', customerData.notes || '');

    // Deshabilitar botón
    const confirmBtn = document.querySelector('.step-btn.confirm');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    }

    try {
        // Llamar a processOrder
        if (typeof window.processOrder === 'function') {
            console.log('✅ Llamando a window.processOrder...');
            await window.processOrder();

            // Si llegamos aquí, el pedido se procesó exitosamente
            console.log('✅ Pedido procesado exitosamente');

            // Cerrar el modal
            closePaymentModal();

            // Limpiar datos del modal
            clearModalData();

        } else {
            throw new Error('La función processOrder no está disponible');
        }

    } catch (error) {
        console.error('❌ Error al procesar el pedido:', error);
        alert('Error al procesar el pedido: ' + error.message);

        // Rehabilitar botón
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Pedido';
        }
    }
}

async function submitOrder(requestData) {
    try {
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
            throw new Error(errorData?.message || 'Error al procesar el pedido');
        }

        const data = await response.json();

        if (data.success) {
            const dailyOrderNumber = data.daily_order_number;

            // Mostrar vista previa de impresión
            const printConfirmed = await showPrintConfirmation(dailyOrderNumber);

            if (!printConfirmed) {
                console.log('Impresión cancelada por el usuario');
                return;
            }

            // Limpiar todo y redirigir
            clearOrderData();
            closePaymentModal();
            window.location.href = window.routes.menuIndex;

        } else {
            throw new Error(data.message || 'Error al procesar el pedido');
        }

    } catch (error) {
        console.error('❌ Error en submitOrder:', error);
        alert(`Error: ${error.message}`);
    }
}

function clearOrderData() {
    localStorage.removeItem('order');
    localStorage.removeItem('orderType');
    localStorage.removeItem('tableNumber');
    localStorage.removeItem('orderNotes');
    localStorage.removeItem('paymentMethods');
    localStorage.removeItem('customerName');
    localStorage.removeItem('customerEmail');
    localStorage.removeItem('customerPhone');
    localStorage.removeItem('deliveryService');
    localStorage.removeItem('pickupNotes');
    localStorage.removeItem('paymentProcessed');
}

// ============================================
// GESTIÓN DE MESAS EN MODAL DE PAGO
// ============================================

function loadModalTables() {
    const tableGrid = document.getElementById('table-grid');
    const tableLoading = document.getElementById('table-loading');
    const tableError = document.getElementById('table-error');

    if (!tableGrid) return;

    tableLoading.classList.remove('hidden');
    tableError.classList.add('hidden');
    tableGrid.innerHTML = '';

    fetch(window.routes.tablesAvailable)
        .then(response => response.json())
        .then(data => {
            tableLoading.classList.add('hidden');

            if (data.tables && data.tables.length > 0) {
                renderTables(data.tables);
            } else {
                tableGrid.innerHTML = '<p class="text-center text-gray-500 py-4">No hay mesas disponibles</p>';
            }
        })
        .catch(error => {
            console.error('Error loading tables:', error);
            tableLoading.classList.add('hidden');
            tableError.classList.remove('hidden');
        });
}
function renderTables(tables) {
    const tableGrid = document.getElementById('table-grid');
    tableGrid.innerHTML = '';

    tables.forEach(table => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'table-btn';
        button.textContent = `Mesa ${table.number}`;
        button.dataset.tableId = table.id;
        button.dataset.tableNumber = table.number;

        const state = table.state.toLowerCase().replace(' ', '-');

        if (state === 'disponible') {
            button.addEventListener('click', () => selectTable(table.id, table.number));
        } else {
            button.classList.add(state);
            button.disabled = true;
            button.title = `Estado: ${table.state}`;
        }

        tableGrid.appendChild(button);
    });
}
function selectTable(tableId, tableNumber) {
    selectedTable = { id: tableId, number: tableNumber };

    document.querySelectorAll('.table-btn').forEach(btn => {
        btn.classList.remove('selected');
    });

    const selectedBtn = document.querySelector(`[data-table-id="${tableId}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('selected');
    }

    console.log('Mesa seleccionada:', selectedTable);
}

function loadDeliveryServices() {
    const select = document.getElementById('modal-delivery-service');
    if (!select) return;

    // Aquí deberías cargar los servicios de delivery desde tu backend
    // Por ahora usaremos servicios de ejemplo
    const services = [
        { id: 1, name: 'PedidosYa' },
        { id: 2, name: 'Uber Eats' },
        { id: 3, name: 'Rappi' },
        { id: 4, name: 'Delivery Propio' }
    ];

    select.innerHTML = '<option value="">Seleccione un servicio de delivery</option>';
    services.forEach(service => {
        const option = document.createElement('option');
        option.value = service.id;
        option.textContent = service.name;
        select.appendChild(option);
    });
}
function loadPickupNotes() {
    const notesTextarea = document.getElementById('modal-pickup-notes-text');
    if (!notesTextarea) return;

    const savedNotes = localStorage.getItem('pickupNotes');
    if (savedNotes) {
        notesTextarea.value = savedNotes;
    }

    notesTextarea.addEventListener('input', function () {
        if (this.value.trim()) {
            localStorage.setItem('pickupNotes', this.value);
        } else {
            localStorage.removeItem('pickupNotes');
        }
    });
}

// ============================================
// CONFIGURACIÓN DE MESAS (Sin cambios)
// ============================================

function openTablesConfigModal() {
    console.log('🔧 Abriendo configuración de mesas...');

    const modal = document.getElementById('tables-config-modal');
    if (!modal) {
        console.error('❌ Modal tables-config-modal no encontrado');
        return;
    }

    modal.classList.add('show');
    loadCurrentTablesConfig();
}

function closeTablesConfigModal() {
    const modal = document.getElementById('tables-config-modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

async function loadCurrentTablesConfig() {
    console.log('📥 Cargando configuración de mesas desde BD...');

    const tablesSection = document.getElementById('tables-management-section');
    const toggleContainer = document.getElementById('toggle-container');
    const toggleInput = document.getElementById('tables-enabled-input');

    if (toggleInput) {
        toggleInput.checked = window.tablesConfigState.tablesEnabled;
    }

    if (window.tablesConfigState.tablesEnabled) {
        toggleContainer?.classList.add('active');
        tablesSection?.classList.add('show');
        await loadTablesFromDB();
    } else {
        tablesSection?.classList.remove('show');
        toggleContainer?.classList.remove('active');
    }

    setupToggleListener();
}

async function loadTablesFromDB() {
    const tbody = document.getElementById('tables-tbody');
    const emptyState = document.getElementById('empty-state');
    const tablesCount = document.getElementById('tables-count');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Cargando mesas...</p>
            </td>
        </tr>
    `;

    try {
        const response = await fetch(TABLES_API.available, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Error al cargar las mesas');
        }

        window.tablesConfigState.tables = result.data || [];

        renderTablesTable(window.tablesConfigState.tables);

        if (tablesCount) {
            const count = window.tablesConfigState.tables.length;
            tablesCount.textContent = `${count} ${count === 1 ? 'mesa' : 'mesas'}`;
        }

        console.log('✓ Mesas cargadas:', window.tablesConfigState.tables.length);

    } catch (error) {
        console.error('❌ Error al cargar mesas:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-500 mb-2"></i>
                    <p class="text-red-600">Error al cargar las mesas</p>
                    <button onclick="loadTablesFromDB()" class="mt-2 text-sm text-blue-600 hover:underline">
                        <i class="fas fa-redo mr-1"></i>Intentar de nuevo
                    </button>
                </td>
            </tr>
        `;
    }
}

function setupToggleListener() {
    const toggleInput = document.getElementById('tables-enabled-input');

    if (!toggleInput) return;

    toggleInput.removeEventListener('change', handleToggleChange);
    toggleInput.addEventListener('change', handleToggleChange);
}

function handleToggleChange(e) {
    const isEnabled = e.target.checked;
    const toggleContainer = document.getElementById('toggle-container');
    const tablesSection = document.getElementById('tables-management-section');

    window.tablesConfigState.tablesEnabled = isEnabled;

    if (isEnabled) {
        toggleContainer?.classList.add('active');
        tablesSection?.classList.add('show');
        loadTablesFromDB();
    } else {
        toggleContainer?.classList.remove('active');
        tablesSection?.classList.remove('show');
    }
}

function renderTablesTable(tables) {
    const tbody = document.getElementById('tables-tbody');
    const emptyState = document.getElementById('empty-state');

    if (!tbody) return;

    if (!tables || tables.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) {
            emptyState.style.display = 'block';
        }
        return;
    }

    if (emptyState) {
        emptyState.style.display = 'none';
    }

    tbody.innerHTML = tables.map(table => `
        <tr data-table-id="${table.id}">
            <td>
                <strong>Mesa ${table.number}</strong>
            </td>
            <td>
                ${renderStateBadge(table.state)}
            </td>
            <td>
                <div class="table-actions">
                    <button 
                        class="table-action-btn edit" 
                        onclick="openEditTableModal(${table.id}, '${table.number}', '${table.state}')"
                        title="Editar mesa"
                    >
                        <i class="fas fa-edit"></i>
                        Editar
                    </button>
                    <button 
                        class="table-action-btn delete" 
                        onclick="confirmDeleteTable(${table.id}, '${table.number}')"
                        title="Eliminar mesa"
                    >
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderStateBadge(state) {
    const stateConfig = {
        'Disponible': { class: 'disponible', icon: '✓' },
        'Ocupada': { class: 'ocupada', icon: '●' },
        'Reservada': { class: 'reservada', icon: '◐' },
        'No Disponible': { class: 'no-disponible', icon: '✗' }
    };

    const config = stateConfig[state] || stateConfig['Disponible'];

    return `
        <span class="table-state-badge ${config.class}">
            ${config.icon} ${state}
        </span>
    `;
}

// ============================================
// CRUD DE MESAS (Sin cambios mayores)
// ============================================

function openCreateTableModal() {
    console.log('➕ Abriendo modal para crear mesa');

    const modal = document.getElementById('create-table-modal');
    const title = document.getElementById('create-table-title');
    const form = document.getElementById('create-table-form');
    const tableIdInput = document.getElementById('edit-table-id');

    if (!modal || !form) {
        console.error('❌ Modal o formulario no encontrado');
        return;
    }

    form.reset();
    tableIdInput.value = '';

    if (title) {
        title.innerHTML = '<i class="fas fa-plus-circle"></i> Crear Nueva Mesa';
    }

    modal.classList.add('show');
}

function closeCreateTableModal() {
    const modal = document.getElementById('create-table-modal');
    const form = document.getElementById('create-table-form');

    if (modal) {
        modal.classList.remove('show');
    }

    if (form) {
        form.reset();
    }
}

function openEditTableModal(id, number, state) {
    console.log('✏️ Abriendo modal para editar mesa:', id);

    const modal = document.getElementById('create-table-modal');
    const title = document.getElementById('create-table-title');
    const form = document.getElementById('create-table-form');
    const tableIdInput = document.getElementById('edit-table-id');
    const numberInput = document.getElementById('table-number-input');
    const stateInput = document.getElementById('table-state-input');

    if (!modal || !form) return;

    if (tableIdInput) tableIdInput.value = id;
    if (numberInput) numberInput.value = number;
    if (stateInput) stateInput.value = state;

    if (title) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Mesa';
    }

    modal.classList.add('show');
}

async function handleCreateTable(event) {
    event.preventDefault();

    const form = event.target;
    const tableId = document.getElementById('edit-table-id').value;
    const number = document.getElementById('table-number-input').value;
    const state = document.getElementById('table-state-input').value;
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!number || !state) {
        alert('Por favor complete todos los campos');
        return;
    }

    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const isEdit = tableId !== '';

        // ✅ Preparar datos como JSON (no FormData)
        const requestData = {
            number: number,
            state: state,
            _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
        };

        console.log('📤 Enviando datos:', {
            isEdit,
            tableId,
            url: isEdit ? `/tables/${tableId}` : '/tables',
            data: requestData
        });

        let response;

        if (isEdit) {
            // ✅ Para UPDATE: usar PUT
            response = await fetch(`/tables/${tableId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': requestData._token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    number: requestData.number,
                    state: requestData.state
                })
            });
        } else {
            // ✅ Para CREATE: usar POST
            response = await fetch('/tables', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': requestData._token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    number: requestData.number,
                    state: requestData.state
                })
            });
        }

        console.log('📡 Respuesta HTTP:', response.status, response.statusText);

        // ✅ Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const htmlText = await response.text();
            console.error('❌ Respuesta no es JSON:', htmlText.substring(0, 500));
            throw new Error('El servidor devolvió HTML en lugar de JSON. Revisa las rutas y el controlador.');
        }

        const result = await response.json();
        console.log('📥 Respuesta del servidor:', result);

        if (!response.ok) {
            throw new Error(result.message || `Error HTTP: ${response.status}`);
        }

        if (!result.success) {
            throw new Error(result.message || 'Error al guardar la mesa');
        }

        // ✅ Éxito
        closeCreateTableModal();
        showSuccessMessage(isEdit ? 'Mesa actualizada correctamente' : 'Mesa creada correctamente');
        await loadTablesFromDB();

    } catch (error) {
        console.error('❌ Error al guardar mesa:', error);
        alert('Error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function confirmDeleteTable(id, number) {
    if (confirm(`¿Estás seguro de que deseas eliminar la Mesa ${number}?\n\nEsta acción no se puede deshacer.`)) {
        deleteTable(id);
    }
}

async function deleteTable(id) {
    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        formData.append('_method', 'DELETE');

        const response = await fetch(TABLES_API.delete(id), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Error al eliminar la mesa');
        }

        showSuccessMessage('Mesa eliminada correctamente');
        await loadTablesFromDB();

    } catch (error) {
        console.error('❌ Error al eliminar mesa:', error);
        alert('Error: ' + error.message);
    }
}

// ============================================
// CAMBIO MASIVO DE ESTADO (Sin cambios)
// ============================================

function openBulkStateModal() {
    console.log('🔄 Abriendo modal de cambio masivo');

    const modal = document.getElementById('bulk-state-modal');
    if (!modal) {
        console.error('❌ Modal bulk-state-modal no encontrado');
        return;
    }

    modal.classList.add('show');
    loadBulkStats();
}

function closeBulkStateModal() {
    const modal = document.getElementById('bulk-state-modal');
    const form = document.getElementById('bulk-state-form');

    if (modal) {
        modal.classList.remove('show');
    }

    if (form) {
        form.reset();
    }
}

async function loadBulkStats() {
    const statsContent = document.getElementById('bulk-stats-content');
    const statTotal = document.getElementById('stat-total');

    if (!statsContent) return;

    statsContent.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando estadísticas...';

    try {
        const response = await fetch(TABLES_API.stats, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error('Error al cargar estadísticas');
        }

        statsContent.innerHTML = `
            <div class="stat-item">
                <span class="stat-dot green"></span>
                <span>Disponible: <strong>${result.stats.Disponible || 0}</strong></span>
            </div>
            <div class="stat-item">
                <span class="stat-dot red"></span>
                <span>Ocupada: <strong>${result.stats.Ocupada || 0}</strong></span>
            </div>
            <div class="stat-item">
                <span class="stat-dot yellow"></span>
                <span>Reservada: <strong>${result.stats.Reservada || 0}</strong></span>
            </div>
            <div class="stat-item">
                <span class="stat-dot gray"></span>
                <span>No Disponible: <strong>${result.stats['No Disponible'] || 0}</strong></span>
            </div>
        `;

        if (statTotal) {
            statTotal.textContent = result.total;
        }

    } catch (error) {
        console.error('❌ Error al cargar estadísticas:', error);
        statsContent.innerHTML = '<span class="text-red-500">Error al cargar estadísticas</span>';
    }
}

async function handleBulkStateChange(event) {
    event.preventDefault();

    const form = event.target;
    const stateSelect = document.getElementById('bulk-state-select');
    const newState = stateSelect.value;

    if (!newState) {
        alert('Por favor seleccione un estado');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

        const formData = new FormData();
        formData.append('state', newState);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

        const response = await fetch(TABLES_API.bulkState, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al actualizar las mesas');
        }

        closeBulkStateModal();
        showSuccessMessage(`${result.message}\nMesas actualizadas: ${result.updated_count}`);
        await loadTablesFromDB();

    } catch (error) {
        console.error('❌ Error al cambiar estado masivo:', error);
        alert('Error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// ============================================
// GUARDAR CONFIGURACIÓN
// ============================================

async function saveTablesConfig() {
    const toggleInput = document.getElementById('tables-enabled-input');
    const saveBtn = document.getElementById('save-tables-config');

    if (!toggleInput || !saveBtn) return;

    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const formData = new FormData();
        formData.append('tables_enabled', toggleInput.checked ? '1' : '0');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

        const response = await fetch('/settings/update', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al guardar la configuración');
        }

        showSuccessMessage('✓ Configuración guardada correctamente');

        setTimeout(() => {
            closeTablesConfigModal();

            if (!toggleInput.checked) {
                window.location.reload();
            }
        }, 1500);

    } catch (error) {
        console.error('❌ Error al guardar configuración:', error);
        alert('Error: ' + error.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}

function showSuccessMessage(message) {
    const successEl = document.getElementById('config-success-message');
    const messageText = document.getElementById('success-message-text');

    if (!successEl || !messageText) return;

    messageText.textContent = message;
    successEl.classList.add('show');

    setTimeout(() => {
        successEl.classList.remove('show');
    }, 3000);
}

// ============================================
// UTILIDADES
// ============================================

function showPickupPaymentWarning() {
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';

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

            const summaryTitle = paymentSummary.querySelector('h3');
            if (summaryTitle) {
                summaryTitle.after(warningDiv);
            } else {
                paymentSummary.insertBefore(warningDiv, paymentSummary.firstChild);
            }
        }
    }
}
async function confirmAndProcessOrder() {
    console.log('🚀 Confirmando y procesando pedido...');

    syncPaymentRowsFromDOM();

    console.log('📦 window.paymentRows actual:', window.paymentRows);

    // ✅ VALIDACIÓN CRÍTICA
    if (!window.paymentRows || window.paymentRows.length === 0) {
        alert('Error: No hay métodos de pago registrados. Por favor regresa al Paso 2 y agrega un método de pago.');
        console.error('❌ No hay métodos de pago en window.paymentRows');
        return;
    }
    // Validar que todos los métodos tengan datos válidos
    const validPayments = window.paymentRows.filter(row =>
        row.method && row.amount > 0
    );
    if (validPayments.length === 0) {
        alert('Por favor completa todos los métodos de pago con método y monto válido');
        return;
    }
    // Validar formulario de cliente
    const customerName = document.getElementById('modal-customer-name')?.value?.trim();

    if (!customerName) {
        alert('Por favor ingresa el nombre del cliente');
        return;
    }

    // Recopilar datos del cliente
    const customerData = {
        name: customerName,
        email: document.getElementById('modal-customer-email')?.value?.trim() || '',
        phone: document.getElementById('modal-customer-phone')?.value?.trim() || '',
        notes: document.getElementById('modal-customer-notes')?.value?.trim() || ''
    };

    // Preparar métodos de pago
    const paymentMethods = window.paymentRows.map(row => ({
        method: row.method,
        amount: parseFloat(row.amount),
        transaction_number: row.reference || null
    }));

    console.log('💳 Métodos de pago preparados:', paymentMethods);

    // Guardar en localStorage
    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));
    localStorage.setItem('customerName', customerData.name);
    localStorage.setItem('customerEmail', customerData.email);
    localStorage.setItem('customerPhone', customerData.phone);
    localStorage.setItem('customerNotes', customerData.notes);

    // Deshabilitar botón
    const confirmBtn = document.querySelector('.step-btn.confirm');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    }

    try {
        if (typeof window.processOrder === 'function') {
            console.log('✅ Llamando a window.processOrder...');
            await window.processOrder();

            console.log('✅ Pedido procesado exitosamente');
            closePaymentModal();
            clearModalData();

        } else {
            throw new Error('La función processOrder no está disponible');
        }

    } catch (error) {
        console.error('❌ Error al procesar el pedido:', error);
        alert('Error al procesar el pedido: ' + error.message);

        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Pedido';
        }
    }
}
// Función auxiliar para limpiar datos del modal
function clearModalData() {
    // Limpiar formulario del cliente
    const customerForm = document.getElementById('modal-customer-details-form');
    if (customerForm) {
        customerForm.reset();
    }

    // ✅ LIMPIAR window.paymentRows
    window.paymentRows = [];

    const paymentContainer = document.getElementById('payment-rows-container');
    if (paymentContainer) {
        paymentContainer.innerHTML = '';
    }

    // Resetear paso al inicio
    currentStep = 1;

    // Limpiar selección de mesa
    selectedTable = null;

    // Limpiar servicio de delivery
    selectedDeliveryService = null;

    console.log('✅ Datos del modal limpiados');
}
function debugPaymentRowsInRealTime() {
    console.log('\n🔍 === DEBUG EN TIEMPO REAL ===\n');

    console.log('1️⃣ ESTADO DEL ARRAY:');
    console.log('   - window.paymentRows existe:', typeof window.paymentRows !== 'undefined');
    console.log('   - Cantidad de filas:', window.paymentRows?.length || 0);
    console.log('   - Contenido:', window.paymentRows);

    console.log('\n2️⃣ ESTADO DEL DOM:');
    const domRows = document.querySelectorAll('.payment-row');
    console.log('   - Filas en DOM:', domRows.length);

    console.log('\n3️⃣ COMPARACIÓN DETALLADA:');
    domRows.forEach((row, index) => {
        const rowId = parseInt(row.dataset.rowId);
        const arrayRow = window.paymentRows?.find(r => r.id === rowId);

        // Intentar encontrar elementos con múltiples selectores
        const methodSelect = row.querySelector('.payment-type') ||
            row.querySelector('select.form-select') ||
            row.querySelector('select');

        const amountInput = row.querySelector('.total-paid') ||
            row.querySelector('input[type="number"]');

        console.log(`\n   Fila ${index} (ID: ${rowId}):`);
        console.log('   - Existe en Array:', !!arrayRow);
        console.log('   - Método (DOM):', methodSelect?.value || 'NO ENCONTRADO');
        console.log('   - Método (Array):', arrayRow?.method || 'N/A');
        console.log('   - Monto (DOM):', amountInput?.value || 'NO ENCONTRADO');
        console.log('   - Monto (Array):', arrayRow?.amount || 'N/A');

        if (!arrayRow) {
            console.error('   ❌ FILA NO EXISTE EN ARRAY');
        }

        if (!methodSelect) {
            console.error('   ❌ NO SE ENCONTRÓ SELECT DE MÉTODO');
        }

        if (!amountInput) {
            console.error('   ❌ NO SE ENCONTRÓ INPUT DE MONTO');
        }
    });

    console.log('\n🔍 === FIN DEBUG ===\n');

    // Ejecutar diagnóstico de estructura
    diagnosePaymentRowStructure();
}

// ============================================
// EXPONER FUNCIONES GLOBALMENTE
// ============================================


// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Inicializando sistema de pagos y mesas (3 pasos)...');

    // Botón de pago múltiple
    const btnMultiplePayment = document.getElementById('btn-multiple-payment');
    if (btnMultiplePayment) {
        btnMultiplePayment.addEventListener('click', showPaymentModal);
    }

    // Formulario de crear/editar mesa
    const createForm = document.getElementById('create-table-form');
    if (createForm) {
        createForm.addEventListener('submit', handleCreateTable);
    }

    // Formulario de cambio masivo
    const bulkForm = document.getElementById('bulk-state-form');
    if (bulkForm) {
        bulkForm.addEventListener('submit', handleBulkStateChange);
    }

    // Cerrar modales al hacer clic fuera
    window.addEventListener('click', function (event) {
        const modals = {
            'tables-config-modal': closeTablesConfigModal,
            'create-table-modal': closeCreateTableModal,
            'bulk-state-modal': closeBulkStateModal
        };

        Object.keys(modals).forEach(modalId => {
            if (event.target.id === modalId) {
                modals[modalId]();
            }
        });
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.tables-config-modal.show').forEach(modal => {
                modal.classList.remove('show');
            });

            const paymentModal = document.getElementById('payment-modal');
            if (paymentModal && !paymentModal.classList.contains('hidden')) {
                closePaymentModal();
            }
        }
    });

    console.log('✅ Sistema inicializado correctamente (3 pasos)');
});
// Inicializar listeners cuando carga el DOM
document.addEventListener('DOMContentLoaded', function () {
    // Listeners para botones de tipo de pedido
    document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            selectOrderType(this.dataset.type);
        });
    });

    // Listener para cerrar modal al hacer click en el overlay
    const overlay = document.querySelector('.payment-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', closePaymentModal);
    }
});

function diagnosePaymentRowStructure() {
    console.log('🔍 === DIAGNÓSTICO DE ESTRUCTURA ===');

    const container = document.getElementById('payment-rows-container');
    if (!container) {
        console.error('❌ No se encontró payment-rows-container');
        return;
    }

    const rows = container.querySelectorAll('.payment-row');
    console.log(`📦 Total de filas en DOM: ${rows.length}`);

    rows.forEach((row, index) => {
        console.log(`\n🔍 Analizando fila ${index}:`);
        console.log('   - dataset.rowId:', row.dataset.rowId);
        console.log('   - HTML completo:', row.innerHTML.substring(0, 200));

        // Intentar encontrar todos los posibles selectores
        const selects = row.querySelectorAll('select');
        const inputs = row.querySelectorAll('input');

        console.log(`   - Cantidad de <select>: ${selects.length}`);
        console.log(`   - Cantidad de <input>: ${inputs.length}`);

        selects.forEach((select, i) => {
            console.log(`   - Select ${i}:`, {
                class: select.className,
                value: select.value,
                'data-row-id': select.dataset?.rowId
            });
        });

        inputs.forEach((input, i) => {
            console.log(`   - Input ${i}:`, {
                type: input.type,
                class: input.className,
                value: input.value,
                'data-row-id': input.dataset?.rowId
            });
        });
    });

    console.log('🔍 === FIN DIAGNÓSTICO ===\n');
}

window.updatePaymentRowFromSelect = updatePaymentRowFromSelect;
window.updatePaymentRowFromInput = updatePaymentRowFromInput;

console.log('✅ Payment Modal JS cargado correctamente');
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.debugPaymentRowsInRealTime = debugPaymentRowsInRealTime;
window.showPaymentModal = showPaymentModal;
window.addPaymentRow = addPaymentRow;
window.renderPaymentRows = renderPaymentRows;
window.validateStep2 = validateStep2;
window.updatePaymentRow = updatePaymentRow;
window.removePaymentRow = removePaymentRow;
window.updateNoPaymentsMessage = updateNoPaymentsMessage;
window.openTablesConfigModal = openTablesConfigModal;
window.closeTablesConfigModal = closeTablesConfigModal;
window.loadCurrentTablesConfig = loadCurrentTablesConfig;
window.loadTablesFromDB = loadTablesFromDB;
window.saveTablesConfig = saveTablesConfig;
window.openCreateTableModal = openCreateTableModal;
window.closeCreateTableModal = closeCreateTableModal;
window.openEditTableModal = openEditTableModal;
window.handleCreateTable = handleCreateTable;
window.confirmDeleteTable = confirmDeleteTable;
window.deleteTable = deleteTable;

window.openBulkStateModal = openBulkStateModal;
window.closeBulkStateModal = closeBulkStateModal;
window.handleBulkStateChange = handleBulkStateChange;

window.loadModalTables = loadModalTables;
window.selectTable = selectTable;
window.goToStep = goToStep;
window.prevStep = prevStep;

// Nuevas funciones del paso 3
window.loadStep3Summary = loadStep3Summary;
window.loadStep3OrderSummary = loadStep3OrderSummary;
window.loadStep3PaymentDetails = loadStep3PaymentDetails;
window.loadStep3CustomerData = loadStep3CustomerData;
window.validateStep1 = validateStep1;
window.validateStep2 = validateStep2;
window.processPayment = processPayment;
window.submitOrder = submitOrder;
window.clearOrderData = clearOrderData;

window.nextStep = nextStep;
window.confirmAndProcessOrder = confirmAndProcessOrder;
window.renderPaymentRows = renderPaymentRows;
window.removePaymentRow = removePaymentRow;
window.syncPaymentRowsFromDOM = syncPaymentRowsFromDOM;
window.updatePaymentRowField = updatePaymentRowField;
window.debugPaymentRowsInRealTime = debugPaymentRowsInRealTime;
window.diagnosePaymentRowStructure = diagnosePaymentRowStructure;

