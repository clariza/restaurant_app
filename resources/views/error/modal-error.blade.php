<div class="text-center py-12">
    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
    <p class="text-gray-600 mb-4">Error al cargar la información de caja chica</p>
    <p class="text-sm text-gray-500 mb-4">{{ $error }}</p>
    <button onclick="openPettyCashModal()" class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#47517c]">
        <i class="fas fa-redo mr-2"></i> Reintentar
    </button>
</div>