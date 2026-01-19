(async function initializeTablesState() {
    try {
        console.log('🔄 Sincronizando estado inicial de mesas desde payment-modal.js...');

        const response = await fetch('/settings/tables-status?t=' + Date.now(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        });

        if (!response.ok) {
            throw new Error('Error al obtener estado de mesas');
        }

        const data = await response.json();
        const tablesEnabled = data.tables_enabled || false;

        // Inicializar TODAS las variables globales con el mismo valor
        window.tablesConfigState = {
            tables: [],
            isLoading: false,
            tablesEnabled: tablesEnabled
        };

        window.tablesManagementEnabled = tablesEnabled;

        console.log('✅ Estado inicial de mesas sincronizado desde JS:', {
            tablesEnabled: tablesEnabled,
            source: 'payment-modal.js'
        });

    } catch (error) {
        console.error('❌ Error al sincronizar estado inicial:', error);

        // Fallback: inicializar como deshabilitado
        window.tablesConfigState = {
            tables: [],
            isLoading: false,
            tablesEnabled: false
        };

        window.tablesManagementEnabled = false;

        console.log('⚠️ Usando valor fallback: mesas deshabilitadas');
    }
})();

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
    maxSteps: 3,
    selectedOrderType: 'comer-aqui',
    selectedTable: null,
    paymentRows: [],
    customerData: {},
    selectedDeliveryService: null,
    pickupNotes: ''
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
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido');
        return;
    }
    window.paymentModalState.currentStep = 1;
    window.paymentRows = [];
    paymentRowCounter = 0;
    const modal = document.getElementById('payment-modal');
    if (!modal) {
        console.error('❌ No se encontró el modal');
        return;
    }

    modal.classList.remove('hidden');
    goToStep(1);
    updateOrderTotal();
    loadOrderData();
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';

    setTimeout(() => {
        initializeModal();
        if (typeof showPickupPaymentWarning === 'function') {
            showPickupPaymentWarning();
        }
    }, 50);
}

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

    // ✅ ACTUALIZAR MENSAJE
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
            syncPaymentRowsFromDOM();
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
    console.log(`\n🔄 === AVANZANDO DE PASO ${currentStep} A ${currentStep + 1} ===`);

    // ✅ CRÍTICO: Si estamos en el Paso 2, sincronizar ANTES de validar
    if (currentStep === 2) {
        console.log('💳 Sincronizando métodos de pago antes de avanzar...');
        syncPaymentRowsFromDOM();
        console.log('📦 window.paymentRows sincronizado:', window.paymentRows);
    }

    // ✅ Validar el paso actual
    if (!validateCurrentStep()) {
        console.warn('⚠️ Validación fallida, no se puede avanzar');
        return;
    }

    // ✅ Avanzar al siguiente paso
    if (currentStep < totalSteps) {
        currentStep++;
        console.log(`✅ Avanzando al paso ${currentStep}`);

        updateStepDisplay();

        // ✅ Acciones específicas por paso
        if (currentStep === 2) {
            setTimeout(() => {
                const rowsContainer = document.getElementById('payment-rows-container');
                if (rowsContainer && rowsContainer.children.length === 0) {
                    console.log('➕ Agregando primera fila automáticamente...');
                    addPaymentRow();
                }
            }, 100);
        }
        else if (currentStep === 3) {
            // ✅✅ CRÍTICO: Actualizar resumen en el Paso 3
            console.log('📋 Preparando Paso 3...');

            setTimeout(() => {
                console.log('🔄 Ejecutando updateStep3Summary...');
                updateStep3Summary();
                console.log('✅ Resumen del Paso 3 actualizado');
            }, 150);
        }
    }

    console.log('=== FIN AVANCE DE PASO ===\n');
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
/**
 * Agregar una nueva fila de gasto en el modal de cierre
 */
