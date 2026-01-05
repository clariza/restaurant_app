@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <a href="{{ route('branches.index') }}" 
                   class="text-gray-600 hover:text-[#203363] transition duration-200 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-store mr-3 text-[#203363]"></i>
                    {{ $branch->name }}
                    @if($branch->is_main)
                        <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star mr-1"></i> Principal
                        </span>
                    @endif
                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas fa-{{ $branch->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                        {{ $branch->is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                </h1>
            </div>
            <a href="{{ route('branches.edit', $branch) }}" 
               class="bg-[#203363] text-white px-6 py-3 rounded-lg hover:bg-[#1a2850] transition duration-200 flex items-center shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Editar Sucursal
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información de la Sucursal -->
        <div class="lg:col-span-2">
            <!-- Información Básica -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-info-circle mr-2 text-[#203363]"></i>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Código</label>
                        <p class="text-lg font-semibold text-gray-900 font-mono">{{ $branch->code }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $branch->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-map-marker-alt mr-2 text-[#203363]"></i>
                    Ubicación
                </h3>
                
                <div class="space-y-4">
                    @if($branch->address)
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-3 mt-1 w-5"></i>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                                <p class="text-gray-900">{{ $branch->address }}</p>
                            </div>
                        </div>
                    @endif

                    @if($branch->city || $branch->state)
                        <div class="flex items-start">
                            <i class="fas fa-city text-gray-400 mr-3 mt-1 w-5"></i>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ciudad / Departamento</label>
                                <p class="text-gray-900">
                                    {{ $branch->city }}@if($branch->city && $branch->state), @endif{{ $branch->state }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!$branch->address && !$branch->city && !$branch->state)
                        <p class="text-gray-400 italic text-center py-4">No hay información de ubicación registrada</p>
                    @endif
                </div>
            </div>

            <!-- Contacto -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-phone mr-2 text-[#203363]"></i>
                    Información de Contacto
                </h3>
                
                <div class="space-y-4">
                    @if($branch->phone)
                        <div class="flex items-start">
                            <i class="fas fa-phone text-gray-400 mr-3 mt-1 w-5"></i>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                                <p class="text-gray-900">{{ $branch->phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($branch->email)
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-gray-400 mr-3 mt-1 w-5"></i>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                <p class="text-gray-900">{{ $branch->email }}</p>
                            </div>
                        </div>
                    @endif

                    @if(!$branch->phone && !$branch->email)
                        <p class="text-gray-400 italic text-center py-4">No hay información de contacto registrada</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas y Usuarios -->
        <div class="lg:col-span-1">
            <!-- Estadísticas -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-chart-bar mr-2 text-[#203363]"></i>
                    Estadísticas
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-users text-blue-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Usuarios</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $branch->users->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-shopping-cart text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-gray-600">Órdenes</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $branch->orders->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios Asignados -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-users mr-2 text-[#203363]"></i>
                    Usuarios Asignados
                </h3>
                
                @if($branch->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($branch->users as $user)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                                <div class="flex-shrink-0 h-10 w-10 bg-[#203363] rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->role ?? 'Usuario' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 italic text-center py-4">No hay usuarios asignados a esta sucursal</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection