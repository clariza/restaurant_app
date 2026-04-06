<!-- Overlay para el modal de cierre -->
<div id="closure-internal-overlay" class="closure-overlay"></div>
<!-- Modal de cierre interno -->
<div class="closure-internal-modal">
    <?php if($openPettyCash): ?>
        <input id="petty_cash_id_closure" hidden name="petty_cash_id_closure" value="<?php echo e($openPettyCash->id); ?>">
        <?php
            // $existingExpenses = solo source='modal' (filtrado en el controlador)
            // $totalExpenses     = todos los gastos modal+form (del controlador)
            $totalExpensesModal = $existingExpenses->sum('amount');
            $totalExpensesForm  = ($totalExpenses ?? 0) - $totalExpensesModal;
        ?>
        <div class="closure-scroll-content">
            <div class="modal-content">
                <!-- ══════════════ SECCIÓN DE GASTOS ══════════════ -->
                <div class="expenses-section">
                    <div class="expenses-header">
                        <h4 class="expenses-title">Registro de Gastos</h4>
                        <button type="button" class="add-expense-btn" onclick="addExpenseModalClosure()">
                            <i class="fas fa-plus"></i> Agregar Gasto
                        </button>
                    </div>
                    <div class="expense-columns-header">
                        <div>Nombre del gasto</div>
                        <div>Descripción / Categoría</div>
                        <div>Monto</div>
                        <div>Acciones</div>
                    </div>
                    <div class="expenses-container" id="expensesContainerClosure">
                        
                        <?php if(isset($existingExpenses) && $existingExpenses->count() > 0): ?>
                            <?php $__currentLoopData = $existingExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="expense-row expense-row--saved" data-expense-id="<?php echo e($expense->id); ?>">
                                    <div class="expense-cell">
                                        <input type="text"
                                               class="expense-input expense-input--saved"
                                               value="<?php echo e($expense->expense_name); ?>"
                                               readonly>
                                    </div>
                                    <div class="expense-cell">
                                        <input type="text"
                                               class="expense-input expense-input--saved"
                                               value="<?php echo e($expense->description ?? ''); ?>"
                                               readonly>
                                    </div>
                                    <div class="expense-cell">
                                        <input type="number"
                                               class="expense-input expense-input--saved"
                                               value="<?php echo e(number_format($expense->amount, 2, '.', '')); ?>"
                                               step="0.01"
                                               readonly>
                                    </div>
                                    <div class="expense-cell expense-actions-cell">
                                        <span class="expense-saved-badge">
                                            <i class="fas fa-check-circle"></i> Guardado
                                        </span>
                                        <?php if(auth()->user()->role === 'admin'): ?>
                                        <button type="button"
                                            class="exp-btn exp-btn--del js-delete-expense"
                                            data-expense-id="<?php echo e($expense->id); ?>"
                                            title="Eliminar gasto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        
                        <div class="expense-row expense-row--new">
                            <div class="expense-cell">
                                <input type="text" class="expense-input"
                                       placeholder="Nombre del gasto"
                                       name="expense_name[]" autocomplete="off">
                            </div>
                            <div class="expense-cell">
                                <input type="text" class="expense-input"
                                       placeholder="Descripción/Categoría"
                                       name="expense_description[]" autocomplete="off">
                            </div>
                            <div class="expense-cell">
                                <input type="number" class="expense-input"
                                       placeholder="Monto" step="0.01" min="0"
                                       name="expense_amount[]" autocomplete="off">
                            </div>
                            <div class="expense-cell expense-actions-cell">
                                <button type="button" class="exp-btn exp-btn--save js-save-expense"
                                    title="Guardar gasto"><i class="fas fa-save"></i></button>
                                <?php if(auth()->user()->role === 'admin'): ?>
                                <button type="button" class="exp-btn exp-btn--del"
                                        onclick="removeExpenseRow(this)" title="Eliminar fila">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════ DENOMINACIONES + RESUMEN ══════════════ -->
                <div class="closure-grid">
                    <div class="denominations-section">
                        <div class="section-container">
                            <h4 class="section-title">Conteo de Efectivo</h4>
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
                                                <strong>Bs. <?php echo e(number_format($denominacion, 2)); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" min="0" max="999"
                                                       class="denomination-input2 contar-input-closure"
                                                       data-denominacion="<?php echo e($denominacion); ?>"
                                                       placeholder="0" autocomplete="off">
                                            </td>
                                            <td class="text-right">
                                                <span class="subtotal-closure">Bs.0.00</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="total-row">
                                            <td colspan="2" class="text-right"><strong>Total Efectivo:</strong></td>
                                            <td class="text-right">
                                                <strong><span id="total-closure">Bs.0.00</span></strong>
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
                                <div class="input-group">
                                    <label for="total-gastos-closure">Total Gastos</label>
                                    
                                    <input type="number"
                                           id="total-gastos-closure"
                                           class="form-control"
                                           value="<?php echo e(number_format($totalExpenses ?? 0, 2, '.', '')); ?>"
                                           data-gastos-bd="<?php echo e(number_format($totalExpensesModal, 2, '.', '')); ?>"
                                           data-gastos-form="<?php echo e(number_format($totalExpensesForm, 2, '.', '')); ?>"
                                           step="0.01" readonly tabindex="-1">
                                </div>
                                <div class="input-group">
                                    <label for="ventas-efectivo-closure">
                                        Ventas en Efectivo
                                        <span class="label-hint">(Calculado desde el conteo de efectivo)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               id="ventas-efectivo-closure"
                                               class="form-control efectivo-readonly"
                                               value="0.00"
                                               step="0.01"
                                               readonly
                                               tabindex="-1">
                                        
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label for="ventas-qr-closure">Ventas QR</label>
                                    <input type="number" id="ventas-qr-closure"
                                           class="form-control"
                                           value="<?php echo e(number_format($totalSalesQR ?? 0, 2, '.', '')); ?>"
                                           step="0.01" min="0" autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <label for="ventas-tarjeta-closure">Ventas Tarjeta</label>
                                    <input type="number" id="ventas-tarjeta-closure"
                                           class="form-control"
                                           value="<?php echo e(number_format($totalSalesCard ?? 0, 2, '.', '')); ?>"
                                           step="0.01" min="0" autocomplete="off">
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary"
                                            onclick="closeInternalModalClosure()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm save-btn"
                                            onclick="guardarCierreUnificado()">
                                        <i class="fas fa-save mr-1"></i> Guardar Cierre
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════ NOTAS ══════════════ -->
                <div class="closure-notes-section">
                    <label for="closure-notes-modal">
                        <i class="fas fa-sticky-note" style="color:#6b7280; font-size:14px;"></i>
                        Notas de cierre
                        <span class="label-hint">(opcional)</span>
                    </label>
                    <textarea id="closure-notes-modal" name="closure_notes" rows="3"
                              maxlength="500"
                              placeholder="Observaciones, incidencias, comentarios del cierre..."
                              class="form-control" style="resize:none; margin-top:.375rem;"></textarea>
                    <div style="text-align:right; font-size:.75rem; color:#9ca3af; margin-top:2px;">
                        <span id="notes-char-count-modal">0</span>/500
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="closure-header" style="justify-content: flex-end;">
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
.expense-row {
    display: grid;
    grid-template-columns: minmax(120px, 1.5fr) minmax(120px, 1.5fr) 110px 80px;
    gap: .5rem; align-items: center; padding: .375rem .5rem;
    background: #f9fafb; border-radius: .375rem;
    transition: background .2s; animation: slideIn .25s ease; min-width: 0;
}
.expense-row:hover        { background: #f3f4f6; }
.expense-row--saved       { background: #f0fdf4; border-left: 3px solid #22c55e; }
.expense-row--saved:hover { background: #dcfce7; }
.expense-row--new         { border-left: 3px solid #e5e7eb; }
.expense-columns-header {
    display: grid;
    grid-template-columns: minmax(120px, 1.5fr) minmax(120px, 1.5fr) 110px 80px;
    gap: .5rem; padding: .25rem .5rem .375rem;
    font-size: .7rem; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .05em;
    border-bottom: 1px solid #e5e7eb; margin-bottom: .375rem;
}
.expense-cell { min-width: 0; display: flex; align-items: center; }
.expense-actions-cell {
    display: flex; align-items: center; justify-content: flex-start;
    gap: .3rem; flex-wrap: nowrap;
}
.expense-cell .expense-input {
    width: 100%; min-width: 0; padding: .4rem .5rem;
    border: 1px solid #d1d5db; border-radius: .3125rem;
    font-size: .8rem; background: white;
    transition: border-color .15s, box-shadow .15s; box-sizing: border-box;
}
.expense-cell .expense-input:focus {
    outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1);
}
.expense-cell .expense-input::placeholder { color: #9ca3af; }
.expense-cell .expense-input--saved {
    background: #f0fdf4; color: #374151; border-color: #d1fae5; cursor: default;
}
.expense-cell .expense-input--saved:focus { border-color: #d1fae5; box-shadow: none; }
.exp-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border: none; border-radius: .3125rem;
    cursor: pointer; font-size: .8rem; flex-shrink: 0; transition: all .18s;
}
.exp-btn--save {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white; box-shadow: 0 2px 4px rgba(59,130,246,.25);
}
.exp-btn--save:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); transform: translateY(-1px); }
.exp-btn--del         { background: #fee2e2; color: #dc2626; }
.exp-btn--del:hover   { background: #fecaca; transform: scale(1.08); }
.expense-saved-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .65rem; font-weight: 700; color: #16a34a;
    background: #dcfce7; border: 1px solid #bbf7d0;
    border-radius: 9999px; padding: .15rem .45rem; white-space: nowrap; flex-shrink: 0;
}
.expenses-container { display: flex; flex-direction: column; gap: .375rem; }
.closure-notes-section {
    background: white; padding: 1.25rem 1.5rem; border-radius: .5rem;
    border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.closure-notes-section label {
    font-size: .875rem; font-weight: 500; color: #374151;
    display: flex; align-items: center; gap: .375rem; margin-bottom: 0;
}
.closure-internal-modal { display: flex; flex-direction: column; height: 100%; min-height: 0; }
.closure-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.6);
    z-index: 10001; backdrop-filter: blur(2px);
    display: none; opacity: 0; transition: opacity .3s ease;
}
.closure-overlay.active { display: block !important; opacity: 1; }
.closure-header {
    padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb;
    background-color: #203363;
    display: flex; justify-content: flex-end; align-items: center; flex-shrink: 0;
}
.closure-close-btn {
    background: none; border: none; font-size: 1.5rem; color: white;
    cursor: pointer; padding: .5rem; border-radius: .375rem;
    transition: color .2s; display: flex; align-items: center;
    justify-content: center; width: 40px; height: 40px;
}
.closure-close-btn:hover { color: #d1d5db; }
.closure-scroll-content {
    padding: 1.5rem; flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden;
}
.closure-scroll-content::-webkit-scrollbar { width: 8px; }
.closure-scroll-content::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.closure-scroll-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.closure-scroll-content::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.modal-content { display: flex; flex-direction: column; gap: 1.5rem; }
.closure-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; }
.section-container {
    padding: 1.25rem; background: linear-gradient(to bottom,#f8fafc,#ffffff);
    border-radius: .5rem; border: 1px solid #e5e7eb;
    display: flex; flex-direction: column; height: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.section-title {
    font-size: 1.125rem; font-weight: 600; color: #1f2937;
    margin: 0 0 1rem 0; padding-bottom: .75rem; border-bottom: 2px solid #e5e7eb;
}
.expenses-section {
    background: white; padding: 1.5rem; border-radius: .5rem;
    border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.expenses-header {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: .625rem; gap: 1rem;
}
.expenses-title { margin: 0; font-size: 1.125rem; color: #1f2937; font-weight: 600; }
.add-expense-btn {
    background: linear-gradient(to bottom,#dbeafe,#bfdbfe);
    color: #1e40af; border: 1px solid #3b82f6;
    padding: .5rem 1rem; border-radius: .375rem; cursor: pointer;
    font-size: .8125rem; font-weight: 500;
    display: inline-flex; align-items: center; gap: .5rem; transition: all .2s;
}
.add-expense-btn:hover {
    background: linear-gradient(to bottom,#bfdbfe,#93c5fd);
    transform: translateY(-1px); box-shadow: 0 4px 8px rgba(59,130,246,.2);
}
.table-container { overflow-x: auto; flex: 1; }
.denominations-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.denominations-table th {
    background: linear-gradient(to bottom,#f1f5f9,#e2e8f0);
    padding: .75rem; font-weight: 600; color: #374151;
    border-bottom: 2px solid #cbd5e1; text-align: left; position: sticky; top: 0; z-index: 1;
}
.denominations-table td { padding: .75rem; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
.denominations-table tbody tr:hover { background: #f9fafb; }
.text-right { text-align: right; } .text-left { text-align: left; } .text-center { text-align: center; }
.total-row { background: linear-gradient(to bottom,#dbeafe,#bfdbfe) !important; font-weight: 600; border-top: 2px solid #3b82f6; }
.total-row td { padding: 1rem .75rem; color: #1e40af; font-size: 1rem; }
.denomination-input2 {
    width: 100%; max-width: 80px; padding: .5rem;
    border: 2px solid #d1d5db; border-radius: .375rem;
    font-size: .875rem; text-align: center; transition: all .2s; font-weight: 500;
}
.denomination-input2:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); transform: scale(1.05); }
.subtotal-closure { font-weight: 600; color: #059669; }
.form-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
.input-group { display: flex; flex-direction: column; gap: .375rem; }
.input-group label { font-size: .875rem; font-weight: 500; color: #374151; margin: 0; display: flex; align-items: center; gap: .25rem; }
.label-hint { font-size: .75rem; color: #6b7280; font-weight: 400; }
.form-control { width: 100%; padding: .625rem .75rem; border: 1px solid #d1d5db; border-radius: .375rem; font-size: .875rem; transition: all .2s; background: white; box-sizing: border-box; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.form-control[readonly] { background-color: #f9fafb; color: #6b7280; cursor: not-allowed; border-style: dashed; }
/* ── Wrapper del campo Ventas en Efectivo ── */

.efectivo-input-wrapper .form-control.efectivo-readonly:focus {
    box-shadow: none;
    border-color: #86efac;
}
.efectivo-badge {
    position: absolute;
    right: .5rem;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .65rem;
    font-weight: 700;
    color: #15803d;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 9999px;
    padding: .2rem .55rem;
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
}
/* Animación de pulso cuando el valor cambia */
@keyframes efectivoPulse {
    0%   { box-shadow: 0 0 0 0 rgba(34,197,94,.4); }
    70%  { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.efectivo-input-wrapper .form-control.efectivo-pulse {
    animation: efectivoPulse .5s ease-out;
}
.btn {
    padding: .5rem .875rem; border: none; border-radius: .375rem;
    font-size: .8125rem; font-weight: 500; cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem; white-space: nowrap;
}
.btn:disabled { opacity: .55; cursor: not-allowed; transform: none !important; }
.btn-primary { background: linear-gradient(to bottom,#10b981,#059669); color: white; box-shadow: 0 2px 4px rgba(16,185,129,.2); }
.btn-primary:hover { background: linear-gradient(to bottom,#059669,#047857); transform: translateY(-1px); }
.btn-secondary { background: linear-gradient(to bottom,#6b7280,#4b5563); color: white; }
.btn-secondary:hover { background: linear-gradient(to bottom,#4b5563,#374151); transform: translateY(-1px); }
.form-actions { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: .75rem; }
.alert-warning {
    background: linear-gradient(to right,#fef3c7,#fde68a); border: 2px solid #fbbf24; color: #92400e;
    padding: 1.5rem; border-radius: .5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;
}
.alert-warning i { font-size: 2rem; flex-shrink: 0; }
.alert-warning strong { display: block; font-size: 1.125rem; margin-bottom: .25rem; }
.alert-warning p { margin: 0; font-size: .875rem; }
.no-petty-cash-actions { text-align: center; margin-top: 2rem; }
@keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 768px) {
    .closure-scroll-content { padding: 1rem; }
    .closure-grid           { grid-template-columns: 1fr; gap: 1rem; }
    .expense-columns-header { display: none; }
    .expense-row { grid-template-columns: 1fr; gap: .375rem; padding: .625rem; border: 1px solid #e5e7eb; border-left-width: 3px; border-radius: .375rem; background: #fff; }
    .expense-row--saved   { background: #f0fdf4; }
    .expense-actions-cell { justify-content: flex-start; }
    .expenses-header      { flex-direction: column; align-items: flex-start; }
    .add-expense-btn      { width: 100%; justify-content: center; }
    .form-actions         { flex-direction: column; gap: .5rem; }
    .btn                  { width: 100%; }
    .denominations-table  { font-size: .8rem; }
    .denominations-table th, .denominations-table td { padding: .5rem .375rem; }
    .denomination-input2  { max-width: 60px; padding: .375rem; }
    .expenses-section     { padding: 1rem; }
    .efectivo-input-wrapper .form-control.efectivo-readonly { padding-right: 6rem; }
}
</style>
<?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/petty_cash/modal-content.blade.php ENDPATH**/ ?>