
<?php $__env->startSection('content'); ?>
    <!-- Header -->
    <!-- Main Menu Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-8">
        <?php if(auth()->user()->role === 'admin'): ?>
            <!-- Menu Index (mostrar para admin) -->
            <a href="<?php echo e(route('menu.index')); ?>" class="bg-[#b6e0f6] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#a4c8e0] transition-colors">
                <i class="fas fa-utensils text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Menú</h2>
                <p class="text-[#203363]">Administra los productos del menú</p>
            </a>

            <!-- Lista de Pedidos (mostrar para admin) -->
            <a href="<?php echo e(route('orders.index')); ?>" class="bg-[#8e92ae] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#7c7f9a] transition-colors">
                <i class="fas fa-list text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Lista de Ventas</h2>
                <p class="text-[#203363]">Revisa y gestiona los pedidos</p>
            </a>

            <!-- Lista de Compras (mostrar solo para admin) -->
            <a href="#" class="bg-[#6a7095] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#5a5f7d] transition-colors">
                <i class="fas fa-shopping-cart text-4xl text-[#ffffff] mb-4"></i>
                <h2 class="text-xl font-bold text-[#ffffff]">Lista de Compras</h2>
                <p class="text-[#ffffff]">Revisa las compras realizadas</p>
            </a>

            <!-- Lista de Gastos (mostrar para admin) -->
            <a href="#" class="bg-[#47517c] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#3a4266] transition-colors">
                <i class="fas fa-money-bill-wave text-4xl text-[#ffffff] mb-4"></i>
                <h2 class="text-xl font-bold text-[#ffffff]">Lista de Gastos</h2>
                <p class="text-[#ffffff]">Revisa y gestiona los gastos</p>
            </a>
        <?php else: ?>
            <!-- Menú (mostrar para vendedor) -->
            <a href="<?php echo e(route('menu.index')); ?>" class="bg-[#b6e0f6] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#a4c8e0] transition-colors">
                <i class="fas fa-utensils text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Menú</h2>
                <p class="text-[#203363]">Administra los productos del menú</p>
            </a>

            <!-- Lista de Ventas (mostrar para vendedor) -->
            <a href="<?php echo e(route('orders.index')); ?>" class="bg-[#8e92ae] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#7c7f9a] transition-colors">
                <i class="fas fa-list text-4xl text-[#203363] mb-4"></i>
                <h2 class="text-xl font-bold text-[#203363]">Lista de Ventas</h2>
                <p class="text-[#203363]">Revisa y gestiona los pedidos</p>
            </a>

            <!-- Lista de Gastos (mostrar para vendedor) -->
            <a href="#" class="bg-[#47517c] p-6 rounded-lg shadow-md flex flex-col items-center justify-center text-center hover:bg-[#3a4266] transition-colors">
                <i class="fas fa-money-bill-wave text-4xl text-[#ffffff] mb-4"></i>
                <h2 class="text-xl font-bold text-[#ffffff]">Lista de Gastos</h2>
                <p class="text-[#ffffff]">Revisa y gestiona los gastos</p>
            </a>
        <?php endif; ?>
    </div>

    <!-- Sales Reports Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12 pt-8">
        <!-- Total Sales by Period -->
        <div class="bg-[#ffffff] p-6 rounded-lg shadow-md w-full">
            <h3 class="text-xl font-bold text-[#203363] mb-4">Total de Ventas por Período</h3>
            <div class="w-full h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Sales Comparison by Type -->
        <div class="bg-[#ffffff] p-6 rounded-lg shadow-md w-full">
            <h3 class="text-xl font-bold text-[#203363] mb-4">Comparación de Tipos de Ventas</h3>
            <div class="w-full h-64">
                <canvas id="salesTypeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para el gráfico de ventas por período
        const salesChartData = {
            labels: <?php echo json_encode($labels, 15, 512) ?>,
            datasets: [{
                label: 'Total de Ventas',
                data: <?php echo json_encode($data, 15, 512) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
            }]
        };

        // Inicializar el gráfico de ventas por período
        const salesChartCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesChartCtx, {
            type: 'line',
            data: salesChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Datos para el gráfico de comparación de tipos de ventas
        const salesTypeChartData = {
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
        };

        // Inicializar el gráfico de comparación de tipos de ventas
        const salesTypeChartCtx = document.getElementById('salesTypeChart').getContext('2d');
        new Chart(salesTypeChartCtx, {
            type: 'bar',
            data: salesTypeChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>