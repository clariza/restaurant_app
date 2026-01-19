<!-- Overlay para el modal de cierre -->
<div id="closure-internal-overlay" class="closure-overlay"></div>

<!-- Modal de cierre interno -->
<div class="closure-internal-modal">
    <?php if($openPettyCash): ?>
        <!-- ✅ INPUT OCULTO CON ID DE CAJA CHICA -->
        <input  id="petty_cash_id_closure" hidden name="petty_cash_id_closure" value="<?php echo e($openPettyCash->id); ?>">
        
        <!-- Header del modal -->
        <div class="closure-header">
            <h3 class="closure-title">Cierre de Caja Chica</h3>
        </div>

        <!-- Contenido desplazable -->
        <div class="closure-scroll-content">
            <div class="modal-content">
                
                <!-- Sección de Gastos -->
                <div class="expenses-section">
                    <div class="expenses-header">
                        <h4 class="expenses-title">Registro de Gastos</h4>
                        <button type="button" class="add-expense-btn" onclick="addExpenseModalClosure()">
                            <i class="fas fa-plus"></i> Agregar Gasto
                        </button>
                    </div>
                    <div class="expenses-container" id="expensesContainerClosure">
                        <!-- Fila de gasto inicial -->
                        <div class="expense-row">
                            <div class="expense-field">
                                <input type="text" 
                                       class="expense-input" 
                                       placeholder="Nombre del gasto" 
                                       name="expense_name[]"
                                       autocomplete="off">
                            </div>
                            <div class="expense-field">
                                <input type="text" 
                                       class="expense-input" 
                                       placeholder="Descripción/Categoría" 
                                       name="expense_description[]"
                                       autocomplete="off">
                            </div>
                            <div class="expense-field">
                                <input type="number" 
                                       class="expense-input" 
                                       placeholder="Monto" 
                                       step="0.01" 
                                       min="0" 
                                       name="expense_amount[]"
                                       autocomplete="off">
                            </div>
                            <div class="expense-actions">
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="removeExpenseClosure(this)"
                                        aria-label="Eliminar gasto">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Cierre (Denominaciones + Resumen) -->
                <div class="closure-grid">
                    
                    <div class="denominations-section">
                        <div class="section-container">
                            <h4 class="section-title">Conteo de Efectivo en modal content</h4>
                            <div class="table-container">
                                <table class="denominations-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Denominación</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = [0.5, 1, 2, 5, 10, 20, 50, 100, 200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denominacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-left">
                                                <strong>$<?php echo e(number_format($denominacion, 2)); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" 
                                                       min="0" 
                                                       max="999"
                                                       class="denomination-input2 contar-input-closure"
                                                       data-denominacion="<?php echo e($denominacion); ?>" 
                                                       placeholder="0"
                                                       autocomplete="off">
                                            </td>
                                            <td class="text-right">
                                                <span class="subtotal-closure">$0.00</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="total-row">
                                            <td colspan="2" class="text-right">
                                                <strong>Total Efectivo:</strong>
                                            </td>
                                            <td class="text-right">
                                                <strong><span id="total-closure">$0.00</span></strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="closure-form-section">
                        <div class="section-container">
                            <h4 class="section-title">Resumen de Cierre</h4>
                            <div class="form-grid">
                                
                                <!-- Total Gastos -->
                                <div class="input-group">
                                    <label for="total-gastos-closure">Total Gastos</label>
                                    <input type="number" 
                                           id="total-gastos-closure" 
                                           class="form-control"
                                           value="<?php echo e($totalExpenses ?? 0); ?>" 
                                           data-gastos-bd="<?php echo e($totalExpenses ?? 0); ?>"
                                           step="0.01" 
                                           readonly
                                           tabindex="-1">
                                </div>

                                <!-- Ventas en Efectivo (se actualiza automáticamente) -->
                                <div class="input-group">
                                    <label for="ventas-efectivo-closure">
                                        Ventas en Efectivo
                                        <span class="label-hint">(Calculado automáticamente)</span>
                                    </label>
                                    <input type="number" 
                                           id="ventas-efectivo-closure" 
                                           class="form-control"
                                           value="0" 
                                           step="0.01"
                                           readonly
                                           tabindex="-1">
                                </div>

                                <!-- Ventas QR -->
                                <div class="input-group">
                                    <label for="ventas-qr-closure">Ventas QR</label>
                                    <input type="number" 
                                           id="ventas-qr-closure" 
                                           class="form-control"
                                           value="<?php echo e($totalSalesQR ?? 0); ?>" 
                                           step="0.01"
                                           min="0"
                                           autocomplete="off">
                                </div>

                                <!-- Ventas Tarjeta -->
                                <div class="input-group">
                                    <label for="ventas-tarjeta-closure">Ventas Tarjeta</label>
                                    <input type="number" 
                                           id="ventas-tarjeta-closure" 
                                           class="form-control"
                                           value="<?php echo e($totalSalesCard ?? 0); ?>" 
                                           step="0.01"
                                           min="0"
                                           autocomplete="off">
                                </div>
                                
                                <!-- Acciones del formulario -->
                                <div class="form-actions">
                                    <button type="button" 
                                            class="btn btn-secondary" 
                                            onclick="closeInternalModalClosure()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="button" 
        class="btn btn-primary btn-sm save-btn" 
        onclick="guardarCierreUnificado()">
    <i class="fas fa-save mr-1"></i> Guardar Cierre
    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php else: ?>
        
        <div class="closure-header">
            <h3 class="closure-title">Cierre de Caja Chica</h3>
            <button type="button" onclick="closeInternalModalClosure()" class="closure-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="closure-scroll-content">
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>No hay caja chica abierta</strong>
                    <p>Debe abrir una caja chica antes de poder registrar un cierre.</p>
                </div>
            </div>
            <div class="no-petty-cash-actions">
                <button onclick="window.location.href='<?php echo e(route('petty-cash.create')); ?>'" 
                        class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Abrir Nueva Caja Chica
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>




