<style>
    * {
        box-sizing: border-box;
    }

    body {
        background: #f3f4f6;
    }

    /* Estilos para el modal de cierre INTERNO - SCROLL INDEPENDIENTE */
    #modal-closure-internal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: white;
        z-index: 10002;
        display: none;
        flex-direction: column;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 95%;
        max-width: 900px;
        max-height: 80vh;
        overflow: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }

    #modal-closure-internal.active {
        display: flex;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .closure-internal-content {
        padding: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        max-height: 80vh;
    }

    /* Overlay para el modal interno */
    #closure-internal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10001;
        display: none;
    }

    #closure-internal-overlay.active {
        display: block;
    }

    /* Header interno para el modal de cierre */
    .closure-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .closure-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .closure-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .closure-close-btn:hover {
        background: #e5e7eb;
        color: #374151;
    }

    /* Contenido desplazable - SCROLL INDEPENDIENTE */
    .closure-scroll-content {
        padding: 1.5rem;
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: calc(80vh - 80px);
    }

    /* Contenedor principal del modal */
    .modal-content {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* Grid principal para las dos columnas */
    .closure-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        align-items: start;
    }

    /* Contenedores de sección uniformes */
    .section-container {
        padding: 1.25rem;
        background-color: #f8fafc;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 1rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }

    /* Tabla de denominaciones */
    .table-container {
        overflow-x: auto;
        flex: 1;
    }

    .denominations-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .denominations-table th {
        background-color: #f1f5f9;
        padding: 0.75rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        text-align: left;
    }

    .denominations-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .denominations-table tr:last-child td {
        border-bottom: none;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .total-row {
        background-color: #f1f5f9;
        font-weight: 600;
    }

    /* Input de denominación */
    .denomination-input {
        width: 100%;
        max-width: 80px;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-align: center;
        transition: all 0.2s;
    }

    .denomination-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Sección de gastos */
    .expenses-section {
        background: white;
        padding: 1.25rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
    }

    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .expenses-header h4 {
        margin: 0;
        font-size: 1.125rem;
        color: #374151;
        font-weight: 600;
    }

    .expenses-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    /* Fila de gastos perfectamente alineada */
    .expense-row {
        display: grid;
        grid-template-columns: 1.5fr 1.5fr 120px 50px;
        gap: 0.75rem;
        align-items: center;
    }

    .expense-field {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .expense-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .expense-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .expense-actions {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Formulario de cierre con grid uniforme */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .input-group label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin: 0;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control[readonly] {
        background-color: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }

    /* Botones */
    .btn {
        padding: 0.625rem 1.25rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-primary {
        background-color: #10b981;
        color: white;
    }

    .btn-primary:hover {
        background-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-danger {
        background-color: #fee2e2;
        color: #dc2626;
        padding: 0.5rem;
        width: 40px;
        height: 40px;
    }

    .btn-danger:hover {
        background-color: #fecaca;
        transform: scale(1.05);
    }

    .add-expense-btn {
        background-color: #dbeafe;
        color: #1e40af;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .add-expense-btn:hover {
        background-color: #bfdbfe;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Acciones del formulario */
    .form-actions {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Alerta de advertencia */
    .alert-warning {
        background-color: #fef3c7;
        border: 1px solid #fbbf24;
        color: #92400e;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Media queries para responsive */
    @media (max-width: 768px) {
        #modal-closure-internal {
            width: 98%;
            max-height: 90vh;
            margin: 1rem;
        }

        .closure-scroll-content {
            padding: 1rem;
        }

        .closure-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .expense-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .expense-actions {
            justify-content: flex-start;
        }

        .section-container {
            padding: 1rem;
        }

        .form-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn {
            width: 100%;
        }

        .expenses-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .add-expense-btn {
            width: 100%;
            justify-content: center;
        }

        .denominations-table {
            font-size: 0.8rem;
        }

        .denominations-table th,
        .denominations-table td {
            padding: 0.5rem 0.25rem;
        }

        .denomination-input {
            max-width: 60px;
            padding: 0.375rem;
        }
    }

    @media (max-width: 480px) {
        .closure-scroll-content {
            padding: 0.75rem;
        }

        .section-title {
            font-size: 1rem;
        }

        .expenses-header h4 {
            font-size: 1rem;
        }
    }
</style>


<?php if($openPettyCash): ?>
    <!-- ✅ INPUT OCULTO AL INICIO DEL CONTENIDO -->
    <input type="hidden" id="petty_cash_id_closure" name="petty_cash_id_closure" value="<?php echo e($openPettyCash->id); ?>">

    <!-- SOLO el contenido interno, sin wrappers adicionales -->
    <div class="closure-internal-content">
        <!-- Contenido desplazable -->
        <div class="closure-scroll-content">
            <div class="modal-content">
                <!-- Sección de Gastos -->
                <div class="expenses-section">
                    <div class="expenses-header">
                        <h4 class="font-medium" style="margin: 0; font-size: 1.125rem; color: #374151;">Registro de Gastos</h4>
                        <button type="button" class="add-expense-btn" onclick="addExpenseModalClosure()">
                            <i class="fas fa-plus"></i> Agregar Gasto
                        </button>
                    </div>

                    <div class="expenses-container" id="expensesContainerClosure">
                        <!-- Fila de gasto inicial -->
                        <div class="expense-row">
                            <div class="expense-field">
                                <input type="text" class="expense-input" placeholder="Nombre del gasto" name="expense_name[]">
                            </div>
                            <div class="expense-field">
                                <input type="text" class="expense-input" placeholder="Descripción/Categoría" name="expense_description[]">
                            </div>
                            <div class="expense-field">
                                <input type="number" class="expense-input" placeholder="Monto" step="0.01" min="0" name="expense_amount[]">
                            </div>
                            <div class="expense-actions">
                                <button type="button" class="btn btn-danger" onclick="removeExpenseClosure(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Cierre en grid -->
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
                                        <?php $__currentLoopData = [0.5, 1, 2, 5, 10, 20, 50, 100, 200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denominacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-left">$<?php echo e(number_format($denominacion, 2)); ?></td>
                                            <td>
                                                <input type="number" min="0" class="denomination-input contar-input-closure"
                                                    data-denominacion="<?php echo e($denominacion); ?>" placeholder="0">
                                            </td>
                                            <td class="text-right">
                                                <span class="subtotal-closure">$0.00</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="total-row">
                                            <td colspan="2" class="text-right"><strong>Total Efectivo:</strong></td>
                                            <td class="text-right">
                                                <strong><span id="total-closure">$0.00</span></strong>
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
                                    <label for="total-gastos-closure">Total Gastos</label>
                                    <input type="number" id="total-gastos-closure" class="form-control"
                                        value="<?php echo e($totalExpenses ?? 0); ?>" 
                                        data-gastos-bd="<?php echo e($totalExpenses ?? 0); ?>"
                                        step="0.01" readonly>
                                </div>

                                <div class="input-group">
                                    <label for="ventas-efectivo-closure">Ventas en Efectivo</label>
                                    <input type="number" id="ventas-efectivo-closure" class="form-control"
                                        value="<?php echo e($totalSalesCash ?? 0); ?>" step="0.01" readonly>
                                </div>

                                <div class="input-group">
                                    <label for="ventas-qr-closure">Ventas QR</label>
                                    <input type="number" id="ventas-qr-closure" class="form-control"
                                        value="<?php echo e($totalSalesQR ?? 0); ?>" step="0.01">
                                </div>

                                <div class="input-group">
                                    <label for="ventas-tarjeta-closure">Ventas Tarjeta</label>
                                    <input type="number" id="ventas-tarjeta-closure" class="form-control"
                                        value="<?php echo e($totalSalesCard ?? 0); ?>" step="0.01">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="closeInternalModalClosure()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    
                                    <button type="button" class="btn btn-primary" onclick="saveClosureClosure(<?php echo e($openPettyCash->id); ?>)">
                                        <i class="fas fa-save"></i> Guardar Cierre
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    
    <div class="closure-internal-content">
        <div class="closure-scroll-content">
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>No hay caja chica abierta</strong>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">Debe abrir una caja chica antes de poder registrar un cierre.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <button onclick="window.location.href='<?php echo e(route('petty-cash.create')); ?>'" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Abrir Nueva Caja Chica
                </button>
            </div>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/modal-content.blade.php ENDPATH**/ ?>