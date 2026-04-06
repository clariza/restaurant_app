// ========================================
// 📁 public/js/petty-cash-index.js
// ========================================
console.log('📦 Cargando petty-cash-index.js...');

// ========================================
// GESTIÓN DEL MODAL PRINCIPAL (index.blade.php)
// ========================================

window.openModal = async function (id) {
    console.log('🔓 Abriendo modal para caja chica ID:', id);
    if (!id) return;

    const modal = document.getElementById('modal');
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    const pettyCashIdInput = document.getElementById('petty_cash_id')
        || document.querySelector('input[name="petty_cash_id"]');
    if (pettyCashIdInput) pettyCashIdInput.value = id;

    document.querySelectorAll('.denomination-input').forEach(i => i.value = '');
    document.querySelectorAll('.subtotal').forEach(s => s.textContent = 'Bs.0.00');
    const totalEl = document.getElementById('total');
    if (totalEl) totalEl.textContent = 'Bs.0.00';

    const container = document.getElementById('expensesContainer');
    if (container) container.innerHTML = '';

    const notesTextarea = document.getElementById('closure-notes');
    const charCount = document.getElementById('notes-char-count');
    if (notesTextarea) notesTextarea.value = '';
    if (charCount) charCount.textContent = '0';

    const gastosInput = document.getElementById('total-gastos');
    if (gastosInput) {
        gastosInput.value = '0.00';
        gastosInput.setAttribute('data-gastos-bd', '0');
        gastosInput.setAttribute('data-gastos-form', '0');
    }

    const qrInput = document.getElementById('ventas-qr');
    const cardInput = document.getElementById('ventas-tarjeta');
    if (qrInput) qrInput.value = '0.00';
    if (cardInput) cardInput.value = '0.00';

    try {
        const csrfToken = window.pettyCashData?.csrfToken
            || document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch(`/petty-cash/closure-data?petty_cash_id=${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        console.log('📦 closure-data recibido:', data);

        if (data.success) {
            if (qrInput) qrInput.value = (data.total_sales_qr || 0).toFixed(2);
            if (cardInput) cardInput.value = (data.total_sales_card || 0).toFixed(2);

            if (gastosInput) {
                const totalAll = parseFloat(data.total_expenses) || 0;
                const totalModal = (data.expenses || []).reduce((s, e) => s + parseFloat(e.amount || 0), 0);
                const totalForm = totalAll - totalModal;
                gastosInput.value = totalAll.toFixed(2);
                gastosInput.setAttribute('data-gastos-bd', totalModal.toFixed(2));
                gastosInput.setAttribute('data-gastos-form', totalForm.toFixed(2));
            }

            if (container) {
                if (data.expenses && data.expenses.length > 0) {
                    data.expenses.forEach(expense => _appendSavedExpenseRow(container, expense));
                }
                addExpenseRow('', '', '');
            }
        } else {
            console.error('❌ Error del servidor:', data.message);
            if (container) addExpenseRow('', '', '');
        }
    } catch (error) {
        console.error('❌ Error en fetch:', error);
        if (container) addExpenseRow('', '', '');
    }
};

// ========================================
// HELPERS DE ESTRUCTURA DE FILA
// ========================================

function _isClosure(container) {
    return container.id === 'expensesContainerClosure';
}

function _actionsSelector(row) {
    return row.querySelector('.expense-actions-cell') || row.querySelector('.expense-actions');
}

function _getCsrf() {
    return window.pettyCashData?.csrfToken
        || document.querySelector('meta[name="csrf-token"]')?.content;
}

function _getPettyCashId() {
    return document.getElementById('petty_cash_id_closure')?.value
        || document.getElementById('petty_cash_id')?.value
        || document.querySelector('input[name="petty_cash_id"]')?.value
        || document.querySelector('input[name="petty_cash_id_closure"]')?.value;
}

function _isAdminUser() {
    return window.isAdmin === true
        || document.querySelector('meta[name="user-role"]')?.content === 'admin';
}

/**
 * Inserta una fila guardada (readonly) en el contenedor.
 */
function _appendSavedExpenseRow(container, expense) {
    const isAdmin = _isAdminUser();
    const isClosure = _isClosure(container);
    const row = document.createElement('div');

    row.className = 'expense-row expense-row--saved';
    row.dataset.expenseId = expense.id;
    row.style.borderLeft = '3px solid #22c55e';

    if (isClosure) {
        row.innerHTML = `
            <div class="expense-cell">
                <input type="text" class="expense-input expense-input--saved"
                       name="expense_name[]"
                       value="${(expense.expense_name || '').replace(/"/g, '&quot;')}"
                       readonly>
            </div>
            <div class="expense-cell">
                <input type="text" class="expense-input expense-input--saved"
                       name="expense_description[]"
                       value="${(expense.description || '').replace(/"/g, '&quot;')}"
                       readonly>
            </div>
            <div class="expense-cell">
                <input type="number" class="expense-input expense-input--saved"
                       name="expense_amount[]"
                       value="${parseFloat(expense.amount || 0).toFixed(2)}"
                       readonly>
            </div>
            <div class="expense-cell expense-actions-cell">
                <span class="expense-saved-badge">
                    <i class="fas fa-check-circle"></i> Guardado
                </span>
                ${isAdmin ? `
                <button type="button" class="exp-btn exp-btn--del"
                        data-expense-id="${expense.id}"
                        title="Eliminar gasto">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
            </div>
        `;
    } else {
        row.innerHTML = `
            <div class="expense-field">
                <input type="text" class="expense-input expense-input--saved"
                       name="expense_name[]"
                       value="${(expense.expense_name || '').replace(/"/g, '&quot;')}"
                       readonly>
            </div>
            <div class="expense-field">
                <input type="text" class="expense-input expense-input--saved"
                       name="expense_description[]"
                       value="${(expense.description || '').replace(/"/g, '&quot;')}"
                       readonly>
            </div>
            <div class="expense-field">
                <input type="number" class="expense-input expense-input--saved"
                       name="expense_amount[]"
                       value="${parseFloat(expense.amount || 0).toFixed(2)}"
                       readonly>
            </div>
            <div class="expense-actions">
                <span class="expense-saved-badge">
                    <i class="fas fa-check-circle"></i> Guardado
                </span>
                ${isAdmin ? `
                <button type="button" class="remove-expense-btn"
                        data-expense-id="${expense.id}"
                        title="Eliminar gasto">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
            </div>
        `;
    }

    container.appendChild(row);
}

/**
 * Transforma una fila --new en --saved tras éxito en BD.
 */
function _markRowAsSaved(row, expense, totalInputId) {
    row.classList.remove('expense-row--new');
    row.classList.add('expense-row--saved');
    row.dataset.expenseId = expense.id;
    row.style.borderLeft = '3px solid #22c55e';
    row.style.background = '#f0fdf4';
    row.style.opacity = '';
    row.style.pointerEvents = '';

    row.querySelectorAll('input').forEach(inp => {
        inp.readOnly = true;
        inp.style.background = '#f0fdf4';
        inp.style.borderColor = '#d1fae5';
    });

    const actionsContainer = _actionsSelector(row);
    if (!actionsContainer) {
        console.error('❌ _markRowAsSaved: no se encontró contenedor de acciones');
        return;
    }

    const isClosure = !!row.closest('#expensesContainerClosure');

    if (isClosure) {
        actionsContainer.innerHTML = `
            <span class="expense-saved-badge">
                <i class="fas fa-check-circle"></i> Guardado
            </span>
            ${_isAdminUser() ? `
            <button type="button" class="exp-btn exp-btn--del"
                    data-expense-id="${expense.id}"
                    title="Eliminar gasto">
                <i class="fas fa-trash"></i>
            </button>` : ''}
        `;
    } else {
        actionsContainer.innerHTML = `
            <span class="expense-saved-badge">
                <i class="fas fa-check-circle"></i> Guardado
            </span>
            ${_isAdminUser() ? `
            <button type="button"
                    class="remove-expense-btn"
                    data-expense-id="${expense.id}"
                    title="Eliminar gasto"
                    style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;
                           padding:.4rem .5rem;border-radius:.375rem;font-size:.8rem;">
                <i class="fas fa-trash"></i>
            </button>` : ''}
        `;
    }

    _refreshTotalGastos(totalInputId);
    console.log(`✅ Gasto guardado ID ${expense.id}`);
}

/**
 * Recalcula el total de gastos: data-gastos-form + suma(filas --saved)
 */
function _refreshTotalGastos(totalInputId) {
    const containerId = totalInputId === 'total-gastos-closure'
        ? 'expensesContainerClosure'
        : 'expensesContainer';

    let totalModal = 0;
    document.querySelectorAll(`#${containerId} .expense-row--saved`).forEach(row => {
        const inp = row.querySelector('input[type="number"]');
        totalModal += parseFloat(inp?.value || 0);
    });

    const el = document.getElementById(totalInputId);
    if (!el) return;

    const totalForm = parseFloat(el.getAttribute('data-gastos-form') || 0);
    const totalAll = totalForm + totalModal;

    el.value = totalAll.toFixed(2);
    el.setAttribute('data-gastos-bd', totalModal.toFixed(2));

    console.log(`💰 [${totalInputId}] form=${totalForm.toFixed(2)} + modal=${totalModal.toFixed(2)} = ${totalAll.toFixed(2)}`);
}

