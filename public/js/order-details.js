let paymentProcessed = false;
let paymentRowCounter = 0;
let currentPrintContent = '';

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
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
        link.addEventListener('click', function(e) {
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
    
    // 1. Actualizar almacenamiento y elementos básicos
    const orderTypeInput = document.getElementById('order-type');
    if (orderTypeInput) {
        orderTypeInput.value = type;
    }
    localStorage.setItem('orderType', type);
    
    // 2. Limpiar datos irrelevantes según el tipo seleccionado
    if (type !== 'Comer aquí') {
        localStorage.removeItem('tableNumber');
        // Limpiar selección de mesa visual si existe
        const tableSelect = document.getElementById('table-number');
        if (tableSelect) {
            tableSelect.value = '';
        }
    }
    
    if (type !== 'Para llevar') {
        localStorage.removeItem('deliveryService');
        // Limpiar selección de delivery visual si existe
        const deliverySelect = document.getElementById('delivery-service');
        if (deliverySelect) {
            deliverySelect.value = '';
        }
    }
    
    // 3. Actualizar detalles del pedido
    updateOrderDetails();
    
    console.log('✅ Tipo de pedido establecido:', type);
}
function showPaymentModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    // Usar la función del modal
    if (typeof openPaymentModal === 'function') {
        openPaymentModal();
    } else {
        console.error('❌ Función openPaymentModal no encontrada');
    }
}
function syncOrderTypeWithModal(orderType) {
    console.log('🔄 Sincronizando tipo de pedido con modal:', orderType);
    
    // Mapear tipos de pedido del sistema principal al modal
    let modalType = 'comer-aqui';
    switch(orderType) {
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
    switch(type) {
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
            
            // Revertir TODO el stock del ítem eliminado
            const newStock = currentStock + item.quantity;
            updateStockBadge(item.id, newStock, minStock, stockType, stockUnit);
        }

        // Eliminar el ítem completamente del array
        order.splice(index, 1);

        // Actualizar el localStorage y la vista
        localStorage.setItem('order', JSON.stringify(order));
        updateOrderDetails();
    } else {
        console.error('Índice no válido:', index);
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
 * Muestra el modal de pago
 */
// function showPaymentModal() {
//     const order = JSON.parse(localStorage.getItem('order')) || [];
//     if (order.length === 0) {
//         alert('No hay ítems en el pedido para realizar el pago');
//         return;
//     }

//     const modal = document.getElementById('payment-modal');
//     modal.classList.remove('hidden');

//     const paymentRowsContainer = document.getElementById('payment-rows-container');
//     const scrollableContainer = document.getElementById('payment-rows-scrollable'); 

//     // Limpiar completamente el contenedor de filas y cualquier total previo
//     paymentRowsContainer.innerHTML = '';
    
//     // Eliminar cualquier display de total previo de manera más específica
//     const existingTotalDisplays = document.querySelectorAll('.total-display');
//     existingTotalDisplays.forEach(display => display.remove());

//     // Mostrar el total del pedido
//     const totalAmount = calcularTotal();
//     const totalDisplay = document.createElement('div');
//     totalDisplay.className = 'total-display text-sm font-bold text-[#203363] mb-4';
//     totalDisplay.innerHTML = `Total del Pedido: $${totalAmount}`;

//     // Insertar el total en el lugar correcto
//     scrollableContainer.insertBefore(totalDisplay, paymentRowsContainer);

//     // Inicializar contador y agregar primera fila
//     paymentRowCounter = 0;
//     addPaymentRow();
    
//     // Asegurarse de que el scroll esté desactivado inicialmente
//     scrollableContainer.classList.remove('has-scroll');
// }
// Función para cerrar el modal de pago
function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Limpiar el contenedor de métodos de pago
    const paymentContainer = document.getElementById('payment-rows-container');
    if (paymentContainer) {
        paymentContainer.innerHTML = '';
    }
    
    // Resetear variables globales del modal si existen
    if (typeof window.currentStep !== 'undefined') {
        window.currentStep = 1;
    }
    if (typeof window.paymentRows !== 'undefined') {
        window.paymentRows = [];
    }
    
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
    const sellerName = window.authUserName || 'Usuario';
    
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
    try {
        
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

        const dailyOrderNumber = data.daily_order_number;
        // Mostrar vista previa y esperar confirmación
        const printConfirmed = await showPrintConfirmation(dailyOrderNumber);

        if (!printConfirmed) {
            console.log('Impresión cancelada por el usuario');
            return;
        }

        // Éxito - limpiar y redirigir
        if (data.success) {
            unlockOrderInterface();
            localStorage.removeItem('paymentProcessed');
            paymentProcessed = false;
            localStorage.removeItem('order');
            localStorage.removeItem('orderType');
            localStorage.removeItem('tableNumber');
            localStorage.removeItem('orderNotes');
            localStorage.removeItem('customerData');
            localStorage.removeItem('paymentMethods');
            localStorage.removeItem('paymentDetails');
            
            window.location.href = window.routes.menuIndex;
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
                'X-CSRF-TOKEN': window.csrfToken,
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
    
    // 🔥 RESTAURAR opacidad al limpiar el pedido
    unlockOrderInterface();
    
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
        switch(state) {
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
            alert('Por favor, seleccione un estado');
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
        
        switch(newState) {
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
        newBtn.addEventListener('click', function() {
            handleOrderTypeChange(this);
        });
    });
    
    // Configurar navegación por steps
    const stepItems = document.querySelectorAll('#payment-modal .step-item');
    stepItems.forEach(item => {
        const newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);
        
        newItem.addEventListener('click', function() {
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
    switch(selectedType) {
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
    
    // Obtener el tipo de pedido actual del modal
    let currentType = 'comer-aqui';
    const selectedBtn = document.querySelector('#payment-modal .order-type-btn.selected');
    if (selectedBtn) {
        currentType = selectedBtn.dataset.type;
    }
    
    console.log('🔄 Actualizando visibilidad de secciones del modal, tipo:', currentType);
    
    // Ocultar todas las secciones primero
    if (tableSelection) {
        tableSelection.classList.add('hidden');
        console.log('❌ Ocultando selección de mesas');
    }
    if (deliverySelection) {
        deliverySelection.classList.add('hidden');
        console.log('❌ Ocultando selección de delivery');
    }
    
    // Mostrar secciones según el tipo de pedido
    switch(currentType) {
        case 'comer-aqui':
            if (tableSelection) {
                console.log('✅ Mostrando selección de mesas');
                tableSelection.classList.remove('hidden');
                // Cargar mesas si la función existe
                if (typeof window.loadModalTables === 'function') {
                    window.loadModalTables();
                }
            }
            break;
            
        case 'para-llevar':
            if (deliverySelection) {
                console.log('✅ Mostrando selección de delivery');
                deliverySelection.classList.remove('hidden');
                // Cargar servicios de delivery si la función existe
                if (typeof window.loadDeliveryServices === 'function') {
                    window.loadDeliveryServices();
                }
            }
            break;
            
        case 'recoger':
            console.log('✅ Tipo Recoger - sin secciones adicionales');
            // No mostrar ninguna sección adicional
            break;
            
        default:
            console.log('⚠️ Tipo de pedido no reconocido:', currentType);
            break;
    }
}

function loadDeliveryServices() {
    const deliverySelect = document.getElementById('modal-delivery-service');
    if (!deliverySelect) {
        console.error('❌ No se encontró el select de delivery en el modal');
        return;
    }
    
    console.log('🚚 Cargando servicios de delivery...');
    
    // Lista de servicios de delivery (en implementación real vendría del servidor)
    const deliveryServices = [
        { name: 'Delivery Express', id: 1 },
        { name: 'Rápido Delivery', id: 2 },  
        { name: 'Food Delivery', id: 3 },
        { name: 'Uber Eats', id: 4 },
        { name: 'Rappi', id: 5 }
    ];
    
    // Limpiar opciones existentes
    deliverySelect.innerHTML = '<option value="">Seleccione un servicio de delivery</option>';
    
    // Agregar opciones de servicios
    deliveryServices.forEach(service => {
        const option = document.createElement('option');
        option.value = service.name;
        option.textContent = service.name;
        deliverySelect.appendChild(option);
    });
    
    // Seleccionar el servicio guardado previamente si existe
    const savedService = localStorage.getItem('deliveryService');
    if (savedService && deliverySelect.querySelector(`option[value="${savedService}"]`)) {
        deliverySelect.value = savedService;
        console.log('📋 Servicio de delivery restaurado:', savedService);
    }
    
    // Configurar evento change (reemplazar elemento para evitar múltiples listeners)
    const newDeliverySelect = deliverySelect.cloneNode(true);
    deliverySelect.parentNode.replaceChild(newDeliverySelect, deliverySelect);
    
    newDeliverySelect.addEventListener('change', function() {
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
}
function validateStep1() {
    const selectedBtn = document.querySelector('#payment-modal .order-type-btn.selected');
    if (!selectedBtn) {
        alert('Por favor, selecciona un tipo de pedido');
        return false;
    }
    
    const orderType = selectedBtn.dataset.type;
    
    // Validaciones específicas según el tipo de pedido
    switch(orderType) {
        case 'comer-aqui':
            // Validar que se haya seleccionado una mesa
            const selectedTable = document.querySelector('#payment-modal .table-btn.selected');
            if (!selectedTable) {
                alert('Por favor, selecciona una mesa para "Comer aquí"');
                return false;
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
            break;
            
        default:
            alert('Tipo de pedido no válido');
            return false;
    }
    
    return true;
}

async function loadModalTables() {
    const tableGrid = document.getElementById('table-grid');
    const loadingElement = document.getElementById('table-loading');
    const errorElement = document.getElementById('table-error');
    const errorMessage = document.getElementById('table-error-message');
    
    if (!tableGrid) {
        console.error('❌ No se encontró table-grid');
        return;
    }
    
    // Mostrar loading
    if (loadingElement) loadingElement.classList.remove('hidden');
    if (errorElement) errorElement.classList.add('hidden');
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
            switch(table.state) {
                case 'Disponible':
                    button.addEventListener('click', function() {
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
    } finally {
        // Ocultar loading
        if (loadingElement) loadingElement.classList.add('hidden');
    }
}
function setupCustomerDetailsObserver() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
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



function selectModalTable(tableElement) {
    console.log('✅ Mesa seleccionada en modal:', tableElement.dataset.tableNumber);
    
    // Deseleccionar mesa anterior
    document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });
    
    // Seleccionar nueva mesa
    tableElement.classList.add('selected');
    
    // Actualizar el sistema principal también
    const tableSelect = document.getElementById('table-number');
    if (tableSelect) {
        tableSelect.value = tableElement.dataset.tableId;
        localStorage.setItem('tableNumber', tableElement.dataset.tableId);
    }
}
// Cargar el estado al cambiar de mesa
document.addEventListener('DOMContentLoaded', function() {
    const tableSelect = document.getElementById('table-number');
    if (tableSelect) {
        // Cargar estado inicial
        loadTableState();
        
        // Actualizar estado cuando cambia la selección
        tableSelect.addEventListener('change', loadTableState);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Solo ejecutar si estamos en la página del menú
    if (document.getElementById('order-panel')) {
        initializeOrderSystem();
        setupEventListeners();
        setupLogoutHandlers();
        setupTableSelectStyles();
        setupCustomerDetailsObserver();
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
document.addEventListener('click', function(e) {
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
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('payment-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closePaymentModal();
        }
    }
});