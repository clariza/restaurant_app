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
            
            <button id="btn-clear-order" class="flex-1 bg-gray-500 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center" 
                onclick="clearOrder()">
                <i class="fas fa-trash-alt mr-2"></i> Limpiar
            </button>
            <button id="btn-proforma" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center" 
                onclick="generateProforma()">
                <i class="fas fa-file-invoice mr-2"></i> Proforma
            </button>
        </div>

        
        <button id="btn-multiple-payment" class="w-full bg-primary text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center mb-2" 
            onclick="showPaymentModal()">
            Realizar Pago
        </button>
    
        <div class="flex space-x-2">
            <a onclick="event.preventDefault(); openExpensesModal();"class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm flex items-center justify-center"
                title="Gastos">
                <i class="fas fa-receipt"></i>
            </a>
            <a href="<?php echo e(route('orders.index')); ?>" class="flex-1 bg-[#6380a6] text-white py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors text-sm flex items-center justify-center"
                title="Historial">
                <i class="fas fa-history"></i>
            </a>
            <!-- ✅ CAMBIO: Botón que abre el modal -->
            <button onclick="openPettyCashModal()" class="flex-1 bg-[#EF476F] text-white py-2 px-3 rounded-lg hover:bg-accent-dark transition-colors text-sm flex items-center justify-center"
                title="Caja Chica">
                <i class="fas fa-cash-register"></i>
            </button>
        </div>
    </div>

    <!-- Input oculto para el tipo de pedido -->
    <input type="hidden" name="order_type" id="order-type" value="Comer aquí">
</div>
<!-- ✅ Modal de Gastos -->
<div id="expenses-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="bg-white rounded-lg w-full max-w-7xl my-8 shadow-xl transform transition-all">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-[#203363]">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-receipt mr-3"></i>
                    Gestión de Gastos
                </h2>
                <button onclick="closeExpensesModal()" class="text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Contenido del Modal -->
            <div id="expenses-content" class="p-6 max-h-[80vh] overflow-y-auto">
                <!-- Encabezado con botón de crear -->
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

                <!-- Formulario de Crear/Editar Gasto (Oculto por defecto) -->
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
                                <label for="expense-name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Gasto *
                                </label>
                                <input type="text" 
                                       id="expense-name" 
                                       name="expense_name" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                       placeholder="Ej: Compra de ingredientes">
                            </div>
                            
                            <div>
                                <label for="expense-amount" class="block text-sm font-medium text-gray-700 mb-1">
                                    Monto (S/) *
                                </label>
                                <input type="number" 
                                       id="expense-amount" 
                                       name="amount" 
                                       required
                                       step="0.01"
                                       min="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            
                            <div>
                                <label for="expense-date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha *
                                </label>
                                <input type="date" 
                                       id="expense-date" 
                                       name="date" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="expense-description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Descripción
                                </label>
                                <textarea id="expense-description" 
                                          name="description" 
                                          rows="1"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#203363] focus:border-transparent"
                                          placeholder="Descripción del gasto"></textarea>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 mt-4">
                            <button type="submit" 
                                    class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200 inline-flex items-center">
                                <i class="fas fa-save mr-2"></i>Guardar Gasto
                            </button>
                            <button type="button" 
                                    onclick="hideExpenseForm()"
                                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 inline-flex items-center">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de gastos -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <div id="expenses-table-container">
                        <!-- Loader inicial -->
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

