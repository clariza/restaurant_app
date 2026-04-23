<?php $__env->startSection('content'); ?>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8">
        <?php if(auth()->user()->role === 'admin'): ?>

            <a href="<?php echo e(route('menu.index')); ?>"
               class="bg-[#b6e0f6] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#a4c8e0] transition-colors">
                <i class="fas fa-utensils text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Menú</h2>
                <p class="text-[#203363]">Administra los productos del menú</p>
            </a>

            <a href="<?php echo e(route('orders.index')); ?>"
               class="bg-[#8e92ae] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#7c7f9a] transition-colors">
                <i class="fas fa-list text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Lista de Ventas</h2>
                <p class="text-[#203363]">Revisa y gestiona los pedidos</p>
            </a>

            <a href="<?php echo e(route('purchases.index')); ?>"
               class="bg-[#6a7095] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#5a5f7d] transition-colors">
                <i class="fas fa-shopping-cart text-4xl text-white mb-4"></i>
                <h2 class="text-xl font-bold text-white">Lista de Compras</h2>
                <p class="text-white">Revisa las compras realizadas</p>
            </a>

        <?php else: ?>

            <a href="<?php echo e(route('menu.index')); ?>"
               class="bg-[#b6e0f6] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#a4c8e0] transition-colors">
                <i class="fas fa-utensils text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Menú</h2>
                <p class="text-[#203363]">Administra los productos del menú</p>
            </a>

            <a href="<?php echo e(route('orders.index')); ?>"
               class="bg-[#8e92ae] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#7c7f9a] transition-colors">
                <i class="fas fa-list text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Lista de Ventas</h2>
                <p class="text-[#203363]">Revisa y gestiona los pedidos</p>
            </a>

            <a href="<?php echo e(route('clients.index')); ?>"
               class="bg-[#5f6fb5] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#4f5ea0] transition-colors">
                <i class="fas fa-users text-4xl text-white mb-4"></i>
                <h2 class="text-xl font-bold text-white">Clientes</h2>
                <p class="text-white">Gestiona y crea clientes</p>
            </a>

        <?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-[#203363] mb-4">Total de Ventas por Período</h3>
            <div class="w-full h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-[#203363] mb-4">Comparación de Tipos de Ventas</h3>
            <div class="w-full h-64">
                <canvas id="salesTypeChart"></canvas>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels, 15, 512) ?>,
                datasets: [{
                    label: 'Total de Ventas',
                    data: <?php echo json_encode($data, 15, 512) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('salesTypeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($typeLabels, 15, 512) ?>,
                datasets: [{
                    label: 'Ventas',
                    data: <?php echo json_encode($typeData, 15, 512) ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>