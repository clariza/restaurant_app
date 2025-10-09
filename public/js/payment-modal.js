// Variables específicas del modal de pago
let paymentRowCounter = 0;

// Función para mostrar el modal de pago
function showPaymentModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');

    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const scrollableContainer = document.querySelector('.payment-rows-scrollable');

    // Limpiar completamente el contenedor de filas y cualquier total previo
    paymentRowsContainer.innerHTML = '';
    
    // Eliminar cualquier display de total previo
    const existingTotalDisplays = document.querySelectorAll('.total-display');
    existingTotalDisplays.forEach(display => display.remove());

    // Mostrar el total del pedido
    const totalAmount = calcularTotal();
    const totalDisplay = document.createElement('div');
    totalDisplay.className = 'total-display';
    totalDisplay.innerHTML = `Total del Pedido: $${totalAmount}`;

    // Insertar el total en el lugar correcto
    scrollableContainer.insertBefore(totalDisplay, paymentRowsContainer);

    // Inicializar contador y agregar primera fila
    paymentRowCounter = 0;
    addPaymentRow();
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

// Función para agregar una fila de pago
function addPaymentRow() {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
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
    paymentRow.className = 'payment-row';
    paymentRow.innerHTML = `
        <div class="payment-row-header">
            <div class="payment-icons-container">
                <span class="payment-icon" data-type="QR" style="display: none;">
                    <i class="fas fa-qrcode"></i>
                </span>
                <span class="payment-icon" data-type="Efectivo" style="display: none;">
                    <i class="fas fa-money-bill-wave"></i>
                </span>
                <span class="payment-icon" data-type="Tarjeta" style="display: none;">
                    <i class="fas fa-credit-card"></i>
                </span>
            </div>
            <button onclick="removePaymentRow('${paymentRow.id}')" class="payment-row-remove">✕</button>
        </div>
        <div class="payment-type-container">
            <label class="payment-label">Tipo de Pago:</label>
            <select class="payment-type" onchange="updatePaymentFields(this, '${paymentRow.id}')">
                ${!existingPaymentTypes.has('Efectivo') ? '<option value="Efectivo">Efectivo</option>' : ''}
                ${!existingPaymentTypes.has('QR') ? '<option value="QR">QR</option>' : ''}
                ${!existingPaymentTypes.has('Tarjeta') ? '<option value="Tarjeta">Tarjeta</option>' : ''}
            </select>
        </div>
        <div id="transaction-field-${paymentRowCounter}" class="transaction-field hidden">
            <label class="payment-label">Nro Transacción:</label>
            <input type="text" class="transaction-number" placeholder="Ingrese el número de transacción">
        </div>
        <div class="payment-amount-group">
            <div class="payment-amount-input">
                <label class="payment-label">Total a Pagar:</label>
                <input type="text" class="payment-input total-amount" value="${totalToPay.toFixed(2)}" readonly>
            </div>
            <div class="payment-amount-input">
                <label class="payment-label">Total Pagado:</label>
                <input type="text" class="payment-input total-paid" oninput="updateChange('${paymentRow.id}')">
            </div>
            <div class="payment-amount-input">
                <label class="payment-label">Cambio:</label>
                <input type="text" class="payment-input change" readonly>
            </div>
        </div>
    `;

    // Agregar la nueva fila al contenedor
    paymentRowsContainer.appendChild(paymentRow);

    // Mostrar el ícono del tipo de pago inicial
    updatePaymentIcon(paymentRow.querySelector('.payment-type'), paymentRow.id);

    // Actualizar campos según el tipo de pago seleccionado
    updatePaymentFields(paymentRow.querySelector('.payment-type'), paymentRow.id);
}

// Función para actualizar los campos según el tipo de pago
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

// Función para actualizar el ícono del tipo de pago
function updatePaymentIcon(selectElement, rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const icons = row.querySelectorAll('.payment-icon');
        icons.forEach(icon => {
            icon.style.display = 'none';
            if (icon.dataset.type === selectElement.value) {
                icon.style.display = 'inline-block';
            }
        });
    }
}

// Función para eliminar una fila de pago
function removePaymentRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
        // Recalcular los totales
        updateAllPaymentRows();
    }
}

// Función para actualizar el cambio en una fila de pago
function updateChange(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        const totalAmountInput = row.querySelector('.total-amount');
        const totalPaidInput = row.querySelector('.total-paid');
        const changeInput = row.querySelector('.change');

        const totalAmount = parseFloat(totalAmountInput.value) || 0;
        const totalPaid = parseFloat(totalPaidInput.value) || 0;

        const change = totalPaid - totalAmount;

        if (!isNaN(change)) {
            changeInput.value = change.toFixed(2);
            
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
            totalPaidInput.classList.remove('error-input', 'success-input');
            changeInput.classList.remove('error-input', 'success-input');
        }

        updateRemainingTotal(rowId);
    }
}

// Función para actualizar el Total a Pagar en las filas posteriores
function updateRemainingTotal(currentRowId) {
    const paymentRowsContainer = document.getElementById('payment-rows-container');
    const paymentRows = paymentRowsContainer.querySelectorAll('.payment-row');

    let totalAmount = calcularTotal();
    let totalPaid = 0;

    paymentRows.forEach(row => {
        const totalPaidInput = row.querySelector('.total-paid');
        totalPaid += parseFloat(totalPaidInput.value) || 0;
    });

    const remainingTotal = totalAmount - totalPaid;

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

// Función para validar el pago
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

// Función para procesar el pago
function processPayment() {
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

    localStorage.setItem('paymentDetails', JSON.stringify(paymentDetails));
    localStorage.setItem('paymentMethods', JSON.stringify(paymentMethods));

    closePaymentModal();
    paymentProcessed = true;
    localStorage.setItem('paymentProcessed', 'true');
    lockOrderInterface();
    loadCustomerDetails(paymentDetails);
}

// Asegúrate de que el DOM esté cargado antes de asignar eventos
document.addEventListener('DOMContentLoaded', function() {
    // Asignar evento al botón de pago múltiple en order-details
    const btnMultiplePayment = document.getElementById('btn-multiple-payment');
    if (btnMultiplePayment) {
        btnMultiplePayment.addEventListener('click', showPaymentModal);
    }
});