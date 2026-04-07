console.log('📦 petty-cash-modal.js cargado');

// =============================================
// CIERRE DE MODALES — función centralizada
// =============================================

/**
 * Cierra TODOS los modales relacionados con caja chica.
 * Es la única función que debe llamarse para cerrar — sin duplicados.
 */
function _closeAllModals() {
    var outerModal = document.getElementById('petty-cash-modal');
    if (outerModal) {
        outerModal.classList.add('hidden');
        console.log('✅ [CLOSE] #petty-cash-modal ocultado');
    }

    var overlay = document.getElementById('closure-internal-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.style.display = 'none';
        console.log('✅ [CLOSE] #closure-internal-overlay ocultado');
    }

    document.body.style.overflow = '';
    document.body.classList.remove('overflow-hidden');
    console.log('✅ [CLOSE] Scroll del body restaurado');
}

function closeInternalModalClosure() {
    _closeAllModals();
}

function openInternalModalClosure() {
    var overlay = document.getElementById('closure-internal-overlay');
    var modal = document.getElementById('petty-cash-modal');
    if (overlay) overlay.style.display = 'block';
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openCreatePettyCashModal() {
    _closeAllModals();
    setTimeout(function () {
        window.location.href = '/petty-cash/create';
    }, 300);
}

// =============================================
// DETECCIÓN DE CONTEXTO Y SELECTORES
// =============================================

/**
 * Detecta si estamos en el modal flotante (modal-content) o en la página índice (index).
 */
function detectContext() {
    if (document.getElementById('closure-internal-overlay')) return 'modal-content';
    if (document.getElementById('modal')) return 'index';
    return null;
}

function getSelectors(context) {
    var map = {
        'index': {
            pettyCashIdInput: '#petty_cash_id',
            totalEfectivoInput: '#total-efectivo',
            totalGastosInput: '#total-gastos',
            closureNotesInput: '#closure-notes',
            ventasQRInput: '#ventas-qr',
            ventasTarjetaInput: '#ventas-tarjeta',
            expensesContainer: '#expensesContainer',
            saveButton: '.save-btn',
        },
        'modal-content': {
            pettyCashIdInput: '#petty_cash_id_closure',
            totalEfectivoInput: '#ventas-efectivo-closure',
            totalGastosInput: '#total-gastos-closure',
            closureNotesInput: '#closure-notes-modal',
            ventasQRInput: '#ventas-qr-closure',
            ventasTarjetaInput: '#ventas-tarjeta-closure',
            expensesContainer: '#expensesContainerClosure',
            saveButton: '.save-btn',
        }
    };
    return map[context] || map['modal-content'];
}

// =============================================
// CÁLCULO DE DENOMINACIONES
// =============================================

/**
 * Recorre todos los inputs .contar-input-closure, actualiza subtotales
 * por fila, el span de total general y el input #ventas-efectivo-closure.
 */
function calcularTotalClosure() {
    var totalEfectivo = 0;

    document.querySelectorAll('.contar-input-closure').forEach(function (input) {
        var denominacion = parseFloat(input.getAttribute('data-denominacion')) || 0;
        var cantidad = parseFloat(input.value) || 0;
        var subtotal = denominacion * cantidad;

        var fila = input.closest('tr');
        if (fila) {
            var spanSub = fila.querySelector('.subtotal-closure');
            if (spanSub) spanSub.textContent = 'Bs.' + subtotal.toFixed(2);
        }

        totalEfectivo += subtotal;
    });

    var spanTotal = document.getElementById('total-closure');
    if (spanTotal) spanTotal.textContent = 'Bs.' + totalEfectivo.toFixed(2);

    var inputEfectivo = document.getElementById('ventas-efectivo-closure');
    if (inputEfectivo) {
        var anterior = parseFloat(inputEfectivo.value) || 0;
        inputEfectivo.value = totalEfectivo.toFixed(2);

        if (Math.abs(anterior - totalEfectivo) > 0.001) {
            inputEfectivo.classList.remove('efectivo-pulse');
            void inputEfectivo.offsetWidth;
            inputEfectivo.classList.add('efectivo-pulse');
            inputEfectivo.addEventListener('animationend', function () {
                inputEfectivo.classList.remove('efectivo-pulse');
            }, { once: true });
        }
    }

    return totalEfectivo;
}

// =============================================
// CÁLCULO DE GASTOS
// =============================================

/**
 * Recalcula el input de total-gastos sumando:
 *   - gastos del formulario principal (data-gastos-form, fijo desde el servidor)
 *   - filas --saved visibles en el DOM
 */
function _recalcularTotalGastos(totalInputId) {
    var containerId = totalInputId === 'total-gastos-closure'
        ? 'expensesContainerClosure'
        : 'expensesContainer';

    var el = document.getElementById(totalInputId);
    if (!el) return 0;

    var totalForm = parseFloat(el.getAttribute('data-gastos-form') || 0);

    var totalModalSaved = 0;
    document.querySelectorAll('#' + containerId + ' .expense-row--saved').forEach(function (row) {
        var inp = row.querySelector('input[type="number"]');
        totalModalSaved += parseFloat(inp ? inp.value : 0) || 0;
    });

    el.setAttribute('data-gastos-bd', totalModalSaved.toFixed(2));
    var totalAll = totalForm + totalModalSaved;
    el.value = totalAll.toFixed(2);

    console.log('💰 [' + totalInputId + '] form=' + totalForm.toFixed(2) +
        ' + modal_saved=' + totalModalSaved.toFixed(2) +
        ' = ' + totalAll.toFixed(2));

    return totalAll;
}
window._recalcularTotalGastos = _recalcularTotalGastos;

/**
 * Calcula el total de gastos a enviar al guardar:
 *   form (fijo) + filas --saved + filas --new con datos completos.
 */
function calcularTotalGastosUnificado() {
    var context = detectContext();
    if (!context) return 0;

    var selectors = getSelectors(context);
    var totalGastosInput = document.querySelector(selectors.totalGastosInput);
    if (!totalGastosInput) return 0;

    var totalForm = parseFloat(totalGastosInput.getAttribute('data-gastos-form') || 0);

    var totalModalSaved = 0;
    document.querySelectorAll(selectors.expensesContainer + ' .expense-row--saved').forEach(function (row) {
        var inp = row.querySelector('input[type="number"]');
        totalModalSaved += parseFloat(inp ? inp.value : 0) || 0;
    });

    var totalNew = 0;
    document.querySelectorAll(selectors.expensesContainer + ' .expense-row--new').forEach(function (row) {
        var name = ((row.querySelector('input[name="expense_name[]"]') || {}).value || '').trim();
        var amount = parseFloat((row.querySelector('input[name="expense_amount[]"]') || {}).value) || 0;
        if (name && amount > 0) totalNew += amount;
    });

    var total = totalForm + totalModalSaved + totalNew;
    totalGastosInput.value = total.toFixed(2);

    console.log('💾 calcularTotalGastosUnificado: form=' + totalForm.toFixed(2) +
        ' + saved=' + totalModalSaved.toFixed(2) +
        ' + new=' + totalNew.toFixed(2) +
        ' = ' + total.toFixed(2));

    return total;
}

// =============================================
// GUARDAR CIERRE — FUNCIÓN PRINCIPAL
// =============================================

/**
 * Guarda el cierre de caja.
 *
 * FLUJO:
 *   1. Detectar contexto y recoger valores del DOM
 *   2. POST a /petty-cash/save-closure
 *   3. success → cerrar modal PRIMERO → SweetAlert → redirigir
 *   4. error   → restaurar botón → mostrar alerta (modal permanece abierto)
 */
async function guardarCierreUnificado(pettyCashId) {
    var context = detectContext();
    if (!context) {
        alert('Error: No se pudo determinar el contexto del modal');
        return;
    }

    var selectors = getSelectors(context);
    var pettyCashIdEl = document.querySelector(selectors.pettyCashIdInput);
    var finalPettyCashId = pettyCashId || (pettyCashIdEl && pettyCashIdEl.value);

    if (!finalPettyCashId) {
        if (confirm('⚠️ No hay una caja chica abierta.\n\n¿Deseas abrir una nueva caja chica ahora?')) {
            openCreatePettyCashModal();
        } else {
            _closeAllModals();
        }
        return;
    }

    // Recoger valores del formulario
    var totalSalesCash = parseFloat((document.querySelector(selectors.totalEfectivoInput) || {}).value) || 0;
    var totalSalesQR = parseFloat((document.querySelector(selectors.ventasQRInput) || {}).value) || 0;
    var totalSalesCard = parseFloat((document.querySelector(selectors.ventasTarjetaInput) || {}).value) || 0;
    var totalExpenses = calcularTotalGastosUnificado();
    var closureNotes = selectors.closureNotesInput
        ? ((document.querySelector(selectors.closureNotesInput) || {}).value || '').trim()
        : '';

    // Recoger gastos nuevos (filas --new con nombre y monto)
    var expenses = [];
    document.querySelectorAll(selectors.expensesContainer + ' .expense-row--new').forEach(function (row) {
        var name = ((row.querySelector('input[name="expense_name[]"]') || {}).value || '').trim();
        var description = ((row.querySelector('input[name="expense_description[]"]') || {}).value || '').trim();
        var amount = parseFloat((row.querySelector('input[name="expense_amount[]"]') || {}).value) || 0;
        if (name && amount > 0) {
            expenses.push({ name: name, description: description, amount: amount });
        }
    });

    var dataToSend = {
        petty_cash_id: finalPettyCashId,
        total_sales_cash: totalSalesCash,
        total_sales_qr: totalSalesQR,
        total_sales_card: totalSalesCard,
        total_expenses: totalExpenses,
        closure_notes: closureNotes,
        expenses: expenses
    };

    console.log('📤 [guardarCierreUnificado] Enviando:', dataToSend);

    // Deshabilitar botón mientras se guarda
    var saveButton = document.querySelector(selectors.saveButton);
    var originalText = saveButton ? saveButton.innerHTML : '';
    if (saveButton) {
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
        saveButton.disabled = true;
    }

    try {
        var response = await fetch('/petty-cash/save-closure', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        });

        var data = await response.json();
        console.log('📥 [guardarCierreUnificado] Respuesta:', data);

        if (data.success) {
            // ── 1. Cerrar el modal ANTES del SweetAlert ──────────────────────
            _closeAllModals();
            console.log('✅ [guardarCierreUnificado] Modal cerrado. Mostrando SweetAlert...');
            // ── 2. SweetAlert (el modal ya no es visible) ────────────────────
            Swal.fire({
                icon: 'success',
                title: '¡Cierre de Caja Exitoso!',
                html: [
                    '<div style="text-align:center;">',
                    '  <p style="font-size:16px;margin:15px 0;">El cierre se ha guardado correctamente</p>',
                    '  <hr style="margin:20px 0;">',
                    '  <div style="background:#f8f9fa;padding:15px;border-radius:8px;">',
                    '    <p style="margin:8px 0;"><strong>📊 Gastos registrados:</strong> ' +
                    ((data.data && data.data.new_expenses_count) || 0) + '</p>',
                    '    <p style="margin:8px 0;"><strong>💰 Monto final:</strong> Bs.' +
                    ((data.data && data.data.current_amount)
                        ? parseFloat(data.data.current_amount).toFixed(2)
                        : '0.00') + '</p>',
                    '  </div>',
                    '  <p style="margin-top:20px;color:#6c757d;font-size:14px;">',
                    '    <i class="fas fa-spinner fa-spin mr-2"></i> Redirigiendo...',
                    '  </p>',
                    '</div>'
                ].join(''),
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didClose: function () {
                    window.location.href = '/petty-cash/create';
                }
            });
            // ── 3. Redirigir como fallback por si didClose no dispara ────────
            setTimeout(function () {
                window.location.href = '/petty-cash/create';
            }, 2600);

        } else {
            throw new Error(data.message || 'No se pudo guardar el cierre');
        }

    } catch (error) {
        console.error('❌ [guardarCierreUnificado] Error:', error);

        // Restaurar botón antes de mostrar el error (modal permanece abierto)
        if (saveButton) {
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }

        if (error.message && (error.message.includes('no encontrada') || error.message.includes('cerrada'))) {
            if (confirm('⚠️ ' + error.message + '\n\n¿Deseas abrir una nueva caja chica?')) {
                openCreatePettyCashModal();
            } else {
                _closeAllModals();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al guardar',
                text: error.message || 'Error inesperado. Intenta de nuevo.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ef4444',
            });
        }
    }
}

