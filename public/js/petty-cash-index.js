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
window.openModal = async function (id) {
    console.log('🔓 Abriendo modal para caja chica ID:', id);
    if (!id) return;

    const modal = document.getElementById('modal');
    if (!modal) return;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Setear ID
    const pettyCashIdInput = document.getElementById('petty_cash_id')
        || document.querySelector('input[name="petty_cash_id"]');
    if (pettyCashIdInput) pettyCashIdInput.value = id;

    // Reset denominaciones
    document.querySelectorAll('.denomination-input').forEach(i => i.value = '');
    document.querySelectorAll('.subtotal').forEach(s => s.textContent = 'Bs.0.00');
    const totalEl = document.getElementById('total');
    if (totalEl) totalEl.textContent = 'Bs.0.00';

    // ✅ Limpiar container MANUALMENTE — sin llamar a resetExpensesContainer()
    const container = document.getElementById('expensesContainer');
    if (container) container.innerHTML = '';

    // Limpiar textarea de notas
    const notesTextarea = document.getElementById('closure-notes');
    const charCount = document.getElementById('notes-char-count');
    if (notesTextarea && charCount) {
        notesTextarea.addEventListener('input', function () {
            charCount.textContent = this.value.length;
            charCount.style.color = this.value.length > 450 ? '#ef4444' : '#9ca3af';
        });
    }
    const gastosInput = document.getElementById('total-gastos');
    if (gastosInput) {
        gastosInput.value = '0.00';
        gastosInput.setAttribute('data-gastos-bd', '0');
    }
    const qrInput = document.getElementById('ventas-qr');
    const cardInput = document.getElementById('ventas-tarjeta');
    if (qrInput) qrInput.value = '0.00';
    if (cardInput) cardInput.value = '0.00';

    // ✅ Fetch datos reales de la caja
    try {
        const csrfToken = window.pettyCashData?.csrfToken
            || document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch(`/petty-cash/closure-data?petty_cash_id=${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();
        console.log('📦 closure-data recibido:', data);

        if (data.success) {
            // Ventas
            if (qrInput) qrInput.value = (data.total_sales_qr || 0).toFixed(2);
            if (cardInput) cardInput.value = (data.total_sales_card || 0).toFixed(2);

            // Total gastos
            if (gastosInput) {
                const existingExpenses = parseFloat(data.total_expenses) || 0;
                gastosInput.value = existingExpenses.toFixed(2);
                gastosInput.setAttribute('data-gastos-bd', existingExpenses);
                console.log('✅ total-gastos:', existingExpenses);
            }

            // ✅ Renderizar gastos existentes
            if (container) {
                if (data.expenses && data.expenses.length > 0) {
                    data.expenses.forEach(expense => {
                        const row = document.createElement('div');
                        row.className = 'expense-row';
                        row.innerHTML = `
                            <div class="expense-field">
                                <input type="text" class="form-control form-control-sm expense-input"
                                       value="${(expense.expense_name || '').replace(/"/g, '&quot;')}"
                                       readonly style="background:#f3f4f6;color:#6b7280;">
                            </div>
                            <div class="expense-field">
                                <input type="text" class="form-control form-control-sm expense-input"
                                       value="${(expense.description || '').replace(/"/g, '&quot;')}"
                                       readonly style="background:#f3f4f6;color:#6b7280;">
                            </div>
                            <div class="expense-field">
                                <input type="number" class="form-control form-control-sm expense-input"
                                       value="${parseFloat(expense.amount || 0).toFixed(2)}"
                                       readonly style="background:#f3f4f6;color:#6b7280;">
                            </div>
                            <div class="expense-actions">
                                <span style="font-size:11px;color:#9ca3af;padding:8px 4px;">✓ guardado</span>
                            </div>
                        `;
                        container.appendChild(row);
                    });
                    console.log(`✅ ${data.expenses.length} gastos renderizados`);
                }

                // Fila vacía para nuevos gastos
                addExpenseRow('', '', '');
            }

            console.log('✅ Modal cargado correctamente');
        } else {
            console.error('❌ Error del servidor:', data.message);
            addExpenseRow('', '', '');
        }

    } catch (error) {
        console.error('❌ Error en fetch:', error);
        if (container) addExpenseRow('', '', '');
    }
};
window.renderExistingExpenses = function (expenses) {
    const container = document.getElementById('expensesContainer');
    if (!container) return;

    container.innerHTML = ''; // limpiar

    expenses.forEach(expense => {
        const row = document.createElement('div');
        row.className = 'expense-row';
        row.innerHTML = `
            <div class="expense-field">
                <input type="text" class="form-control form-control-sm expense-input"
                       value="${expense.expense_name}" readonly
                       style="background:#f8f9fa; color:#6b7280;">
            </div>
            <div class="expense-field">
                <input type="text" class="form-control form-control-sm expense-input"
                       value="${expense.description ?? ''}" readonly
                       style="background:#f8f9fa; color:#6b7280;">
            </div>
            <div class="expense-field">
                <input type="number" class="form-control form-control-sm expense-input"
                       value="${expense.amount}" readonly
                       style="background:#f8f9fa; color:#6b7280;">
            </div>
            <div class="expense-actions">
                <span style="font-size:11px; color:#9ca3af; padding:4px;">guardado</span>
            </div>
        `;
        container.appendChild(row);
    });

    // Agregar una fila vacía al final para nuevos gastos
    addExpenseRow('', '', '');

    console.log(`✅ ${expenses.length} gastos existentes renderizados`);
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

    const canDeleteExpenses = window.isAdmin === true;

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
        ${canDeleteExpenses ? `
        <div class="expense-actions">
            <button type="button" class="btn btn-outline-danger btn-sm remove-expense-btn" 
                    onclick="removeExpense(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>` : ''}
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
    if (window.isAdmin !== true) {
        console.warn('⛔ Usuario sin permisos para eliminar gastos en este modal');
        return;
    }

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

