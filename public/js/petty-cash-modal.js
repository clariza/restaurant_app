console.log('📦 petty-cash-modal.js cargado');

// =============================================
// 🔥 FUNCIONES DEL MODAL DE CIERRE (closure-modal)
// =============================================

/**
 * Inicializar el modal de cierre cuando se carga dinámicamente
 */
window.initializeClosureModal = function (pettyCashId) {
    console.log('🚀 [INIT] Inicializando modal de cierre para caja:', pettyCashId);

    // Esperar un momento para que el DOM se renderice
    setTimeout(() => {
        setupClosureEventListeners();
        calculateInitialTotals();
        console.log('✅ [INIT] Modal de cierre completamente inicializado');
    }, 100);
};

/**
 * Configurar event listeners del modal de cierre
 */
function setupClosureEventListeners() {
    console.log('🔧 [SETUP] Configurando event listeners...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [SETUP] Wrapper del modal no encontrado');
        return;
    }

    // ✅ Event listeners para inputs de denominaciones
    const denominationInputs = wrapper.querySelectorAll('.denomination-input22');
    console.log(`📊 [SETUP] Encontrados ${denominationInputs.length} inputs de denominaciones`);

    denominationInputs.forEach((input, index) => {
        // Múltiples eventos para asegurar que funcione
        input.addEventListener('input', function (e) {
            console.log(`⌨️ [INPUT] Denominación ${index + 1}: ${e.target.value}`);
            calculateDenominationsTotal();
        });

        input.addEventListener('change', function (e) {
            console.log(`🔄 [CHANGE] Denominación ${index + 1}: ${e.target.value}`);
            calculateDenominationsTotal();
        });

        input.addEventListener('keyup', function (e) {
            console.log(`⬆️ [KEYUP] Denominación ${index + 1}: ${e.target.value}`);
            calculateDenominationsTotal();
        });
    });

    // ✅ Event listeners para inputs de gastos
    const expenseInputs = wrapper.querySelectorAll('.expense-amount-input');
    console.log(`📊 [SETUP] Encontrados ${expenseInputs.length} inputs de gastos`);

    expenseInputs.forEach((input, index) => {
        input.addEventListener('input', function (e) {
            console.log(`💰 [INPUT] Gasto ${index + 1}: ${e.target.value}`);
            calculateExpensesTotal();
        });

        input.addEventListener('change', function (e) {
            console.log(`💰 [CHANGE] Gasto ${index + 1}: ${e.target.value}`);
            calculateExpensesTotal();
        });
    });

    // ✅ Event listener para botón agregar gasto
    const addExpenseBtn = wrapper.querySelector('#add-expense-btn');
    if (addExpenseBtn) {
        addExpenseBtn.addEventListener('click', function () {
            console.log('➕ [CLICK] Agregar gasto');
            addExpenseRow();
        });
        console.log('✅ [SETUP] Listener agregado a botón agregar gasto');
    }

    // ✅ Event listeners para botones eliminar gasto
    const removeButtons = wrapper.querySelectorAll('.remove-expense-btn');
    removeButtons.forEach((btn, index) => {
        btn.addEventListener('click', function () {
            console.log(`🗑️ [CLICK] Eliminar gasto ${index + 1}`);
            removeExpenseRow(this);
        });
    });
    console.log(`✅ [SETUP] Listeners agregados a ${removeButtons.length} botones eliminar`);

    // ✅ Event listener para botón guardar
    const saveBtn = wrapper.querySelector('#save-closure-btn');
    if (saveBtn) {
        const pettyCashId = saveBtn.getAttribute('data-petty-cash-id');
        saveBtn.addEventListener('click', function () {
            console.log('💾 [CLICK] Guardar cierre');
            saveClosure(pettyCashId);
        });
        console.log('✅ [SETUP] Listener agregado a botón guardar');
    }

    // ✅ Event listener para botón cancelar
    const cancelBtn = wrapper.querySelector('#cancel-closure-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            console.log('❌ [CLICK] Cancelar');
            if (typeof window.closePettyCashModal === 'function') {
                window.closePettyCashModal();
            }
        });
        console.log('✅ [SETUP] Listener agregado a botón cancelar');
    }

    console.log('✅ [SETUP] Todos los event listeners configurados');
}