<style>
    #modal-closure-internal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.95);
        background: white;
        z-index: 10002;
        display: none;
        flex-direction: column;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 95%;
        max-width: 900px;
        max-height: 85vh;
        overflow: hidden;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #modal-closure-internal.active {
        display: flex;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    /* Overlay del modal */
    .closure-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 10001;
    backdrop-filter: blur(2px);
    
    /* ✅ CRÍTICO: Oculto por defecto */
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}
    
    .closure-overlay.active {
    display: block !important;
    opacity: 1;
}

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ========================================== */
    /* HEADER DEL MODAL */
    /* ========================================== */
    
    .closure-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #f8fafc, #ffffff);
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .closure-title::before {
        content: '💰';
        font-size: 1.75rem;
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
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }
    
    .closure-close-btn:hover {
        background: #fee2e2;
        color: #dc2626;
        transform: rotate(90deg);
    }

    /* ========================================== */
    /* CONTENIDO DESPLAZABLE */
    /* ========================================== */
    
    .closure-scroll-content {
        padding: 1.5rem;
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: calc(85vh - 90px);
    }

    .closure-scroll-content::-webkit-scrollbar {
        width: 8px;
    }

    .closure-scroll-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .closure-scroll-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .closure-scroll-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .modal-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* ========================================== */
    /* GRID PRINCIPAL (2 COLUMNAS) */
    /* ========================================== */
    
    .closure-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    /* ========================================== */
    /* CONTENEDORES DE SECCIÓN */
    /* ========================================== */
    
    .section-container {
        padding: 1.25rem;
        background: linear-gradient(to bottom, #f8fafc, #ffffff);
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 1rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }

    /* ========================================== */
    /* SECCIÓN DE GASTOS */
    /* ========================================== */
    
    .expenses-section {
        background: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .expenses-title {
        margin: 0;
        font-size: 1.125rem;
        color: #1f2937;
        font-weight: 600;
    }
    
    .expenses-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .expense-row {
        display: grid;
        grid-template-columns: 1.5fr 1.5fr 120px 50px;
        gap: 0.75rem;
        align-items: center;
        padding: 0.5rem;
        background: #f9fafb;
        border-radius: 0.375rem;
        transition: background 0.2s;
    }

    .expense-row:hover {
        background: #f3f4f6;
    }
    
    .expense-field {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    
    .expense-input {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
    }
    
    .expense-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .expense-input::placeholder {
        color: #9ca3af;
    }
    
    .expense-actions {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* ========================================== */
    /* TABLA DE DENOMINACIONES */
    /* ========================================== */
    
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
        background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
        padding: 0.75rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #cbd5e1;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    
    .denominations-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .denominations-table tbody tr:hover {
        background: #f9fafb;
    }
    
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    
    .total-row {
        background: linear-gradient(to bottom, #dbeafe, #bfdbfe) !important;
        font-weight: 600;
        border-top: 2px solid #3b82f6;
    }

    .total-row td {
        padding: 1rem 0.75rem;
        color: #1e40af;
        font-size: 1rem;
    }
    
    .denomination-input {
        width: 100%;
        max-width: 80px;
        padding: 0.5rem;
        border: 2px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-align: center;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    .denomination-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        transform: scale(1.05);
    }

    .denomination-input:hover {
        border-color: #93c5fd;
    }

    .subtotal-closure {
        font-weight: 600;
        color: #059669;
    }

    /* ========================================== */
    /* FORMULARIO DE CIERRE */
    /* ========================================== */
    
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
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .label-hint {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 400;
    }
    
    .form-control {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
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
        border-style: dashed;
    }

   

    /* ========================================== */
    /* BOTONES */
    /* ========================================== */
    
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
        background: linear-gradient(to bottom, #10b981, #059669);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .btn-primary:hover {
        background: linear-gradient(to bottom, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-secondary {
        background: linear-gradient(to bottom, #6b7280, #4b5563);
        color: white;
        box-shadow: 0 2px 4px rgba(107, 114, 128, 0.2);
    }
    
    .btn-secondary:hover {
        background: linear-gradient(to bottom, #4b5563, #374151);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(107, 114, 128, 0.3);
    }
    
    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.5rem;
        width: 40px;
        height: 40px;
        border-radius: 0.375rem;
    }
    
    .btn-danger:hover {
        background: #fecaca;
        transform: scale(1.1) rotate(5deg);
    }
    
    .add-expense-btn {
        background: linear-gradient(to bottom, #dbeafe, #bfdbfe);
        color: #1e40af;
        border: 1px solid #3b82f6;
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
        background: linear-gradient(to bottom, #bfdbfe, #93c5fd);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
    }
    
    .form-actions {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* ========================================== */
    /* ALERTA DE ADVERTENCIA */
    /* ========================================== */
    
    .alert-warning {
        background: linear-gradient(to right, #fef3c7, #fde68a);
        border: 2px solid #fbbf24;
        color: #92400e;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 4px rgba(251, 191, 36, 0.2);
    }

    .alert-warning i {
        font-size: 2rem;
        flex-shrink: 0;
    }

    .alert-warning strong {
        display: block;
        font-size: 1.125rem;
        margin-bottom: 0.25rem;
    }

    .alert-warning p {
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .no-petty-cash-actions {
        text-align: center;
        margin-top: 2rem;
    }

    /* ========================================== */
    /* RESPONSIVE DESIGN */
    /* ========================================== */
    
    @media (max-width: 768px) {
        #modal-closure-internal {
            width: 98%;
            max-height: 90vh;
            margin: 1vh;
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
            padding: 0.5rem 0.375rem;
        }

        .denomination-input {
            max-width: 60px;
            padding: 0.375rem;
        }

        .closure-title {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .closure-scroll-content {
            padding: 0.75rem;
        }

        .section-title {
            font-size: 1rem;
        }

        .expenses-title {
            font-size: 1rem;
        }

        .expenses-section {
            padding: 1rem;
        }
    }

    /* ========================================== */
    /* ANIMACIONES */
    /* ========================================== */
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .expense-row {
        animation: slideIn 0.3s ease;
    }
</style><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/modal-content.blade.php ENDPATH**/ ?>