window.saveClosure = async function () {
    console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    console.log('💾 Iniciando proceso de guardado de cierre...');

    try {
        // Validar configuración
        if (typeof window.pettyCashData === 'undefined') {
            console.error('❌ window.pettyCashData es undefined');
            alert('Error: La configuración de la aplicación no se cargó correctamente. Por favor, recarga la página.');
            return;
        }

        if (!window.pettyCashData.saveClosureUrl) {
            console.error('❌ saveClosureUrl no está definido');
            console.log('Datos disponibles:', window.pettyCashData);
            alert('Error: URL de guardado no disponible. Por favor, contacta al administrador.');
            return;
        }

        if (!window.pettyCashData.csrfToken) {
            console.error('❌ csrfToken no está definido');
            alert('Error: Token de seguridad no disponible. Por favor, recarga la página.');
            return;
        }

        // Obtener ID de caja chica
        const pettyCashId = document.getElementById('petty_cash_id')?.value;

        if (!pettyCashId) {
            alert('Error: No se encontró el ID de la caja chica');
            console.error('❌ petty_cash_id no encontrado');
            return;
        }

        console.log('📌 Caja chica ID:', pettyCashId);

        // Obtener valores de ventas
        const totalSalesCash = parseFloat(document.getElementById('total-efectivo')?.value) || 0;
        const totalSalesQR = parseFloat(document.getElementById('ventas-qr')?.value) || 0;
        const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta')?.value) || 0;


        // Calcular total de gastos
        const totalExpenses = calculateTotalExpenses();
        console.log('💸 Total gastos:', totalExpenses);

        // Recopilar gastos nuevos
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

        // Capturar notas de cierre
        const closureNotes = document.getElementById('closure-notes')?.value?.trim() || '';

        // Preparar datos
        const dataToSend = {
            petty_cash_id: pettyCashId,
            total_sales_cash: totalSalesCash,
            total_sales_qr: totalSalesQR,
            total_sales_card: totalSalesCard,
            total_expenses: totalExpenses,
            closure_notes: closureNotes,
            expenses: expenses
        };

        console.log('📤 Datos a enviar:', dataToSend);

        // Deshabilitar botón
        const saveBtn = document.querySelector('#btn-save-closure');
        if (!saveBtn) {
            console.error('❌ Botón de guardar no encontrado');
            return;
        }

        const originalBtnText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
        saveBtn.disabled = true;

        // Enviar petición
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
            console.log('✅ Cierre guardado exitosamente');
            console.log('🚪 Cerrando modal...');

            // Cerrar el modal
            closeModal();
            const notesEl = document.getElementById('closure-notes');
            if (notesEl) {
                notesEl.value = '';
                const charCount = document.getElementById('notes-char-count');
                if (charCount) charCount.textContent = '0';
            }
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Cierre de Caja Exitoso!',
                    html: `
                        <div style="text-align: center;">
                            <p style="font-size: 16px; margin: 15px 0;">El cierre se ha guardado correctamente</p>
                            <hr style="margin: 20px 0;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                                <p style="margin: 8px 0;"><strong>📊 Gastos registrados:</strong> ${data.data?.expenses_count || expenses.length}</p>
                                <p style="margin: 8px 0;"><strong>💰 Monto final:</strong> Bs.${data.data?.current_amount?.toFixed(2) || '0.00'}</p>
                            </div>
                            <p style="margin-top: 20px; color: #6c757d; font-size: 14px;">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Redirigiendo a nueva caja...
                            </p>
                        </div>
                    `,
                    showConfirmButton: false, // ✅ SIN BOTÓN
                    timer: 2500, // ✅ Se cierra automáticamente en 2.5 segundos
                    timerProgressBar: true, // ✅ Muestra barra de progreso
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'animated fadeInDown faster'
                    },
                    // ✅ REDIRECCIÓN AUTOMÁTICA cuando se cierra el modal
                    didClose: () => {
                        console.log('🔄 [SUCCESS] Redirigiendo automáticamente...');
                        console.log('🔄 [SUCCESS] URL destino: /petty-cash/create');
                        window.location.href = '/petty-cash/create';
                    }
                });

                // ✅ REDIRECCIÓN DE RESPALDO: Por si el didClose falla
                setTimeout(() => {
                    console.log('🔄 [FALLBACK] Ejecutando redirección de respaldo...');
                    window.location.href = '/petty-cash/create';
                }, 2500); // 100ms después de que se cierre el modal

            }, 400); // Esperar a que el modal se cierre

            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

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