function addExpenseModalClosure() {
    console.log('➕ Agregando nueva fila de gasto en modal de cierre...');

    const container = document.getElementById('expensesContainerClosure');

    if (!container) {
        console.error('❌ No se encontró expensesContainerClosure');
        return;
    }

    // Crear nueva fila de gasto
    const expenseRow = document.createElement('div');
    expenseRow.className = 'expense-row';

    expenseRow.innerHTML = `
        <div class="expense-field">
            <input type="text" 
                   class="expense-input" 
                   placeholder="Nombre del gasto" 
                   name="expense_name[]"
                   autocomplete="off">
        </div>
        <div class="expense-field">
            <input type="text" 
                   class="expense-input" 
                   placeholder="Descripción/Categoría" 
                   name="expense_description[]"
                   autocomplete="off">
        </div>
        <div class="expense-field">
            <input type="number" 
                   class="expense-input" 
                   placeholder="Monto" 
                   step="0.01" 
                   min="0" 
                   name="expense_amount[]"
                   autocomplete="off"
                   oninput="actualizarTotalGastosClosure()">
        </div>
        <div class="expense-actions">
            <button type="button" 
                    class="btn btn-danger" 
                    onclick="removeExpenseClosure(this)"
                    aria-label="Eliminar gasto">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    // Agregar animación de entrada
    expenseRow.style.opacity = '0';
    expenseRow.style.transform = 'translateY(-10px)';

    container.appendChild(expenseRow);

    // Animar entrada
    setTimeout(() => {
        expenseRow.style.transition = 'all 0.3s ease';
        expenseRow.style.opacity = '1';
        expenseRow.style.transform = 'translateY(0)';
    }, 10);

    console.log('✅ Nueva fila de gasto agregada');

    // Scroll al final del contenedor
    container.scrollTop = container.scrollHeight;
}
/**
 * Eliminar una fila de gasto del modal de cierre
 */
function removeExpenseClosure(button) {
    console.log('🗑️ Eliminando fila de gasto...');

    const expenseRow = button.closest('.expense-row');
    const container = document.getElementById('expensesContainerClosure');

    if (!expenseRow) {
        console.error('❌ No se encontró la fila de gasto');
        return;
    }

    // Verificar que no sea la única fila
    const totalRows = container.querySelectorAll('.expense-row').length;

    if (totalRows === 1) {
        // Si es la única fila, limpiar los campos en lugar de eliminar
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');

        console.log('ℹ️ Se limpiaron los campos de la única fila existente');
        showNotification('No se puede eliminar la última fila. Se limpiaron los campos.', 'info');

        // Actualizar total de gastos
        actualizarTotalGastosClosure();
        return;
    }

    // Animar salida
    expenseRow.style.transition = 'all 0.3s ease';
    expenseRow.style.opacity = '0';
    expenseRow.style.transform = 'translateX(-20px)';

    setTimeout(() => {
        expenseRow.remove();
        console.log('✅ Fila de gasto eliminada');

        // Actualizar total de gastos después de eliminar
        actualizarTotalGastosClosure();
    }, 300);
}
/**
 * Actualizar el total de gastos en el modal de cierre
 */
function actualizarTotalGastosClosure() {
    console.log('🔄 Actualizando total de gastos...');

    const container = document.getElementById('expensesContainerClosure');
    const totalGastosInput = document.getElementById('total-gastos-closure');

    if (!container || !totalGastosInput) {
        console.error('❌ No se encontraron elementos necesarios');
        return;
    }

    // Obtener todos los inputs de montos
    const amountInputs = container.querySelectorAll('input[name="expense_amount[]"]');

    let totalGastos = 0;

    amountInputs.forEach(input => {
        const amount = parseFloat(input.value) || 0;
        totalGastos += amount;
    });

    // Obtener gastos existentes de la BD (si existen)
    const gastosExistentesBD = parseFloat(totalGastosInput.dataset.gastosBd) || 0;

    // Sumar gastos nuevos + gastos de BD
    const totalFinal = totalGastos + gastosExistentesBD;

    // Actualizar input
    totalGastosInput.value = totalFinal.toFixed(2);

    console.log('💰 Total gastos actualizado:', {
        nuevosGastos: totalGastos.toFixed(2),
        gastosExistentes: gastosExistentesBD.toFixed(2),
        totalFinal: totalFinal.toFixed(2)
    });

    // Actualizar cálculos de cierre (si existe la función)
    if (typeof actualizarCalculosCierre === 'function') {
        actualizarCalculosCierre();
    }
}
/**
 * Validar gastos antes de guardar el cierre
 */
function validarGastosCierre() {
    console.log('🔍 Validando gastos del cierre...');

    const container = document.getElementById('expensesContainerClosure');

    if (!container) {
        console.error('❌ No se encontró el contenedor de gastos');
        return false;
    }

    const rows = container.querySelectorAll('.expense-row');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];

        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput?.value?.trim();
        const amount = parseFloat(amountInput?.value) || 0;

        // Si hay monto pero no hay nombre
        if (amount > 0 && !name) {
            alert(`⚠️ Por favor ingresa un nombre para el gasto de la fila ${i + 1}`);
            nameInput?.focus();
            return false;
        }

        // Si hay nombre pero no hay monto (o es cero)
        if (name && amount <= 0) {
            alert(`⚠️ Por favor ingresa un monto válido para "${name}"`);
            amountInput?.focus();
            return false;
        }
    }

    console.log('✅ Validación de gastos exitosa');
    return true;
}
/**
 * Recopilar datos de gastos para enviar al backend
 */
function recopilarGastosCierre() {
    console.log('📦 Recopilando datos de gastos...');

    const container = document.getElementById('expensesContainerClosure');

    if (!container) {
        console.error('❌ No se encontró el contenedor de gastos');
        return [];
    }

    const rows = container.querySelectorAll('.expense-row');
    const gastos = [];

    rows.forEach((row, index) => {
        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const descriptionInput = row.querySelector('input[name="expense_description[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput?.value?.trim();
        const description = descriptionInput?.value?.trim();
        const amount = parseFloat(amountInput?.value) || 0;

        // Solo agregar si tiene nombre Y monto
        if (name && amount > 0) {
            gastos.push({
                expense_name: name,
                description: description || null,
                amount: amount
            });

            console.log(`  📝 Gasto ${index + 1}:`, {
                name,
                description: description || '(sin descripción)',
                amount: amount.toFixed(2)
            });
        }
    });

    console.log(`✅ Total gastos recopilados: ${gastos.length}`);
    return gastos;
}
/**
 * Limpiar todos los gastos del modal de cierre
 */
function limpiarGastosCierre() {
    console.log('🧹 Limpiando gastos del modal de cierre...');

    const container = document.getElementById('expensesContainerClosure');

    if (!container) {
        console.error('❌ No se encontró el contenedor de gastos');
        return;
    }

    // Eliminar todas las filas excepto la primera
    const rows = container.querySelectorAll('.expense-row');

    rows.forEach((row, index) => {
        if (index === 0) {
            // Limpiar la primera fila
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        } else {
            // Eliminar las demás filas
            row.remove();
        }
    });

    // Actualizar total
    actualizarTotalGastosClosure();

    console.log('✅ Gastos limpiados');
}
/**
 * Mostrar notificación en el modal de cierre
 */
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
            type === 'info' ? 'bg-blue-500' :
                'bg-yellow-500';

    const icon = type === 'success' ? 'check-circle' :
        type === 'error' ? 'exclamation-circle' :
            type === 'info' ? 'info-circle' :
                'exclamation-triangle';

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-[10000] transform transition-all duration-300`;
    notification.style.animation = 'slideInRight 0.3s ease-out';

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(20px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
/**
 * Función principal para guardar el cierre (MODIFICADA)
 */
async function guardarCierreUnificado() {
    console.log('\n🚀 === INICIANDO GUARDADO DE CIERRE ===\n');

    // 1. Validar gastos PRIMERO
    if (!validarGastosCierre()) {
        console.error('❌ Validación de gastos fallida');
        return;
    }

    // 2. Recopilar datos de gastos
    const gastosNuevos = recopilarGastosCierre();
    console.log('📦 Gastos nuevos a guardar:', gastosNuevos);

    // 3. Obtener ID de caja chica
    const pettyCashId = document.getElementById('petty_cash_id_closure')?.value;

    if (!pettyCashId) {
        alert('❌ Error: No se encontró el ID de caja chica');
        console.error('❌ No se encontró petty_cash_id_closure');
        return;
    }

    // 4. Recopilar otros datos del cierre (denominaciones, ventas, etc.)
    const cierreData = {
        petty_cash_id: pettyCashId,
        gastos_nuevos: gastosNuevos, // ✅ Agregar gastos al objeto
        total_gastos: parseFloat(document.getElementById('total-gastos-closure')?.value) || 0,
        ventas_efectivo: parseFloat(document.getElementById('ventas-efectivo-closure')?.value) || 0,
        ventas_qr: parseFloat(document.getElementById('ventas-qr-closure')?.value) || 0,
        ventas_tarjeta: parseFloat(document.getElementById('ventas-tarjeta-closure')?.value) || 0,
        total_efectivo: parseFloat(document.getElementById('total-closure')?.textContent?.replace('$', '')) || 0,
        denominaciones: recopilarDenominaciones()
    };

    console.log('📤 Datos completos del cierre:', cierreData);

    // 5. Deshabilitar botón de guardar
    const saveBtn = document.querySelector('.save-btn');
    const originalText = saveBtn?.innerHTML;

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    try {
        // 6. Enviar al backend
        const response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(cierreData)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `Error HTTP: ${response.status}`);
        }

        const result = await response.json();
        console.log('✅ Respuesta del servidor:', result);

        if (!result.success) {
            throw new Error(result.message || 'Error al guardar el cierre');
        }

        // 7. Éxito
        showNotification('✅ Cierre guardado exitosamente', 'success');

        // 8. Cerrar modal y limpiar
        setTimeout(() => {
            closeInternalModalClosure();

            // Opcional: recargar página o actualizar lista
            if (typeof loadPettyCashData === 'function') {
                loadPettyCashData();
            }
        }, 1500);

        console.log('✅ === CIERRE GUARDADO EXITOSAMENTE ===\n');

    } catch (error) {
        console.error('❌ Error al guardar cierre:', error);
        alert(`❌ Error al guardar el cierre:\n${error.message}`);

    } finally {
        // Rehabilitar botón
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }
}
/**
 * Cerrar modal interno de cierre
 */
function closeInternalModalClosure() {
    const modal = document.getElementById('modal-closure-internal');
    const overlay = document.getElementById('closure-internal-overlay');

    if (modal) {
        modal.classList.remove('active');
    }

    if (overlay) {
        overlay.classList.remove('active');
    }

    // Limpiar gastos
    limpiarGastosCierre();
}
/**
 * Recopilar datos de denominaciones
 */
