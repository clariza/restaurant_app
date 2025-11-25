<div id="print-preview-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[1000]">
    <div class="modal-container bg-white rounded-lg p-6 w-full max-w-md mx-auto my-8">
        <div class="modal-header flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#203363]">Vista previa de impresión</h3>
            <button onclick="closePrintPreview()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="print-preview-content" class="modal-body bg-white p-4 border border-gray-300 mb-4 max-h-[60vh] overflow-y-auto"></div>
        <div class="modal-footer flex justify-end space-x-2">
            <button onclick="closePrintPreview()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                Cancelar
            </button>
            <button onclick="confirmPrint()" class="bg-[#203363] text-white px-4 py-2 rounded-lg hover:bg-[#47517c]">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>
</div><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/partials/print-preview-modal.blade.php ENDPATH**/ ?>