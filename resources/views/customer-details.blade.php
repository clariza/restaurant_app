<!-- Contenedor principal flex -->
<div class="flex h-screen">
  <!-- Vista 1 (75% del ancho) -->
  <div class="w-3/4 overflow-y-auto p-4">
    <!-- Contenido de la primera vista (sin cambios) -->
    <div class="w-full bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Detalles del Pedido</h2>
    <form id="customer-details-form">
        @csrf

        <!-- Sección para mostrar los detalles del pago -->
        <div id="payment-details-section" class="mb-6">
            <h3 class="text-lg font-bold mb-4 text-[#203363]">Detalles de Pago</h3>
            <!-- Los detalles del pago se agregarán aquí dinámicamente -->
        </div>

        <!-- Campos de detalles del cliente -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-4 text-[#203363]">Detalles del Cliente</h3>
            <div class="mb-4">
                <label for="customer-name" class="block text-sm font-medium text-[#203363]">Nombre del Cliente</label>
                <input type="text" id="customer-name" name="customer_name" class="w-full border rounded-lg p-2" required>
            </div>
            <div class="mb-4">
                <label for="customer-email" class="block text-sm font-medium text-[#203363]">Correo Electrónico</label>
                <input type="email" id="customer-email" name="customer_email" class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label for="customer-phone" class="block text-sm font-medium text-[#203363]">Teléfono</label>
                <input type="tel" id="customer-phone" name="customer_phone" class="w-full border rounded-lg p-2">
            </div>
        </div>

        <!-- Botones para procesar el pedido o volver atrás -->
        <div class="flex justify-between">
            <button type="button" onclick="goBack()" class="bg-gray-400 text-white py-2 px-4 rounded-lg hover:bg-gray-500 transition-colors">
                Atrás
            </button>
            <button type="button" onclick="processOrder()" class="bg-[#203363] text-white py-2 px-4 rounded-lg hover:bg-[#47517c] transition-colors">
                Procesar Pedido
            </button>
        </div>
    </form>
</div>
</div>
</div>