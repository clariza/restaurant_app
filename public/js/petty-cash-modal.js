console.log('📦 petty-cash-modal.js cargado');

// =============================================
// FUNCIONES DEL MODAL DE CIERRE INTERNO
// =============================================

/**
 * Abrir el modal interno de cierre - VERSIÓN CORREGIDA
 */
/**
 * Abrir el modal interno de cierre - VERSIÓN CORREGIDA
 */
window.openInternalModalClosure = async function (id) {
    console.log('🔓 Abriendo modal de cierre interno para caja:', id);

    // ✅ VALIDAR QUE EL ID SEA VÁLIDO ANTES DE CONTINUAR
    if (!id) {
        console.error('❌ ID de caja chica no proporcionado');
        alert('Error: No se pudo identificar la caja chica');
        return;
    }

    const modal = document.getElementById('modal-closure-internal');
    const overlay = document.getElementById('closure-internal-overlay');

    if (!modal || !overlay) {
        console.error('❌ No se encontraron los elementos del modal de cierre');
        return;
    }

    // ✅ BUSCAR EL INPUT O CREARLO SI NO EXISTE
    let pettyCashIdInput = document.getElementById('petty_cash_id_closure');
    if (!pettyCashIdInput) {
        console.warn('⚠️ Input petty_cash_id_closure no encontrado, creándolo...');
        pettyCashIdInput = document.createElement('input');
        pettyCashIdInput.type = 'hidden';
        pettyCashIdInput.id = 'petty_cash_id_closure';
        pettyCashIdInput.name = 'petty_cash_id_closure';
        // Agregar al inicio del modal
        const contentDiv = modal.querySelector('.closure-internal-content') || modal;
        contentDiv.insertBefore(pettyCashIdInput, contentDiv.firstChild);
        console.log('✅ Input petty_cash_id_closure creado dinámicamente');
    }

    // Asegurar que el valor sea un número válido
    pettyCashIdInput.value = String(id).trim();

    // Verificación adicional
    console.log('✅ ID de caja chica establecido:', {
        id: pettyCashIdInput.id,
        valor: pettyCashIdInput.value,
        tipo: typeof pettyCashIdInput.value,
        existe: !!pettyCashIdInput.value
    });

    // Mostrar el modal
    overlay.classList.add('active');
    modal.classList.add('active');

    console.log('✅ Modal de cierre activado, cargando datos...');

    // 🔥 CARGAR DATOS DINÁMICAMENTE DESDE EL SERVIDOR
    try {
        console.log('📡 Solicitando datos a /petty-cash/closure-data...');

        const response = await fetch('/petty-cash/closure-data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            console.log('📊 Datos recibidos del servidor:', data);

            // ✅ VERIFICAR Y ESTABLECER EL ID NUEVAMENTE
            if (data.petty_cash_id) {
                pettyCashIdInput.value = data.petty_cash_id;
                console.log('✅ ID confirmado desde servidor:', data.petty_cash_id);
            }

            // Cargar datos en los campos
            loadClosureData(data);

            console.log('✅ Datos del modal de cierre cargados exitosamente');
        } else {
            throw new Error(data.message || 'Error al cargar datos');
        }
    } catch (error) {
        console.error('❌ Error al cargar datos del modal:', error);
        alert('Error al cargar los datos del cierre. Por favor, intenta nuevamente.');

        // Inicializar con valores por defecto pero mantener el ID
        initializeDefaultValues();
    }
};
/**
 * Inicializar valores por defecto
 */
function initializeDefaultValues() {
    const totalGastosInput = document.getElementById('total-gastos-closure');
    if (totalGastosInput) {
        totalGastosInput.value = '0.00';
        totalGastosInput.setAttribute('data-gastos-bd', '0.00');
    }

    const ventasEfectivoInput = document.getElementById('ventas-efectivo-closure');
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = '0.00';
    }

    const ventasQRInput = document.getElementById('ventas-qr-closure');
    if (ventasQRInput) {
        ventasQRInput.value = '0.00';
    }

    const ventasTarjetaInput = document.getElementById('ventas-tarjeta-closure');
    if (ventasTarjetaInput) {
        ventasTarjetaInput.value = '0.00';
    }

    resetClosureModal();
}


