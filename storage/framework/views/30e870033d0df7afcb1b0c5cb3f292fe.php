<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-[var(--primary-color)]">Lista de Categorías</h1>

    
    <button onclick="openModal()"
        class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-[var(--secondary-color)] transition duration-200">
        <i class="fas fa-plus mr-2"></i>Crear Categoría
    </button>

    
    <?php if(session('success')): ?>
        <div id="success-alert"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php echo e(session('success')); ?>

            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
                onclick="document.getElementById('success-alert').remove()">✕</span>
        </div>
    <?php endif; ?>

    
    <div id="save-indicator"
        class="hidden mb-3 text-sm text-green-600 flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>Orden guardado correctamente</span>
    </div>
    <div id="save-error"
        class="hidden mb-3 text-sm text-red-600 flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        <span>Error al guardar el orden</span>
    </div>

    
    <p class="text-sm text-gray-500 mb-3 flex items-center gap-2">
        <i class="fas fa-grip-vertical text-gray-400"></i>
        Arrastra las filas para cambiar el orden de las categorías en el menú
    </p>

    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-[var(--primary-color)]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase w-8"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Orden</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Icono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody id="sortable-categories" class="divide-y divide-gray-200">
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-grab active:cursor-grabbing"
                    data-id="<?php echo e($categoria->id); ?>">
                    
                    <td class="px-4 py-4 text-gray-400 drag-handle select-none">
                        <i class="fas fa-grip-vertical text-lg"></i>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-sm order-badge">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-xs font-bold">
                            <?php echo e($index + 1); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 text-[var(--table-data-color)] font-medium">
                        <?php echo e($categoria->name); ?>

                    </td>
                    <td class="px-6 py-4 text-[var(--table-data-color)]">
                        <i class="<?php echo e($categoria->icon); ?> mr-1"></i>
                        <span class="text-sm text-gray-500"><?php echo e($categoria->icon); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?php echo e(route('categories.edit', $categoria->id)); ?>"
                            class="text-[var(--primary-color)] hover:text-[var(--secondary-color)] mr-4">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="<?php echo e(route('categories.destroy', $categoria->id)); ?>"
                            method="POST" class="inline"
                            onsubmit="return confirm('¿Eliminar esta categoría?')">
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


<div id="modal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-[var(--primary-color)]">
                Crear Nueva Categoría
            </h3>
            <form action="<?php echo e(route('categories.store')); ?>" method="POST" class="mt-2">
                <?php echo csrf_field(); ?>
                <div class="mt-4 text-left">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" id="name"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm"
                        required>
                </div>
                <div class="mt-4 text-left">
                    <label for="icon" class="block text-sm font-medium text-gray-700">
                        Icono <span class="text-gray-400 text-xs">(clase FontAwesome, ej: fas fa-utensils)</span>
                    </label>
                    <input type="text" name="icon" id="icon"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] sm:text-sm"
                        placeholder="fas fa-utensils">
                    <div class="mt-2 text-gray-500 text-sm">
                        Vista previa: <i id="icon-preview" class="fas fa-question ml-1"></i>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-600 transition duration-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition duration-200">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
    // ── Modal ──────────────────────────────────────────────────
    function openModal() {
        document.getElementById('modal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    document.addEventListener('click', (e) => {
        if (e.target === document.getElementById('modal')) closeModal();
    });

    // Vista previa del icono en el modal
    document.getElementById('icon').addEventListener('input', function () {
        const preview = document.getElementById('icon-preview');
        preview.className = this.value || 'fas fa-question';
    });

    // ── Drag & Drop con SortableJS ─────────────────────────────
    const tbody = document.getElementById('sortable-categories');

    const sortable = Sortable.create(tbody, {
        animation: 150,
        handle: '.drag-handle',         // solo arrastrar desde el ícono grip
        ghostClass: 'bg-blue-50',       // color del elemento fantasma
        dragClass: 'opacity-50',
        onEnd: function () {
            updateOrderBadges();
            saveOrder();
        }
    });

    // Actualizar los números de orden visibles tras cada movimiento
    function updateOrderBadges() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const badge = row.querySelector('.order-badge span');
            if (badge) badge.textContent = index + 1;
        });
    }

    // Enviar el nuevo orden al servidor vía AJAX
    function saveOrder() {
        const rows   = tbody.querySelectorAll('tr[data-id]');
        const order  = Array.from(rows).map(row => parseInt(row.dataset.id));
        const csrf   = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('<?php echo e(route("categories.reorder")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) showIndicator('save-indicator');
            else showIndicator('save-error');
        })
        .catch(() => showIndicator('save-error'));
    }

    // Mostrar notificación temporal
    function showIndicator(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 2500);
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/categories/index.blade.php ENDPATH**/ ?>