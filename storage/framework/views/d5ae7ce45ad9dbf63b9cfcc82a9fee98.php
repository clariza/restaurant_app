

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Editar Cliente</h1>
        </div>
    </div>

    <!-- Formulario -->
    <form action="<?php echo e(route('clients.update', $client)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            
            <!-- Información Personal -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--primary-color)]">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name', $client->name)); ?>" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: Juan">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-[var(--primary-color)]">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo e(old('last_name', $client->last_name)); ?>" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: Pérez">
                        <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Documento de Identidad -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Documento de Identidad
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tipo de Documento -->
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-[var(--primary-color)]">
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select name="document_type" id="document_type" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="CI" <?php echo e(old('document_type', $client->document_type) == 'CI' ? 'selected' : ''); ?>>CI - Carnet de Identidad</option>
                            <option value="NIT" <?php echo e(old('document_type', $client->document_type) == 'NIT' ? 'selected' : ''); ?>>NIT</option>
                            <option value="Pasaporte" <?php echo e(old('document_type', $client->document_type) == 'Pasaporte' ? 'selected' : ''); ?>>Pasaporte</option>
                        </select>
                        <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Número de Documento -->
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-[var(--primary-color)]">
                            Número de Documento
                        </label>
                        <input type="text" name="document_number" id="document_number" value="<?php echo e(old('document_number', $client->document_number)); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 font-mono <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: 1234567">
                        <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Información de Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-[var(--primary-color)]">
                            Teléfono
                        </label>
                        <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone', $client->phone)); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: +591 71234567">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--primary-color)]">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="<?php echo e(old('email', $client->email)); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: cliente@email.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Ubicación
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <!-- Dirección -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-[var(--primary-color)]">
                            Dirección
                        </label>
                        <input type="text" name="address" id="address" value="<?php echo e(old('address', $client->address)); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: Calle 123, Zona Centro">
                        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-[var(--primary-color)]">
                            Ciudad
                        </label>
                        <input type="text" name="city" id="city" value="<?php echo e(old('city', $client->city)); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: La Paz">
                        <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Información Adicional
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <!-- Notas -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-[var(--primary-color)]">
                            Notas
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Observaciones o notas adicionales sobre el cliente"><?php echo e(old('notes', $client->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Cliente Activo -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            <?php echo e(old('is_active', $client->is_active) ? 'checked' : ''); ?>

                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_active" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Cliente activo
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex space-x-4 justify-end pt-4 border-t">
                <a href="<?php echo e(route('clients.index')); ?>"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                    Atrás
                </a>
                <button type="submit"
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition-colors duration-300">
                    Actualizar
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/clients/edit.blade.php ENDPATH**/ ?>