/**
 * Función auxiliar para cargar datos en el modal
 */
function loadClosureData(data) {
    // Total Gastos de BD
    const totalGastosInput = document.getElementById('total-gastos-closure');
    if (totalGastosInput) {
        const totalGastosBD = parseFloat(data.total_expenses || 0);
        totalGastosInput.value = totalGastosBD.toFixed(2);
        totalGastosInput.setAttribute('data-gastos-bd', totalGastosBD.toFixed(2));
        console.log('✅ Total gastos de BD:', totalGastosBD.toFixed(2));
    }

    // Ventas en Efectivo
    const ventasEfectivoInput = document.getElementById('ventas-efectivo-closure');
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = '0.00';
    }

    // Ventas QR
    const ventasQRInput = document.getElementById('ventas-qr-closure');
    if (ventasQRInput) {
        ventasQRInput.value = parseFloat(data.total_sales_qr || 0).toFixed(2);
        console.log('✅ Ventas QR cargadas:', ventasQRInput.value);
    }

    // Ventas Tarjeta
    const ventasTarjetaInput = document.getElementById('ventas-tarjeta-closure');
    if (ventasTarjetaInput) {
        ventasTarjetaInput.value = parseFloat(data.total_sales_card || 0).toFixed(2);
        console.log('✅ Ventas tarjeta cargadas:', ventasTarjetaInput.value);
    }

    // Limpiar denominaciones
    document.querySelectorAll('.contar-input-closure').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('.subtotal-closure').forEach(span => {
        span.textContent = '$0.00';
    });
    const totalElement = document.getElementById('total-closure');
    if (totalElement) {
        totalElement.textContent = '$0.00';
    }

    // Limpiar gastos
    const container = document.getElementById('expensesContainerClosure');
    if (container) {
        container.innerHTML = '';
        addExpenseRowClosure(null);
        console.log('✅ Contenedor de nuevos gastos inicializado');
    }
}
/**
 * Agregar una fila de gasto (SOLO para nuevos gastos)
 */
/**
 * Agregar una fila de gasto (SOLO para nuevos gastos)
 */