// ========================================
// GASTOS — index.blade.php
// ========================================

window.renderExistingExpenses = function (expenses) {
    const container = document.getElementById('expensesContainer');
    if (!container) return;
    container.innerHTML = '';
    expenses.forEach(expense => _appendSavedExpenseRow(container, expense));
    addExpenseRow('', '', '');
    _refreshTotalGastos('total-gastos');
    console.log(`✅ ${expenses.length} gastos existentes renderizados`);
};

window.resetExpensesContainer = function () {
    const container = document.getElementById('expensesContainer');
    if (!container) return;
    container.innerHTML = '';
    addExpenseRow('', '', '');
};

window.addExpenseRow = function (name = '', description = '', amount = '') {
    const container = document.getElementById('expensesContainer');
    if (!container) return;
    const isAdmin = _isAdminUser();
    const row = document.createElement('div');
    row.className = 'expense-row expense-row--new';
    row.innerHTML = `
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input"
                   placeholder="Nombre del gasto" name="expense_name[]" value="${name}">
        </div>
        <div class="expense-field">
            <input type="text" class="form-control form-control-sm expense-input"
                   placeholder="Descripción/Categoría" name="expense_description[]" value="${description}">
        </div>
        <div class="expense-field">
            <input type="number" class="form-control form-control-sm expense-input"
                   placeholder="Monto" step="0.01" min="0" name="expense_amount[]" value="${amount}">
        </div>
        <div class="expense-actions">
            <button type="button" class="btn-save-expense-index"
                    title="Guardar gasto">
                <i class="fas fa-save"></i>
            </button>
            ${isAdmin ? `
            <button type="button" class="btn btn-outline-danger btn-sm remove-expense-btn"
                    title="Eliminar fila">
                <i class="fas fa-trash"></i>
            </button>` : ''}
        </div>
    `;
    container.appendChild(row);
};

