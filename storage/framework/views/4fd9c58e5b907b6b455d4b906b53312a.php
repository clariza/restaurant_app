<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Nueva Sucursal</h1>
        </div>
    </div>

    <!-- Formulario -->
    <form action="<?php echo e(route('branches.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            
            <!-- Información Básica -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Información Básica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--primary-color)]">
                            Nombre de la Sucursal <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: Sucursal Centro">
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

                    <!-- Código -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-[var(--primary-color)]">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 font-mono <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: SUC-01">
                        <?php $__errorArgs = ['code'];
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
                        <input type="text" name="address" id="address" value="<?php echo e(old('address')); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: Av. Principal 123">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Ciudad -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-[var(--primary-color)]">
                                Ciudad
                            </label>
                            <input type="text" name="city" id="city" value="<?php echo e(old('city')); ?>"
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

                        <!-- Departamento -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-[var(--primary-color)]">
                                Departamento
                            </label>
                            <input type="text" name="state" id="state" value="<?php echo e(old('state')); ?>"
                                class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="Ej: La Paz">
                            <?php $__errorArgs = ['state'];
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
            </div>

            <!-- Contacto -->
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
                        <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone')); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: +591 2 1234567">
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
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Ej: sucursal@empresa.com">
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

            <!-- Configuración -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-[var(--primary-color)] mb-4 border-b pb-2">
                    Configuración
                </h3>
                <div class="space-y-3">
                    <!-- Es Principal -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_main" id="is_main" value="1"
                            <?php echo e(old('is_main') ? 'checked' : ''); ?>

                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_main" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Establecer como sucursal principal
                        </label>
                    </div>

                    <!-- Está Activa -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_active" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Sucursal activa
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex space-x-4 justify-end pt-4 border-t">
                <a href="<?php echo e(route('branches.index')); ?>"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-300">
                    Atrás
                </a>
                <button type="submit"
                    class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--secondary-color)] transition-colors duration-300">
                    Guardar
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/branches/create.blade.php ENDPATH**/ ?>