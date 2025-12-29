// ========================================
// 📁 public/js/petty-cash-index.js
// VERSIÓN UNIFICADA - Incluye funcionalidad de index + modal
// ========================================

console.log('📦 Cargando petty-cash-index.js UNIFICADO...');

// ========================================
// GESTIÓN DEL MODAL PRINCIPAL (index.blade.php)
// ========================================

/**
 * Abrir modal de cierre (index.blade.php)
 */
window.openModal = function (id) {
    console.log('🔓 Abriendo modal para caja chica ID:', id);

    const modal = document.getElementById('modal');
    if (!modal) {
        console.error('❌ Modal no encontrado');
        return;
    }

    modal.classList.add('active');

    const pettyCashIdInput = document.getElementById('petty_cash_id');
    if (pettyCashIdInput) {
        pettyCashIdInput.value = id;
        console.log('✅ ID de caja establecido:', id);
    }

    // Resetear denominaciones
    document.querySelectorAll('.denomination-input').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('.subtotal').forEach(span => {
        span.textContent = '$0.00';
    });

    const totalElement = document.getElementById('total');
    if (totalElement) {
        totalElement.textContent = '$0.00';
    }

    const totalEfectivoInput = document.getElementById('total-efectivo');
    if (totalEfectivoInput) {
        totalEfectivoInput.value = '0';
    }

    const totalSalesCashInput = document.getElementById('total_sales_cash');
    if (totalSalesCashInput) {
        totalSalesCashInput.value = '0';
    }

    resetExpensesContainer();

    if (window.pettyCashData) {
        const ventasQRInput = document.getElementById('ventas-qr');
        const ventasTarjetaInput = document.getElementById('ventas-tarjeta');
        const totalGastosInput = document.getElementById('total-gastos');

        if (ventasQRInput) {
            ventasQRInput.value = window.pettyCashData.totalSalesQR || 0;
        }
        if (ventasTarjetaInput) {
            ventasTarjetaInput.value = window.pettyCashData.totalSalesCard || 0;
        }
        if (totalGastosInput) {
            const existingExpenses = window.pettyCashData.totalExpenses || 0;
            totalGastosInput.value = existingExpenses.toFixed(2);
            totalGastosInput.setAttribute('data-gastos-bd', existingExpenses);

            const totalExpensesHidden = document.getElementById('total_expenses');
            if (totalExpensesHidden) {
                totalExpensesHidden.value = existingExpenses.toFixed(2);
            }
        }
    }

    console.log('✅ Modal abierto correctamente');
};

/**
 * Cerrar modal (index.blade.php)
 */
window.closeModal = function () {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.classList.remove('active');
    }
};
/**
 * Cerrar modal interno de cierre
 */
window.closeInternalModalClosure = function () {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('🚪 [CLOSE] Cerrando modal interno...');

    // Buscar TODOS los posibles contenedores
    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');
    const modalWrapper = document.querySelector('.closure-internal-modal');
    const parentModal = document.getElementById('petty-cash-modal');

    console.log('🔍 Elementos encontrados:');
    console.log('  - Overlay:', overlay ? '✅' : '❌');
    console.log('  - Modal:', modal ? '✅' : '❌');
    console.log('  - Wrapper:', modalWrapper ? '✅' : '❌');
    console.log('  - Parent Modal:', parentModal ? '✅' : '❌');

    // Ocultar overlay
    if (overlay) {
        overlay.style.display = 'none';
        overlay.style.opacity = '0';
        overlay.style.visibility = 'hidden';
        console.log('✅ Overlay ocultado');
    }

    // Ocultar modal
    if (modal) {
        modal.style.display = 'none';
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';
        console.log('✅ Modal ocultado');
    }

    // Ocultar wrapper
    if (modalWrapper && modalWrapper !== modal) {
        modalWrapper.style.display = 'none';
        modalWrapper.style.opacity = '0';
        modalWrapper.style.visibility = 'hidden';
        console.log('✅ Wrapper ocultado');
    }

    // Restaurar scroll
    document.body.style.overflow = '';
    console.log('✅ Scroll restaurado');

    console.log('✅ Modal interno cerrado');
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
};

/**
 * Abrir modal interno
 */
window.openInternalModalClosure = function () {
    console.log('🔓 Abriendo modal interno...');

    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');

    if (overlay) {
        overlay.style.display = 'block';
        overlay.style.opacity = '1';
        overlay.style.visibility = 'visible';
    }

    if (modal) {
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.visibility = 'visible';
    }

    document.body.style.overflow = 'hidden';
    console.log('✅ Modal interno abierto');
};
// ========================================
// GESTIÓN DE GASTOS
// ========================================
/**
 * Resetear contenedor de gastos
 */
