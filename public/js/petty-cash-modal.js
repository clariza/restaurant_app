console.log('📦 petty-cash-modal.js cargado');

// =============================================
// FUNCIONES DEL MODAL DE CIERRE INTERNO
// =============================================

/**
 * Abrir el modal interno de cierre
 */
window.openInternalModalClosure = function (id) {
    console.log('🔓 Abriendo modal de cierre interno para caja:', id);

    const modal = document.getElementById('modal-closure-internal');
    const overlay = document.getElementById('closure-internal-overlay');

    if (modal && overlay) {
        overlay.classList.add('active');
        modal.classList.add('active');
        document.getElementById('petty_cash_id_closure').value = id;
        resetClosureModal();

        console.log('✅ Modal de cierre activado');
    } else {
        console.error('❌ No se encontraron los elementos del modal de cierre');
    }
};

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
    // Resetear inputs de denominaciones
    document.querySelectorAll('.contar-input-closure').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('.subtotal-closure').forEach(span => {
        span.textContent = '$0.00';
    });

    const totalElement = document.getElementById('total-closure');
    const ventasEfectivo = document.getElementById('ventas-efectivo-closure');
    const totalGastos = document.getElementById('total-gastos-closure');

    if (totalElement) totalElement.textContent = '$0.00';
    if (ventasEfectivo) ventasEfectivo.value = '0';
    if (totalGastos) totalGastos.value = '0';

    // Limpiar gastos excepto el primero
    const expensesContainer = document.getElementById('expensesContainerClosure');
    if (expensesContainer) {
        while (expensesContainer.children.length > 1) {
            expensesContainer.removeChild(expensesContainer.lastChild);
        }

        // Resetear el primer gasto
        const firstExpense = expensesContainer.firstElementChild;
        if (firstExpense) {
            const inputs = firstExpense.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    }
};

/**
 * Agregar gasto en el modal de cierre interno
 */
