@extends('layouts.app')
@section('content')
<style>
    /* Estilos mejorados para botones */
    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    
    .btn-view {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #2563eb;
    }
    
    .btn-edit {
        background-color: #10b981;
        color: white;
        border: 1px solid #059669;
    }
    
    .btn-delete {
        background-color: #ef4444;
        color: white;
        border: 1px solid #dc2626;
    }
    
    .btn-close {
        background-color: #8b5cf6;
        color: white;
        border: 1px solid #7c3aed;
    }
    
    .btn-print {
        background-color: #6b7280;
        color: white;
        border: 1px solid #4b5563;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Resto de estilos... */
    .input-group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    .input-group label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.input-group input {
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

   /* Estilos mejorados para el modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        background-color: white;
        border-radius: 0.75rem; /* Bordes más redondeados */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 900px; /* Un poco más ancho */
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        padding: 2rem; /* Padding interno general */
    }

    .modal-overlay.active .modal-container {
        transform: translateY(0);
    }

       .modal-header {
        padding: 1.5rem 0; /* Más espacio en el header */
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem; /* Separación del contenido */
    }

    .modal-title {
        font-size: 1.5rem; /* Título más grande */
        font-weight: 600;
        color: #1f2937;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.75rem; /* Icono de cerrar más grande */
        color: #6b7280;
        cursor: pointer;
        transition: color 0.2s;
        padding: 0.5rem;
        margin-left: 1rem;
    }

    .modal-close:hover {
        color: #1f2937;
    }

    .modal-content {
        display: flex;
        flex-direction: column;
        gap: 2rem; /* Más espacio entre secciones */
    }
    .closure-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    }
    .section-container {
        padding: 1.5rem;
    background-color: #f8fafc;
    border-radius: 0.5rem;
    padding: 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
}
.denominations-section {
    display: flex;
    flex-direction: column;
}
.denomination-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.25rem;
    text-align: center;
}
.total-row {
    background-color: #f1f5f9;
    font-weight: 500;
}
.table-container {
    overflow-x: auto;
}
.denominations-table {
    width: 100%;
    border-collapse: collapse;
}

.denominations-table th {
    background-color: #f1f5f9;
    padding: 0.75rem;
    font-weight: 500;
    color: #334155;
}

.denominations-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.denominations-table tr:last-child td {
    border-bottom: none;
}
.closure-form-section {
    display: flex;
    flex-direction: column;
}
.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

    .modal-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

      /* Estilos mejorados para la sección de gastos */
    .expenses-section {
        margin-bottom: 1.5rem;
    }

    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .expense-actions {
    flex: 0 0 auto;
    width: 40px; /* Ancho fijo para el botón de eliminar */
    }
    .expenses-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .expense-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    width: 100%;
}
    .expense-field {
    flex: 1;
    min-width: 0; /* Previene que los campos se salgan del contenedor */
}

    .expense-input-container {
        flex: 1;
        min-width: 0;
    }

    .expense-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}
