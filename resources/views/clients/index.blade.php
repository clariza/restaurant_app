@extends('layouts.app')

@section('content')
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
        <a href="{{ route('clients.create') }}" 
           class="bg-[#203363] text-white px-6 py-3 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center shadow-lg">
            <i class="fas fa-plus-circle mr-2"></i>
            Nuevo Cliente
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- ===================== FILTROS DE CUMPLEAÑOS ===================== -->
    <div class="bg-white rounded-lg shadow-md p-5 mb-6 border-l-4 border-[#203363]">
        <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-birthday-cake mr-2 text-[#203363]"></i>
            Filtrar por Cumpleaños
        </h2>
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-4 items-end">
            <!-- Filtro: Mes -->
            <div class="flex flex-col min-w-[160px]">
                <label for="birthday_month" class="text-sm font-medium text-gray-600 mb-1">
                    <i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Mes
                </label>
                <select name="birthday_month" id="birthday_month"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#203363] focus:border-transparent">
                    <option value="">Todos los meses</option>
                    <option value="1"  {{ request('birthday_month') == '1'  ? 'selected' : '' }}>Enero</option>
                    <option value="2"  {{ request('birthday_month') == '2'  ? 'selected' : '' }}>Febrero</option>
                    <option value="3"  {{ request('birthday_month') == '3'  ? 'selected' : '' }}>Marzo</option>
                    <option value="4"  {{ request('birthday_month') == '4'  ? 'selected' : '' }}>Abril</option>
                    <option value="5"  {{ request('birthday_month') == '5'  ? 'selected' : '' }}>Mayo</option>
                    <option value="6"  {{ request('birthday_month') == '6'  ? 'selected' : '' }}>Junio</option>
                    <option value="7"  {{ request('birthday_month') == '7'  ? 'selected' : '' }}>Julio</option>
                    <option value="8"  {{ request('birthday_month') == '8'  ? 'selected' : '' }}>Agosto</option>
                    <option value="9"  {{ request('birthday_month') == '9'  ? 'selected' : '' }}>Septiembre</option>
                    <option value="10" {{ request('birthday_month') == '10' ? 'selected' : '' }}>Octubre</option>
                    <option value="11" {{ request('birthday_month') == '11' ? 'selected' : '' }}>Noviembre</option>
                    <option value="12" {{ request('birthday_month') == '12' ? 'selected' : '' }}>Diciembre</option>
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
                    <option value="today"      {{ request('birthday_filter') == 'today'      ? 'selected' : '' }}>🎂 Hoy</option>
                    <option value="this_week"  {{ request('birthday_filter') == 'this_week'  ? 'selected' : '' }}>📅 Esta semana</option>
                    <option value="this_month" {{ request('birthday_filter') == 'this_month' ? 'selected' : '' }}>🗓️ Este mes</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-[#203363] text-white px-5 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center text-sm shadow">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                @if(request('birthday_month') || request('birthday_filter'))
                    <a href="{{ route('clients.index') }}"
                       class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition duration-200 flex items-center text-sm">
                        <i class="fas fa-times mr-2"></i> Limpiar
                    </a>
                @endif
            </div>

            <!-- Indicador de resultados activos -->
            @if(request('birthday_month') || request('birthday_filter'))
                <div class="flex items-center ml-2">
                    <span class="bg-[#203363] text-white text-xs px-3 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-birthday-cake"></i>
                        Filtrando cumpleaños
                    </span>
                </div>
            @endif
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
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition duration-150
                            {{-- Resaltar si es cumpleaños hoy --}}
                            {{ $client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d') ? 'bg-yellow-50' : '' }}">

                            <!-- Cliente -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-[#203363] rounded-full flex items-center justify-center relative">
                                        <i class="fas fa-user text-white"></i>
                                        {{-- Ícono de pastel si hoy es su cumpleaños --}}
                                        @if($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d'))
                                            <span class="absolute -top-1 -right-1 text-xs">🎂</span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $client->full_name }}
                                        </div>
                                        @if($client->email)
                                            <div class="text-sm text-gray-500">
                                                {{ $client->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- CELDA CAMBIADA: Cumpleaños en lugar de Documento -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->birthdays)
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 flex items-center gap-1">
                                            <i class="fas fa-calendar text-gray-400 w-4"></i>
                                            {{ $client->birthdays->format('d/m/Y') }}
                                        </div>
                                        <div class="text-gray-500 text-xs mt-0.5">
                                            @php
                                                $today     = now();
                                                $birthday  = $client->birthdays->setYear($today->year);
                                                if ($birthday->isPast() && !$birthday->isToday()) {
                                                    $birthday->addYear();
                                                }
                                                $daysLeft = (int) $today->diffInDays($birthday, false);
                                            @endphp

                                            @if($client->birthdays->format('m-d') === $today->format('m-d'))
                                                <span class="text-yellow-600 font-semibold flex items-center gap-1">
                                                    🎉 ¡Hoy es su cumpleaños!
                                                </span>
                                            @elseif($daysLeft <= 7)
                                                <span class="text-orange-500 font-medium">
                                                    En {{ $daysLeft }} día{{ $daysLeft !== 1 ? 's' : '' }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">
                                                    En {{ $daysLeft }} días
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-sm">Sin fecha</span>
                                @endif
                            </td>

                            <!-- Contacto -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($client->phone)
                                        <div class="flex items-center text-gray-900">
                                            <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                            {{ $client->phone }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Sin teléfono</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Ubicación -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($client->city)
                                        <div class="flex items-center">
                                            <i class="fas fa-city text-gray-400 mr-2 w-4"></i>
                                            {{ $client->city }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Sin ciudad</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Estado -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="toggleStatus({{ $client->id }}, {{ $client->is_active ? 'true' : 'false' }})"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-all duration-200
                                               {{ $client->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
                                        id="status-btn-{{ $client->id }}">
                                    <i class="fas fa-{{ $client->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                    <span id="status-text-{{ $client->id }}">
                                        {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </button>
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('clients.show', $client) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 transition duration-200"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 transition duration-200"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">No hay clientes registrados</p>
                                    <a href="{{ route('clients.create') }}" 
                                       class="bg-[#203363] text-white px-6 py-2 rounded-lg hover:bg-[#1a2850] transition duration-200">
                                        <i class="fas fa-plus-circle mr-2"></i>Crear primer cliente
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($clients->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleStatus(clientId, currentStatus) {
    fetch(`/clients/${clientId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection