// Variables específicas del modal de pago
let paymentRowCounter = 0;

// Función para mostrar el modal de pago
function showPaymentModal() {
    const order = JSON.parse(localStorage.getItem('order')) || [];
    if (order.length === 0) {
        alert('No hay ítems en el pedido para realizar el pago');
        return;
    }

    // 🔥 NUEVO: Verificar tipo de pedido y mostrar advertencia si es "Recoger"
    const orderType = localStorage.getItem('orderType') || 'Comer aquí';
    if (orderType === 'Recoger') {
        const confirmMessage = '⚠️ IMPORTANTE: Para pedidos "Recoger" solo están disponibles los métodos de pago QR y Transferencia Bancaria.\n\n¿Desea continuar?';
        if (!confirm(confirmMessage)) {
            return;
        }
    }

    // Usar la función del modal
    if (typeof openPaymentModal === 'function') {
        openPaymentModal();
    } else {
        console.error('❌ Función openPaymentModal no encontrada');
    }
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



function openPaymentModal() {
    console.log('🚀 Abriendo modal de pagos...');

    const modal = document.getElementById('payment-modal');
    if (!modal) {
        console.error('❌ No se encontró el modal');
        return;
    }

    modal.classList.remove('hidden');
    loadOrderData();

    // Inicializar modal
    setTimeout(() => {
        initializeModal();
        showPickupPaymentWarning(); // 🔥 NUEVA LÍNEA
    }, 50);
}

// Asegúrate de que el DOM esté cargado antes de asignar eventos
document.addEventListener('DOMContentLoaded', function () {
    // Asignar evento al botón de pago múltiple en order-details
    const btnMultiplePayment = document.getElementById('btn-multiple-payment');
    if (btnMultiplePayment) {
        btnMultiplePayment.addEventListener('click', showPaymentModal);
    }
});