function addExpenseRowClosure(expense = null) {
    const container = document.getElementById('expensesContainerClosure');
    if (!container) {
        console.error('❌ Contenedor de gastos no encontrado');
        return;
    }

    const row = document.createElement('div');
    row.className = 'expense-row';

    const expenseName = expense ? expense.expense_name : '';
    const expenseDescription = expense ? (expense.description || '') : '';
    const expenseAmount = expense ? expense.amount : '';

    row.innerHTML = `
        <div class="expense-field">
            <input type="text" class="expense-input" placeholder="Nombre del gasto" 
                name="expense_name[]" value="${expenseName}">
        </div>
        <div class="expense-field">
            <input type="text" class="expense-input" placeholder="Descripción/Categoría" 
                name="expense_description[]" value="${expenseDescription}">
        </div>
        <div class="expense-field">
            <input type="number" class="expense-input expense-amount-input" placeholder="Monto" 
                step="0.01" min="0" name="expense_amount[]" value="${expenseAmount}">
        </div>
        <div class="expense-actions">
            <button type="button" class="btn btn-danger" onclick="removeExpenseClosure(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(row);

    // ✅ AGREGAR EVENT LISTENERS al input de monto recién creado
    const amountInput = row.querySelector('input[name="expense_amount[]"]');
    if (amountInput) {
        amountInput.addEventListener('input', handleExpenseInputChange);
        amountInput.addEventListener('change', handleExpenseInputChange);
        amountInput.addEventListener('keyup', handleExpenseInputChange);
        amountInput.addEventListener('blur', handleExpenseInputChange);
        console.log('✅ Event listeners agregados al input de gasto');
    }
}


/**
 * Manejador unificado para cambios en inputs de gastos
 */
function handleExpenseInputChange(e) {
    console.log('📝 Input de gasto modificado:', e.target.value);
    calculateTotalExpensesClosure();
}

/**
 * Cerrar el modal interno de cierre
 */
window.closeInternalModalClosure = function () {
    console.log('🔒 Cerrando modal de cierre interno');

    const modal = document.getElementById('modal-closure-internal');
    const overlay = document.getElementById('closure-internal-overlay');

    if (modal && overlay) {
        modal.classList.remove('active');
        overlay.classList.remove('active');
        console.log('✅ Modal de cierre cerrado');
    }
};

/**
 * Resetear inputs del modal de cierre interno
 */
window.resetClosureModal = function () {
    console.log('🔄 Reseteando modal de cierre...');

    // Resetear inputs de denominaciones
    document.querySelectorAll('.contar-input-closure').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('.subtotal-closure').forEach(span => {
        span.textContent = '$0.00';
    });

    // Resetear totales
    const totalElement = document.getElementById('total-closure');
    const ventasEfectivo = document.getElementById('ventas-efectivo-closure');
    const ventasQR = document.getElementById('ventas-qr-closure');
    const ventasTarjeta = document.getElementById('ventas-tarjeta-closure');
    const totalGastos = document.getElementById('total-gastos-closure');

    if (totalElement) totalElement.textContent = '$0.00';

    if (ventasEfectivo) {
        ventasEfectivo.value = '0.00';
        console.log('✅ Ventas en Efectivo reseteado a 0.00');
    }

    if (ventasQR) ventasQR.value = '0.00';
    if (ventasTarjeta) ventasTarjeta.value = '0.00';

    if (totalGastos) {
        totalGastos.value = '0.00';
        totalGastos.removeAttribute('data-gastos-bd');
    }

    // Limpiar gastos y dejar solo uno vacío
    const expensesContainer = document.getElementById('expensesContainerClosure');
    if (expensesContainer) {
        expensesContainer.innerHTML = '';
        addExpenseRowClosure(null);
    }

    console.log('✅ Modal reseteado completamente');
};
/**
 * Agregar gasto en el modal de cierre interno
 */
window.addExpenseModalClosure = function () {
    console.log('➕ Agregando nuevo gasto...');
    addExpenseRowClosure(null);
};
/**
 * Eliminar gasto del modal de cierre interno
 */
window.removeExpenseClosure = function (button) {
    const expenseRow = button.closest('.expense-row');
    const container = document.getElementById('expensesContainerClosure');

    if (!container) {
        console.error('❌ Contenedor de gastos no encontrado');
        return;
    }

    if (container.children.length > 1) {
        console.log('🗑️ Eliminando gasto...');
        expenseRow.remove();
    } else {
        console.log('ℹ️ Limpiando último gasto (debe quedar al menos uno vacío)');
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
    }

    calculateTotalExpensesClosure();
};

/**
 * Calcular total de efectivo en el modal de cierre interno
 */
window.calcularTotalClosure = function () {
    console.log('💰 Calculando total de denominaciones...');

    let total = 0;

    document.querySelectorAll('.contar-input-closure').forEach(input => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion'));
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        const subtotalElement = input.closest('tr').querySelector('.subtotal-closure');
        if (subtotalElement) {
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
        }

        total += subtotal;

        console.log(`  Denominación ${denominacion}: ${cantidad} x ${denominacion} = ${subtotal.toFixed(2)}`);
    });

    const totalElement = document.getElementById('total-closure');
    if (totalElement) {
        totalElement.textContent = `$${total.toFixed(2)}`;
    }

    const ventasEfectivoInput = document.getElementById('ventas-efectivo-closure');
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = total.toFixed(2);
        console.log(`✅ Ventas en Efectivo actualizado: ${total.toFixed(2)}`);
    } else {
        console.error('❌ Input ventas-efectivo-closure no encontrado');
    }

    console.log(`💵 TOTAL EFECTIVO FINAL: $${total.toFixed(2)}`);

    return total;
};

/**
 * Calcular total de gastos en el modal de cierre interno
 * SUMA: Gastos de BD + Nuevos gastos ingresados manualmente
 */
window.calculateTotalExpensesClosure = function () {
    const totalGastosInput = document.getElementById('total-gastos-closure');

    if (!totalGastosInput) {
        console.error('❌ Input total-gastos-closure no encontrado');
        return 0;
    }

    // 🔥 Obtener el total de gastos de la BD (guardado como data-attribute)
    const gastosBD = parseFloat(totalGastosInput.getAttribute('data-gastos-bd') || '0');

    console.log(`💰 Gastos de BD (data-attribute): ${gastosBD.toFixed(2)}`);

    // 🔥 Calcular SOLO los nuevos gastos del contenedor
    let gastosNuevos = 0;
    const inputs = document.querySelectorAll('#expensesContainerClosure input[name="expense_amount[]"]');

    console.log(`📝 Contando ${inputs.length} inputs de gastos nuevos...`);

    inputs.forEach((input, index) => {
        const nameInput = input.closest('.expense-row')?.querySelector('input[name="expense_name[]"]');
        const name = nameInput ? nameInput.value.trim() : '';
        const value = parseFloat(input.value) || 0;

        // Solo contar si tiene nombre Y monto
        if (name && value > 0) {
            gastosNuevos += value;
            console.log(`  ✓ Gasto ${index + 1}: "${name}" = ${value.toFixed(2)}`);
        } else {
            console.log(`  ✗ Gasto ${index + 1}: Vacío o sin nombre`);
        }
    });

    // 🔥 TOTAL = BD + Nuevos
    const totalFinal = gastosBD + gastosNuevos;

    console.log(`━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`);
    console.log(`📊 Gastos de BD:     ${gastosBD.toFixed(2)}`);
    console.log(`📊 Gastos nuevos:    ${gastosNuevos.toFixed(2)}`);
    console.log(`✅ TOTAL GASTOS:     ${totalFinal.toFixed(2)}`);
    console.log(`━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`);

    // Actualizar el campo visible
    totalGastosInput.value = totalFinal.toFixed(2);

    return totalFinal;
};

/**
 * Guardar cierre desde el modal interno - VERSIÓN CORREGIDA
 */
window.saveClosureClosure = async function (petty_cash_id = null) {
    console.log('💾 Iniciando guardado de cierre...');

    // ✅ OBTENER Y VALIDAR EL ID - CON CREACIÓN AUTOMÁTICA SI NO EXISTE
    let pettyCashIdInput = document.getElementById('petty_cash_id_closure');

    if (!pettyCashIdInput) {
        console.error('❌ Input petty_cash_id_closure no encontrado en el DOM');
        alert('Error: Por favor, cierra y vuelve a abrir el modal de cierre.');
        return;
    }

    const pettyCashId = petty_cash_id;

    console.log('🔍 Valor del input:', {
        existe: !!pettyCashIdInput,
        valor: pettyCashId,
        tipo: typeof pettyCashId,
        longitud: pettyCashId ? pettyCashId.length : 0
    });

    if (!pettyCashId || pettyCashId === '' || pettyCashId === '0' || pettyCashId === 'null' || pettyCashId === 'undefined') {
        console.error('❌ ID de caja chica inválido:', pettyCashId);
        alert('Error: No se ha seleccionado una caja chica válida. Por favor, cierra y vuelve a abrir el modal.');
        return;
    }

    console.log('✅ ID de caja chica validado:', pettyCashId);

    // Obtener valores
    const totalSalesCash = parseFloat(document.getElementById('ventas-efectivo-closure')?.value) || 0;
    const totalSalesQR = parseFloat(document.getElementById('ventas-qr-closure')?.value) || 0;
    const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta-closure')?.value) || 0;
    const totalExpenses = calculateTotalExpensesClosure();

    console.log('📊 Valores a enviar:', {
        pettyCashId,
        totalSalesCash,
        totalSalesQR,
        totalSalesCard,
        totalExpenses
    });

    // Validar que haya al menos un valor
    if (totalSalesCash === 0 && totalSalesQR === 0 && totalSalesCard === 0 && totalExpenses === 0) {
        if (!confirm('¿Estás seguro de cerrar la caja sin registrar movimientos?')) {
            return;
        }
    }

    // Recopilar gastos
    const expenses = [];
    document.querySelectorAll('#expensesContainerClosure .expense-row').forEach((row) => {
        const name = row.querySelector('input[name="expense_name[]"]')?.value.trim();
        const description = row.querySelector('input[name="expense_description[]"]')?.value.trim();
        const amount = row.querySelector('input[name="expense_amount[]"]')?.value;

        if (name && amount && parseFloat(amount) > 0) {
            expenses.push({
                name: name,
                description: description || '',
                amount: parseFloat(amount)
            });
        }
    });

    console.log('📝 Gastos a enviar:', expenses);

    // Preparar datos
    const dataToSend = {
        petty_cash_id: parseInt(pettyCashId), // ✅ Asegurar que sea número
        total_sales_cash: totalSalesCash,
        total_sales_qr: totalSalesQR,
        total_sales_card: totalSalesCard,
        total_expenses: totalExpenses,
        expenses: expenses
    };

    console.log('📤 Datos completos a enviar:', dataToSend);

    try {
        // Deshabilitar botón de guardar
        const saveBtn = document.querySelector('.btn-primary');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        }

        const response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        });

        console.log('📡 Respuesta del servidor:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error del servidor:', errorText);
            throw new Error(`Error del servidor: ${response.status}`);
        }

        const data = await response.json();
        console.log('✅ Respuesta procesada:', data);

        if (data.success) {
            alert('Cierre guardado correctamente');
            closeInternalModalClosure();

            // Recargar contenido
            if (typeof openPettyCashModal === 'function') {
                await openPettyCashModal();
            } else {
                window.location.reload();
            }
        } else {
            throw new Error(data.message || 'No se pudo guardar el cierre');
        }
    } catch (error) {
        console.error('❌ Error al guardar cierre:', error);
        alert('Error al guardar el cierre: ' + error.message);

        // Rehabilitar botón
        const saveBtn = document.querySelector('.btn-primary');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cierre';
        }
    }
};

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
        console.error('❌ Ruta pettyCashModalContent no definida');
        const baseUrl = window.location.origin;
        window.routes = window.routes || {};
        window.routes.pettyCashModalContent = `${baseUrl}/petty-cash/modal-content`;
        console.log('✅ Ruta construida manualmente:', window.routes.pettyCashModalContent);
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        console.log('📡 Cargando desde:', window.routes.pettyCashModalContent);

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

        console.log('✅ Contenido de caja chica cargado correctamente');
    } catch (error) {
        console.error('❌ Error al cargar contenido de caja chica:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600 mb-4">Error al cargar la información de caja chica</p>
                <p class="text-sm text-gray-500 mb-4">${error.message}</p>
                <button onclick="openPettyCashModal()" class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#47517c]">
                    <i class="fas fa-redo mr-2"></i> Reintentar
                </button>
            </div>
        `;
    }
}

