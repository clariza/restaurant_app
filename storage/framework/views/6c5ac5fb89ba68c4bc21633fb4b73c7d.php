
<div id="order-panel" class="w-full md:w-1/5 bg-white p-4 rounded-lg shadow-lg fixed right-0 top-16 h-[calc(100vh-4rem)] flex flex-col z-40">

    <div class="scroll-container flex-1">
        <div id="order-details" class="mb-4">
            <!-- Los ítems del pedido se agregarán aquí dinámicamente -->
        </div>

        <!-- Notas del pedido -->
        <div class="notes-container">
            <label for="order-notes" class="notes-label">Notas especiales para el pedido:</label>
            <textarea id="order-notes" name="order_notes" class="notes-textarea"
                placeholder="Ej: Quiero una hamburguesa sin queso cheddar, salsa aparte..."
                maxlength="250" oninput="updateNotesCounter()"></textarea>
            <div class="notes-counter"><span id="notes-chars">0</span>/250 caracteres</div>
            <div class="notes-examples">Ejemplos:
                <span onclick="insertExample('Sin cebolla')">Sin cebolla</span>
                <span onclick="insertExample('Salsa aparte')">Salsa aparte</span>
                <span onclick="insertExample('Bien cocido')">Bien cocido</span>
                <span onclick="insertExample('Poco sal')">Poco sal</span>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="buttons-container mt-auto">
        <div class="flex space-x-2 mb-2">
            <button id="btn-clear-order"
                class="flex-1 bg-gray-500 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center"
                onclick="clearOrder()">
                <i class="fas fa-trash-alt mr-2"></i> Limpiar
            </button>
            <button id="btn-proforma"
                class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center"
                onclick="generateProforma()">
                <i class="fas fa-file-invoice mr-2"></i> Proforma
            </button>
        </div>

        <button id="btn-multiple-payment"
            class="w-full bg-primary text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center mb-2"
            onclick="showPaymentModal()">
            Realizar Pago
        </button>

        
        <div class="flex space-x-2">
            <a onclick="event.preventDefault(); openExpensesModal();"
               class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center"
               title="Gastos">
                <i class="fas fa-receipt"></i>
            </a>
            <a href="<?php echo e(route('orders.index')); ?>"
               class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center"
               title="Historial">
                <i class="fas fa-history"></i>
            </a>

            
            <button onclick="openUnifiedModal(null, 'list')"
                class="flex-1 bg-[#EF476F] text-white py-2 px-3 rounded-lg hover:bg-accent-dark transition-colors text-sm flex items-center justify-center"
                title="Caja Chica">
                <i class="fas fa-cash-register"></i>
            </button>
        </div>
    </div>

    <!-- Input oculto para el tipo de pedido -->
    <input type="hidden" name="order_type" id="order-type" value="Comer aquí">
</div>


