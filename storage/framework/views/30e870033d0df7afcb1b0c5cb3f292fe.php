

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Lista de Categorías</h1>
    
    <!-- Botón para abrir el modal -->
    <button onclick="openModal()" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-[var(--secondary-color)] transition duration-200">
        <i class="fas fa-plus mr-2"></i>Crear Categoría
    </button>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal content -->
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-[var(--primary-color)]">Crear Nueva Categoría</h3>
                <form action="<?php echo e(route('categories.store')); ?>" method="POST" class="mt-2">
                    <?php echo csrf_field(); ?>
                    <div class="mt-4">
                        <label for="name" class="block text-sm font-medium text-[var(--table-data-color)]">Nombre</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm" required>
                    </div>
                    <div class="mt-4">
                        <label for="icon" class="block text-sm font-medium text-[var(--table-data-color)]">Icono</label>
                        <input type="text" name="icon" id="icon" class="mt-1 block w-full px-3 py-2 border border-[var(--tertiary-color)] rounded-md shadow-sm focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm">
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600 transition duration-200">Cancelar</button>
                        <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Categorías -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Icono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($categoria->name); ?></td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]"><?php echo e($categoria->icon); ?></td>
                    <td class="px-6 py-4">
                        <!-- Icono de Editar -->
                        <a href="<?php echo e(route('categories.edit', $categoria->id)); ?>" class="text-[var(--primary-color)] hover:text-[var(--secondary-color)] mr-2">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <!-- Icono de Eliminar -->
                        <form action="<?php echo e(route('categories.destroy', $categoria->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script para manejar el modal -->
<script>
    function openModal() {
        document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    // Cerrar el modal al hacer clic fuera de él
    document.addEventListener('click', (event) => {
        const modal = document.getElementById('modal');
        if (event.target === modal) {
            closeModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/categories/index.blade.php ENDPATH**/ ?>