function closePettyCashModal() {
    console.log('🔒 Cerrando modal de caja chica...');
    const modal = document.getElementById('petty-cash-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function initializePettyCashModal() {
    console.log('⚙️ Inicializando funcionalidades del modal...');

    const denominationInputs = document.querySelectorAll('.contar-input');
    denominationInputs.forEach(input => {
        input.addEventListener('input', calcularTotalModal);
    });

    const expenseInputs = document.querySelectorAll('input[name="expense_amount[]"]');
    expenseInputs.forEach(input => {
        input.addEventListener('input', calculateTotalExpensesModal);
    });

    console.log('✅ Funcionalidades inicializadas');
}

function calcularTotalModal() {
    let total = 0;
    document.querySelectorAll('#petty-cash-modal .contar-input').forEach(input => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion'));
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;

        const subtotalElement = input.closest('tr').querySelector('.subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
        }

        total += subtotal;
    });

    const totalElement = document.getElementById('total-modal');
    if (totalElement) {
        totalElement.textContent = `$${total.toFixed(2)}`;
    }

    const ventasEfectivoInput = document.getElementById('ventas-efectivo-modal');
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = total.toFixed(2);
    }
}

function calculateTotalExpensesModal() {
    let total = 0;
    document.querySelectorAll('#petty-cash-modal input[name="expense_amount[]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const totalGastosInput = document.getElementById('total-gastos-modal');
    if (totalGastosInput) {
        totalGastosInput.value = total.toFixed(2);
    }

    return total;
}

function openModalInModal(id) {
    const closureModal = document.getElementById('closure-modal');
    if (closureModal) {
        closureModal.classList.remove('hidden');
        document.getElementById('petty_cash_id_modal').value = id;
        resetClosureModalInputs();
    }
}

function closeClosureModal() {
    const closureModal = document.getElementById('closure-modal');
    if (closureModal) {
        closureModal.classList.add('hidden');
    }
}

function resetClosureModalInputs() {
    document.querySelectorAll('#closure-modal .contar-input').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('#closure-modal .subtotal').forEach(span => {
        span.textContent = '$0.00';
    });

    const totalElement = document.getElementById('total-modal');
    if (totalElement) {
        totalElement.textContent = '$0.00';
    }

    const ventasEfectivoInput = document.getElementById('ventas-efectivo-modal');
    if (ventasEfectivoInput) {
        ventasEfectivoInput.value = '0';
    }

    const totalGastosInput = document.getElementById('total-gastos-modal');
    if (totalGastosInput) {
        totalGastosInput.value = '0';
    }

    const expensesContainer = document.getElementById('expensesContainer-modal');
    if (expensesContainer) {
        while (expensesContainer.children.length > 1) {
            expensesContainer.removeChild(expensesContainer.lastChild);
        }
        const firstExpense = expensesContainer.firstChild;
        if (firstExpense) {
            firstExpense.querySelector('input[name="expense_name[]"]').value = '';
            firstExpense.querySelector('input[name="expense_description[]"]').value = '';
            firstExpense.querySelector('input[name="expense_amount[]"]').value = '';
        }
    }
}

function addExpenseModal() {
    const expensesContainer = document.getElementById('expensesContainer-modal');
    if (!expensesContainer) return;

    const newExpenseRow = document.createElement('div');
    newExpenseRow.className = 'expense-row';
    newExpenseRow.innerHTML = `
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input" placeholder="Nombre del gasto" name="expense_name[]">
        </div>
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input" placeholder="Descripción/Categoría" name="expense_description[]">
        </div>
        <div class="expense-field">
            <input type="number" class="form-control form-control-sm expense-input" placeholder="Monto" step="0.01" min="0" name="expense_amount[]" oninput="calculateTotalExpensesModal()">
        </div>
        <div class="expense-actions">
            <button type="button" class="btn btn-outline-danger btn-sm remove-expense-btn" onclick="removeExpenseModal(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    expensesContainer.appendChild(newExpenseRow);
}

function removeExpenseModal(button) {
    const expenseRow = button.closest('.expense-row');
    const expensesContainer = document.getElementById('expensesContainer-modal');

    if (expensesContainer && expensesContainer.children.length > 1) {
        expenseRow.remove();
        calculateTotalExpensesModal();
    } else if (expenseRow) {
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        calculateTotalExpensesModal();
    }
}

async function saveClosureModal() {
    const pettyCashId = document.getElementById('petty_cash_id_modal').value;
    const totalSalesCash = parseFloat(document.getElementById('ventas-efectivo-modal').value) || 0;
    const totalSalesQR = parseFloat(document.getElementById('ventas-qr-modal').value) || 0;
    const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta-modal').value) || 0;
    const totalExpenses = calculateTotalExpensesModal();

    if (!pettyCashId) {
        alert('Error: No se ha seleccionado una caja chica');
        return;
    }

    if (totalSalesCash === 0 && totalSalesQR === 0 && totalSalesCard === 0 && totalExpenses === 0) {
        if (!confirm('¿Estás seguro de cerrar la caja sin registrar movimientos?')) {
            return;
        }
    }

    const expenses = [];
    document.querySelectorAll('#closure-modal .expense-row').forEach((row) => {
        const name = row.querySelector('input[name="expense_name[]"]').value;
        const description = row.querySelector('input[name="expense_description[]"]').value;
        const amount = row.querySelector('input[name="expense_amount[]"]').value;

        if (name && amount) {
            expenses.push({
                name: name,
                description: description,
                amount: parseFloat(amount)
            });
        }
    });

    try {
        const response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                petty_cash_id: pettyCashId,
                total_sales_cash: totalSalesCash,
                total_sales_qr: totalSalesQR,
                total_sales_card: totalSalesCard,
                total_expenses: totalExpenses,
                expenses: expenses
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Cierre guardado correctamente');
            closeClosureModal();
            openPettyCashModal();
        } else {
            alert('Error: ' + (data.message || 'No se pudo guardar el cierre'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al enviar el formulario');
    }
}

function filterPettyCash() {
    const form = document.getElementById('filtersFormModal');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch(`${window.routes.pettyCashModalContent}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('petty-cash-content').innerHTML = html;
                initializePettyCashModal();
            })
            .catch(error => {
                console.error('Error al filtrar:', error);
                alert('Error al aplicar filtros');
            });
    }
}