function recopilarDenominaciones() {
    const denominaciones = {};

    document.querySelectorAll('.contar-input-closure').forEach(input => {
        const denominacion = input.dataset.denominacion;
        const cantidad = parseInt(input.value) || 0;

        if (cantidad > 0) {
            denominaciones[denominacion] = cantidad;
        }
    });

    return denominaciones;
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
    console.log('\n📋 === INICIANDO updateStep3Summary ===');

    // ============================================
    // PASO 1: VERIFICAR DATOS
    // ============================================
    console.log('📦 window.paymentRows:', window.paymentRows);
    console.log('📦 Cantidad:', window.paymentRows?.length || 0);

    const paymentDetails = document.getElementById('step3-payment-methods');

    if (!paymentDetails) {
        console.error('❌ No se encontró el elemento step3-payment-methods');
        return;
    }

    console.log('✅ Elemento step3-payment-methods encontrado');

    // ============================================
    // PASO 2: GENERAR HTML
    // ============================================
    let paymentHTML = '';

    if (!window.paymentRows || window.paymentRows.length === 0) {
        console.error('❌ window.paymentRows está vacío o no existe');

        paymentHTML = `
            <div style="text-align: center; padding: 20px; color: #ef4444; background: #fee2e2; border-radius: 8px; border: 1px solid #fecaca;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 8px;"></i>
                <p style="margin: 0; font-weight: 600;">No hay métodos de pago registrados</p>
                <small style="display: block; margin-top: 8px;">window.paymentRows está vacío</small>
            </div>
        `;
    } else {
        console.log(`✅ Generando HTML para ${window.paymentRows.length} métodos`);

        window.paymentRows.forEach((row, index) => {
            const method = row.method || 'Sin método';
            const reference = row.reference || '';
            const amount = parseFloat(row.amount) || 0;

            console.log(`   💳 Método ${index + 1}:`, { method, reference, amount });

            paymentHTML += `
                <div class="payment-method-item" style="margin-bottom: 12px;">
                    <div class="payment-method-name">
                        <div class="payment-method-icon">
                            <i class="fas fa-${getPaymentMethodIcon(method)}"></i>
                        </div>
                        <div>
                            <strong>${method}</strong>
                            ${reference ? `<br><small style="color: var(--text-secondary);">Ref: ${reference}</small>` : ''}
                        </div>
                    </div>
                    <div class="payment-method-amount">$${amount.toFixed(2)}</div>
                </div>
            `;
        });

        console.log('✅ HTML generado, longitud:', paymentHTML.length);
    }

    // ============================================
    // PASO 3: INSERTAR HTML
    // ============================================
    console.log('📤 Insertando HTML en el DOM...');
    paymentDetails.innerHTML = paymentHTML;
    console.log('✅ HTML insertado');
    console.log('🔍 Verificación final - Contenido del div:', paymentDetails.innerHTML.substring(0, 200));

    console.log('✅ === updateStep3Summary COMPLETADO ===\n');
}
function getPaymentMethodIcon(method) {
    const icons = {
        'Efectivo': 'money-bill-wave',
        'QR': 'qrcode',
        'Tarjeta': 'credit-card',
        'Transferencia': 'exchange-alt'
    };
    return icons[method] || 'dollar-sign';
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
    }
    else if (orderType === 'Recojo por Delivery') {
        const deliverySelect = document.getElementById('modal-delivery-service');
        selectedDeliveryService = deliverySelect?.value;

        if (!selectedDeliveryService) {
            alert('Por favor selecciona un servicio de delivery');
            return false;
        }
    }

    return true;
}
function generateDailyOrderNumber() {
    const today = new Date().toISOString().split('T')[0];
    const savedDate = localStorage.getItem('orderNumberDate');
    let counter = parseInt(localStorage.getItem('orderNumberCounter') || '0');

    if (savedDate !== today) {
        counter = 1;
        localStorage.setItem('orderNumberDate', today);
    } else {
        counter++;
    }

    localStorage.setItem('orderNumberCounter', counter.toString());
    return counter;
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

    // Cargar datos del cliente si existen
    loadStep3CustomerData();
}

async function loadStep3OrderSummary() {
    const summaryContainer = document.getElementById('step3-order-summary');
    const totalElement = document.getElementById('step3-order-total');

    if (!summaryContainer) return;

    const order = JSON.parse(localStorage.getItem('order')) || [];

    if (order.length === 0) {
        summaryContainer.innerHTML = '<p class="text-gray-500 text-center">No hay ítems en el pedido</p>';
        return;
    }

    const total = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const step3Total = document.getElementById('step3-order-total');
    if (step3Total) {
        step3Total.textContent = total.toFixed(2);
    }

    // ✅ OBTENER EL NÚMERO DE PEDIDO DESDE EL BACKEND
    let orderNumber = '...';

    try {
        summaryContainer.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; border: 2px solid #e5e7eb; padding: 20px 24px; border-radius: 8px; margin-bottom: 20px;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Pedido</span>
                <span style="font-size: 2.5rem; font-weight: 700; color: #111827; letter-spacing: 1px; line-height: 1;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: #6b7280;"></i>
                </span>
            </div>
        `;

        const response = await fetch('/api/sales/next-order-number', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            orderNumber = data.next_order_number; // Ya viene con formato "PED-00001"
            localStorage.setItem('currentOrderNumber', orderNumber);
        } else {
            throw new Error('No se pudo obtener el número de pedido');
        }
    } catch (error) {
        console.error('❌ Error al obtener número de pedido:', error);
        orderNumber = generateDailyOrderNumber();
        localStorage.setItem('currentOrderNumber', orderNumber);
    }

    if (summaryContainer) {
        let summaryHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; border: 2px solid #e5e7eb; padding: 20px 24px; border-radius: 8px; margin-bottom: 20px; transition: all 0.3s ease;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Pedido</span>
                <span style="font-size: 2rem; font-weight: 700; color: #111827; letter-spacing: 0.5px; line-height: 1;">${orderNumber}</span>
            </div>
        `;

        summaryContainer.innerHTML = summaryHTML;
    }

    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
}
function getOrderTypeLabel(type) {
    const labels = {
        'comer-aqui': 'Comer aquí',
        'para-llevar': 'Para llevar',
        'recoger': 'Recoger'
    };
    return labels[type] || type;
}
function generateDailyOrderNumber() {
    const today = new Date().toISOString().split('T')[0];
    const savedDate = localStorage.getItem('orderNumberDate');
    let counter = parseInt(localStorage.getItem('orderNumberCounter') || '0');

    if (savedDate !== today) {
        counter = 1;
        localStorage.setItem('orderNumberDate', today);
    } else {
        counter++;
    }

    localStorage.setItem('orderNumberCounter', counter.toString());
    return counter;
}