.form-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}
.save-btn {
    background-color: #10b981;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.save-btn:hover {
    background-color: #059669;
}

    .expense-input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 2px #bfdbfe;
    }

    .add-expense-btn {
        background-color: #e2e8f0;
        color: #475569;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }

    .add-expense-btn:hover {
        background-color: #cbd5e1;
    }

    .remove-expense-btn {
    background-color: #fee2e2;
    color: #dc2626;
    border: none;
    border-radius: 4px;
    padding: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    width: 36px;
    transition: background-color 0.2s;
}

    .remove-expense-btn:hover {
    background-color: #fecaca;
    }
    /* Estilos responsivos */
/* @media (max-width: 768px) {
    .expense-row {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .expense-field {
        flex: 1 1 100%;
    }
    
    .expense-actions {
        flex: 0 0 100%;
        text-align: right;
    }
} */


    
</style>

<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Cierres de Caja Chica</h2>

    <!-- Mensajes de alerta -->
    @if (session('warning'))
        <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
            <button onclick="closeOpenPettyCash()" class="ml-2 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                Cerrar caja abierta
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabla de cierres -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Fecha</th>
                    <th class="p-2 text-right">Monto Actual</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                    <th class="p-2 text-left">Reporte</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pettyCashes as $pettyCash)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 text-left">{{ $pettyCash->date }}</td>
                    <td class="p-2 text-right">${{ number_format($totalSales - $totalExpenses, 2) }}</td>
                    <td class="p-2 text-left">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $pettyCash->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $pettyCash->status === 'open' ? 'Abierta' : 'Cerrada' }}
                        </span>
                    </td>
                    <td class="p-2 text-left space-x-1">
                        <a href="{{ route('petty-cash.show', $pettyCash) }}" 
                           class="btn-action btn-view">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('petty-cash.edit', $pettyCash) }}" 
                           class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('petty-cash.destroy', $pettyCash) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-action btn-delete"
                                    onclick="return confirm('¿Estás seguro de eliminar esta caja chica?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                        @if ($pettyCash->status === 'open')
                            <button onclick="openModal('{{ $pettyCash->id }}')" 
                                    class="btn-action btn-close">
                                <i class="fas fa-lock"></i> Cerrar
                            </button>
                        @endif
                    </td>
                    <td class="p-2 text-left">
                        @if ($pettyCash->status === 'closed')
                            <a href="{{ route('petty-cash.print', $pettyCash) }}" 
                               target="_blank"
                               class="btn-action btn-print">
                                <i class="fas fa-print"></i> PDF
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de cierre -->
<div id="modal" class="modal-overlay">
    <div class="modal-container">
        <!-- Cabecera del modal -->
        <div class="modal-header">
            <h3 class="modal-title">Cierre de Caja Chica</h3>
            <button onclick="closeModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Contenido del modal -->
        <div class="modal-content">
            <!-- Sección de Gastos -->
            <div class="expenses-section">
                <div class="expenses-header">
                    <h4 class="font-medium">Registro de Gastos</h4>
                    <button type="button" class="add-expense-btn" onclick="addExpense()">
                        <i class="fas fa-plus mr-1"></i> Agregar Gasto
                    </button>
                </div>
                
                <div class="expenses-container" id="expensesContainer">
                    <!-- Fila de gasto inicial -->
                    <div class="expense-row">
                        <div class="expense-field">
                            <input type="text" class="expense-input" placeholder="Nombre del gasto" name="expense_name[]">
                        </div>
                        <div class="expense-field">
                            <input type="text" class="expense-input" placeholder="Descripción/Categoría" name="expense_description[]">
                        </div>
                        <div class="expense-field">
                            <input type="number" class="expense-input" placeholder="Monto" step="0.01" min="0" name="expense_amount[]" oninput="calculateTotalExpenses()">
                        </div>
                        <div class="expense-actions">
                            <button type="button" class="remove-expense-btn" onclick="removeExpense(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Cierre en disposición horizontal -->
            <div class="closure-grid">
                <!-- Tabla de denominaciones -->
                <div class="denominations-section">
                    <div class="section-container">
                        <h4 class="section-title">Conteo de Efectivo</h4>
                        <div class="table-container">
                            <table class="denominations-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Denominación</th>
                                        <th>Cantidad</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach([0.5, 1, 2, 5, 10, 20, 50, 100, 200] as $denominacion)
                                    <tr>
                                        <td class="text-left">${{ number_format($denominacion, 2) }}</td>
                                        <td>
                                            <input type="number" min="0" 
                                                   class="denomination-input" 
                                                   data-denominacion="{{ $denominacion }}" 
                                                   placeholder="0">
                                        </td>
                                        <td class="text-right">
                                            <span class="subtotal">$0.00</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="2" class="text-right">Total Efectivo:</td>
                                        <td class="text-right">
                                            <span id="total">$0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Formulario de cierre -->
                <div class="closure-form-section">
                    <div class="section-container">
                        <h4 class="section-title">Resumen de Cierre</h4>
                        <div class="form-grid">
                            <div class="input-group">
                                <label for="total-gastos">Total Gastos</label>
                                <input type="number" id="total-gastos" 
                                       value="0" step="0.01" readonly>
                            </div>
                            
                            <div class="input-group">
                                <label for="ventas-efectivo">Ventas en Efectivo</label>
                                <input type="number" id="ventas-efectivo" 
                                       value="0" step="0.01" readonly>
                            </div>
                            
                            <div class="input-group">
                                <label for="ventas-qr">Ventas QR</label>
                                <input type="number" id="ventas-qr" 
                                       value="{{ $totalSalesQR }}" step="0.01">
                            </div>
                            
                            <div class="input-group">
                                <label for="ventas-tarjeta">Ventas Tarjeta</label>
                                <input type="number" id="ventas-tarjeta" 
                                       value="{{ $totalSalesCard }}" step="0.01">
                            </div>
                            
                            <div class="form-actions">
                                <button onclick="saveClosure()" class="save-btn">
                                    <i class="fas fa-save mr-1"></i> Guardar Cierre
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para cierre -->
<form id="closureForm" action="{{ route('petty-cash.save-closure') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="petty_cash_id" id="petty_cash_id">
    <input type="hidden" name="total_sales_cash" id="total_sales_cash">
    <input type="hidden" name="total_sales_qr" id="total_sales_qr">
    <input type="hidden" name="total_sales_card" id="total_sales_card">
    <input type="hidden" name="total_expenses" id="total_expenses">
    <!-- Campos para gastos dinámicos se agregarán con JavaScript -->