<!-- ✅ Modal de Gastos (sin cambios) -->
<div id="expenses-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="bg-white rounded-lg w-full max-w-7xl my-8 shadow-xl transform transition-all">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-[#203363]">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-receipt mr-3"></i>
                    Gestión de Gastos
                </h2>
                <button onclick="closeExpensesModal()" class="text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="expenses-content" class="p-6 max-h-[80vh] overflow-y-auto">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <button onclick="showCreateExpenseForm()"
                                id="btn-create-expense"
                                class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200 inline-flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>Crear Gasto
                        </button>
                        <div id="no-petty-cash-warning" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-lg">
                            <p class="font-medium text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>No hay caja chica abierta
                            </p>
                        </div>
                    </div>
                </div>
                <div id="expense-form-container" class="hidden mb-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-[#203363]" id="form-title">
                            <i class="fas fa-plus-circle mr-2"></i>Nuevo Gasto
                        </h3>
                        <button onclick="hideExpenseForm()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form id="expense-form" onsubmit="saveExpense(event)">
                        <input type="hidden" id="expense-id" name="expense_id">
                        <input type="hidden" id="form-method" value="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="expense-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Gasto *</label>
                                <input type="text" id="expense-name" name="expense_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                    placeholder="Ej: Compra de ingredientes">
                            </div>
                            <div>
                                <label for="expense-amount" class="block text-sm font-medium text-gray-700 mb-1">Monto (Bs.) *</label>
                                <input type="number" id="expense-amount" name="amount" required step="0.01" min="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                    placeholder="0.00">
                            </div>
                            <div>
                                <label for="expense-date" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                                <input type="date" id="expense-date" name="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent">
                            </div>
                            <div>
                                <label for="expense-description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea id="expense-description" name="description" rows="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                    placeholder="Descripción del gasto"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit"
                                class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200 inline-flex items-center">
                                <i class="fas fa-save mr-2"></i>Guardar Gasto
                            </button>
                            <button type="button" onclick="hideExpenseForm()"
                                class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 inline-flex items-center">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <div id="expenses-table-container">
                        <div class="flex justify-center items-center py-12">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-4xl text-[#203363] mb-4"></i>
                                <p class="text-gray-600">Cargando gastos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales existentes (sin cambios) -->
<?php echo $__env->make('partials.payment-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.proforma-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.print-preview-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<style>
#expense-form-container { animation: slideDown 0.3s ease-out; }
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>



<!-- Variables globales -->
<script>
window.routes = {
    tablesAvailable:      "<?php echo e(route('tables.available')); ?>",
    salesStore:           "<?php echo e(route('sales.store')); ?>",
    customerDetails:      "<?php echo e(route('customer.details')); ?>",
    menuIndex:            "<?php echo e(route('menu.index')); ?>",
    pettyCashIndex:       "<?php echo e(route('petty-cash.index')); ?>",
    pettyCashModalContent:"<?php echo e(route('petty-cash.modal-content')); ?>",
    deliveryServicesApi:  "<?php echo e(route('deliveries.api.active')); ?>"
};
window.csrfToken    = "<?php echo e(csrf_token()); ?>";
window.authUserName = "<?php echo e(Auth::user()->name ?? ''); ?>";
window.isAdmin      = <?php echo e(auth()->user()->role === 'admin' ? 'true' : 'false'); ?>;
window.tablesEnabled = <?php echo json_encode($settings->tables_enabled ?? false, 15, 512) ?>;
</script>

<script>
const tablesEnabled = <?php echo json_encode($settings->tables_enabled ?? false, 15, 512) ?>;
<?php if(!auth()->check()): ?>
    clearOrderOnLogout();
<?php endif; ?>

/* ── Gastos (sin cambios) ── */
let openPettyCash  = false;
let expensesData   = [];

function openExpensesModal() {
    document.getElementById('expenses-modal').classList.remove('hidden');
    loadExpenses();
    checkPettyCashStatus();
}
function closeExpensesModal() {
    document.getElementById('expenses-modal').classList.add('hidden');
    hideExpenseForm();
}
async function checkPettyCashStatus() {
    try {
        const res  = await fetch('/petty-cash/check-status', {
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' }
        });
        const data = await res.json();
        openPettyCash  = data.open || false;
        const btnCreate = document.getElementById('btn-create-expense');
        const warning   = document.getElementById('no-petty-cash-warning');
        if (!openPettyCash) {
            btnCreate.classList.add('opacity-50','cursor-not-allowed');
            btnCreate.disabled = true;
            warning.classList.remove('hidden');
        } else {
            btnCreate.classList.remove('opacity-50','cursor-not-allowed');
            btnCreate.disabled = false;
            warning.classList.add('hidden');
        }
    } catch(e) { console.error('Error al verificar caja chica:', e); }
}
async function loadExpenses() {
    const container = document.getElementById('expenses-table-container');
    container.innerHTML = `<div class="flex justify-center items-center py-12">
        <i class="fas fa-spinner fa-spin text-4xl text-[#203363]"></i></div>`;
    try {
        const res = await fetch('/expenses?json=1', {
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        expensesData = Array.isArray(data) ? data : (data.expenses ?? []);
        renderExpensesTable();
    } catch(e) {
        container.innerHTML = `<div class="text-center py-12 text-red-500">${e.message}</div>`;
    }
}
function renderExpensesTable() {
    const container = document.getElementById('expenses-table-container');
    if (!Array.isArray(expensesData) || expensesData.length === 0) {
        container.innerHTML = `<div class="text-center py-12 text-gray-500">No hay gastos registrados</div>`;
        return;
    }
    container.innerHTML = `
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#203363]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Descripción</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Monto</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                ${expensesData.map(e => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">${e.expense_name}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${e.description || '-'}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">Bs. ${parseFloat(e.amount).toFixed(2)}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${formatDate(e.date)}</td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="editExpense(${e.id})" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit mr-1"></i>Editar</button>
                            ${window.isAdmin ? `<button onclick="deleteExpense(${e.id})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt mr-1"></i>Eliminar</button>` : ''}
                        </td>
                    </tr>`).join('')}
            </tbody>
        </table>`;
}
function showCreateExpenseForm() {
    if (!openPettyCash) { alert('No hay caja chica abierta.'); return; }
    const container = document.getElementById('expense-form-container');
    document.getElementById('expense-form').reset();
    document.getElementById('expense-id').value   = '';
    document.getElementById('form-method').value  = 'POST';
    document.getElementById('expense-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle mr-2"></i>Nuevo Gasto';
    container.classList.remove('hidden');
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideExpenseForm() {
    document.getElementById('expense-form-container').classList.add('hidden');
    document.getElementById('expense-form').reset();
}
async function saveExpense(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const expenseId = document.getElementById('expense-id').value;
    const method    = document.getElementById('form-method').value;
    const url = expenseId ? `/expenses/${expenseId}` : '/expenses';
    if (method === 'PUT') formData.append('_method', 'PUT');
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
            body: formData
        });
        if (!res.ok) { const err = await res.json(); throw new Error(err.message || 'Error'); }
        await loadExpenses();
        hideExpenseForm();
        showNotification(expenseId ? 'Gasto actualizado' : 'Gasto creado', 'success');
    } catch(e) { showNotification(e.message || 'Error al guardar', 'error'); }
}
function editExpense(id) {
    const expense = expensesData.find(e => e.id === id);
    if (!expense) return;
    document.getElementById('expense-id').value          = expense.id;
    document.getElementById('form-method').value         = 'PUT';
    document.getElementById('expense-name').value        = expense.expense_name;
    document.getElementById('expense-amount').value      = expense.amount;
    document.getElementById('expense-date').value        = expense.date;
    document.getElementById('expense-description').value = expense.description || '';
    document.getElementById('form-title').innerHTML = '<i class="fas fa-edit mr-2"></i>Editar Gasto';
    const container = document.getElementById('expense-form-container');
    container.classList.remove('hidden');
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
async function deleteExpense(id) {
    if (!confirm('¿Estás seguro de eliminar este gasto?')) return;
    try {
        const res = await fetch(`/expenses/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Error al eliminar');
        await loadExpenses();
        showNotification('Gasto eliminado', 'success');
    } catch(e) { showNotification('Error al eliminar', 'error'); }
}
function formatDate(dateString) {
    const d = new Date(dateString);
    return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
}
function showNotification(message, type = 'success') {
    const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 ${bg} text-white px-6 py-3 rounded-lg shadow-lg z-[10000] transition-all duration-300`;
    el.innerHTML = `<div class="flex items-center"><i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'} mr-2"></i><span>${message}</span></div>`;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3000);
}
</script>

<!-- Scripts principales (sin cambios) -->
<script src="<?php echo e(asset('js/payment-modal.js')); ?>"></script>
<script src="<?php echo e(asset('js/order-details.js')); ?>"></script>

<?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/layouts/order-details.blade.php ENDPATH**/ ?>