function getPaymentIcon(method) {
    const icons = {
        'QR': '📱',
        'Efectivo': '💵',
        'Tarjeta': '💳',
        'Transferencia': '🏦'
    };
    return icons[method] || '💰';
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

    if (!tableGrid) {
        console.warn('⚠️ table-grid no encontrado');
        return;
    }

    // Mostrar loading
    tableLoading?.classList.remove('hidden');
    tableError?.classList.add('hidden');
    tableGrid.innerHTML = '';

    console.log('📡 Cargando mesas desde:', window.routes?.tablesAvailable || '/tables/available');

    fetch(window.routes?.tablesAvailable || '/tables/available', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Mesas recibidas:', data);

            tableLoading?.classList.add('hidden');

            if (data.success && data.tables && data.tables.length > 0) {
                renderTables(data.tables);

                // ✅ RESTAURAR SELECCIÓN PREVIA SI EXISTE
                if (selectedTable && selectedTable.id) {
                    const previouslySelectedBtn = document.querySelector(`[data-table-id="${selectedTable.id}"]`);
                    if (previouslySelectedBtn && !previouslySelectedBtn.disabled) {
                        previouslySelectedBtn.classList.add('selected');
                        console.log('✅ Selección de mesa restaurada:', selectedTable.number);
                    } else {
                        // Si la mesa ya no está disponible, limpiar selección
                        console.log('⚠️ Mesa previamente seleccionada ya no está disponible');
                        selectedTable = null;
                    }
                }
            } else {
                tableGrid.innerHTML = '<p class="text-center text-gray-500 py-4">No hay mesas disponibles</p>';
            }
        })
        .catch(error => {
            console.error('❌ Error al cargar mesas:', error);
            tableLoading?.classList.add('hidden');
            tableError?.classList.remove('hidden');

            const errorMessage = document.getElementById('table-error-message');
            if (errorMessage) {
                errorMessage.textContent = error.message || 'Error al cargar las mesas';
            }
        });
}

function renderTables(tables) {
    const tableGrid = document.getElementById('table-grid');

    if (!tableGrid) {
        console.error('❌ table-grid no encontrado');
        return;
    }

    tableGrid.innerHTML = '';

    if (!tables || tables.length === 0) {
        tableGrid.innerHTML = '<p class="text-center text-gray-500 py-4">No hay mesas configuradas</p>';
        return;
    }

    console.log(`🎨 Renderizando ${tables.length} mesas`);

    tables.forEach(table => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'table-btn';
        button.textContent = `Mesa ${table.number}`;
        button.dataset.tableId = table.id;
        button.dataset.tableNumber = table.number;

        // Normalizar estado
        const state = (table.state || 'Disponible').toLowerCase().replace(/\s+/g, '-');

        console.log(`📍 Mesa ${table.number} - Estado: ${table.state} (${state})`);

        if (state === 'disponible') {
            // ✅ Mesa disponible - puede ser seleccionada
            button.addEventListener('click', () => selectTable(table.id, table.number));

            // Si esta mesa estaba previamente seleccionada, marcarla
            if (selectedTable && selectedTable.id === table.id) {
                button.classList.add('selected');
            }
        } else {
            // ❌ Mesa no disponible
            button.classList.add(state);
            button.disabled = true;
            button.title = `Estado: ${table.state}`;
        }

        tableGrid.appendChild(button);
    });

    console.log('✅ Mesas renderizadas correctamente');
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
function openTablesConfigModalFromPayment() {
    console.log('🔧 Abriendo configuración de mesas desde modal de pago...');

    // Marcar que venimos del modal de pago
    window.openedFromPaymentModal = true;

    openTablesConfigModal();
}

