<div id="proforma-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#203363]">Reserva de Pedido</h3>
            <button type="button" onclick="closeProformaModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="proforma-form" class="space-y-4" onsubmit="saveProforma(event)">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <div>
                <label class="block text-sm font-medium text-[#203363] mb-1">Nombre del Cliente*</label>
                <input type="text" name="customer_name" id="proforma-customer-name" class="w-full border border-gray-300 rounded-md p-2 focus:border-[#203363] focus:ring-2 focus:ring-[#203363]" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-[#203363] mb-1">Notas Adicionales</label>
                <textarea name="notes" id="proforma-notes" class="w-full border border-gray-300 rounded-md p-2 focus:border-[#203363] focus:ring-2 focus:ring-[#203363]"></textarea>
            </div>
            
            <div id="proforma-summary" class="border-t border-gray-200 pt-4">
                <!-- Aquí se mostrará el resumen del pedido -->
            </div>
            
            <div class="flex justify-end space-x-2 pt-4">
                <button type="button" onclick="closeProformaModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                    <i class="fas fa-save mr-2"></i> Guardar Reserva
                </button>
            </div>
        </form>
    </div>
</div>   <?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/partials/proforma-modal.blade.php ENDPATH**/ ?>