/**
 * Calcular totales iniciales
 */
function calculateInitialTotals() {
    console.log('🧮 [CALC] Calculando totales iniciales...');
    calculateDenominationsTotal();
    calculateExpensesTotal();
}

/**
 * 💵 Calcular total de denominaciones
 */
function calculateDenominationsTotal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💵 [DENOM] Calculando denominaciones...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [DENOM] Wrapper no encontrado');
        return 0;
    }

    let total = 0;
    const inputs = wrapper.querySelectorAll('.denomination-input2');
    console.log(`📊 [DENOM] Inputs encontrados: ${inputs.length}`);

    inputs.forEach((input, index) => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        if (cantidad > 0) {
            console.log(`  ${index + 1}. Bs.${denominacion} x ${cantidad} = Bs.${subtotal.toFixed(2)}`);
        }

        // Actualizar subtotal en la fila
        const row = input.closest('tr');
        if (row) {
            const subtotalElement = row.querySelector('.subtotal-modal');
            if (subtotalElement) {
                subtotalElement.textContent = `Bs. ${subtotal.toFixed(2)}`;
            }
        }

        total += subtotal;
    });

    console.log(`💵 [DENOM] Total: Bs.${total.toFixed(2)}`);

    // Actualizar el total en la tabla
    const totalElement = wrapper.querySelector('#totalModal');
    if (totalElement) {
        totalElement.textContent = `Bs. ${total.toFixed(2)}`;
    }

    // Actualizar el input de total efectivo
    const totalEfectivoInput = wrapper.querySelector('#total-efectivo-modal');
    if (totalEfectivoInput) {
        totalEfectivoInput.value = total.toFixed(2);
        console.log(`✅ [DENOM] Input actualizado: ${total.toFixed(2)}`);
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return total;
}

/**
 * 💰 Calcular total de gastos
 */
function calculateExpensesTotal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💰 [EXPENSE] Calculando gastos...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [EXPENSE] Wrapper no encontrado');
        return 0;
    }

    // Obtener gastos existentes de la BD
    const totalGastosInput = wrapper.querySelector('#total-gastos-modal');
    if (!totalGastosInput) {
        console.error('❌ [EXPENSE] Input de total gastos no encontrado');
        return 0;
    }

    // El valor inicial viene del servidor (gastos ya registrados)
    const existingExpenses = parseFloat(totalGastosInput.getAttribute('data-existing-expenses') ||
        totalGastosInput.value || 0);

    console.log(`📊 [EXPENSE] Gastos existentes: ${existingExpenses.toFixed(2)}`);

    // Calcular gastos nuevos
    let newExpenses = 0;
    const expenseRows = wrapper.querySelectorAll('#expensesContainerModal .expense-row');
    console.log(`📊 [EXPENSE] Filas de gastos: ${expenseRows.length}`);

    expenseRows.forEach((row, index) => {
        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput ? nameInput.value.trim() : '';
        const amount = amountInput ? parseFloat(amountInput.value) || 0 : 0;

        if (name && amount > 0) {
            newExpenses += amount;
            console.log(`  ${index + 1}. "${name}": Bs.${amount.toFixed(2)}`);
        }
    });

    const totalExpenses = existingExpenses + newExpenses;

    console.log(`📊 [EXPENSE] Gastos nuevos: ${newExpenses.toFixed(2)}`);
    console.log(`💰 [EXPENSE] Total: ${totalExpenses.toFixed(2)}`);

    // Actualizar el input de total gastos
    totalGastosInput.value = totalExpenses.toFixed(2);

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return totalExpenses;
}

/**
 * ➕ Agregar fila de gasto
 */
function addExpenseRow() {
    console.log('➕ [ADD] Agregando fila de gasto...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [ADD] Wrapper no encontrado');
        return;
    }

    const container = wrapper.querySelector('#expensesContainerModal');
    if (!container) {
        console.error('❌ [ADD] Contenedor de gastos no encontrado');
        return;
    }

    const newRow = document.createElement('div');
    newRow.className = 'expense-row';
    newRow.innerHTML = `
        <div class="expense-field">
            <input type="text" 
                   class="expense-input expense-name-input" 
                   placeholder="Nombre del gasto" 
                   name="expense_name[]">
        </div>
        <div class="expense-field">
            <input type="text" 
                   class="expense-input" 
                   placeholder="Descripción/Categoría" 
                   name="expense_description[]">
        </div>
        <div class="expense-field" style="max-width: 150px;">
            <input type="number" 
                   class="expense-input expense-amount-input" 
                   placeholder="Monto" 
                   step="0.01" 
                   min="0"
                   name="expense_amount[]">
        </div>
        <button type="button" 
                class="remove-expense-btn">
            <i class="fas fa-trash"></i>
        </button>
    `;

    container.appendChild(newRow);

    // Agregar event listeners a la nueva fila
    const amountInput = newRow.querySelector('.expense-amount-input');
    if (amountInput) {
        amountInput.addEventListener('input', calculateExpensesTotal);
        amountInput.addEventListener('change', calculateExpensesTotal);
    }

    const removeBtn = newRow.querySelector('.remove-expense-btn');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            removeExpenseRow(this);
        });
    }

    console.log('✅ [ADD] Fila agregada con listeners');
}

/**
 * 🗑️ Eliminar fila de gasto
 */