<!-- Modal de Pago -->
<?php echo $__env->make('partials.payment-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Modal de Proforma -->
<?php echo $__env->make('partials.proforma-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Modal de Vista Previa de Impresión -->
<?php echo $__env->make('partials.print-preview-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<style>
/* Estilos adicionales para el modal de gastos */
#expenses-modal .hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

#expense-form input:focus,
#expense-form textarea:focus {
    outline: none;
}

/* Animación suave para el formulario */
#expense-form-container {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- ✅ NUEVO: Modal de Caja Chica -->
<div id="petty-cash-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] overflow-y-auto">
    <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20">
        <div class="bg-white rounded-lg w-full max-w-7xl my-8 shadow-xl transform transition-all">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-[#203363]">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-cash-register mr-3"></i>
                    Gestión de Caja Chica
                </h2>
                <button onclick="closePettyCashModal()" class="text-white hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Contenido del Modal -->
            <div id="petty-cash-content" class="p-6 max-h-[80vh] overflow-y-auto">
                <!-- El contenido se cargará dinámicamente aquí -->
                <div class="flex justify-center items-center py-12">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-[#203363] mb-4"></i>
                        <p class="text-gray-600">Cargando información de caja chica...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="closure-internal-overlay" class="closure-internal-overlay"></div>
<div id="modal-closure-internal" class="closure-internal-modal">
    <!-- El contenido del modal de cierre se cargará aquí dinámicamente -->
</div>
<script>
    const tablesEnabled = <?php echo json_encode($settings->tables_enabled ?? false, 15, 512) ?>;
    <?php if(!auth()->check()): ?>
        clearOrderOnLogout();
    <?php endif; ?>
</script>

<!-- Variables globales PRIMERO -->
<script>
window.routes = {
    tablesAvailable: "<?php echo e(route('tables.available')); ?>",
    salesStore: "<?php echo e(route('sales.store')); ?>",
    customerDetails: "<?php echo e(route('customer.details')); ?>",
    menuIndex: "<?php echo e(route('menu.index')); ?>",
    pettyCashIndex: "<?php echo e(route('petty-cash.index')); ?>",
    pettyCashModalContent: "<?php echo e(route('petty-cash.modal-content')); ?>"
};
window.csrfToken = "<?php echo e(csrf_token()); ?>";
window.authUserName = "<?php echo e(Auth::user()->name ?? ''); ?>";
window.tablesEnabled = <?php echo json_encode($settings->tables_enabled ?? false, 15, 512) ?>;

console.log('✅ Rutas de petty-cash cargadas:', window.routes);
</script>
<script>
// Variables globales para el modal de gastos
let openPettyCash = false;
let expensesData = [];

// Abrir modal de gastos
function openExpensesModal() {
    document.getElementById('expenses-modal').classList.remove('hidden');
    loadExpenses();
    checkPettyCashStatus();
}

// Cerrar modal de gastos
function closeExpensesModal() {
    document.getElementById('expenses-modal').classList.add('hidden');
    hideExpenseForm();
}

// Verificar estado de caja chica
async function checkPettyCashStatus() {
    try {
        // Usar la ruta existente de Laravel
        const response = await fetch('/petty-cash/check-status', {
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        openPettyCash = data.open || false;
        
        const btnCreate = document.getElementById('btn-create-expense');
        const warning = document.getElementById('no-petty-cash-warning');
        
        if (!openPettyCash) {
            btnCreate.classList.add('opacity-50', 'cursor-not-allowed');
            btnCreate.disabled = true;
            warning.classList.remove('hidden');
        } else {
            btnCreate.classList.remove('opacity-50', 'cursor-not-allowed');
            btnCreate.disabled = false;
            warning.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error al verificar estado de caja chica:', error);
    }
}

// Cargar lista de gastos
async function loadExpenses() {
    const container = document.getElementById('expenses-table-container');
    container.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-[#203363] mb-4"></i>
                <p class="text-gray-600">Cargando gastos...</p>
            </div>
        </div>
    `;
    
    try {
        const response = await fetch('/expenses?json=1', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response status:', response.status);
            console.error('Response text:', errorText);
            throw new Error(`Error ${response.status}: ${errorText}`);
        }
        
        const data = await response.json();
        console.log('📦 Datos recibidos completos:', data);
        console.log('📦 Tipo de datos:', typeof data);
        
        // Manejar ambos formatos de respuesta
        if (Array.isArray(data)) {
            console.log('✅ Data es un array directo');
            expensesData = data;
        } else if (data.expenses && Array.isArray(data.expenses)) {
            console.log('✅ Data tiene propiedad expenses (array)');
            expensesData = data.expenses;
        } else {
            console.error('❌ Formato inesperado:', data);
            expensesData = [];
        }
        
        console.log('📊 expensesData final:', expensesData);
        renderExpensesTable();
    } catch (error) {
        console.error('💥 Error completo:', error);
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Error al cargar los gastos</p>
                <p class="text-sm text-gray-500 mt-2">${error.message}</p>
            </div>
        `;
    }
}
// Renderizar tabla de gastos
function renderExpensesTable() {
    const container = document.getElementById('expenses-table-container');
    
    console.log('Renderizando tabla. Datos:', expensesData);
    console.log('Es array?', Array.isArray(expensesData));
    console.log('Tipo:', typeof expensesData);
    
    // Validación robusta
    if (!Array.isArray(expensesData) || expensesData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No hay gastos registrados</p>
            </div>
        `;
        return;
    }
    
    const tableHTML = `
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#203363]">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-white uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                ${expensesData.map(expense => `
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${expense.expense_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${expense.description || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">S/ ${parseFloat(expense.amount).toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${formatDate(expense.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button onclick="editExpense(${expense.id})" 
                                    class="text-blue-600 hover:text-blue-800 mr-3 transition duration-200">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </button>
                            <button onclick="deleteExpense(${expense.id})" 
                                    class="text-red-600 hover:text-red-800 transition duration-200">
                                <i class="fas fa-trash-alt mr-1"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    container.innerHTML = tableHTML;
}

// Mostrar formulario de crear gasto
function showCreateExpenseForm() {
    if (!openPettyCash) {
        alert('No hay caja chica abierta. Por favor, abre una caja chica primero.');
        return;
    }
    
    const container = document.getElementById('expense-form-container');
    const form = document.getElementById('expense-form');
    const formTitle = document.getElementById('form-title');
    
    // Resetear formulario
    form.reset();
    document.getElementById('expense-id').value = '';
    document.getElementById('form-method').value = 'POST';
    
    // Establecer fecha actual
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expense-date').value = today;
    
    formTitle.innerHTML = '<i class="fas fa-plus-circle mr-2"></i>Nuevo Gasto';
    container.classList.remove('hidden');
    
    // Scroll al formulario
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Ocultar formulario
function hideExpenseForm() {
    document.getElementById('expense-form-container').classList.add('hidden');
    document.getElementById('expense-form').reset();
}

// Guardar gasto (crear o actualizar)
async function saveExpense(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const expenseId = document.getElementById('expense-id').value;
    const method = document.getElementById('form-method').value;
    
    const url = expenseId ? `/expenses/${expenseId}` : '/expenses';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        if (!response.ok) throw new Error('Error al guardar el gasto');
        
        // Recargar lista
        await loadExpenses();
        hideExpenseForm();
        
        // Mostrar mensaje de éxito
        showNotification(expenseId ? 'Gasto actualizado exitosamente' : 'Gasto creado exitosamente', 'success');
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al guardar el gasto', 'error');
    }
}

// Editar gasto
function editExpense(id) {
    const expense = expensesData.find(e => e.id === id);
    if (!expense) return;
    
    const container = document.getElementById('expense-form-container');
    const formTitle = document.getElementById('form-title');
    
    // Llenar formulario con datos
    document.getElementById('expense-id').value = expense.id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('expense-name').value = expense.expense_name;
    document.getElementById('expense-amount').value = expense.amount;
    document.getElementById('expense-date').value = expense.date;
    document.getElementById('expense-description').value = expense.description || '';
    
    formTitle.innerHTML = '<i class="fas fa-edit mr-2"></i>Editar Gasto';
    container.classList.remove('hidden');
    
    // Scroll al formulario
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Eliminar gasto
async function deleteExpense(id) {
    if (!confirm('¿Estás seguro de eliminar este gasto?')) return;
    
    try {
        const response = await fetch(`/expenses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) throw new Error('Error al eliminar el gasto');
        
        // Recargar lista
        await loadExpenses();
        showNotification('Gasto eliminado exitosamente', 'success');
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al eliminar el gasto', 'error');
    }
}

// Formatear fecha
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Mostrar notificación
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-[10000] transform transition-all duration-300`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Modificar el enlace de "Gastos" para que abra el modal
document.addEventListener('DOMContentLoaded', function() {
    // Buscar el enlace de gastos y cambiar su comportamiento
    const expensesLink = document.querySelector('a[href*="expenses.index"]');
    if (expensesLink) {
        expensesLink.onclick = function(e) {
            e.preventDefault();
            openExpensesModal();
        };
    }
});
</script>
<!-- Scripts DESPUÉS de las variables -->
<script src="<?php echo e(asset('js/payment-modal.js')); ?>"></script>
<script src="<?php echo e(asset('js/order-details.js')); ?>"></script>
<script src="<?php echo e(asset('js/petty-cash-modal.js')); ?>"></script> <?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/layouts/order-details.blade.php ENDPATH**/ ?>