@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-user mr-3 text-[#203363]"></i>
                Detalle del Cliente
            </h1>
            <p class="text-gray-600 mt-2">Información completa del cliente</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('clients.edit', $client) }}" 
               class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-200 flex items-center shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Editar
            </a>
            <a href="{{ route('clients.index') }}" 
               class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Personal -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-user-circle mr-2 text-[#203363]"></i>
                    Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $client->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipo de Documento</label>
                        <p class="text-gray-900">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $client->document_type }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Número de Documento</label>
                        <p class="text-gray-900 font-mono">{{ $client->document_number ?: 'No especificado' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estado</label>
                        <p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-{{ $client->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-address-book mr-2 text-[#203363]"></i>
                    Información de Contacto
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#203363] rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Teléfono</label>
                            <p class="text-gray-900">{{ $client->phone ?: 'No especificado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#203363] rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $client->email ?: 'No especificado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-map-marker-alt mr-2 text-[#203363]"></i>
                    Ubicación
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Dirección</label>
                        <p class="text-gray-900">{{ $client->address ?: 'No especificada' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Ciudad</label>
                        <p class="text-gray-900">{{ $client->city ?: 'No especificada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            @if($client->notes)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-sticky-note mr-2 text-[#203363]"></i>
                    Notas
                </h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $client->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- Información Rápida -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-info-circle mr-2 text-[#203363]"></i>
                    Información Rápida
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Fecha de registro</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $client->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Última actualización</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $client->updated_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-3">
                    <i class="fas fa-bolt mr-2 text-[#203363]"></i>
                    Acciones Rápidas
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('clients.edit', $client) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Cliente
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" 
                          method="POST" 
                          onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Eliminar Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection