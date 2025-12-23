console.log('📦 petty-cash-modal.js cargado');

// =============================================
// 🔥 FUNCIONES DEL MODAL DE CIERRE (closure-modal)
// =============================================

/**
 * 🔍 FUNCIÓN DE DEPURACIÓN: Ver estructura del DOM
 */
function debugModalStructure() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('🔍 [DEBUG] Analizando estructura del modal...');

    const elements = {
        'closure-internal-overlay': document.getElementById('closure-internal-overlay'),
        'modal-closure-internal': document.getElementById('modal-closure-internal'),
        '.closure-internal-modal': document.querySelector('.closure-internal-modal'),
        'petty-cash-modal': document.getElementById('petty-cash-modal'),
        'petty-cash-content': document.getElementById('petty-cash-content')
    };

    for (const [name, element] of Object.entries(elements)) {
        if (element) {
            console.log(`✅ ${name}:`);
            console.log(`   - display: ${window.getComputedStyle(element).display}`);
            console.log(`   - opacity: ${window.getComputedStyle(element).opacity}`);
            console.log(`   - visibility: ${window.getComputedStyle(element).visibility}`);
            console.log(`   - zIndex: ${window.getComputedStyle(element).zIndex}`);
        } else {
            console.log(`❌ ${name}: NO ENCONTRADO`);
        }
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

/**
 * ✅ FUNCIÓN MEJORADA PARA CERRAR EL MODAL DE CIERRE
 * Cierra TODOS los elementos relacionados con el modal
 */
function closeInternalModalClosure() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('🚪 [CLOSE] Iniciando cierre completo del modal...');

    // 1. Buscar TODOS los posibles contenedores del modal
    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');
    const modalWrapper = document.querySelector('.closure-internal-modal');
    const parentModal = document.getElementById('petty-cash-modal');
    const modalContent = document.getElementById('petty-cash-content');

    console.log('🔍 [CLOSE] Elementos encontrados:');
    console.log('  - Overlay:', overlay ? '✅' : '❌');
    console.log('  - Modal:', modal ? '✅' : '❌');
    console.log('  - Modal Wrapper:', modalWrapper ? '✅' : '❌');
    console.log('  - Parent Modal:', parentModal ? '✅' : '❌');
    console.log('  - Modal Content:', modalContent ? '✅' : '❌');

    // 2. Ocultar overlay
    if (overlay) {
        overlay.style.display = 'none';
        overlay.style.opacity = '0';
        overlay.style.visibility = 'hidden';
        console.log('✅ [CLOSE] Overlay ocultado');
    }

    // 3. Ocultar modal principal
    if (modal) {
        modal.style.display = 'none';
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';
        console.log('✅ [CLOSE] Modal ocultado');
    }

    // 4. Ocultar wrapper si existe y es diferente del modal
    if (modalWrapper && modalWrapper !== modal) {
        modalWrapper.style.display = 'none';
        modalWrapper.style.opacity = '0';
        modalWrapper.style.visibility = 'hidden';
        console.log('✅ [CLOSE] Wrapper ocultado');
    }

    // 5. OPCIÓN A: Cerrar también el modal padre (si existe)
    if (parentModal && !parentModal.classList.contains('hidden')) {
        parentModal.classList.add('hidden');
        console.log('✅ [CLOSE] Modal padre cerrado');
    }

    // 6. OPCIÓN B: Limpiar el contenido cargado dinámicamente
    if (modalContent) {
        // Comentar esta línea si NO quieres limpiar el contenido
        // modalContent.innerHTML = '';
        console.log('📝 [CLOSE] Contenido preservado (no limpiado)');
    }

    // 7. Restaurar scroll del body
    document.body.style.overflow = '';
    console.log('✅ [CLOSE] Scroll restaurado');

    // 8. Log final
    console.log('✅ [CLOSE] Modal cerrado completamente');
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

/**
 * ✅ FUNCIÓN PARA ABRIR EL MODAL DE CIERRE
 * (Normalmente no se usa porque el modal ya está visible al cargar)
 */
function openInternalModalClosure() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('🔓 [OPEN] Abriendo modal interno de cierre...');

    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');

    if (overlay) {
        overlay.style.display = 'block';
        console.log('✅ [OPEN] Overlay activado');
    } else {
        console.error('❌ [OPEN] Overlay no encontrado');
    }

    if (modal) {
        modal.style.display = 'flex';
        console.log('✅ [OPEN] Modal activado');
    } else {
        console.error('❌ [OPEN] Modal no encontrado');
    }

    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';

    console.log('✅ [OPEN] Modal abierto correctamente');
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

/**
 * Abrir página de creación de nueva caja chica
 */
function openCreatePettyCashModal() {
    console.log('🆕 [CREATE] Abriendo modal para crear nueva caja...');
    closeInternalModalClosure();
    setTimeout(() => {
        window.location.href = '/petty-cash/create';
    }, 300);
}

/**
 * ✅ FUNCIÓN PRINCIPAL MEJORADA PARA GUARDAR CIERRE
 * Asegura que el modal se cierre después de guardar exitosamente
 */
async function guardarCierreUnificado(pettyCashId = null) {
    const context = detectContext();
    if (!context) {
        console.error('❌ No se pudo detectar el contexto');
        alert('Error: No se pudo determinar el contexto del modal');
        return;
    }

    const selectors = getSelectors(context);
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log(`💾 [${context.toUpperCase()}] Guardando cierre...`);

    // 1. Obtener el ID de la caja chica
    const pettyCashIdInput = document.querySelector(selectors.pettyCashIdInput);
    const finalPettyCashId = pettyCashId || (pettyCashIdInput ? pettyCashIdInput.value : null);

    if (!finalPettyCashId) {
        console.error('❌ ID de caja chica no encontrado');
        const shouldCreate = confirm(
            '⚠️ No hay una caja chica abierta.\n\n' +
            '¿Deseas abrir una nueva caja chica ahora?'
        );

        if (shouldCreate) {
            openCreatePettyCashModal();
        } else {
            closeInternalModalClosure();
        }
        return;
    }

    console.log(`📌 Caja chica ID: ${finalPettyCashId}`);

    // 2. Obtener datos del formulario
    const totalSalesCash = parseFloat(document.querySelector(selectors.totalEfectivoInput)?.value) || 0;
    const totalSalesQR = parseFloat(document.querySelector(selectors.ventasQRInput)?.value) || 0;
    const totalSalesCard = parseFloat(document.querySelector(selectors.ventasTarjetaInput)?.value) || 0;
    const totalExpenses = calcularTotalGastosUnificado();

    console.log('📊 Datos del cierre:');
    console.log(`   - Total Efectivo: $${totalSalesCash.toFixed(2)}`);
    console.log(`   - Total QR: $${totalSalesQR.toFixed(2)}`);
    console.log(`   - Total Tarjeta: $${totalSalesCard.toFixed(2)}`);
    console.log(`   - Total Gastos: $${totalExpenses.toFixed(2)}`);

    // 3. Recopilar gastos nuevos
    const expenses = [];
    const expenseRows = document.querySelectorAll(`${selectors.expensesContainer} .expense-row`);

    expenseRows.forEach((row, index) => {
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

    console.log(`📋 Gastos nuevos a registrar: ${expenses.length}`);

    // 4. Preparar datos para enviar
    const dataToSend = {
        petty_cash_id: finalPettyCashId,
        total_sales_cash: totalSalesCash,
        total_sales_qr: totalSalesQR,
        total_sales_card: totalSalesCard,
        total_expenses: totalExpenses,
        expenses: expenses
    };

    console.log('📤 Datos a enviar:', dataToSend);

    // 5. Buscar y deshabilitar botón de guardar
    const saveButton = document.querySelector(selectors.saveButton);

    if (!saveButton) {
        console.error('❌ ERROR: Botón de guardar no encontrado');
        alert('Error: No se encontró el botón de guardar.');
        return;
    }

    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
    saveButton.disabled = true;

    try {
        // 6. Enviar petición al servidor
        const response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        });

        console.log('📡 Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Respuesta del servidor:', data);

        if (data.success) {
            console.log('✅ Cierre guardado exitosamente');

            // Mostrar mensaje de éxito
            alert(
                `✅ ¡Cierre guardado correctamente!\n\n` +
                `📊 Gastos registrados: ${data.data?.new_expenses_count || 0}\n` +
                `💰 Monto final: $${data.data?.current_amount?.toFixed(2) || '0.00'}`
            );

            // ✅✅✅ CERRAR EL MODAL INMEDIATAMENTE ✅✅✅
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('🚪 [SUCCESS] Cerrando modal después del éxito...');

            // Cerrar modal de forma inmediata
            closeInternalModalClosure();

            // 8. Recargar la página después de un breve delay
            if (window.location.pathname.includes('petty-cash')) {
                console.log('🔄 [SUCCESS] Recargando página en 800ms...');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            }

            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        } else {
            throw new Error(data.message || 'No se pudo guardar el cierre');
        }

    } catch (error) {
        console.error('❌ Error al guardar:', error);

        if (error.message.includes('no encontrada') ||
            error.message.includes('no abierta') ||
            error.message.includes('cerrada')) {

            const shouldCreate = confirm(
                `⚠️ ${error.message}\n\n` +
                '¿Deseas abrir una nueva caja chica?'
            );

            if (shouldCreate) {
                openCreatePettyCashModal();
            } else {
                closeInternalModalClosure();
            }
        } else {
            alert('❌ Error al guardar el cierre:\n' + error.message);
        }

    } finally {
        // Restaurar botón
        if (saveButton) {
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

/**
 * Detecta en qué contexto estamos
 */
function detectContext() {
    if (document.getElementById('closure-internal-overlay')) {
        console.log('📍 Contexto detectado: MODAL INTERNO (modal-content.blade.php)');
        return 'modal-content';
    }

    if (document.getElementById('modal')) {
        console.log('📍 Contexto detectado: MODAL PRINCIPAL (index.blade.php)');
        return 'index';
    }

    console.warn('⚠️ No se detectó ningún contexto válido');
    return null;
}

/**
 * Obtiene los selectores según el contexto
 */
function getSelectors(context) {
    const selectors = {
        'index': {
            pettyCashIdInput: '#petty_cash_id',
            denominationInputs: '.contar-input',
            subtotalElements: '.subtotal',
            totalElement: '#total',
            totalEfectivoInput: '#total-efectivo',
            totalGastosInput: '#total-gastos',
            ventasQRInput: '#ventas-qr',
            ventasTarjetaInput: '#ventas-tarjeta',
            expensesContainer: '#expensesContainer',
            saveButton: '.save-btn',
            addExpenseButton: '.add-expense-btn'
        },
        'modal-content': {
            pettyCashIdInput: '#petty_cash_id_closure',
            denominationInputs: '.denomination-input2, .contar-input-closure',
            subtotalElements: '.subtotal-closure',
            totalElement: '#total-closure',
            totalEfectivoInput: '#ventas-efectivo-closure',
            totalGastosInput: '#total-gastos-closure',
            ventasQRInput: '#ventas-qr-closure',
            ventasTarjetaInput: '#ventas-tarjeta-closure',
            expensesContainer: '#expensesContainerClosure',
            saveButton: '.save-btn',
            addExpenseButton: '.add-expense-btn'
        }
    };

    return selectors[context] || selectors['index'];
}

/**
 * Calcula el total de gastos
 */
function calcularTotalGastosUnificado() {
    const context = detectContext();
    if (!context) {
        console.error('❌ No se pudo detectar el contexto');
        return 0;
    }

    const selectors = getSelectors(context);
    const totalGastosInput = document.querySelector(selectors.totalGastosInput);

    if (!totalGastosInput) {
        console.error('❌ Input de total gastos no encontrado');
        return 0;
    }

    const existingExpenses = parseFloat(totalGastosInput.getAttribute('data-gastos-bd') || totalGastosInput.value || 0);

    let newExpenses = 0;
    const expenseRows = document.querySelectorAll(`${selectors.expensesContainer} .expense-row`);

    expenseRows.forEach((row) => {
        const nameInput = row.querySelector('input[name="expense_name[]"]');
        const amountInput = row.querySelector('input[name="expense_amount[]"]');

        const name = nameInput ? nameInput.value.trim() : '';
        const amount = amountInput ? parseFloat(amountInput.value) || 0 : 0;

        if (name && amount > 0) {
            newExpenses += amount;
        }
    });

    const totalExpenses = existingExpenses + newExpenses;
    totalGastosInput.value = totalExpenses.toFixed(2);

    return totalExpenses;
}

/**
 * Inicializa el modal de cierre
 */
window.initializeClosureModal = function (pettyCashId) {
    console.log('🚀 [INIT] Inicializando modal de cierre para caja:', pettyCashId);
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

    const denominationInputs = wrapper.querySelectorAll('.denomination-input2');
    console.log(`📊 [SETUP] Encontrados ${denominationInputs.length} inputs de denominaciones`);

    denominationInputs.forEach((input, index) => {
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

    const addExpenseBtn = wrapper.querySelector('#add-expense-btn');
    if (addExpenseBtn) {
        addExpenseBtn.addEventListener('click', function () {
            console.log('➕ [CLICK] Agregar gasto');
            addExpenseRow();
        });
        console.log('✅ [SETUP] Listener agregado a botón agregar gasto');
    }

    const removeButtons = wrapper.querySelectorAll('.remove-expense-btn');
    removeButtons.forEach((btn, index) => {
        btn.addEventListener('click', function () {
            console.log(`🗑️ [CLICK] Eliminar gasto ${index + 1}`);
            removeExpenseRow(this);
        });
    });
    console.log(`✅ [SETUP] Listeners agregados a ${removeButtons.length} botones eliminar`);

    const saveBtn = wrapper.querySelector('#save-closure-btn');
    if (saveBtn) {
        const pettyCashId = saveBtn.getAttribute('data-petty-cash-id');
        saveBtn.addEventListener('click', function () {
            console.log('💾 [CLICK] Guardar cierre');
            saveClosure(pettyCashId);
        });
        console.log('✅ [SETUP] Listener agregado a botón guardar');
    }

    const cancelBtn = wrapper.querySelector('#cancel-closure-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            console.log('❌ [CLICK] Cancelar');
            closeInternalModalClosure();
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
 * Calcular total de denominaciones
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

    const totalElement = wrapper.querySelector('#totalModal');
    if (totalElement) {
        totalElement.textContent = `Bs. ${total.toFixed(2)}`;
    }

    const totalEfectivoInput = wrapper.querySelector('#total-efectivo-modal');
    if (totalEfectivoInput) {
        totalEfectivoInput.value = total.toFixed(2);
        console.log(`✅ [DENOM] Input actualizado: ${total.toFixed(2)}`);
    }

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return total;
}

/**
 * Calcular total de gastos
 */
function calculateExpensesTotal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💰 [EXPENSE] Calculando gastos...');

    const wrapper = document.getElementById('closure-modal-content-wrapper');
    if (!wrapper) {
        console.error('❌ [EXPENSE] Wrapper no encontrado');
        return 0;
    }

    const totalGastosInput = wrapper.querySelector('#total-gastos-modal');
    if (!totalGastosInput) {
        console.error('❌ [EXPENSE] Input de total gastos no encontrado');
        return 0;
    }

    const existingExpenses = parseFloat(totalGastosInput.getAttribute('data-existing-expenses') ||
        totalGastosInput.value || 0);

    console.log(`📊 [EXPENSE] Gastos existentes: ${existingExpenses.toFixed(2)}`);

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

    totalGastosInput.value = totalExpenses.toFixed(2);

    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    return totalExpenses;
}

/**
 * Agregar fila de gasto
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
 * Eliminar fila de gasto
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
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        calculateExpensesTotal();
        console.log('🧹 [REMOVE] Última fila limpiada');
    }
}

/**
 * Guardar cierre
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

    const totalSalesCash = parseFloat(wrapper.querySelector('#total-efectivo-modal')?.value) || 0;
    const totalSalesQR = parseFloat(wrapper.querySelector('#ventas-qr-modal')?.value) || 0;
    const totalSalesCard = parseFloat(wrapper.querySelector('#ventas-tarjeta-modal')?.value) || 0;
    const totalExpenses = calculateExpensesTotal();

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

            // ✅ Cerrar modal inmediatamente
            closeInternalModalClosure();

            // Recargar si estamos en la página de caja chica
            if (window.location.pathname.includes('petty-cash')) {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
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

    document.addEventListener('input', function (e) {
        if (e.target.matches('.denomination-input2, .contar-input-closure, input[data-denominacion]')) {
            console.log('💵 [INPUT] Denominación cambió:', e.target.value);
            calcularTotalModal();
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="expense_amount[]"]')) {
            console.log('💰 [INPUT] Gasto cambió:', e.target.value);
            calculateTotalExpensesModal();
        }
    });

    calcularTotalModal();
    calculateTotalExpensesModal();

    console.log('✅ [INIT] Modal inicializado correctamente');
}

function calcularTotalModal() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💵 [MODAL] Calculando total de denominaciones...');

    let total = 0;

    const inputs = document.querySelectorAll(
        '.denomination-input2, ' +
        '.contar-input-closure, ' +
        'input[data-denominacion]'
    );

    console.log(`📊 [MODAL] Inputs encontrados: ${inputs.length}`);

    if (inputs.length === 0) {
        console.warn('⚠️ [MODAL] No se encontraron inputs de denominación');
        return 0;
    }

    inputs.forEach((input, index) => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        if (cantidad > 0) {
            console.log(`  ${index + 1}. Bs.${denominacion.toFixed(2)} x ${cantidad} = Bs.${subtotal.toFixed(2)}`);
        }

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

    const totalElement = document.querySelector('#total-closure, #total-modal');
    if (totalElement) {
        totalElement.textContent = `Bs.${total.toFixed(2)}`;
        console.log('✅ [MODAL] Total en tabla actualizado');
    } else {
        console.warn('⚠️ [MODAL] Elemento de total no encontrado');
    }

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

    const totalGastosInput = document.querySelector('#total-gastos-closure, #total-gastos-modal');
    if (totalGastosInput) {
        totalGastosBD = parseFloat(totalGastosInput.getAttribute('data-gastos-bd')) || 0;
        console.log(`📊 [MODAL] Gastos en BD: Bs.${totalGastosBD.toFixed(2)}`);
    }

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
        const internalModal = document.getElementById('modal-closure-internal');
        const mainModal = document.getElementById('petty-cash-modal');

        // Verificar si el modal interno está visible
        if (internalModal && internalModal.style.display !== 'none') {
            closeInternalModalClosure();
        } else if (mainModal && !mainModal.classList.contains('hidden')) {
            closePettyCashModal();
        }
    }
});

document.addEventListener('click', function (e) {
    if (e.target.id === 'closure-internal-overlay') {
        console.log('🖱️ Click en overlay detectado');
        closeInternalModalClosure();
    }
});

document.addEventListener('input', function (e) {
    if (e.target.matches('.denomination-input2, .contar-input-closure')) {
        console.log('💵 Input detectado vía delegation:', e.target.value);
        calcularTotalModal();
    }

    if (e.target.matches('input[name="expense_amount[]"]')) {
        console.log('💰 Gasto detectado vía delegation:', e.target.value);
        calculateTotalExpensesModal();
    }
});

// =============================================
// EXPORTAR FUNCIONES AL SCOPE GLOBAL
// =============================================

window.guardarCierreUnificado = guardarCierreUnificado;
window.closeInternalModalClosure = closeInternalModalClosure;
window.openInternalModalClosure = openInternalModalClosure;
window.openCreatePettyCashModal = openCreatePettyCashModal;
window.closePettyCashModal = closePettyCashModal;
window.debugModalStructure = debugModalStructure; // ✅ Función de depuración

console.log('✅ petty-cash-modal.js inicializado correctamente');
console.log('💡 Tip: Ejecuta debugModalStructure() en la consola para ver la estructura del modal');