function removeExpenseRow(button) {
    console.log('🗑️ [REMOVE] Eliminando fila de gasto...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) return;

    const container = wrapper.querySelector('#expensesContainerModal');
    const row = button.closest('.expense-row');

    if (!container || !row) return;

    if (container.children.length > 1) {
        row.remove();
        calculateExpensesTotal();
        console.log('✅ [REMOVE] Fila eliminada');
    } else {
        // Si es la última fila, solo limpiar los inputs
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        calculateExpensesTotal();
        console.log('🧹 [REMOVE] Última fila limpiada');
    }
}

/**
 * 💾 Guardar cierre
 */
async function saveClosure(pettyCashId) {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💾 [SAVE] Guardando cierre...');
    console.log(`📌 [SAVE] Caja chica ID: ${pettyCashId}`);

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [SAVE] Wrapper no encontrado');
        return;
    }

    const saveBtn = wrapper.querySelector('#save-closure-btn');
    if (!saveBtn) {
        console.error('❌ [SAVE] Botón de guardar no encontrado');
        return;
    }

    // Recopilar datos
    const totalSalesCash = parseFloat(wrapper.querySelector('#total-efectivo-modal')?.value) || 0;
    const totalSalesQR = parseFloat(wrapper.querySelector('#ventas-qr-modal')?.value) || 0;
    const totalSalesCard = parseFloat(wrapper.querySelector('#ventas-tarjeta-modal')?.value) || 0;
    const totalExpenses = calculateExpensesTotal();

    // Recopilar gastos nuevos
    const expenses = [];
    const expenseRows = wrapper.querySelectorAll('#expensesContainerModal .expense-row');

    expenseRows.forEach((row) => {
        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const descriptionInput = row.querySelector('input[name="expense_description[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput ? nameInput.value.trim() : '';
        const description = descriptionInput ? descriptionInput.value.trim() : '';
        const amount = amountInput ? parseFloat(amountInput.value) || 0 : 0;

        if (name && amount > 0) {
            expenses.push({ name, description, amount });
        }
    });

    const dataToSend = {
        petty_cash_id: pettyCashId,
        total_sales_cash: totalSalesCash,
        total_sales_qr: totalSalesQR,
        total_sales_card: totalSalesCard,
        total_expenses: totalExpenses,
        expenses: expenses
    };

    console.log('📤 [SAVE] Datos a enviar:', dataToSend);

    // Deshabilitar botón
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
    saveBtn.disabled = true;

    try {
        const response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            console.log('✅ [SAVE] Cierre guardado exitosamente');
            alert(`¡Cierre guardado correctamente!\nGastos registrados: ${data.data?.expenses_count || 0}`);

            // Cerrar modal
            if (typeof window.closePettyCashModal === 'function') {
                window.closePettyCashModal();
            }

            // Recargar si estamos en la página de caja chica
            if (window.location.pathname.includes('petty-cash')) {
                window.location.reload();
            }
        } else {
            throw new Error(data.message || 'No se pudo guardar el cierre');
        }

    } catch (error) {
        console.error('❌ [SAVE] Error:', error);
        alert('Error al guardar el cierre: ' + error.message);
    } finally {
        saveBtn.innerHTML = originalBtnText;
        saveBtn.disabled = false;
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

// =============================================
// FUNCIONES EXISTENTES DEL MODAL PRINCIPAL
// =============================================

async function openPettyCashModal() {
    console.log('🔓 Abriendo modal de caja chica...');

    const modal = document.getElementById('petty-cash-modal');
    const content = document.getElementById('petty-cash-content');

    if (!modal) {
        console.error('❌ Modal de caja chica no encontrado');
        return;
    }

    if (!window.routes || !window.routes.pettyCashModalContent) {
        const baseUrl = window.location.origin;
        window.routes = window.routes || {};
        window.routes.pettyCashModalContent = `${baseUrl}/petty-cash/modal-content`;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const response = await fetch(window.routes.pettyCashModalContent, {
            method: 'GET',
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const html = await response.text();
        content.innerHTML = html;
        initializePettyCashModal();

    } catch (error) {
        console.error('❌ Error al cargar contenido:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600 mb-4">Error al cargar la información</p>
                <button onclick="openPettyCashModal()" class="bg-[#203363] text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-redo mr-2"></i> Reintentar
                </button>
            </div>
        `;
    }
}

function closePettyCashModal() {
    const modal = document.getElementById('petty-cash-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function initializePettyCashModal() {
    console.log('⚙️ [INIT] Inicializando modal principal...');

    // ✅ Event Delegation para inputs de denominación
    document.addEventListener('input', function (e) {
        if (e.target.matches('.denomination-input2, .contar-input-closure, input[data-denominacion]')) {
            console.log('💵 [INPUT] Denominación cambió:', e.target.value);
            calcularTotalModal();
        }
    });

    // ✅ Event Delegation para inputs de gastos
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="expense_amount[]"]')) {
            console.log('💰 [INPUT] Gasto cambió:', e.target.value);
            calculateTotalExpensesModal();
        }
    });

    // Calcular totales iniciales
    calcularTotalModal();
    calculateTotalExpensesModal();

    console.log('✅ [INIT] Modal inicializado correctamente');
}

function calcularTotalModal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💵 [MODAL] Calculando total de denominaciones...');

    let total = 0;

    // ✅ SOLUCIÓN: Buscar los inputs con las clases correctas que están en tu HTML
    // Puedes usar múltiples selectores para mayor compatibilidad
    const inputs = document.querySelectorAll(
        '.denomination-input2, ' +           // Clase del modal de cierre
        '.contar-input-closure, ' +          // Clase alternativa
        'input[data-denominacion]'           // Cualquier input con data-denominacion
    );

    console.log(`📊 [MODAL] Inputs encontrados: ${inputs.length}`);

    if (inputs.length === 0) {
        console.warn('⚠️ [MODAL] No se encontraron inputs de denominación');
        return 0;
    }

    inputs.forEach((input, index) => {
        // Obtener denominación del atributo data
        const denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        // Log solo si hay cantidad
        if (cantidad > 0) {
            console.log(`  ${index + 1}. Bs.${denominacion.toFixed(2)} x ${cantidad} = Bs.${subtotal.toFixed(2)}`);
        }

        // Actualizar subtotal en la fila correspondiente
        const row = input.closest('tr');
        if (row) {
            const subtotalElement = row.querySelector('.subtotal-closure, .subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = `Bs.${subtotal.toFixed(2)}`;
            }
        }

        total += subtotal;
    });

    console.log(`💵 [MODAL] Total calculado: Bs.${total.toFixed(2)}`);

    // Actualizar el total en la tabla
    const totalElement = document.querySelector('#total-closure, #total-modal');
    if (totalElement) {
        totalElement.textContent = `Bs.${total.toFixed(2)}`;
        console.log('✅ [MODAL] Total en tabla actualizado');
    } else {
        console.warn('⚠️ [MODAL] Elemento de total no encontrado');
    }

    // Actualizar el input de ventas en efectivo
    const ventasEfectivoInput = document.querySelector(
        '#ventas-efectivo-closure, #ventas-efectivo-modal'
    );
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = total.toFixed(2);
        console.log('✅ [MODAL] Input de ventas en efectivo actualizado');
    } else {
        console.warn('⚠️ [MODAL] Input de ventas en efectivo no encontrado');
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return total;
}

function calculateTotalExpensesModal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💰 [MODAL] Calculando total de gastos...');

    let totalGastosBD = 0;
    let totalGastosNuevos = 0;

    // Obtener gastos de la BD (si existen)
    const totalGastosInput = document.querySelector('#total-gastos-closure, #total-gastos-modal');
    if (totalGastosInput) {
        totalGastosBD = parseFloat(totalGastosInput.getAttribute('data-gastos-bd')) || 0;
        console.log(`📊 [MODAL] Gastos en BD: Bs.${totalGastosBD.toFixed(2)}`);
    }

    // Calcular gastos nuevos del formulario
    const expenseInputs = document.querySelectorAll('input[name="expense_amount[]"]');
    console.log(`📊 [MODAL] Inputs de gastos encontrados: ${expenseInputs.length}`);

    expenseInputs.forEach((input, index) => {
        const amount = parseFloat(input.value) || 0;
        if (amount > 0) {
            totalGastosNuevos += amount;
            console.log(`  ${index + 1}. Gasto: Bs.${amount.toFixed(2)}`);
        }
    });

    const totalGastos = totalGastosBD + totalGastosNuevos;

    console.log(`💰 [MODAL] Gastos nuevos: Bs.${totalGastosNuevos.toFixed(2)}`);
    console.log(`💰 [MODAL] Total gastos: Bs.${totalGastos.toFixed(2)}`);

    // Actualizar el input
    if (totalGastosInput) {
        totalGastosInput.value = totalGastos.toFixed(2);
        console.log('✅ [MODAL] Input de total gastos actualizado');
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return totalGastos;
}

// =============================================
// EVENT LISTENERS GLOBALES
// =============================================

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('petty-cash-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closePettyCashModal();
        }
    }
});

// =============================================
// EXPORTAR FUNCIONES AL SCOPE GLOBAL
// =============================================
document.addEventListener('input', function (e) {
    // Detectar inputs de denominación
    if (e.target.matches('.denomination-input2, .contar-input-closure')) {
        console.log('💵 Input detectado vía delegation:', e.target.value);
        calcularTotalModal();
    }

    // Detectar inputs de gastos
    if (e.target.matches('input[name="expense_amount[]"]')) {
        console.log('💰 Gasto detectado vía delegation:', e.target.value);
        calculateTotalExpensesModal();
    }
});

window.initializePettyCashModal = initializePettyCashModal;
window.openPettyCashModal = openPettyCashModal;
window.closePettyCashModal = closePettyCashModal;
window.calcularTotalModal = calcularTotalModal;
window.calculateTotalExpensesModal = calculateTotalExpensesModal;

console.log('✅ petty-cash-modal.js inicializado correctamente');