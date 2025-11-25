

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center">
            <!-- <i class="fas fa-table text-2xl mr-2 text-[var(--primary-color)]"></i> -->
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Crear Usuario</h1>
        </div>
    </div>

    <!-- Formulario -->
    <form action="<?php echo e(route('users.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Campo Nombre -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-[var(--primary-color)]">Nombre</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50" required>
            </div>

            <!-- Campo Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-[var(--primary-color)]">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50" required>
            </div>

            <!-- Campo Contraseña -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-[var(--primary-color)]">Contraseña</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50" required>
            </div>

            <!-- Campo Rol -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-[var(--primary-color)]">Rol</label>
                <select name="role" id="role" class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50" required>
                    <option value="admin">Admin</option>
                    <option value="vendedor" selected>Vendedor</option>
                </select>
            </div>

            <!-- Botones Atrás y Guardar -->
            <div class="flex space-x-4 justify-end">
                <!-- Botón Atrás -->
                <a href="<?php echo e(route('users.index')); ?>" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                    Atrás
                </a>

                <!-- Botón Guardar -->
                <button type="submit" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition-colors duration-300">
                    Guardar
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/users/create.blade.php ENDPATH**/ ?>