// ============================================
// VARIABLES GLOBALES
// ============================================
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;

let paymentRowCounter = 0;
let originalTablesEnabled = false;
let tables = [];

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
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    if (orderType === 'Recoger') {
        const confirmMessage = '⚠️ IMPORTANTE: Para pedidos "Recoger" solo están disponibles los métodos de pago QR y Transferencia Bancaria.\n\n¿Desea continuar?';
        if (!confirm(confirmMessage)) {
            return;
        }
    }

    console.log('🔧 Abriendo modal de pago...');
    openPaymentModal();
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

function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }

    const paymentContainer = document.getElementById('payment-rows-container');
    if (paymentContainer) {
        paymentContainer.innerHTML = '';
    }

    // Resetear estado del modal
    window.paymentModalState = {
        currentStep: 1,
        maxSteps: 3,
        selectedOrderType: 'comer-aqui',
        selectedTable: null,
        paymentRows: [],
        customerData: {}
    };

    // Limpiar formulario de cliente
    const customerForm = document.getElementById('modal-customer-details-form');
    if (customerForm) {
        customerForm.reset();
    }

    console.log('✅ Modal de pago cerrado y limpiado');
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
    if (window.paymentModalState.currentStep >= window.paymentModalState.maxSteps) return;
    goToStep(window.paymentModalState.currentStep + 1);
}

function prevStep() {
    if (window.paymentModalState.currentStep <= 1) return;
    goToStep(window.paymentModalState.currentStep - 1);
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
    const selectedBtn = document.querySelector('#payment-modal .order-type-btn.selected');
    if (!selectedBtn) {
        alert('Por favor, selecciona un tipo de pedido');
        return false;
    }

    const orderType = selectedBtn.dataset.type;

    switch (orderType) {
        case 'comer-aqui':
            const selectedTableBtn = document.querySelector('#payment-modal .table-btn.selected');
            if (!selectedTableBtn) {
                alert('Por favor, selecciona una mesa para "Comer aquí"');
                return false;
            }

            const tableId = selectedTableBtn.dataset.tableId;
            const tableNumber = selectedTableBtn.dataset.tableNumber;

            window.paymentModalState.selectedTable = {
                id: tableId,
                number: tableNumber
            };

            localStorage.setItem('tableNumber', tableId);
            break;

        case 'para-llevar':
            const deliverySelect = document.getElementById('modal-delivery-service');
            if (!deliverySelect || !deliverySelect.value) {
                alert('Por favor, selecciona un servicio de delivery para "Para llevar"');
                return false;
            }
            localStorage.setItem('deliveryService', deliverySelect.value);
            break;

        case 'recoger':
            // No hay validaciones obligatorias para "Recoger"
            const pickupNotesText = document.getElementById('modal-pickup-notes-text');
            if (pickupNotesText && pickupNotesText.value.trim()) {
                localStorage.setItem('pickupNotes', pickupNotesText.value);
            }
            break;
    }

    return true;
}

function validateStep2() {
    const paymentRows = document.querySelectorAll('#payment-modal .payment-row');

    if (paymentRows.length === 0) {
        alert('Debe agregar al menos un método de pago');
        return false;
    }

    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    const isPickupOrder = orderType === 'Recoger';
    const allowedMethods = isPickupOrder ? ['QR', 'Transferencia'] : ['Efectivo', 'QR', 'Tarjeta', 'Transferencia'];

    let totalPaid = 0;
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderTotal = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    for (let row of paymentRows) {
        const paymentTypeSelect = row.querySelector('.payment-type');
        const paymentType = paymentTypeSelect ? paymentTypeSelect.value : '';
        const totalPaidInput = row.querySelector('.total-paid');
        const paidValue = parseFloat(totalPaidInput.value);
        const transactionInput = row.querySelector('.transaction-number');

        // Validar método permitido
        if (!allowedMethods.includes(paymentType)) {
            const methodsList = allowedMethods.join(', ');
            alert(`❌ Método de pago no permitido.\n\nPara pedidos "${orderType}" solo se permiten:\n${methodsList}`);
            return false;
        }

        // Validar monto
        if (isNaN(paidValue) || paidValue <= 0) {
            alert('Por favor, ingrese un monto válido en todos los campos de "Total Pagado".');
            totalPaidInput.focus();
            return false;
        }

        // Validar número de transacción para pedidos "Recoger"
        if (isPickupOrder && (paymentType === 'QR' || paymentType === 'Transferencia')) {
            if (transactionInput) {
                const transactionValue = transactionInput.value.trim();
                if (!transactionValue) {
                    alert(`❌ El número de transacción es OBLIGATORIO para pagos con ${paymentType} en pedidos "Recoger"`);
                    transactionInput.focus();
                    return false;
                }

                if (transactionValue.length < 4) {
                    alert(`❌ El número de transacción debe tener al menos 4 caracteres`);
                    transactionInput.focus();
                    return false;
                }
            }
        }

        totalPaid += paidValue;
    }

    // Validar que el total pagado cubra el monto del pedido
    if (totalPaid < orderTotal) {
        alert(`❌ El total pagado (${totalPaid.toFixed(2)}) es menor al total del pedido (${orderTotal.toFixed(2)}).`);
        return false;
    }

    // Guardar métodos de pago en localStorage
    const paymentMethods = [];
    paymentRows.forEach(row => {
        const paymentType = row.querySelector('.payment-type').value;
        const totalPaid = parseFloat(row.querySelector('.total-paid').value) || 0;
        const transactionNumber = row.querySelector('.transaction-number')?.value || null;

        paymentMethods.push({
            method: paymentType,
            amount: totalPaid,
            transaction_number: transactionNumber
        });
    });

    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));

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