window.resetExpensesContainer = function () {
    const expensesContainer = document.getElementById('expensesContainer');
    if (!expensesContainer) {
        console.warn('⚠️ Contenedor de gastos no encontrado');
        return;
    }

    expensesContainer.innerHTML = '';
    addExpenseRow('', '', '');
    console.log('🧹 Contenedor de gastos reseteado');
};
/**
 * Agregar fila de gasto
 */
window.addExpenseRow = function (name = '', description = '', amount = '') {
    const expensesContainer = document.getElementById('expensesContainer');
    if (!expensesContainer) {
        console.error('❌ Contenedor de gastos no encontrado');
        return;
    }

    const newExpenseRow = document.createElement('div');
    newExpenseRow.className = 'expense-row';
    newExpenseRow.innerHTML = `
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input" 
                   placeholder="Nombre del gasto" name="expense_name[]" 
                   value="${name}">
        </div>
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input" 
                   placeholder="Descripción/Categoría" name="expense_description[]" 
                   value="${description}">
        </div>
        <div class="expense-field">
            <input type="number" class="form-control form-control-sm expense-input" 
                   placeholder="Monto" step="0.01" min="0" name="expense_amount[]" 
                   value="${amount}">
        </div>
        <div class="expense-actions">
            <button type="button" class="btn btn-outline-danger btn-sm remove-expense-btn" 
                    onclick="removeExpense(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    expensesContainer.appendChild(newExpenseRow);
    console.log('➕ Fila de gasto agregada');
};
/**
 * Agregar gasto
 */
window.addExpense = function () {
    addExpenseRow('', '', '');
};

/**
 * Eliminar fila de gasto
 */
window.removeExpense = function (button) {
    const expenseRow = button.closest('.expense-row');
    const container = document.getElementById('expensesContainer');

    if (!container || !expenseRow) {
        console.error('❌ No se pudo eliminar la fila de gasto');
        return;
    }

    if (container.children.length > 1) {
        expenseRow.remove();
        console.log('🗑️ Fila de gasto eliminada');
    } else {
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        console.log('🧹 Última fila limpiada');
    }

    calculateTotalExpenses();
};
/**
 * Validar fila de gasto
 */
window.validateExpenseRow = function (input) {
    const row = input.closest('.expense-row');
    if (!row) return;

    const nameInput = row.querySelector('input[name="expense_name[]"]');
    const amountInput = row.querySelector('input[name="expense_amount[]"]');

    if (amountInput && amountInput.value && nameInput && !nameInput.value) {
        nameInput.style.borderColor = '#f87171';
    } else if (nameInput) {
        nameInput.style.borderColor = '';
    }
};

// ========================================
// CÁLCULOS DE DENOMINACIONES
// ========================================

/**
 * Calcular total de denominaciones
 */
window.calcularTotalDenominaciones = function () {
    console.log('💵 Calculando denominaciones...');

    let total = 0;

    document.querySelectorAll('.denomination-input').forEach(input => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        const subtotalElement = input.closest('tr')?.querySelector('.subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
        }

        total += subtotal;
    });

    console.log(`   Total: $${total.toFixed(2)}`);

    const totalElement = document.getElementById('total');
    if (totalElement) {
        totalElement.textContent = `$${total.toFixed(2)}`;
    }

    const totalEfectivoInput = document.getElementById('total-efectivo');
    const totalSalesCashInput = document.getElementById('total_sales_cash');

    if (totalEfectivoInput) {
        totalEfectivoInput.value = total.toFixed(2);
    }
    if (totalSalesCashInput) {
        totalSalesCashInput.value = total.toFixed(2);
    }

    return total;
};

// ========================================
// CÁLCULOS DE GASTOS
// ========================================

/**
 * Calcular total de gastos
 */
window.calculateTotalExpenses = function () {
    console.log('💰 Calculando gastos...');

    let totalNewExpenses = 0;
    let validExpenseCount = 0;

    document.querySelectorAll('#expensesContainer .expense-row').forEach(row => {
        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput?.value?.trim() || '';
        const amount = parseFloat(amountInput?.value) || 0;

        if (name && amount > 0) {
            totalNewExpenses += amount;
            validExpenseCount++;
        }
    });

    const totalGastosInput = document.getElementById('total-gastos');
    const existingExpenses = parseFloat(
        totalGastosInput?.getAttribute('data-gastos-bd') ||
        window.pettyCashData?.totalExpenses ||
        0
    );

    const totalExpenses = existingExpenses + totalNewExpenses;

    console.log(`   Gastos existentes: $${existingExpenses.toFixed(2)}`);
    console.log(`   Gastos nuevos (${validExpenseCount}): $${totalNewExpenses.toFixed(2)}`);
    console.log(`   Total: $${totalExpenses.toFixed(2)}`);

    if (totalGastosInput) {
        totalGastosInput.value = totalExpenses.toFixed(2);
    }

    const totalExpensesElement = document.getElementById('total_expenses');
    if (totalExpensesElement) {
        totalExpensesElement.value = totalExpenses.toFixed(2);
    }

    return totalExpenses;
};

// ========================================
// GUARDAR CIERRE
// ========================================

/**
 * Guardar cierre de caja
 */
window.saveClosure = async function () {
    console.log('💾 Iniciando proceso de guardado de cierre...');

    try {
        if (typeof window.pettyCashData === 'undefined') {
            console.error('❌ window.pettyCashData es undefined');
            alert('Error: La configuración de la aplicación no se cargó correctamente. Por favor, recarga la página.');
            return;
        }

        if (!window.pettyCashData.saveClosureUrl) {
            console.error('❌ saveClosureUrl no está definido en window.pettyCashData');
            console.log('Datos disponibles:', window.pettyCashData);
            alert('Error: URL de guardado no disponible. Por favor, contacta al administrador.');
            return;
        }

        if (!window.pettyCashData.csrfToken) {
            console.error('❌ csrfToken no está definido en window.pettyCashData');
            alert('Error: Token de seguridad no disponible. Por favor, recarga la página.');
            return;
        }
        if (!window.pettyCashData || !window.pettyCashData.saveClosureUrl) {
            throw new Error('Configuración de caja chica no disponible');
        }

        const pettyCashId = document.getElementById('petty_cash_id')?.value;

        if (!pettyCashId) {
            alert('Error: No se encontró el ID de la caja chica');
            console.error('❌ petty_cash_id no encontrado');
            return;
        }

        console.log('📌 Caja chica ID:', pettyCashId);

        const totalSalesCash = parseFloat(document.getElementById('total-efectivo')?.value) || 0;
        const totalSalesQR = parseFloat(document.getElementById('ventas-qr')?.value) || 0;
        const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta')?.value) || 0;

        console.log('💰 Valores de ventas:');
        console.log('   - Efectivo:', totalSalesCash);
        console.log('   - QR:', totalSalesQR);
        console.log('   - Tarjeta:', totalSalesCard);

        const totalExpenses = calculateTotalExpenses();
        console.log('💸 Total gastos:', totalExpenses);

        const expenses = [];
        const expenseRows = document.querySelectorAll('#expensesContainer .expense-row');

        expenseRows.forEach((row, index) => {
            const nameInput = row.querySelector('input[name="expense_name[]"]');
            const descriptionInput = row.querySelector('input[name="expense_description[]"]');
            const amountInput = row.querySelector('input[name="expense_amount[]"]');

            const name = nameInput?.value?.trim() || '';
            const description = descriptionInput?.value?.trim() || '';
            const amount = parseFloat(amountInput?.value) || 0;

            if (name && amount > 0) {
                expenses.push({
                    name: name,
                    description: description,
                    amount: amount
                });
                console.log(`   ✓ Gasto ${index + 1}: ${name} - Bs.${amount}`);
            }
        });

        console.log(`📋 Total gastos nuevos a registrar: ${expenses.length}`);

        const dataToSend = {
            petty_cash_id: pettyCashId,
            total_sales_cash: totalSalesCash,
            total_sales_qr: totalSalesQR,
            total_sales_card: totalSalesCard,
            total_expenses: totalExpenses,
            expenses: expenses
        };

        console.log('📤 Datos a enviar:', dataToSend);

        const saveBtn = document.querySelector('#btn-save-closure');
        if (!saveBtn) {
            console.error('❌ Botón de guardar no encontrado');
            return;
        }

        const originalBtnText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        saveBtn.disabled = true;

        const response = await fetch(window.pettyCashData.saveClosureUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pettyCashData.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        });

        console.log('📡 Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error response:', errorText);
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Respuesta del servidor:', data);

        if (data.success) {
            alert(
                '✅ ¡Cierre guardado correctamente!\n\n' +
                `Gastos registrados: ${data.data?.expenses_count || 0}\n` +
                `Monto final: Bs.${data.data?.current_amount?.toFixed(2) || '0.00'}`
            );

            closeModal();

            setTimeout(() => {
                window.location.reload();
            }, 500);

        } else {
            throw new Error(data.message || 'No se pudo guardar el cierre');
        }

    } catch (error) {
        console.error('❌ Error al guardar cierre:', error);
        alert('Error al guardar el cierre:\n' + error.message);
    } finally {
        const saveBtn = document.querySelector('#btn-save-closure');
        if (saveBtn) {
            saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar Cierre';
            saveBtn.disabled = false;
        }
    }
};

// ========================================
// FUNCIONES DE UTILIDAD
// ========================================

/**
 * Cerrar todas las cajas abiertas
 */
window.closeOpenPettyCash = async function () {
    if (!confirm('¿Estás seguro de cerrar todas las cajas chicas abiertas?')) {
        return;
    }

    console.log('🔒 Cerrando todas las cajas abiertas...');

    try {
        const response = await fetch('/petty-cash/close-all-open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pettyCashData?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({})
        });

        const data = await response.json();

        if (data.success) {
            console.log('✅ Cajas cerradas correctamente');
            window.location.reload();
        } else {
            throw new Error(data.message || 'No se pudieron cerrar las cajas');
        }
    } catch (error) {
        console.error('❌ Error:', error);
        alert('Error al cerrar las cajas: ' + error.message);
    }
};

// ========================================
// EVENT LISTENERS GLOBALES
// ========================================

/**
 * Cerrar modal con ESC
 */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        // Primero verificar modal interno
        const internalOverlay = document.getElementById('closure-internal-overlay');
        if (internalOverlay && internalOverlay.style.display !== 'none') {
            console.log('⌨️ ESC presionado - cerrando modal interno');
            closeInternalModalClosure();
            return;
        }

        // Luego verificar modal principal
        const modal = document.getElementById('modal');
        if (modal && modal.classList.contains('active')) {
            console.log('⌨️ ESC presionado - cerrando modal principal');
            closeModal();
            return;
        }
    }
});

/**
 * Cerrar modal interno al hacer click en overlay
 */
document.addEventListener('click', function (e) {
    if (e.target.id === 'closure-internal-overlay') {
        console.log('🖱️ Click en overlay interno detectado');
        closeInternalModalClosure();
    }
});

// ========================================
// INICIALIZACIÓN
// ========================================

document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Inicializando petty-cash-index.js...');

    // Verificar que estamos en la página correcta
    const modalElement = document.getElementById('modal');
    if (!modalElement) {
        console.log('ℹ️ No estamos en la página de index de caja chica');
        return;
    }

    // Verificar configuración global
    if (!window.pettyCashData) {
        console.error('❌ window.pettyCashData no está disponible');
        return;
    }

    console.log('✅ Configuración cargada:', window.pettyCashData);

    // Listeners para denominaciones
    const denominationInputs = document.querySelectorAll('.denomination-input');
    console.log(`📊 Configurando ${denominationInputs.length} inputs de denominación`);

    denominationInputs.forEach(input => {
        input.addEventListener('input', calcularTotalDenominaciones);
    });

    // Listener global para gastos (event delegation)
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="expense_amount[]"]') ||
            e.target.matches('input[name="expense_name[]"]')) {
            calculateTotalExpenses();
            validateExpenseRow(e.target);
        }
    });

    // Calcular totales iniciales
    calculateTotalExpenses();

    console.log('✅ petty-cash-index.js inicializado correctamente');
});
function initializePettyCash() {
    console.log('🚀 Inicializando petty-cash-index.js...');

    // Verificar que estamos en la página correcta
    const modalElement = document.getElementById('modal');
    if (!modalElement) {
        console.log('ℹ️ No estamos en la página de index de caja chica');
        return;
    }

    // Verificar configuración global
    if (!window.pettyCashData) {
        console.error('❌ window.pettyCashData no está disponible');
        return;
    }

    console.log('✅ Configuración cargada:', window.pettyCashData);

    // Listeners para denominaciones
    const denominationInputs = document.querySelectorAll('.denomination-input');
    console.log(`📊 Configurando ${denominationInputs.length} inputs de denominación`);

    denominationInputs.forEach(input => {
        input.addEventListener('input', calcularTotalDenominaciones);
    });

    // Listener global para gastos (event delegation)
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="expense_amount[]"]') ||
            e.target.matches('input[name="expense_name[]"]')) {
            calculateTotalExpenses();
            validateExpenseRow(e.target);
        }
    });

    // Calcular totales iniciales
    calculateTotalExpenses();

    console.log('✅ petty-cash-index.js inicializado correctamente');
}

// Esperar a que el DOM y los datos estén listos
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        // Esperar a que window.pettyCashData esté disponible
        if (window.pettyCashData) {
            initializePettyCash();
        } else {
            window.addEventListener('pettyCashDataReady', initializePettyCash);
        }
    });
} else {
    // DOM ya está cargado
    if (window.pettyCashData) {
        initializePettyCash();
    } else {
        window.addEventListener('pettyCashDataReady', initializePettyCash);
    }
}
console.log('✅ petty-cash-index.js cargado');