</form>

<script>
   // Función para abrir el modal con el ID correcto
    function openModal(id) {
        const modal = document.getElementById('modal');
        modal.classList.add('active');
        document.getElementById('petty_cash_id').value = id;
        
        // Resetear los inputs al abrir el modal
        document.querySelectorAll('.contar-input').forEach(input => {
            input.value = '';
        });
        document.querySelectorAll('.subtotal').forEach(span => {
            span.textContent = '$0.00';
        });
        document.getElementById('total').textContent = '$0.00';
        document.getElementById('ventas-efectivo').value = '0';
        document.getElementById('total-gastos').value = '0';
        
        // Limpiar gastos excepto el primero
        const expensesContainer = document.getElementById('expensesContainer');
        while (expensesContainer.children.length > 1) {
            expensesContainer.removeChild(expensesContainer.lastChild);
        }
        // Resetear el primer gasto
        const firstExpense = expensesContainer.firstChild;
        firstExpense.querySelector('input[name="expense_name[]"]').value = '';
        firstExpense.querySelector('input[name="expense_description[]"]').value = '';
        firstExpense.querySelector('input[name="expense_amount[]"]').value = '';
    }

    // Función para agregar nuevo gasto en fila
    function addExpense() {
        const expensesContainer = document.getElementById('expensesContainer');
        
        const newExpenseRow = document.createElement('div');
        newExpenseRow.className = 'expense-row';
        newExpenseRow.innerHTML = `
            <div class="expense-input-container">
                <input type="text" class="expense-input" placeholder="Nombre del gasto" name="expense_name[]">
            </div>
            <div class="expense-input-container">
                <input type="text" class="expense-input" placeholder="Descripción/Categoría" name="expense_description[]">
            </div>
            <div class="expense-input-container">
                <input type="number" class="expense-input" placeholder="Monto" step="0.01" min="0" name="expense_amount[]" oninput="calculateTotalExpenses()">
            </div>
            <button type="button" class="remove-expense-btn" onclick="removeExpense(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        expensesContainer.appendChild(newExpenseRow);
    }

    // Función para eliminar fila de gasto
    function removeExpense(button) {
        const expenseRow = button.closest('.expense-row');
        if (document.getElementById('expensesContainer').children.length > 1) {
            expenseRow.remove();
            calculateTotalExpenses();
        } else {
            // Si es el último, solo limpiar los campos
            const inputs = expenseRow.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            calculateTotalExpenses();
        }
    }


    // Función para cerrar el modal
    function closeModal() {
        document.getElementById('modal').classList.remove('active');
    }

    // Calcular subtotales y total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.contar-input').forEach(input => {
            const denominacion = parseFloat(input.getAttribute('data-denominacion'));
            const cantidad = parseFloat(input.value) || 0;
            const subtotal = denominacion * cantidad;
            
            const subtotalElement = input.closest('tr').querySelector('.subtotal');
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
            
            total += subtotal;
        });
        
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;
        document.getElementById('ventas-efectivo').value = total.toFixed(2);
        document.getElementById('total_sales_cash').value = total.toFixed(2);
    }

    // Calcular total de gastos
    function calculateTotalExpenses() {
        let total = 0;
        document.querySelectorAll('input[name="expense_amount[]"]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total-gastos').value = total.toFixed(2);
        document.getElementById('total_expenses').value = total.toFixed(2);
        return total;
    }

    function addExpense() {
    const expensesContainer = document.getElementById('expensesContainer');
    
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
            <input type="number" class="expense-input" placeholder="Monto" step="0.01" min="0" 
                   name="expense_amount[]" oninput="calculateTotalExpenses()">
        </div>
        <div class="expense-actions">
            <button type="button" class="remove-expense-btn" onclick="removeExpense(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    expensesContainer.appendChild(newExpenseRow);
}

    // Eliminar gasto
    function removeExpense(button) {
        const expenseCard = button.closest('.expense-card');
        if (document.getElementById('expensesContainer').children.length > 1) {
            expenseCard.remove();
            calculateTotalExpenses();
            // Renumerar los gastos restantes
            document.querySelectorAll('.expense-card h4').forEach((header, index) => {
                header.textContent = `Gasto #${index + 1}`;
            });
        } else {
            // Si es el último, solo limpiar los campos
            const inputs = expenseCard.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            calculateTotalExpenses();
        }
    }

    // Guardar el cierre
    function saveClosure() {
        const pettyCashId = document.getElementById('petty_cash_id').value;
        const totalSalesCash = parseFloat(document.getElementById('ventas-efectivo').value) || 0;
        const totalSalesQR = parseFloat(document.getElementById('ventas-qr').value) || 0;
        const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta').value) || 0;
        const totalExpenses = calculateTotalExpenses();

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

        // Recopilar datos de gastos
        const expenses = [];
        document.querySelectorAll('.expense-card').forEach((card, index) => {
            const name = card.querySelector('input[name="expense_name[]"]').value;
            const description = card.querySelector('input[name="expense_description[]"]').value;
            const amount = card.querySelector('input[name="expense_amount[]"]').value;
            
            if (name && amount) {
                expenses.push({
                    name: name,
                    description: description,
                    amount: parseFloat(amount)
                });
            }
        });

        // Configurar los datos del formulario
        document.getElementById('total_sales_cash').value = totalSalesCash;
        document.getElementById('total_sales_qr').value = totalSalesQR;
        document.getElementById('total_sales_card').value = totalSalesCard;
        document.getElementById('total_expenses').value = totalExpenses;

        // Enviar el formulario
        fetch("{{ route('petty-cash.save-closure') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cierre guardado correctamente');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar el cierre'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el formulario');
        });
    }

    // Cerrar todas las cajas abiertas
    function closeOpenPettyCash() {
        if (confirm('¿Estás seguro de cerrar todas las cajas chicas abiertas?')) {
            fetch("{{ route('petty-cash.close-all-open') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudieron cerrar las cajas'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cerrar las cajas');
            });
        }
    }

    // Event listeners para cálculos automáticos
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.contar-input').forEach(input => {
            input.addEventListener('input', calcularTotal);
        });
        
        // Escuchar cambios en los inputs de gastos
        document.addEventListener('input', function(e) {
            if (e.target && e.target.matches('input[name="expense_amount[]"]')) {
                calculateTotalExpenses();
            }
        });
    });
</script>
@endsection