function processPayment() {
    console.log('💳 Procesando pedido completo...');

    // Validar datos del cliente
    const customerName = document.getElementById('modal-customer-name')?.value.trim();

    if (!customerName) {
        alert('❌ El nombre del cliente es obligatorio');
        document.getElementById('modal-customer-name')?.focus();
        return;
    }

    // Recopilar datos del cliente
    const customerEmail = document.getElementById('modal-customer-email')?.value.trim() || '';
    const customerPhone = document.getElementById('modal-customer-phone')?.value.trim() || '';
    const customerNotes = document.getElementById('modal-customer-notes')?.value.trim() || '';

    // Guardar datos del cliente
    window.paymentModalState.customerData = {
        name: customerName,
        email: customerEmail,
        phone: customerPhone,
        notes: customerNotes
    };

    // Guardar en localStorage para usar en order-details.js
    localStorage.setItem('customerName', customerName);
    localStorage.setItem('customerEmail', customerEmail);
    localStorage.setItem('customerPhone', customerPhone);

    // Obtener todos los datos necesarios
    const order = JSON.parse(localStorage.getItem('order')) || [];
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    const paymentMethods = JSON.parse(localStorage.getItem('paymentMethods')) || [];
    const orderNotes = localStorage.getItem('orderNotes') || '';

    // Preparar datos para enviar
    let tableNumber = null;
    if (orderType === 'Comer aquí' && window.paymentModalState.selectedTable) {
        tableNumber = window.paymentModalState.selectedTable.id;
    }

    const orderItems = order.map(item => ({
        name: item.name,
        price: item.price,
        quantity: item.quantity
    }));

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

    console.log('📤 Datos a enviar:', requestData);

    // Llamar a la función de procesamiento del pedido
    submitOrder(requestData);
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

async function loadModalTables() {
    const tableGrid = document.getElementById('table-grid');
    const loadingElement = document.getElementById('table-loading');
    const errorElement = document.getElementById('table-error');
    const errorMessage = document.getElementById('table-error-message');

    if (!tableGrid) {
        console.error('❌ No se encontró table-grid');
        return;
    }

    if (loadingElement) loadingElement.classList.remove('hidden');
    if (errorElement) errorElement.classList.add('hidden');
    tableGrid.innerHTML = '';

    try {
        const response = await fetch(TABLES_API.available);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success || !data.data) {
            throw new Error(data.message || 'No se pudieron obtener las mesas');
        }

        const tables = data.data;

        if (tables.length === 0) {
            tableGrid.innerHTML = '<div class="col-span-full text-center text-gray-500">No hay mesas configuradas</div>';
            return;
        }

        tables.forEach(table => {
            const button = document.createElement('button');
            button.className = 'table-btn';
            button.dataset.tableId = table.id;
            button.dataset.tableNumber = table.number;
            button.dataset.status = table.state.toLowerCase().replace(' ', '-');
            button.textContent = `Mesa ${table.number}`;

            switch (table.state) {
                case 'Disponible':
                    button.addEventListener('click', function () {
                        selectTable(this);
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
            }

            tableGrid.appendChild(button);
        });

    } catch (error) {
        console.error('❌ Error al cargar mesas:', error);
        if (errorMessage) errorMessage.textContent = error.message;
        if (errorElement) errorElement.classList.remove('hidden');
    } finally {
        if (loadingElement) loadingElement.classList.add('hidden');
    }
}

function selectTable(tableElement) {
    document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });

    tableElement.classList.add('selected');

    window.paymentModalState.selectedTable = {
        id: tableElement.dataset.tableId,
        number: tableElement.dataset.tableNumber
    };

    localStorage.setItem('tableNumber', tableElement.dataset.tableId);
}

function loadDeliveryServices() {
    const deliverySelect = document.getElementById('modal-delivery-service');
    if (!deliverySelect) return;

    const deliveryServices = [
        { name: 'Delivery Express' },
        { name: 'Rápido Delivery' },
        { name: 'Food Delivery' }
    ];

    deliverySelect.innerHTML = '<option value="">Seleccione un servicio de delivery</option>';
    deliveryServices.forEach(service => {
        const option = document.createElement('option');
        option.value = service.name;
        option.textContent = service.name;
        deliverySelect.appendChild(option);
    });

    const savedService = localStorage.getItem('deliveryService');
    if (savedService) {
        deliverySelect.value = savedService;
    }

    deliverySelect.addEventListener('change', function () {
        if (this.value) {
            localStorage.setItem('deliveryService', this.value);
        } else {
            localStorage.removeItem('deliveryService');
        }
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
        const url = isEdit ? TABLES_API.update(tableId) : TABLES_API.store;
        const method = isEdit ? 'PUT' : 'POST';

        const formData = new FormData();
        formData.append('number', number);
        formData.append('state', state);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Error al guardar la mesa');
        }

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

// ============================================
// EXPONER FUNCIONES GLOBALMENTE
// ============================================

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
window.nextStep = nextStep;
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