// =============================================
// CARGA DINÁMICA DEL MODAL DE CIERRE
// =============================================

async function loadClosureModal(pettyCashId, content) {
    console.log('📥 [loadClosureModal] Cargando modal para caja:', pettyCashId);

    if (!pettyCashId) {
        showCreatePettyCashOption(content);
        return;
    }

    content.innerHTML = [
        '<div class="flex items-center justify-center p-12">',
        '  <div class="text-center">',
        '    <i class="fas fa-spinner fa-spin text-4xl text-green-500 mb-4"></i>',
        '    <p class="text-gray-600">Cargando datos de cierre...</p>',
        '  </div>',
        '</div>'
    ].join('');

    try {
        var response = await fetch('/petty-cash/modal-closure/' + pettyCashId, {
            method: 'GET',
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('HTTP error! status: ' + response.status);

        var html = await response.text();
        content.innerHTML = html;

        // Calcular totales en el siguiente frame (el DOM ya está pintado)
        requestAnimationFrame(function () {
            calcularTotalClosure();
            console.log('✅ [loadClosureModal] calcularTotalClosure() ejecutado tras inyección');
        });

    } catch (error) {
        console.error('❌ [loadClosureModal] Error:', error);
        showErrorContent(content);
    }
}

// =============================================
// ABRIR MODAL PRINCIPAL DE CAJA CHICA
// =============================================

async function openPettyCashModal() {
    var modal = document.getElementById('petty-cash-modal');
    var content = document.getElementById('petty-cash-content');

    if (!modal || !content) {
        console.error('❌ Modal de caja chica no encontrado');
        window.location.href = '/petty-cash/index';
        return;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    content.innerHTML = [
        '<div class="flex items-center justify-center p-12">',
        '  <div class="text-center">',
        '    <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>',
        '    <p class="text-gray-600">Verificando estado de caja chica...</p>',
        '  </div>',
        '</div>'
    ].join('');

    try {
        var res = await fetch('/petty-cash/get-open', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        var data = await res.json();

        if (data.success && data.petty_cash_id) {
            await loadClosureModal(data.petty_cash_id, content);
        } else {
            showCreatePettyCashOption(content);
        }
    } catch (error) {
        console.error('❌ [openPettyCashModal] Error:', error);
        showErrorContent(content);
    }
}

// =============================================
// FUNCIONES DE GASTOS — MODAL DE CIERRE
// =============================================

/**
 * Agrega una nueva fila vacía al contenedor de gastos del modal de cierre.
 */
function addExpenseModalClosure() {
    var container = document.getElementById('expensesContainerClosure');
    if (!container) return;

    var newRow = document.createElement('div');
    newRow.className = 'expense-row expense-row--new';
    newRow.innerHTML = [
        '<div class="expense-cell">',
        '  <input type="text" class="expense-input" placeholder="Nombre del gasto"',
        '         name="expense_name[]" autocomplete="off">',
        '</div>',
        '<div class="expense-cell">',
        '  <input type="text" class="expense-input" placeholder="Descripción/Categoría"',
        '         name="expense_description[]" autocomplete="off">',
        '</div>',
        '<div class="expense-cell">',
        '  <input type="number" class="expense-input" placeholder="Monto"',
        '         step="0.01" min="0" name="expense_amount[]" autocomplete="off">',
        '</div>',
        '<div class="expense-cell expense-actions-cell">',
        '  <button type="button" class="exp-btn exp-btn--save js-save-expense" title="Guardar gasto">',
        '    <i class="fas fa-save"></i>',
        '  </button>',
        '  <button type="button" class="exp-btn exp-btn--del"',
        '          onclick="removeExpenseRow(this)" title="Eliminar fila">',
        '    <i class="fas fa-trash"></i>',
        '  </button>',
        '</div>'
    ].join('');

    container.appendChild(newRow);

    var firstInput = newRow.querySelector('input');
    if (firstInput) firstInput.focus();
}
window.addExpenseModalClosure = addExpenseModalClosure;

/**
 * Elimina la fila de gasto que contiene el botón pulsado.
 */
function removeExpenseRow(button) {
    var row = button.closest('.expense-row');
    if (row) row.remove();
}
window.removeExpenseRow = removeExpenseRow;

// =============================================
// CONTENIDOS DE ESTADO
// =============================================

function showCreatePettyCashOption(content) {
    content.innerHTML = [
        '<div class="p-8 text-center">',
        '  <div class="mb-6">',
        '    <i class="fas fa-info-circle text-6xl text-blue-500 mb-4"></i>',
        '    <h3 class="text-2xl font-semibold text-gray-800 mb-2">No hay caja chica abierta</h3>',
        '    <p class="text-gray-600 mb-6">Para realizar un cierre, primero debes tener una caja chica abierta.</p>',
        '  </div>',
        '  <div class="flex justify-center gap-4">',
        '    <button onclick="createNewPettyCash()"',
        '            class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center gap-2">',
        '      <i class="fas fa-plus-circle"></i><span>Crear Caja Chica</span>',
        '    </button>',
        '    <button onclick="closePettyCashModal()"',
        '            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 flex items-center gap-2">',
        '      <i class="fas fa-times"></i><span>Cancelar</span>',
        '    </button>',
        '  </div>',
        '</div>'
    ].join('');
}

function showErrorContent(content) {
    content.innerHTML = [
        '<div class="p-8 text-center">',
        '  <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>',
        '  <h3 class="text-2xl font-semibold text-gray-800 mb-2">Error de conexión</h3>',
        '  <p class="text-gray-600 mb-6">No se pudo cargar el contenido. Por favor, intenta de nuevo.</p>',
        '  <div class="flex justify-center gap-4">',
        '    <button onclick="openPettyCashModal()"',
        '            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center gap-2">',
        '      <i class="fas fa-redo"></i><span>Reintentar</span>',
        '    </button>',
        '  </div>',
        '</div>'
    ].join('');
}

// =============================================
// INICIALIZACIÓN
// =============================================

window.initializeClosureModal = function (pettyCashId) {
    console.log('🚀 [INIT] Inicializando modal de cierre para caja:', pettyCashId);
    setTimeout(function () {
        calcularTotalClosure();
        console.log('✅ [INIT] Modal de cierre inicializado');
    }, 100);
};

// =============================================
// DEPURACIÓN
// =============================================

function debugModalStructure() {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('🔍 [DEBUG] Analizando estructura del modal...');
    var ids = [
        'closure-internal-overlay',
        'petty-cash-modal',
        'petty-cash-content',
        'petty_cash_id_closure',
        'ventas-efectivo-closure',
        'total-gastos-closure',
        'total-closure',
    ];
    ids.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            var cs = window.getComputedStyle(el);
            console.log('✅ #' + id + ' → display:' + cs.display + ' / visibility:' + cs.visibility);
        } else {
            console.log('❌ #' + id + ' → NO ENCONTRADO');
        }
    });
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
}

// =============================================
// EVENT LISTENERS GLOBALES (delegación en document)
// Los inputs se inyectan dinámicamente, por eso van en document.
// =============================================

// Denominaciones → actualizar subtotales y #ventas-efectivo-closure
document.addEventListener('input', function (e) {
    if (e.target && e.target.matches('.contar-input-closure')) {
        calcularTotalClosure();
    }
});
document.addEventListener('change', function (e) {
    if (e.target && e.target.matches('.contar-input-closure')) {
        calcularTotalClosure();
    }
});

// Contador de caracteres en textarea de notas
document.addEventListener('input', function (e) {
    if (e.target && e.target.id === 'closure-notes-modal') {
        var counter = document.getElementById('notes-char-count-modal');
        if (counter) {
            counter.textContent = e.target.value.length;
            counter.style.color = e.target.value.length > 450 ? '#ef4444' : '#9ca3af';
        }
    }
});

// Cerrar al hacer clic en el overlay interno
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'closure-internal-overlay') {
        _closeAllModals();
    }
});

// Cerrar con Escape
document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var mainModal = document.getElementById('petty-cash-modal');
    if (mainModal && !mainModal.classList.contains('hidden')) {
        _closeAllModals();
    }
});

// =============================================
// EXPORTAR AL SCOPE GLOBAL
// =============================================

window.calcularTotalClosure = calcularTotalClosure;
window.calcularTotalGastosUnificado = calcularTotalGastosUnificado;
window.guardarCierreUnificado = guardarCierreUnificado;
window.closeInternalModalClosure = closeInternalModalClosure;
window.openInternalModalClosure = openInternalModalClosure;
window.openCreatePettyCashModal = openCreatePettyCashModal;
window.openPettyCashModal = openPettyCashModal;
window.loadClosureModal = loadClosureModal;
window.debugModalStructure = debugModalStructure;
window._closeAllModals = _closeAllModals;

console.log('✅ petty-cash-modal.js inicializado correctamente');
console.log('💡 Tip: Ejecuta debugModalStructure() en la consola para ver el estado del DOM');