function closeTablesConfigModal() {
    const modal = document.getElementById('tables-config-modal');
    if (modal) {
        modal.classList.remove('show');

        console.log('🔒 Modal de configuración cerrado');

        // ✅ SI EL MODAL DE PAGO ESTÁ ABIERTO, RECARGAR MESAS
        const paymentModal = document.getElementById('payment-modal');
        if (paymentModal && !paymentModal.classList.contains('hidden')) {
            console.log('🔄 Recargando mesas en modal de pago...');

            // Pequeño delay para asegurar que el modal de configuración se cerró completamente
            setTimeout(() => {
                const tablesEnabled = window.tablesConfigState?.tablesEnabled || false;

                if (tablesEnabled) {
                    loadModalTables();
                }
            }, 300);
        }
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
    console.log('💾 Guardando configuración de mesas...');

    const toggleInput = document.getElementById('tables-enabled-input');
    const saveBtn = document.getElementById('save-tables-config');
    const successMessage = document.getElementById('config-success-message');
    const paymentModal = document.getElementById('payment-modal');

    if (!toggleInput || !saveBtn) {
        console.error('❌ Elementos del formulario no encontrados');
        return;
    }

    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    saveBtn.classList.add('btn-loading');

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('tables_enabled', toggleInput.checked ? '1' : '0');

    const tableRows = document.querySelectorAll('#tables-tbody tr');
    const tablesData = [];

    tableRows.forEach(row => {
        const tableId = row.dataset.tableId;
        const stateSelect = row.querySelector('.table-state-select');
        if (tableId && stateSelect) {
            tablesData.push({
                id: tableId,
                state: stateSelect.value
            });
        }
    });

    formData.append('tables', JSON.stringify(tablesData));

    fetch('/settings/update', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
        .then(response => response.json())
        .then(async data => {
            if (data.success) {
                // ✅ CRÍTICO: Actualizar estado INMEDIATAMENTE
                const newState = toggleInput.checked;
                window.tablesManagementEnabled = newState;

                if (!window.tablesConfigState) {
                    window.tablesConfigState = {};
                }
                window.tablesConfigState.tablesEnabled = newState;

                // 🔥 Resetear TODAS las banderas
                window._tableVisibilityChecked = false;
                window._modalTablesLoaded = false;
                window._orderTypeButtonsConfigured = false;
                _handlingTableVisibility = false;
                _lastHandlingTime = 0;

                console.log('✅ Estado actualizado INMEDIATAMENTE:', {
                    newState: newState,
                    tablesManagementEnabled: window.tablesManagementEnabled,
                    tablesConfigState: window.tablesConfigState.tablesEnabled
                });

                // Mostrar mensaje de éxito
                if (successMessage) {
                    const messageText = successMessage.querySelector('#success-message-text');
                    if (messageText) {
                        messageText.textContent = data.message || 'Configuración guardada exitosamente';
                    }
                    successMessage.classList.add('show');
                    setTimeout(() => {
                        successMessage.classList.remove('show');
                    }, 3000);
                }

                // 🔥 SECCIÓN CORREGIDA: Actualizar modal de pago si está abierto
                setTimeout(async () => {
                    const selectedButton = document.querySelector('.order-type-btn.selected');

                    // ✅ Solo actualizar si "Comer aquí" está seleccionado
                    if (selectedButton && selectedButton.dataset.type === 'comer-aqui') {
                        console.log('🔄 Actualizando visibilidad ANTES de cerrar modal...');
                        await handleTableSelectionVisibility(true);
                    }

                    // Cerrar el modal de configuración
                    closeTablesConfigModal();

                    // Reconfigurar botones
                    setupOrderTypeButtons();

                    // ✅ CRÍTICO: Si modal de pago está abierto, actualizar contenido
                    if (paymentModal && !paymentModal.classList.contains('hidden')) {
                        console.log('🔄 Modal de pago abierto, actualizando contenido...');

                        const tableSelection = document.getElementById('modal-table-selection');

                        if (newState) {
                            // ✅ Mesas HABILITADAS: Mostrar grid
                            console.log('✅ Mesas habilitadas: mostrando grid');

                            if (tableSelection) {
                                tableSelection.classList.remove('hidden');
                            }

                            // Ocultar mensaje de deshabilitado
                            const disabledMessage = document.getElementById('tables-disabled-message');
                            if (disabledMessage) {
                                disabledMessage.classList.add('hidden');
                            }

                            // Mostrar y cargar grid
                            const tableGrid = document.getElementById('table-grid');
                            if (tableGrid) {
                                tableGrid.classList.remove('hidden');
                                // Forzar recarga de mesas
                                window._modalTablesLoaded = false;
                                await loadModalTables(true);
                            }

                        } else {
                            // ✅ Mesas DESHABILITADAS: Mostrar mensaje
                            console.log('❌ Mesas deshabilitadas: mostrando mensaje');

                            // ✅ MANTENER tableSelection VISIBLE (contiene el botón)
                            if (tableSelection) {
                                tableSelection.classList.remove('hidden');
                            }

                            // ✅ MOSTRAR mensaje de deshabilitado
                            const disabledMessage = document.getElementById('tables-disabled-message');
                            if (disabledMessage) {
                                disabledMessage.classList.remove('hidden');
                                console.log('✅ Mensaje de mesas deshabilitadas mostrado');
                            }

                            // ✅ OCULTAR grid de mesas
                            const tableGrid = document.getElementById('table-grid');
                            if (tableGrid) {
                                tableGrid.classList.add('hidden');
                                console.log('✅ Grid de mesas ocultado');
                            }

                            // ✅ Ocultar loading si existe
                            const tableLoading = document.getElementById('table-loading');
                            if (tableLoading) {
                                tableLoading.classList.add('hidden');
                            }
                        }

                        // ✅ Asegurar que el modal de pago permanezca visible
                        paymentModal.classList.remove('hidden');
                        paymentModal.style.display = 'flex';

                        console.log('✅ Actualización completada, verificando estado final...');

                        // Verificación final
                        setTimeout(() => {
                            const finalState = {
                                tableSelection: !document.getElementById('modal-table-selection')?.classList.contains('hidden'),
                                disabledMessage: !document.getElementById('tables-disabled-message')?.classList.contains('hidden'),
                                tableGrid: !document.getElementById('table-grid')?.classList.contains('hidden'),
                                configButton: !!document.querySelector('.tables-config-btn')
                            };
                            console.log('🔍 Estado final de elementos:', finalState);
                        }, 100);
                    }
                }, 500);

            } else {
                throw new Error(data.message || 'Error al guardar la configuración');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('Error al guardar la configuración: ' + error.message);

            if (paymentModal) {
                paymentModal.classList.remove('hidden');
                paymentModal.style.display = 'flex';
            }
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
            saveBtn.classList.remove('btn-loading');
        });
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

    if (!window.paymentRows || window.paymentRows.length === 0) {
        alert('Error: No hay métodos de pago registrados. Por favor regresa al Paso 2 y agrega un método de pago.');
        return;
    }

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

    // Recopilar TODOS los datos del cliente (incluyendo los nuevos campos)
    const customerData = {
        name: customerName,
        email: document.getElementById('modal-customer-email')?.value?.trim() || '',
        phone: document.getElementById('modal-customer-phone')?.value?.trim() || '',
        notes: document.getElementById('modal-customer-notes')?.value?.trim() || '',
        document_type: document.getElementById('modal-customer-doc-type')?.value || 'CI',
        document_number: document.getElementById('modal-customer-doc-number')?.value?.trim() || '',
        address: document.getElementById('modal-customer-address')?.value?.trim() || '',
        city: document.getElementById('modal-customer-city')?.value?.trim() || '',
        client_id: localStorage.getItem('selectedClientId') || null // ID si ya fue guardado
    };

    // Preparar métodos de pago
    const paymentMethods = window.paymentRows.map(row => ({
        method: row.method,
        amount: parseFloat(row.amount),
        transaction_number: row.reference || null
    }));

    console.log('💳 Métodos de pago preparados:', paymentMethods);
    console.log('👤 Datos del cliente:', customerData);

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
    const configBtnInPaymentModal = document.querySelector('#modal-table-selection .tables-config-btn');

    if (configBtnInPaymentModal) {
        // Remover listeners anteriores
        const newBtn = configBtnInPaymentModal.cloneNode(true);
        configBtnInPaymentModal.parentNode.replaceChild(newBtn, configBtnInPaymentModal);

        // Agregar nuevo listener
        newBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openTablesConfigModal();
        });

        console.log('✅ Botón de configuración actualizado en modal de pago');
    }
});
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
window.goToStep = function (step) {
    console.log(`📍 Navegando al paso ${step}`);

    // Validar paso anterior antes de avanzar
    if (step > window.paymentModalState.currentStep) {
        if (!validateCurrentStep()) {
            return;
        }
    }

    // ✅ SI VAMOS AL PASO 3, SINCRONIZAR ANTES DE CAMBIAR
    if (step === 3) {
        console.log('📋 Preparando paso 3...');
        syncPaymentRowsFromDOM();
        console.log('📦 window.paymentRows antes de mostrar:', window.paymentRows);
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

    // ✅ ACCIONES ESPECÍFICAS POR PASO
    if (step === 1) {
        console.log('📝 Paso 1: Tipo de pedido');
        const orderType = window.paymentModalState?.selectedOrderType || 'comer-aqui';
        const orderTypeBtn = document.querySelector(`#payment-modal .order-type-btn[data-type="${orderType}"]`);
        if (orderTypeBtn) {
            document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            orderTypeBtn.classList.add('selected');
        }
    } else if (step === 2) {
        console.log('💳 Paso 2: Métodos de pago');
        updateOrderTotal();
        updateNoPaymentsMessage();

        setTimeout(() => {
            const container = document.getElementById('payment-rows-container');
            if (container && container.children.length === 0) {
                console.log('➕ Agregando primera fila automáticamente...');
                addPaymentRow();
            }
        }, 100);
    } else if (step === 3) {
        // ✅✅ CRÍTICO: Actualizar resumen DESPUÉS de mostrar el paso
        console.log('📋 Paso 3: Actualizando resumen...');

        setTimeout(() => {
            console.log('🔄 Ejecutando updateStep3Summary...');
            updateStep3Summary();
            console.log('✅ Resumen actualizado');
        }, 100);
    }
};
function showNoPaymentsMessage() {
    const message = document.getElementById('no-payments-message');
    const container = document.getElementById('payment-rows-container');

    if (!message) {
        console.warn('⚠️ Elemento no-payments-message no encontrado');
        return;
    }

    const hasPaymentRows = container && container.children.length > 0;

    if (!hasPaymentRows) {
        message.style.display = 'block';
        console.log('📭 Mostrando mensaje "no hay pagos"');
    } else {
        message.style.display = 'none';
        console.log('✅ Ocultando mensaje "no hay pagos" (hay filas presentes)');
    }
}
window.nextStep = function () {
    if (window.paymentModalState.currentStep < 3) {
        goToStep(window.paymentModalState.currentStep + 1);
    }
};
window.prevStep = function () {
    if (window.paymentModalState.currentStep > 1) {
        goToStep(window.paymentModalState.currentStep - 1);
    }
};
// ✅ FUNCIÓN DE DIAGNÓSTICO
window.debugPaymentStep3 = function () {
    console.log('\n🔍 === DIAGNÓSTICO PASO 3 ===');
    console.log('1. window.paymentRows:', window.paymentRows);
    console.log('2. Cantidad:', window.paymentRows?.length || 0);

    const domRows = document.querySelectorAll('#payment-rows-container .payment-row');
    console.log('3. Filas en DOM:', domRows.length);

    domRows.forEach((row, i) => {
        const method = row.querySelector('select')?.value;
        const amount = row.querySelector('input[type="number"]')?.value;
        console.log(`   Fila ${i}: ${method} - $${amount}`);
    });

    console.log('4. Contenido de step3-payment-methods:');
    console.log(document.getElementById('step3-payment-methods')?.innerHTML.substring(0, 200));
    console.log('=========================\n');
};
function addPaymentRow() {
    console.log('➕ Agregando nueva fila de pago...');

    // ✅ ASEGURAR que window.paymentRows existe
    if (!window.paymentRows) {
        window.paymentRows = [];
        console.log('📦 window.paymentRows inicializado');
    }

    const paymentRowsContainer = document.getElementById('payment-rows-container');
    // ... resto del código existente ...

    // Agregar la nueva fila al contenedor
    paymentRowsContainer.appendChild(paymentRow);

    // Actualizar clases de scroll según cantidad de filas
    updateScrollContainer();

    // Mostrar el ícono del tipo de pago inicial
    updatePaymentIcon(paymentRow.querySelector('.payment-type'), paymentRow.id);

    // Actualizar campos según el tipo de pago seleccionado
    updatePaymentFields(paymentRow.querySelector('.payment-type'), paymentRow.id);

    // ✅ OCULTAR MENSAJE "NO HAY PAGOS"
    hideNoPaymentsMessage();

    console.log('✅ Fila agregada al DOM');
    console.log('🎯 Verificación:');
    console.log('   - Array:', window.paymentRows.length);
    console.log('   - DOM:', document.querySelectorAll('.payment-row').length);
}
function hideNoPaymentsMessage() {
    const message = document.getElementById('no-payments-message');

    if (message) {
        message.style.display = 'none';
        console.log('✅ Mensaje "no hay pagos" ocultado');
    }
}
// ============================================
// FUNCIÓN DE DIAGNÓSTICO PASO 3
// ============================================
window.debugStep3 = function () {
    console.log('\n🔍 === DIAGNÓSTICO COMPLETO PASO 3 ===\n');

    console.log('1️⃣ ESTADO DE window.paymentRows:');
    console.log('   Existe:', typeof window.paymentRows !== 'undefined');
    console.log('   Es array:', Array.isArray(window.paymentRows));
    console.log('   Cantidad:', window.paymentRows?.length || 0);
    console.log('   Contenido:', window.paymentRows);

    console.log('\n2️⃣ ESTADO DEL DOM (Paso 2):');
    const domRows = document.querySelectorAll('#payment-rows-container .payment-row');
    console.log('   Filas en DOM:', domRows.length);
    domRows.forEach((row, i) => {
        const method = row.querySelector('select')?.value;
        const amount = row.querySelector('input[type="number"]')?.value;
        const reference = row.querySelector('input[type="text"]')?.value;
        console.log(`   Fila ${i}:`, { method, amount, reference });
    });

    console.log('\n3️⃣ ESTADO DEL PASO 3:');
    const step3Element = document.getElementById('step-3');
    const paymentMethodsDiv = document.getElementById('step3-payment-methods');
    console.log('   Paso 3 visible:', step3Element?.classList.contains('active'));
    console.log('   Div de métodos existe:', !!paymentMethodsDiv);
    console.log('   Contenido actual:', paymentMethodsDiv?.innerHTML.substring(0, 200));

    console.log('\n4️⃣ EJECUTAR SINCRONIZACIÓN MANUAL:');
    if (typeof syncPaymentRowsFromDOM === 'function') {
        syncPaymentRowsFromDOM();
        console.log('   ✅ Sincronización ejecutada');
        console.log('   Resultado:', window.paymentRows);
    }

    console.log('\n5️⃣ EJECUTAR ACTUALIZACIÓN DE RESUMEN:');
    if (typeof updateStep3Summary === 'function') {
        updateStep3Summary();
        console.log('   ✅ Resumen actualizado');
        console.log('   Nuevo contenido:', paymentMethodsDiv?.innerHTML.substring(0, 200));
    }

    console.log('\n=== FIN DIAGNÓSTICO ===\n');
};
// ============================================
// GESTIÓN DE CLIENTES EN MODAL DE PAGO
// ============================================

let clientsData = [];

// Abrir modal de gestión de clientes
function openClientsConfigModal() {
    console.log('🔧 Abriendo modal de gestión de clientes...');

    const modal = document.getElementById('clients-config-modal');
    if (!modal) {
        console.error('❌ Modal clients-config-modal no encontrado');
        return;
    }

    modal.classList.add('show');
    loadClientsFromDB();
}

// Cerrar modal de gestión de clientes
function closeClientsConfigModal() {
    const modal = document.getElementById('clients-config-modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

// Cargar clientes desde la base de datos
async function loadClientsFromDB() {
    const tbody = document.getElementById('clients-tbody');
    const emptyState = document.getElementById('clients-empty-state');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Cargando clientes...</p>
            </td>
        </tr>
    `;

    try {
        const response = await fetch('/clients?json=1', {
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
        let clientsArray = [];

        if (result.clients && result.clients.data) {
            // Si viene paginado (estructura de Laravel)
            clientsArray = result.clients.data;
        } else if (Array.isArray(result.clients)) {
            // Si viene como array directo
            clientsArray = result.clients;
        } else if (Array.isArray(result)) {
            // Si el resultado es directamente el array
            clientsArray = result;
        }
        clientsData = clientsArray;

        console.log('✅ Clientes cargados:', clientsData.length);

        renderClientsTable(clientsData);

    } catch (error) {
        console.error('❌ Error al cargar clientes:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-500 mb-2"></i>
                    <p class="text-red-600">Error al cargar los clientes</p>
                    <button onclick="loadClientsFromDB()" class="mt-2 text-sm text-blue-600 hover:underline">
                        <i class="fas fa-redo mr-1"></i>Intentar de nuevo
                    </button>
                </td>
            </tr>
        `;
    }
}

// Renderizar tabla de clientes
// Renderizar tabla de clientes
function renderClientsTable(clients) {
    const tbody = document.getElementById('clients-tbody');
    const emptyState = document.getElementById('clients-empty-state');

    if (!tbody) return;

    if (!clients || clients.length === 0) {
        tbody.innerHTML = '';
        if (emptyState) {
            emptyState.style.display = 'block';
        }
        return;
    }

    if (emptyState) {
        emptyState.style.display = 'none';
    }

    tbody.innerHTML = clients.map(client => `
        <tr data-client-id="${client.id}">
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; background: #203363; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <strong>${client.full_name || client.name + ' ' + client.last_name}</strong>
                        ${client.email ? `<br><small style="color: #6b7280;">${client.email}</small>` : ''}
                    </div>
                </div>
            </td>
            <td>
                <strong>${client.document_type || 'N/A'}</strong>
                ${client.document_number ? `<br><span style="font-family: monospace;">${client.document_number}</span>` : ''}
            </td>
            <td>
                ${client.phone ? `<i class="fas fa-phone" style="color: #6b7280; margin-right: 6px;"></i>${client.phone}` : '<span style="color: #9ca3af;">Sin teléfono</span>'}
            </td>
            <td>
                <span class="table-state-badge ${client.is_active ? 'disponible' : 'ocupada'}">
                    ${client.is_active ? '✓ Activo' : '✗ Inactivo'}
                </span>
            </td>
            <td>
                <div class="table-actions">
                    <button 
                        class="table-action-btn edit" 
                        onclick="selectClientForOrder(${client.id})"
                        title="Seleccionar cliente para el pedido"
                    >
                        <i class="fas fa-check"></i>
                    </button>
                    <button 
                        class="table-action-btn edit" 
                        onclick="openEditClientModal(${client.id})"
                        title="Editar cliente"
                    >
                        <i class="fas fa-edit"></i>
                    </button>
                    <button 
                        class="table-action-btn delete" 
                        onclick="confirmDeleteClient(${client.id}, '${(client.name || '').replace(/'/g, "\\'")}')"
                        title="Eliminar cliente"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Filtrar clientes
function filterClients() {
    const searchTerm = document.getElementById('clients-search').value.toLowerCase();

    if (!searchTerm) {
        renderClientsTable(clientsData);
        return;
    }

    const filtered = clientsData.filter(client => {
        const fullName = `${client.name} ${client.last_name}`.toLowerCase();
        const docNumber = (client.document_number || '').toLowerCase();
        const phone = (client.phone || '').toLowerCase();

        return fullName.includes(searchTerm) ||
            docNumber.includes(searchTerm) ||
            phone.includes(searchTerm);
    });

    renderClientsTable(filtered);
}

// Seleccionar cliente para el pedido
function selectClientForOrder(clientId) {
    const client = clientsData.find(c => c.id === clientId);

    if (!client) {
        console.error('Cliente no encontrado');
        return;
    }

    // Rellenar el formulario del paso 3
    const nameInput = document.getElementById('modal-customer-name');
    const emailInput = document.getElementById('modal-customer-email');
    const phoneInput = document.getElementById('modal-customer-phone');

    if (nameInput) nameInput.value = client.full_name || `${client.name} ${client.last_name}`;
    if (emailInput) emailInput.value = client.email || '';
    if (phoneInput) phoneInput.value = client.phone || '';

    // Guardar en localStorage
    localStorage.setItem('customerName', client.full_name || `${client.name} ${client.last_name}`);
    localStorage.setItem('customerEmail', client.email || '');
    localStorage.setItem('customerPhone', client.phone || '');
    localStorage.setItem('selectedClientId', client.id);

    // Cerrar modal
    closeClientsConfigModal();

    // Mostrar notificación
    showSuccessMessage(`Cliente "${client.full_name || client.name}" seleccionado correctamente`);
}

// Abrir modal para crear cliente
function openCreateClientModal() {
    const modal = document.getElementById('create-client-modal');
    const title = document.getElementById('create-client-title');
    const form = document.getElementById('create-client-form');

    if (!modal || !form) return;

    form.reset();
    document.getElementById('edit-client-id').value = '';

    if (title) {
        title.innerHTML = '<i class="fas fa-plus-circle"></i> Crear Nuevo Cliente';
    }

    modal.classList.add('show');
}

// Cerrar modal de crear/editar cliente
function closeCreateClientModal() {
    const modal = document.getElementById('create-client-modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

// Abrir modal para editar cliente
function openEditClientModal(clientId) {
    const client = clientsData.find(c => c.id === clientId);

    if (!client) {
        console.error('Cliente no encontrado');
        return;
    }

    const modal = document.getElementById('create-client-modal');
    const title = document.getElementById('create-client-title');

    if (!modal) return;

    // Rellenar formulario
    document.getElementById('edit-client-id').value = client.id;
    document.getElementById('client-name-input').value = client.name || '';
    document.getElementById('client-lastname-input').value = client.last_name || '';
    document.getElementById('client-doc-type-input').value = client.document_type || 'CI';
    document.getElementById('client-doc-number-input').value = client.document_number || '';
    document.getElementById('client-phone-input').value = client.phone || '';
    document.getElementById('client-email-input').value = client.email || '';
    document.getElementById('client-address-input').value = client.address || '';
    document.getElementById('client-city-input').value = client.city || '';
    document.getElementById('client-notes-input').value = client.notes || '';
    document.getElementById('client-active-input').checked = client.is_active;

    if (title) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Cliente';
    }

    modal.classList.add('show');
}

// Manejar creación/edición de cliente
async function handleCreateClient(event) {
    event.preventDefault();

    const form = event.target;
    const clientId = document.getElementById('edit-client-id').value;
    const submitBtn = form.querySelector('button[type="submit"]');

    const clientData = {
        name: document.getElementById('client-name-input').value,
        last_name: document.getElementById('client-lastname-input').value,
        document_type: document.getElementById('client-doc-type-input').value,
        document_number: document.getElementById('client-doc-number-input').value || null,
        phone: document.getElementById('client-phone-input').value || null,
        email: document.getElementById('client-email-input').value || null,
        address: document.getElementById('client-address-input').value || null,
        city: document.getElementById('client-city-input').value || null,
        notes: document.getElementById('client-notes-input').value || null,
        is_active: document.getElementById('client-active-input').checked,
        _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
    };

    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const isEdit = clientId !== '';
        const url = isEdit ? `/clients/${clientId}` : '/clients';
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method === 'PUT' ? 'POST' : method, // Laravel necesita POST con _method
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': clientData._token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(isEdit ? { ...clientData, _method: 'PUT' } : clientData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al guardar el cliente');
        }

        closeCreateClientModal();
        showSuccessMessage(isEdit ? 'Cliente actualizado correctamente' : 'Cliente creado correctamente');
        await loadClientsFromDB();

    } catch (error) {
        console.error('❌ Error:', error);
        alert('Error: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Confirmar eliminación de cliente
function confirmDeleteClient(clientId, clientName) {
    if (confirm(`¿Estás seguro de eliminar al cliente "${clientName}"?\n\nEsta acción no se puede deshacer.`)) {
        deleteClient(clientId);
    }
}

// Eliminar cliente
async function deleteClient(clientId) {
    try {
        const response = await fetch(`/clients/${clientId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Error al eliminar el cliente');
        }

        showSuccessMessage('Cliente eliminado correctamente');
        await loadClientsFromDB();

    } catch (error) {
        console.error('❌ Error:', error);
        alert('Error: ' + error.message);
    }
}

// Actualizar lista de clientes
function refreshClientsList() {
    loadClientsFromDB();
}
// ============================================
// GUARDAR CLIENTE ACTUAL DEL FORMULARIO
// ============================================

async function saveCurrentClientToDatabase() {
    console.log('💾 Guardando cliente actual del formulario...');

    // Obtener datos del formulario
    const customerName = document.getElementById('modal-customer-name')?.value?.trim();
    const customerEmail = document.getElementById('modal-customer-email')?.value?.trim();
    const customerPhone = document.getElementById('modal-customer-phone')?.value?.trim();
    const customerDocType = document.getElementById('modal-customer-doc-type')?.value;
    const customerDocNumber = document.getElementById('modal-customer-doc-number')?.value?.trim();
    const customerAddress = document.getElementById('modal-customer-address')?.value?.trim();
    const customerCity = document.getElementById('modal-customer-city')?.value?.trim();
    const customerNotes = document.getElementById('modal-customer-notes')?.value?.trim();

    // Validar nombre completo (requerido)
    if (!customerName) {
        alert('⚠️ Por favor ingresa el nombre completo del cliente antes de guardar');
        document.getElementById('modal-customer-name')?.focus();
        return;
    }

    // Separar nombre y apellido
    const nameParts = customerName.split(' ');
    let firstName = '';
    let lastName = '';

    if (nameParts.length === 1) {
        firstName = nameParts[0];
        lastName = '';
    } else if (nameParts.length === 2) {
        firstName = nameParts[0];
        lastName = nameParts[1];
    } else {
        // Si tiene más de 2 palabras, tomar la primera como nombre y el resto como apellido
        firstName = nameParts[0];
        lastName = nameParts.slice(1).join(' ');
    }

    // Construir objeto de datos
    const clientData = {
        name: firstName,
        last_name: lastName,
        document_type: customerDocType || 'CI',
        document_number: customerDocNumber || null,
        phone: customerPhone || null,
        email: customerEmail || null,
        address: customerAddress || null,
        city: customerCity || null,
        notes: customerNotes || null,
        is_active: true,
        _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
    };

    console.log('📤 Datos a enviar:', clientData);

    // Obtener botón y mostrar loading
    const saveBtn = document.getElementById('save-current-client-btn');
    const originalBtnContent = saveBtn?.innerHTML;

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    try {
        const response = await fetch('/clients', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': clientData._token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(clientData)
        });

        console.log('📡 Respuesta HTTP:', response.status, response.statusText);

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `Error HTTP: ${response.status}`);
        }

        const result = await response.json();
        console.log('📥 Respuesta del servidor:', result);

        if (!result.success) {
            throw new Error(result.message || 'Error al guardar el cliente');
        }

        // ✅ Cliente guardado exitosamente
        const clientId = result.client?.id || result.id;

        // Guardar el ID en localStorage
        localStorage.setItem('selectedClientId', clientId);

        // Mostrar indicador de éxito
        showClientSavedIndicator(clientId, customerName);

        // Mostrar notificación
        showSuccessMessage(`✅ Cliente "${customerName}" guardado correctamente (ID: ${clientId})`);

        // Actualizar la lista de clientes si el modal está abierto
        const clientsModal = document.getElementById('clients-config-modal');
        if (clientsModal && clientsModal.classList.contains('show')) {
            await loadClientsFromDB();
        }

        console.log('✅ Cliente guardado exitosamente');

    } catch (error) {
        console.error('❌ Error al guardar cliente:', error);
        alert(`❌ Error al guardar el cliente:\n${error.message}`);
    } finally {
        // Restaurar botón
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnContent;
        }
    }
}