async function closeOpenPettyCashModal() {
    if (!confirm('¿Estás seguro de cerrar todas las cajas chicas abiertas?')) {
        return;
    }

    try {
        const response = await fetch('/petty-cash/close-all-open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Cajas cerradas correctamente');
            openPettyCashModal();
        } else {
            alert('Error: ' + (data.message || 'No se pudieron cerrar las cajas'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cerrar las cajas');
    }
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

        const closureModal = document.getElementById('modal-closure-internal');
        if (closureModal && closureModal.classList.contains('active')) {
            closeInternalModalClosure();
        }
    }
});


document.addEventListener('DOMContentLoaded', function () {
    console.log('🔍 Verificando elementos del modal de cierre...');

    const pettyCashIdInput = document.getElementById('petty_cash_id_closure');
    if (pettyCashIdInput) {
        console.log('✅ Input petty_cash_id_closure encontrado');
    } else {
        console.warn('⚠️ Input petty_cash_id_closure NO encontrado al cargar');
    }

    const modal = document.getElementById('modal-closure-internal');
    if (modal) {
        console.log('✅ Modal de cierre encontrado');
    } else {
        console.warn('⚠️ Modal de cierre NO encontrado al cargar');
    }
});

// =============================================
// EXPORTAR FUNCIONES AL SCOPE GLOBAL
// =============================================

window.addExpenseRowClosure = addExpenseRowClosure;
window.handleExpenseInputChange = handleExpenseInputChange;
window.openPettyCashModal = openPettyCashModal;
window.closePettyCashModal = closePettyCashModal;
window.openModalInModal = openModalInModal;
window.closeClosureModal = closeClosureModal;
window.addExpenseModal = addExpenseModal;
window.removeExpenseModal = removeExpenseModal;
window.saveClosureModal = saveClosureModal;
window.filterPettyCash = filterPettyCash;
window.closeOpenPettyCashModal = closeOpenPettyCashModal;
window.calcularTotalModal = calcularTotalModal;
window.calculateTotalExpensesModal = calculateTotalExpensesModal;

console.log('✅ petty-cash-modal.js inicializado correctamente - Todas las funciones exportadas');