window.addExpense = function () { addExpenseRow('', '', ''); };

// ========================================
// GASTOS — modal-content.blade.php (closure)
// ========================================

window.addExpenseModalClosure = function () {
    const container = document.getElementById('expensesContainerClosure');
    if (!container) return;
    _appendNewExpenseRow(container);
};

function _appendNewExpenseRow(container) {
    const isAdmin = _isAdminUser();
    const isClosure = _isClosure(container);
    const row = document.createElement('div');
    row.className = 'expense-row expense-row--new';
    row.style.borderLeft = '3px solid #e5e7eb';

    if (isClosure) {
        row.innerHTML = `
            <div class="expense-cell">
                <input type="text" class="expense-input"
                       placeholder="Nombre del gasto" name="expense_name[]" autocomplete="off">
            </div>
            <div class="expense-cell">
                <input type="text" class="expense-input"
                       placeholder="Descripción/Categoría" name="expense_description[]" autocomplete="off">
            </div>
            <div class="expense-cell">
                <input type="number" class="expense-input"
                       placeholder="Monto" step="0.01" min="0" name="expense_amount[]" autocomplete="off">
            </div>
            <div class="expense-cell expense-actions-cell">
                <button type="button" class="exp-btn exp-btn--save"
                        title="Guardar gasto">
                    <i class="fas fa-save"></i>
                </button>
                ${isAdmin ? `
                <button type="button" class="exp-btn exp-btn--del"
                        title="Eliminar fila">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
            </div>
        `;
        row.querySelector('input').focus();
    } else {
        row.innerHTML = `
            <div class="expense-field">
                <input type="text" class="expense-input"
                       placeholder="Nombre del gasto" name="expense_name[]" autocomplete="off">
            </div>
            <div class="expense-field">
                <input type="text" class="expense-input"
                       placeholder="Descripción/Categoría" name="expense_description[]" autocomplete="off">
            </div>
            <div class="expense-field">
                <input type="number" class="expense-input"
                       placeholder="Monto" step="0.01" min="0" name="expense_amount[]" autocomplete="off">
            </div>
            <div class="expense-actions">
                <button type="button" class="btn-save-expense-index"
                        title="Guardar gasto">
                    <i class="fas fa-save"></i>
                </button>
                ${isAdmin ? `
                <button type="button"
                        title="Eliminar fila"
                        style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;
                               padding:.45rem .5rem;border-radius:.375rem;font-size:.8rem;">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
            </div>
        `;
    }

    container.appendChild(row);
}

// ========================================
// NÚCLEO: guardar / eliminar gasto
// ========================================

window.saveExpenseSave = async function (btn) {
    const row = btn.closest('.expense-row');
    if (!row) return;

    const nameInput = row.querySelector('input[name="expense_name[]"]');
    const descInput = row.querySelector('input[name="expense_description[]"]');
    const amountInput = row.querySelector('input[name="expense_amount[]"]');

    const name = nameInput?.value?.trim() || '';
    const desc = descInput?.value?.trim() || '';
    const amount = parseFloat(amountInput?.value) || 0;

    if (!name) { nameInput.style.borderColor = '#f87171'; nameInput.focus(); return; }
    if (amount <= 0) { amountInput.style.borderColor = '#f87171'; amountInput.focus(); return; }
    nameInput.style.borderColor = amountInput.style.borderColor = '';

    const pettyCashId = _getPettyCashId();
    if (!pettyCashId) { alert('Error: No se encontró el ID de caja chica.'); return; }

    const inClosure = !!row.closest('#expensesContainerClosure');
    const totalInputId = inClosure ? 'total-gastos-closure' : 'total-gastos';

    row.style.opacity = '.6';
    row.style.pointerEvents = 'none';
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const response = await fetch('/expenses/modal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': _getCsrf(),
            },
            body: JSON.stringify({
                petty_cash_id: pettyCashId,
                expense_name: name,
                description: desc,
                amount: amount,
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || `HTTP ${response.status}`);
        _markRowAsSaved(row, data.expense, totalInputId);
    } catch (err) {
        console.error('❌ Error guardando gasto:', err);
        row.style.opacity = '';
        row.style.pointerEvents = '';
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        alert('No se pudo guardar el gasto: ' + err.message);
    }
};

window.saveExpenseDelete = async function (btn, expenseId) {
    if (!expenseId) return;
    if (!confirm('¿Deseas eliminar este gasto? Esta acción no se puede deshacer.')) return;

    const row = btn.closest('.expense-row');
    const inClosure = !!row?.closest('#expensesContainerClosure');
    const totalInputId = inClosure ? 'total-gastos-closure' : 'total-gastos';

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const response = await fetch(`/expenses/${expenseId}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _getCsrf() },
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Error al eliminar');

        row.style.transition = 'all .25s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => { row.remove(); _refreshTotalGastos(totalInputId); }, 250);
    } catch (err) {
        console.error('❌ Error eliminando gasto:', err);
        btn.innerHTML = '<i class="fas fa-trash"></i>';
        btn.disabled = false;
        alert('No se pudo eliminar el gasto: ' + err.message);
    }
};

// Aliases de compatibilidad
window.saveExpenseFromIndex = window.saveExpenseSave;
window.saveExpenseClosure = window.saveExpenseSave;
window.deleteExpenseFromIndex = (btn, id) => window.saveExpenseDelete(btn, id);
window.deleteExpenseClosure = (btn, id) => window.saveExpenseDelete(btn, id);

window.removeExpenseRow = function (btn) {
    const row = btn.closest('.expense-row');
    if (!row) return;
    row.style.transition = 'all .2s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(16px)';
    setTimeout(() => row.remove(), 200);
};

window.removeExpenseClosure = window.removeExpenseRow;

window.removeExpense = function (button) {
    if (!_isAdminUser()) return;
    const row = button.closest('.expense-row');
    if (!row) return;
    row.style.transition = 'all .2s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(16px)';
    setTimeout(() => row.remove(), 200);
};

window.validateExpenseRow = function (input) {
    const row = input.closest('.expense-row');
    if (!row) return;
    const nameInput = row.querySelector('input[name="expense_name[]"]');
    const amountInput = row.querySelector('input[name="expense_amount[]"]');
    if (amountInput?.value && nameInput && !nameInput.value) {
        nameInput.style.borderColor = '#f87171';
    } else if (nameInput) {
        nameInput.style.borderColor = '';
    }
};

// ========================================
// EVENT DELEGATION — maneja todos los clicks de gastos
// ========================================

document.addEventListener('click', function (e) {

    // Guardar gasto (botón sin data-expense-id en fila --new)
    const saveBtn = e.target.closest('.btn-save-expense-index, .exp-btn--save');
    if (saveBtn && !saveBtn.closest('.expense-row--saved')) {
        e.preventDefault();
        window.saveExpenseSave(saveBtn);
        return;
    }

    // Eliminar gasto guardado en BD (tiene data-expense-id)
    const delSavedBtn = e.target.closest('[data-expense-id]');
    if (delSavedBtn && delSavedBtn.dataset.expenseId) {
        e.preventDefault();
        window.saveExpenseDelete(delSavedBtn, delSavedBtn.dataset.expenseId);
        return;
    }

    // Eliminar fila nueva (no tiene data-expense-id)
    const removeBtn = e.target.closest('.exp-btn--del, .remove-expense-btn');
    if (removeBtn && !removeBtn.dataset.expenseId) {
        e.preventDefault();
        const row = removeBtn.closest('.expense-row');
        if (!row || row.classList.contains('expense-row--saved')) return;
        row.style.transition = 'all .2s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(16px)';
        setTimeout(() => row.remove(), 200);
        return;
    }

    // Cerrar overlay de cierre interno
    if (e.target.id === 'closure-internal-overlay') {
        closeInternalModalClosure();
    }
});

// ========================================
// MODALES
// ========================================

window.closeModal = function () {
    const modal = document.getElementById('modal');
    if (modal) modal.classList.remove('active');
};

window.closeInternalModalClosure = function () {
    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');
    const modalWrapper = document.querySelector('.closure-internal-modal');
    if (overlay) { overlay.style.display = 'none'; overlay.style.opacity = '0'; overlay.style.visibility = 'hidden'; }
    if (modal) { modal.style.display = 'none'; modal.style.opacity = '0'; modal.style.visibility = 'hidden'; }
    if (modalWrapper && modalWrapper !== modal) {
        modalWrapper.style.display = 'none'; modalWrapper.style.opacity = '0'; modalWrapper.style.visibility = 'hidden';
    }
    document.body.style.overflow = '';
};

window.openInternalModalClosure = function () {
    const overlay = document.getElementById('closure-internal-overlay');
    const modal = document.getElementById('modal-closure-internal');
    if (overlay) { overlay.style.display = 'block'; overlay.style.opacity = '1'; overlay.style.visibility = 'visible'; }
    if (modal) { modal.style.display = 'flex'; modal.style.opacity = '1'; modal.style.visibility = 'visible'; }
    document.body.style.overflow = 'hidden';
};

// ========================================
// CÁLCULOS DE DENOMINACIONES
// ========================================

window.calcularTotalDenominaciones = function () {
    let total = 0;
    document.querySelectorAll('.denomination-input').forEach(input => {
        const denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        const cantidad = parseFloat(input.value) || 0;
        const subtotal = denominacion * cantidad;
        const subtotalEl = input.closest('tr')?.querySelector('.subtotal');
        if (subtotalEl) subtotalEl.textContent = `Bs.${subtotal.toFixed(2)}`;
        total += subtotal;
    });
    const totalEl = document.getElementById('total');
    if (totalEl) totalEl.textContent = `Bs.${total.toFixed(2)}`;
    const totalEfectivoInput = document.getElementById('total-efectivo');
    const totalSalesCashInput = document.getElementById('total_sales_cash');
    if (totalEfectivoInput) totalEfectivoInput.value = total.toFixed(2);
    if (totalSalesCashInput) totalSalesCashInput.value = total.toFixed(2);
    return total;
};

// ========================================
// CÁLCULOS DE GASTOS
// ========================================

window.calculateTotalExpenses = function () {
    let totalSaved = 0;
    document.querySelectorAll('#expensesContainer .expense-row--saved').forEach(row => {
        const inp = row.querySelector('input[type="number"]');
        totalSaved += parseFloat(inp?.value || 0);
    });

    const gastosEl = document.getElementById('total-gastos');
    if (!gastosEl) return totalSaved;

    const totalForm = parseFloat(gastosEl.getAttribute('data-gastos-form') || 0);
    const totalAll = totalForm + totalSaved;

    gastosEl.value = totalAll.toFixed(2);
    gastosEl.setAttribute('data-gastos-bd', totalSaved.toFixed(2));
    return totalAll;
};

// ========================================
// GUARDAR CIERRE
// ========================================

window.saveClosure = async function () {
    console.log('💾 Iniciando guardado de cierre...');

    try {
        if (!window.pettyCashData?.saveClosureUrl) throw new Error('URL de guardado no disponible.');
        if (!window.pettyCashData?.csrfToken) throw new Error('Token CSRF no disponible.');

        const unsavedRows = document.querySelectorAll('#expensesContainer .expense-row--new');
        let hasUnsaved = false;
        unsavedRows.forEach(row => {
            const name = row.querySelector('input[name="expense_name[]"]')?.value?.trim() || '';
            const amount = parseFloat(row.querySelector('input[name="expense_amount[]"]')?.value) || 0;
            if (name && amount > 0) hasUnsaved = true;
        });

        if (hasUnsaved) {
            const confirmClose = await Swal.fire({
                title: 'Gastos sin guardar',
                text: 'Hay filas de gastos sin guardar. ¿Deseas continuar? Los gastos sin guardar NO se incluirán en el cierre.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continuar de todas formas',
                cancelButtonText: 'Volver y guardar',
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#203363',
            });
            if (!confirmClose.isConfirmed) return;
        }

        const pettyCashId = document.getElementById('petty_cash_id')?.value;
        if (!pettyCashId) { alert('Error: No se encontró el ID de la caja chica'); return; }

        const totalSalesCash = parseFloat(document.getElementById('total-efectivo')?.value) || 0;
        const totalSalesQR = parseFloat(document.getElementById('ventas-qr')?.value) || 0;
        const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta')?.value) || 0;
        const totalExpenses = calculateTotalExpenses();
        const closureNotes = document.getElementById('closure-notes')?.value?.trim() || '';

        const dataToSend = {
            petty_cash_id: pettyCashId,
            total_sales_cash: totalSalesCash,
            total_sales_qr: totalSalesQR,
            total_sales_card: totalSalesCard,
            total_expenses: totalExpenses,
            closure_notes: closureNotes,
            expenses: [],
        };

        const saveBtn = document.querySelector('#btn-save-closure');
        if (!saveBtn) return;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        saveBtn.disabled = true;

        const response = await fetch(window.pettyCashData.saveClosureUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pettyCashData.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(dataToSend),
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();

        if (data.success) {
            closeModal();
            const notesEl = document.getElementById('closure-notes');
            if (notesEl) {
                notesEl.value = '';
                const cc = document.getElementById('notes-char-count');
                if (cc) cc.textContent = '0';
            }

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Cierre de Caja Exitoso!',
                    html: `<div style="text-align:center;">
                        <p style="font-size:16px;margin:15px 0;">El cierre se ha guardado correctamente</p>
                        <hr style="margin:20px 0;">
                        <div style="background:#f8f9fa;padding:15px;border-radius:8px;">
                            <p style="margin:8px 0;"><strong>💰 Monto final:</strong> Bs.${data.data?.current_amount?.toFixed(2) || '0.00'}</p>
                        </div>
                        <p style="margin-top:20px;color:#6c757d;font-size:14px;">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Redirigiendo...
                        </p>
                    </div>`,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didClose: () => { window.location.href = '/petty-cash/create'; },
                });
                setTimeout(() => { window.location.href = '/petty-cash/create'; }, 2600);
            }, 400);
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
// UTILIDADES
// ========================================

window.closeOpenPettyCash = async function () {
    if (!confirm('¿Estás seguro de cerrar todas las cajas chicas abiertas?')) return;
    try {
        const response = await fetch('/petty-cash/close-all-open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pettyCashData?.csrfToken
                    || document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({}),
        });
        const data = await response.json();
        if (data.success) window.location.reload();
        else throw new Error(data.message || 'No se pudieron cerrar las cajas');
    } catch (error) {
        alert('Error al cerrar las cajas: ' + error.message);
    }
};

// ========================================
// EVENT LISTENERS GLOBALES
// ========================================

document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const internalOverlay = document.getElementById('closure-internal-overlay');
    if (internalOverlay && internalOverlay.style.display !== 'none') {
        closeInternalModalClosure();
        return;
    }
    const modal = document.getElementById('modal');
    if (modal && modal.classList.contains('active')) closeModal();
});

// ========================================
// INICIALIZACIÓN
// ========================================

function initializePettyCash() {
    console.log('🚀 Inicializando petty-cash-index.js...');

    const modalElement = document.getElementById('modal');
    if (!modalElement) {
        console.log('ℹ️ Modal de index no encontrado — funciones globales activas igualmente');
        return;
    }

    if (!window.pettyCashData) {
        console.error('❌ window.pettyCashData no está disponible');
        return;
    }

    document.querySelectorAll('.denomination-input').forEach(input => {
        input.addEventListener('input', calcularTotalDenominaciones);
    });

    document.addEventListener('input', function (e) {
        if (e.target.matches('input[name="expense_name[]"]')) validateExpenseRow(e.target);
    });

    calculateTotalExpenses();
    console.log('✅ petty-cash-index.js inicializado');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePettyCash);
} else {
    initializePettyCash();
}

console.log('✅ petty-cash-index.js cargado');