// Mostrar indicador de que el cliente fue guardado
function showClientSavedIndicator(clientId, clientName) {
    const indicator = document.getElementById('client-saved-indicator');
    const savedIdSpan = document.getElementById('saved-client-id');

    if (indicator && savedIdSpan) {
        savedIdSpan.textContent = clientId;
        indicator.style.display = 'block';

        // Agregar animación
        indicator.style.animation = 'slideInDown 0.4s ease-out';

        // Opcional: ocultar después de unos segundos
        setTimeout(() => {
            indicator.style.opacity = '0';
            indicator.style.transition = 'opacity 0.5s ease';

            setTimeout(() => {
                indicator.style.display = 'none';
                indicator.style.opacity = '1';
            }, 500);
        }, 5000);
    }
}

// Limpiar indicador de cliente guardado
function clearClientSavedIndicator() {
    const indicator = document.getElementById('client-saved-indicator');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

// Modificar la función selectClientForOrder para incluir más campos
function selectClientForOrder(clientId) {
    const client = clientsData.find(c => c.id === clientId);

    if (!client) {
        console.error('Cliente no encontrado');
        return;
    }

    // Rellenar TODOS los campos del formulario
    const nameInput = document.getElementById('modal-customer-name');
    const emailInput = document.getElementById('modal-customer-email');
    const phoneInput = document.getElementById('modal-customer-phone');
    const docTypeInput = document.getElementById('modal-customer-doc-type');
    const docNumberInput = document.getElementById('modal-customer-doc-number');
    const addressInput = document.getElementById('modal-customer-address');
    const cityInput = document.getElementById('modal-customer-city');
    const notesInput = document.getElementById('modal-customer-notes');

    const fullName = client.full_name || `${client.name} ${client.last_name}`;

    if (nameInput) nameInput.value = fullName;
    if (emailInput) emailInput.value = client.email || '';
    if (phoneInput) phoneInput.value = client.phone || '';
    if (docTypeInput) docTypeInput.value = client.document_type || 'CI';
    if (docNumberInput) docNumberInput.value = client.document_number || '';
    if (addressInput) addressInput.value = client.address || '';
    if (cityInput) cityInput.value = client.city || '';
    if (notesInput) notesInput.value = client.notes || '';

    // Guardar en localStorage
    localStorage.setItem('customerName', fullName);
    localStorage.setItem('customerEmail', client.email || '');
    localStorage.setItem('customerPhone', client.phone || '');
    localStorage.setItem('selectedClientId', client.id);

    // Mostrar indicador de que el cliente ya está en BD
    showClientSavedIndicator(client.id, fullName);

    // Cerrar modal
    closeClientsConfigModal();

    // Mostrar notificación
    showSuccessMessage(`Cliente "${fullName}" seleccionado correctamente`);
}

// Agregar listener para limpiar el indicador cuando se cambie el nombre
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('modal-customer-name');
    if (nameInput) {
        nameInput.addEventListener('input', function () {
            // Si el usuario modifica el nombre, ocultar el indicador
            clearClientSavedIndicator();
        });
    }
});