window.addExpenseModalClosure = function () {
    const expensesContainer = document.getElementById('expensesContainerClosure');
    if (!expensesContainer) return;

    const newExpenseRow = document.createElement('div');
    newExpenseRow.className = 'expense-row';
    newExpenseRow.innerHTML = `
        <div class="expense-field">
            <input type="text" class="expense-input" placeholder="Nombre del gasto" name="expense_name[]">
        </div>
        <div class="expense-field">
            <input type="text" class="expense-input" placeholder="Descripción/Categoría" name="expense_description[]">
        </div>
        <div class="expense-field">
            <input type="number" class="expense-input" placeholder="Monto" step="0.01" min="0" name="expense_amount[]" oninput="calculateTotalExpensesClosure()">
        </div>
        <div class="expense-actions">
            <button type="button" class="btn btn-danger" onclick="removeExpenseClosure(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    expensesContainer.appendChild(newExpenseRow);
};

/**
 * Eliminar gasto del modal de cierre interno
 */
window.removeExpenseClosure = function (button) {
    const expenseRow = button.closest('.expense-row');
    const container = document.getElementById('expensesContainerClosure');
    if (container && container.children.length > 1) {
        expenseRow.remove();
        calculateTotalExpensesClosure();
    } else if (container) {
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        calculateTotalExpensesClosure();
    }
};

/**
 * Calcular total de efectivo en el modal de cierre interno
 */
window.calcularTotalClosure = function () {
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
    });

    const totalElement = document.getElementById('total-closure');
    const ventasEfectivo = document.getElementById('ventas-efectivo-closure');

    if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
    if (ventasEfectivo) ventasEfectivo.value = total.toFixed(2);
};

/**
 * Calcular total de gastos en el modal de cierre interno
 */
window.calculateTotalExpensesClosure = function () {
    let total = 0;
    document.querySelectorAll('#expensesContainerClosure input[name="expense_amount[]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const totalGastos = document.getElementById('total-gastos-closure');
    if (totalGastos) totalGastos.value = total.toFixed(2);

    return total;
};

/**
 * Guardar cierre desde el modal interno
 */
window.saveClosureClosure = async function () {
    const pettyCashId = document.getElementById('petty_cash_id_closure').value;
    const totalSalesCash = parseFloat(document.getElementById('ventas-efectivo-closure').value) || 0;
    const totalSalesQR = parseFloat(document.getElementById('ventas-qr-closure').value) || 0;
    const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta-closure').value) || 0;
    const totalExpenses = calculateTotalExpensesClosure();

    if (!pettyCashId) {
        alert('Error: No se ha seleccionado una caja chica');
        return;
    }

    // Validar que al menos haya un valor ingresado
    if (totalSalesCash === 0 && totalSalesQR === 0 && totalSalesCard === 0 && totalExpenses === 0) {
        if (!confirm('¿Estás seguro de cerrar la caja sin registrar movimientos?')) {
            return;
        }
    }

    // Recopilar gastos
    const expenses = [];
    document.querySelectorAll('#expensesContainerClosure .expense-row').forEach((row) => {
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
            closeInternalModalClosure();
            // Recargar el contenido del modal principal
            if (typeof openPettyCashModal === 'function') {
                openPettyCashModal();
            } else {
                window.location.reload();
            }
        } else {
            alert('Error: ' + (data.message || 'No se pudo guardar el cierre'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al enviar el formulario');
    }
};

// =============================================
// FUNCIONES EXISTENTES DEL MODAL PRINCIPAL
// =============================================

/**
 * Abrir el modal de caja chica
 */
async function openPettyCashModal() {
    console.log('🔓 Abriendo modal de caja chica...');

    const modal = document.getElementById('petty-cash-modal');
    const content = document.getElementById('petty-cash-content');

    if (!modal) {
        console.error('❌ Modal de caja chica no encontrado');
        return;
    }

    // ✅ VERIFICAR que la ruta esté definida
    if (!window.routes || !window.routes.pettyCashModalContent) {
        console.error('❌ Ruta pettyCashModalContent no definida');
        console.log('Rutas disponibles:', window.routes);

        // Intentar construir la URL manualmente como fallback
        const baseUrl = window.location.origin;
        window.routes = window.routes || {};
        window.routes.pettyCashModalContent = `${baseUrl}/petty-cash/modal-content`;
        console.log('✅ Ruta construida manualmente:', window.routes.pettyCashModalContent);
    }

    // Mostrar el modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Cargar el contenido
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

        // Inicializar funcionalidades después de cargar el contenido
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

/**
 * Cerrar el modal de caja chica
 */
function closePettyCashModal() {
    console.log('🔒 Cerrando modal de caja chica...');

    const modal = document.getElementById('petty-cash-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

/**
 * Inicializar funcionalidades del modal
 */
function initializePettyCashModal() {
    console.log('⚙️ Inicializando funcionalidades del modal...');

    // Inicializar event listeners para los inputs de denominaciones
    const denominationInputs = document.querySelectorAll('.contar-input');
    denominationInputs.forEach(input => {
        input.addEventListener('input', calcularTotalModal);
    });

    // Inicializar event listeners para gastos
    const expenseInputs = document.querySelectorAll('input[name="expense_amount[]"]');
    expenseInputs.forEach(input => {
        input.addEventListener('input', calculateTotalExpensesModal);
    });

    console.log('✅ Funcionalidades inicializadas');
}

/**
 * Calcular total de efectivo en el modal
 */
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

/**
 * Calcular total de gastos en el modal
 */
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

/**
 * Abrir modal de cierre dentro del modal principal
 */
function openModalInModal(id) {
    const closureModal = document.getElementById('closure-modal');
    if (closureModal) {
        closureModal.classList.remove('hidden');
        document.getElementById('petty_cash_id_modal').value = id;

        // Resetear inputs
        resetClosureModalInputs();
    }
}

/**
 * Cerrar modal de cierre
 */
function closeClosureModal() {
    const closureModal = document.getElementById('closure-modal');
    if (closureModal) {
        closureModal.classList.add('hidden');
    }
}

/**
 * Resetear inputs del modal de cierre
 */
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

    // Limpiar gastos
    const expensesContainer = document.getElementById('expensesContainer-modal');
    if (expensesContainer) {
        while (expensesContainer.children.length > 1) {
            expensesContainer.removeChild(expensesContainer.lastChild);
        }
        // Resetear primer gasto
        const firstExpense = expensesContainer.firstChild;
        if (firstExpense) {
            firstExpense.querySelector('input[name="expense_name[]"]').value = '';
            firstExpense.querySelector('input[name="expense_description[]"]').value = '';
            firstExpense.querySelector('input[name="expense_amount[]"]').value = '';
        }
    }
}

/**
 * Agregar gasto en el modal
 */
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

/**
 * Eliminar gasto del modal
 */
function removeExpenseModal(button) {
    const expenseRow = button.closest('.expense-row');
    const expensesContainer = document.getElementById('expensesContainer-modal');

    if (expensesContainer && expensesContainer.children.length > 1) {
        expenseRow.remove();
        calculateTotalExpensesModal();
    } else if (expenseRow) {
        // Si es el último, solo limpiar
        const inputs = expenseRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        calculateTotalExpensesModal();
    }
}

/**
 * Guardar cierre desde el modal
 */
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

    // Validar que al menos haya un valor ingresado
    if (totalSalesCash === 0 && totalSalesQR === 0 && totalSalesCard === 0 && totalExpenses === 0) {
        if (!confirm('¿Estás seguro de cerrar la caja sin registrar movimientos?')) {
            return;
        }
    }

    // Recopilar gastos
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
            // Recargar el contenido del modal
            openPettyCashModal();
        } else {
            alert('Error: ' + (data.message || 'No se pudo guardar el cierre'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al enviar el formulario');
    }
}

/**
 * Filtrar cajas chicas
 */
function filterPettyCash() {
    const form = document.getElementById('filtersFormModal');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Recargar el contenido con los filtros
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

/**
 * Cerrar todas las cajas abiertas
 */
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
            // Recargar contenido
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

// Cerrar modal con ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('petty-cash-modal');
        if (modal && !modal.classList.contains('hidden')) {
            closePettyCashModal();
        }

        // También cerrar modal de cierre interno si está abierto
        const closureModal = document.getElementById('modal-closure-internal');
        if (closureModal && closureModal.classList.contains('active')) {
            closeInternalModalClosure();
        }
    }
});

// Inicializar event listeners del modal de cierre interno cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    // Overlay para cerrar modal interno
    const overlay = document.getElementById('closure-internal-overlay');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                closeInternalModalClosure();
            }
        });
    }

    // Event listeners para cálculos automáticos del modal de cierre interno
    document.querySelectorAll('.contar-input-closure').forEach(input => {
        input.addEventListener('input', calcularTotalClosure);
    });

    // Escuchar cambios en los inputs de gastos del modal de cierre interno
    document.addEventListener('input', function (e) {
        if (e.target && e.target.matches('#expensesContainerClosure input[name="expense_amount[]"]')) {
            calculateTotalExpensesClosure();
        }
    });
});

// =============================================
// EXPORTAR FUNCIONES AL SCOPE GLOBAL
// =============================================

// Funciones del modal principal
window.openPettyCashModal = openPettyCashModal;
window.closePettyCashModal = closePettyCashModal;
window.openModalInModal = openModalInModal;
window.closeClosureModal = closeClosureModal;
window.addExpenseModal = addExpenseModal;
window.removeExpenseModal = removeExpenseModal;
window.saveClosureModal = saveClosureModal;
window.filterPettyCash = filterPettyCash;
window.closeOpenPettyCashModal = closeOpenPettyCashModal;

// Funciones de cálculos del modal principal
window.calcularTotalModal = calcularTotalModal;
window.calculateTotalExpensesModal = calculateTotalExpensesModal;

// Funciones del modal de cierre interno (ya están en window desde su definición)

console.log('✅ petty-cash-modal.js inicializado correctamente - Todas las funciones exportadas');