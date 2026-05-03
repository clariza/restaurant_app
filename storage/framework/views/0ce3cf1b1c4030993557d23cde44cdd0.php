<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-users mr-3 text-[#203363]"></i>
                Gestión de Clientes
            </h1>
            <p class="text-gray-600 mt-2">Administra los clientes del restaurante</p>
        </div>
        <a href="<?php echo e(route('clients.create')); ?>" 
           class="bg-[#203363] text-white px-6 py-3 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>
            Nuevo Cliente
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- ===================== FILTROS DE CUMPLEAÑOS ===================== -->
    <div class="bg-white rounded-lg shadow-md p-5 mb-6 border-l-4 border-[#203363]">
        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-birthday-cake mr-2 text-[#203363]"></i>
            Filtrar por Cumpleaños
        </h2>
        <form method="GET" action="<?php echo e(route('clients.index')); ?>" class="flex flex-wrap gap-4 items-end">
            <!-- Filtro: Mes -->
            <div class="flex flex-col min-w-[160px]">
                <label for="birthday_month" class="text-sm font-medium text-gray-600 mb-1">
                    <i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Mes
                </label>
                <select name="birthday_month" id="birthday_month"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#203363] focus:border-transparent">
                    <option value="">Todos los meses</option>
                    <option value="1"  <?php echo e(request('birthday_month') == '1'  ? 'selected' : ''); ?>>Enero</option>
                    <option value="2"  <?php echo e(request('birthday_month') == '2'  ? 'selected' : ''); ?>>Febrero</option>
                    <option value="3"  <?php echo e(request('birthday_month') == '3'  ? 'selected' : ''); ?>>Marzo</option>
                    <option value="4"  <?php echo e(request('birthday_month') == '4'  ? 'selected' : ''); ?>>Abril</option>
                    <option value="5"  <?php echo e(request('birthday_month') == '5'  ? 'selected' : ''); ?>>Mayo</option>
                    <option value="6"  <?php echo e(request('birthday_month') == '6'  ? 'selected' : ''); ?>>Junio</option>
                    <option value="7"  <?php echo e(request('birthday_month') == '7'  ? 'selected' : ''); ?>>Julio</option>
                    <option value="8"  <?php echo e(request('birthday_month') == '8'  ? 'selected' : ''); ?>>Agosto</option>
                    <option value="9"  <?php echo e(request('birthday_month') == '9'  ? 'selected' : ''); ?>>Septiembre</option>
                    <option value="10" <?php echo e(request('birthday_month') == '10' ? 'selected' : ''); ?>>Octubre</option>
                    <option value="11" <?php echo e(request('birthday_month') == '11' ? 'selected' : ''); ?>>Noviembre</option>
                    <option value="12" <?php echo e(request('birthday_month') == '12' ? 'selected' : ''); ?>>Diciembre</option>
                </select>
            </div>

            <!-- Filtro acceso rápido: hoy / esta semana / este mes -->
            <div class="flex flex-col min-w-[180px]">
                <label for="birthday_filter" class="text-sm font-medium text-gray-600 mb-1">
                    <i class="fas fa-filter mr-1 text-gray-400"></i> Acceso Rápido
                </label>
                <select name="birthday_filter" id="birthday_filter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#203363] focus:border-transparent">
                    <option value="">-- Seleccionar --</option>
                    <option value="today"      <?php echo e(request('birthday_filter') == 'today'      ? 'selected' : ''); ?>>🎂 Hoy</option>
                    <option value="this_week"  <?php echo e(request('birthday_filter') == 'this_week'  ? 'selected' : ''); ?>>📅 Esta semana</option>
                    <option value="this_month" <?php echo e(request('birthday_filter') == 'this_month' ? 'selected' : ''); ?>>🗓️ Este mes</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-[#203363] text-white px-5 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center text-sm shadow">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <?php if(request('birthday_month') || request('birthday_filter')): ?>
                    <a href="<?php echo e(route('clients.index')); ?>"
                       class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition duration-200 flex items-center text-sm">
                        <i class="fas fa-times mr-2"></i> Limpiar
                    </a>
                <?php endif; ?>
            </div>

            <!-- Indicador de resultados activos -->
            <?php if(request('birthday_month') || request('birthday_filter')): ?>
                <div class="flex items-center ml-2">
                    <span class="bg-[#203363] text-white text-xs px-3 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-birthday-cake"></i>
                        Filtrando cumpleaños
                    </span>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <!-- ================================================================ -->

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#203363]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i> Cliente
                        </th>
                        <!-- COLUMNA CAMBIADA: Documento → Cumpleaños -->
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-birthday-cake mr-1"></i> Cumpleaños
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-phone mr-1"></i> Contacto
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-map-marker-alt mr-1"></i> Ubicación
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-toggle-on mr-1"></i> Estado
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i> Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition duration-150
                            
                            <?php echo e($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d') ? 'bg-yellow-50' : ''); ?>">

                            <!-- Cliente -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-[#203363] rounded-full flex items-center justify-center relative">
                                        <i class="fas fa-user text-white"></i>
                                        
                                        <?php if($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d')): ?>
                                            <span class="absolute -top-1 -right-1 text-xs">🎂</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($client->full_name); ?>

                                        </div>
                                        <?php if($client->email): ?>
                                            <div class="text-sm text-gray-500">
                                                <?php echo e($client->email); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- CELDA CAMBIADA: Cumpleaños en lugar de Documento -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($client->birthdays): ?>
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 flex items-center gap-1">
                                            <i class="fas fa-calendar text-gray-400 w-4"></i>
                                            <?php echo e($client->birthdays->format('d/m/Y')); ?>

                                        </div>
                                        <div class="text-gray-500 text-xs mt-0.5">
                                            <?php
                                                $today     = now();
                                                $birthday  = $client->birthdays->setYear($today->year);
                                                if ($birthday->isPast() && !$birthday->isToday()) {
                                                    $birthday->addYear();
                                                }
                                                $daysLeft = (int) $today->diffInDays($birthday, false);
                                            ?>

                                            <?php if($client->birthdays->format('m-d') === $today->format('m-d')): ?>
                                                <span class="text-yellow-600 font-semibold flex items-center gap-1">
                                                    🎉 ¡Hoy es su cumpleaños!
                                                </span>
                                            <?php elseif($daysLeft <= 7): ?>
                                                <span class="text-orange-500 font-medium">
                                                    En <?php echo e($daysLeft); ?> día<?php echo e($daysLeft !== 1 ? 's' : ''); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400">
                                                    En <?php echo e($daysLeft); ?> días
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic text-sm">Sin fecha</span>
                                <?php endif; ?>
                            </td>

                            <!-- Contacto -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <?php if($client->phone): ?>
                                        <div class="flex items-center text-gray-900">
                                            <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($client->phone); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Sin teléfono</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Ubicación -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?php if($client->city): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-city text-gray-400 mr-2 w-4"></i>
                                            <?php echo e($client->city); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Sin ciudad</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Estado -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="toggleStatus(<?php echo e($client->id); ?>, <?php echo e($client->is_active ? 'true' : 'false'); ?>)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200
                                               <?php echo e($client->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200'); ?>"
                                        id="status-btn-<?php echo e($client->id); ?>">
                                    <i class="fas fa-<?php echo e($client->is_active ? 'check-circle' : 'times-circle'); ?> mr-1"></i>
                                    <span id="status-text-<?php echo e($client->id); ?>">
                                        <?php echo e($client->is_active ? 'Activo' : 'Inactivo'); ?>

                                    </span>
                                </button>
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <?php if(($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d')) || $client->is_active): ?>
        <button onclick="openCoupon(<?php echo e($client->id); ?>)"
        class="transition duration-200 <?php echo e($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d') ? 'text-red-500 hover:text-red-700' : 'text-amber-500 hover:text-amber-700'); ?>"
        title="<?php echo e($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d') ? 'Cupón cumpleaños' : 'Cupón descuento'); ?>">
        <i class="fas fa-ticket-alt"></i>
        </button>
        <?php endif; ?>
                                    <a href="<?php echo e(route('clients.show', $client)); ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('clients.edit', $client)); ?>" 
                                       class="text-yellow-600 hover:text-yellow-800 transition duration-200"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('clients.destroy', $client)); ?>" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 transition duration-200"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">No hay clientes registrados</p>
                                    <a href="<?php echo e(route('clients.create')); ?>" 
                                       class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200">
                                        <i class="fas fa-plus-circle mr-2"></i>Crear primer cliente
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if($clients->hasPages()): ?>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <?php echo e($clients->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    
<?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('clients.partials.coupon', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<script>
    function downloadCoupon(id) {
    const btn      = document.getElementById(`download-btn-${id}`);
    const couponEl = document.querySelector(`#coupon-preview-${id} .coupon-strip`);

    if (!couponEl) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML  = '⏳ Generando...';
    btn.disabled   = true;

    html2canvas(couponEl, {
        scale: 3,
        useCORS: true,
        backgroundColor: null,
        logging: false,
    }).then(canvas => {
        const link    = document.createElement('a');
        link.download = `cupon-${id}.png`;
        link.href     = canvas.toDataURL('image/png');
        link.click();
        btn.innerHTML = originalHTML;
        btn.disabled  = false;
    }).catch(() => {
        showNotification('Error al generar la imagen', 'error');
        btn.innerHTML = originalHTML;
        btn.disabled  = false;
    });
}
    function formatDateES(dateStr) {
    if (!dateStr) return '';
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const [y, m, d] = dateStr.split('-');
    return `${d} ${meses[parseInt(m,10)-1]} ${y}`;
}
function updateCouponPreview(id) {
    const pctEl       = document.getElementById(`pct-${id}`);
    const validityEl  = document.getElementById(`validity-${id}`);
    const subEl       = document.getElementById(`sub-${id}`);

    // Porcentaje (solo cupón premium)
    if (pctEl) {
        const pct = pctEl.value || '0';
        const stamEl = document.getElementById(`stamp-pct-${id}`);
        const headEl = document.getElementById(`headline-${id}`);
        if (stamEl) stamEl.textContent = pct + '%';
        if (headEl) headEl.textContent = pct + '% OFF';
    }

    // Vigencia
    if (validityEl) {
        const vEl = document.getElementById(`coupon-validity-text-${id}`);
        if (vEl) {
            // Si es un date-input (premium) formateamos, si es text (birthday) usamos directo
            const raw = validityEl.value;
            const isDate = validityEl.type === 'date';
            vEl.innerHTML = isDate
                ? `Válido hasta: ${formatDateES(raw)} &nbsp;·&nbsp; *Aplican términos`
                : raw;
        }
    }

    // Sub-texto / descripción
    if (subEl) {
        const sEl = document.getElementById(`coupon-sub-text-${id}`);
        if (sEl) sEl.textContent = subEl.value;
    }
}
function toggleStatus(clientId, currentStatus) {
    fetch(`/clients/${clientId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`status-btn-${clientId}`);
            const text = document.getElementById(`status-text-${clientId}`);
            
            if (data.is_active) {
                btn.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 bg-green-100 text-green-800 hover:bg-green-200';
                text.textContent = 'Activo';
                btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + text.outerHTML;
            } else {
                btn.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 bg-red-100 text-red-800 hover:bg-red-200';
                text.textContent = 'Inactivo';
                btn.innerHTML = '<i class="fas fa-times-circle mr-1"></i>' + text.outerHTML;
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al cambiar el estado', 'error');
    });
}
function backToEditor(id) {
    document.getElementById(`coupon-preview-${id}`).classList.add('hidden');
    document.getElementById(`coupon-editor-${id}`).classList.remove('hidden');
}
// Muestra el cupón y oculta el editor
function confirmCoupon(id) {
    updateCouponPreview(id); // aplica última edición
    document.getElementById(`coupon-editor-${id}`).classList.add('hidden');
    document.getElementById(`coupon-preview-${id}`).classList.remove('hidden');
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
function openCoupon(id) {
    const overlay = document.getElementById(`coupon-${id}`);
    const editor  = document.getElementById(`coupon-editor-${id}`);
    const preview = document.getElementById(`coupon-preview-${id}`);
    if (editor)  editor.classList.remove('hidden');
    if (preview) preview.classList.add('hidden');
    overlay.classList.remove('hidden');
}
function closeCoupon(id) {
    document.getElementById('coupon-' + id).classList.add('hidden');
}
// Cerrar al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('coupon-modal-overlay')) {
        e.target.classList.add('hidden');
    }
});
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');
/* ── Editor de cupón ─────────────────────────────── */
.coupon-editor {
    padding: .25rem 0 1rem;
}
.coupon-editor-title {
    font-size: 16px; font-weight: 700; color: #203363;
    margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: .5rem;
}
.coupon-editor-subtitle {
    font-size: 13px; font-weight: 400; color: #6b7280;
}
.coupon-editor-fields {
    display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: 1rem;
}
.coupon-field-group {
    display: flex; flex-direction: column; gap: .3rem; min-width: 140px; flex: 1;
}
.coupon-field-full { flex-basis: 100%; }
.coupon-field-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .05em; color: #6b7280;
}
.coupon-field-row {
    display: flex; align-items: center; gap: .4rem;
}
.coupon-field-input {
    border: 1.5px solid #d1d5db; border-radius: 8px;
    padding: 7px 10px; font-size: 14px; width: 100%;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.coupon-field-input:focus {
    border-color: #203363;
    box-shadow: 0 0 0 3px rgba(32,51,99,.12);
}
.coupon-field-unit {
    font-size: 14px; color: #6b7280; white-space: nowrap;
}
.coupon-confirm-btn {
    background: #203363; color: #fff;
    border: none; border-radius: 8px;
    padding: 9px 22px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .2s;
    display: block; margin-left: auto;
}
.coupon-confirm-btn:hover { background: #1a2850; }
.coupon-back-btn {
    background: #f3f4f6; color: #374151;
    border: none; border-radius: 8px;
    padding: 8px 18px; font-size: 13px;
    cursor: pointer; transition: background .2s;
}
.coupon-back-btn:hover { background: #e5e7eb; }
.coupon-modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.55);
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
}
.coupon-modal-overlay.hidden { display: none; }
.coupon-modal-box {
    background: #fff; border-radius: 16px;
    padding: 1.5rem; max-width: 580px; width: 100%;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.coupon-close {
    position: absolute; top: 10px; right: 14px;
    font-size: 22px; color: #888; background: none;
    border: none; cursor: pointer; line-height: 1;
}
.coupon-strip {
    display: flex; border-radius: 12px; overflow: hidden;
}
.coupon-premium { background: #1e3a5f; }  
.coupon-birthday { background: #c0392b; }

.coupon-left {
    flex: 1; padding: 1.25rem; display: flex;
    flex-direction: column; gap: 0.4rem;
}
.coupon-badge {
    display: inline-block; font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 20px; width: fit-content;
    text-transform: uppercase; letter-spacing: 0.05em;
}
.coupon-premium .coupon-badge   { background: #2a4a72; color: #7aafd4; } 
.coupon-birthday .coupon-badge { background: #fff; color: #c0392b; }
.coupon-premium .coupon-client  { color: #b8d4ea; } 
.coupon-premium .coupon-stamp   { border-color: #2a4a72; }
.coupon-premium .coupon-stamp-pct{ color: #7ec8e3; } 
.coupon-premium .coupon-stamp-off{ color: rgba(255,255,255,.5); }
.coupon-premium .coupon-code-label{ color: rgba(255,255,255,.4); } 
.coupon-code { color: #7ec8e3; background: rgba(126,200,227,.1); border-color: rgba(126,200,227,.35); }
.coupon-birthday .coupon-code   { background: #e8d8c8; color: #5c3d24; border-color: #c8b8a8; }
.coupon-premium .coupon-bar { background: rgba(255,255,255,.4); }
.coupon-premium .coupon-notch   { background: #fff; }
.coupon-headline {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 44px; line-height: 1;
}
.coupon-premium .coupon-headline{ color: #f5f5f5; }
.coupon-birthday .coupon-headline { color: #fff; }

.coupon-sub { font-size: 12px; line-height: 1.4; }
.coupon-premium .coupon-sub     { color: #6a9bbf; } 
.coupon-birthday .coupon-sub { color: rgba(255,255,255,0.85); }

.coupon-client { font-size: 14px; font-weight: 700; color: #fff; }
.coupon-validity { font-size: 10px; margin-top: auto; }
.coupon-premium .coupon-validity{ color: #3f6a8a; } 
.coupon-premium .coupon-headline{ color: #e8f2fb; } 

.coupon-divider {
    width: 2px; margin: 1rem 0; flex-shrink: 0;
    background: repeating-linear-gradient(
        to bottom, transparent, transparent 6px, rgba(255,255,255,0.15) 6px, rgba(255,255,255,0.15) 12px
    );
    position: relative;
}
.coupon-divider::before, .coupon-divider::after {
    content: ''; position: absolute;
    width: 18px; height: 18px; background: #fff;
    border-radius: 50%; left: 50%; transform: translateX(-50%); z-index: 2;
}
.coupon-divider::before { top: -9px; }
.coupon-divider::after  { bottom: -9px; }

.coupon-right {
    width: 130px; flex-shrink: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 0.6rem; padding: 1rem 0.75rem;
}
.coupon-premium .coupon-right { background: #152d4a; }
.coupon-birthday .coupon-right { background: #a93226; }
.coupon-premium .coupon-divider { border-right: 1px dashed rgba(255,255,255,0.12); }

.coupon-stamp {
    width: 50px; height: 50px; border-radius: 50%;
    border: 2px dashed rgba(255,255,255,0.4);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
}
.coupon-stamp-pct {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px; color: #f5a623; line-height: 1;
}
.coupon-stamp-off {
    font-size: 9px; color: rgba(255,255,255,0.6);
    text-transform: uppercase; letter-spacing: 0.05em;
}
.coupon-code-label {
    font-size: 10px; color: rgba(255,255,255,0.5);
    text-transform: uppercase; letter-spacing: 0.1em;
}
.coupon-code {
    font-family: monospace; font-size: 15px;
    color: #f5a623; letter-spacing: 0.1em;
    background: rgba(245,166,35,0.12);
    padding: 3px 8px; border-radius: 5px;
    border: 1px dashed rgba(245,166,35,0.4);
}
.coupon-birthday .coupon-code {
    color: #fff; background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.35);
}
.coupon-barcode { display: flex; gap: 2px; align-items: flex-end; }
.coupon-bar { background: rgba(255,255,255,0.55); border-radius: 1px; }

.coupon-print-btn {
    background: #203363; color: #fff;
    border: none; border-radius: 8px;
    padding: 8px 20px; font-size: 13px;
    cursor: pointer; transition: background 0.2s;
}
.coupon-print-btn:hover { background: #1a2850; }
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/clients/index.blade.php ENDPATH**/ ?>