// Exponer funciones globalmente
window.saveCurrentClientToDatabase = saveCurrentClientToDatabase;
window.showClientSavedIndicator = showClientSavedIndicator;
window.clearClientSavedIndicator = clearClientSavedIndicator;

console.log('✅ Función de guardar cliente desde formulario cargada');

// Exponer funciones globalmente
window.openClientsConfigModal = openClientsConfigModal;
window.closeClientsConfigModal = closeClientsConfigModal;
window.loadClientsFromDB = loadClientsFromDB;
window.openCreateClientModal = openCreateClientModal;
window.closeCreateClientModal = closeCreateClientModal;
window.openEditClientModal = openEditClientModal;
window.handleCreateClient = handleCreateClient;
window.confirmDeleteClient = confirmDeleteClient;
window.deleteClient = deleteClient;
window.selectClientForOrder = selectClientForOrder;
window.filterClients = filterClients;
window.refreshClientsList = refreshClientsList;

console.log('✅ Gestión de clientes cargada correctamente');

window.updatePaymentRowFromSelect = updatePaymentRowFromSelect;
window.updatePaymentRowFromInput = updatePaymentRowFromInput;

console.log('✅ Payment Modal JS cargado correctamente');
window.showNoPaymentsMessage = showNoPaymentsMessage;
window.hideNoPaymentsMessage = hideNoPaymentsMessage;
window.updateNoPaymentsMessage = updateNoPaymentsMessage;
window.goToStep = goToStep;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.addPaymentRow = addPaymentRow;
window.removePaymentRow = removePaymentRow;
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
window.openTablesConfigModalFromPayment = openTablesConfigModalFromPayment;
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
window.renderTables = renderTables;
window.selectTable = selectTable;
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

window.confirmAndProcessOrder = confirmAndProcessOrder;
window.renderPaymentRows = renderPaymentRows;
window.syncPaymentRowsFromDOM = syncPaymentRowsFromDOM;
window.updatePaymentRowField = updatePaymentRowField;
window.debugPaymentRowsInRealTime = debugPaymentRowsInRealTime;
window.diagnosePaymentRowStructure = diagnosePaymentRowStructure;

window.addExpenseModalClosure = addExpenseModalClosure;
window.removeExpenseClosure = removeExpenseClosure;
window.actualizarTotalGastosClosure = actualizarTotalGastosClosure;
window.validarGastosCierre = validarGastosCierre;
window.recopilarGastosCierre = recopilarGastosCierre;
window.limpiarGastosCierre = limpiarGastosCierre;
window.guardarCierreUnificado = guardarCierreUnificado;
window.closeInternalModalClosure = closeInternalModalClosure;