@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Editar Sucursal</h1>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('branches.update', $branch) }}" method="POST">
        @csrf
        @method('PUT')
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
                        <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Ej: Sucursal Centro">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-[var(--primary-color)]">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code', $branch->code) }}" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 font-mono @error('code') border-red-500 @enderror"
                            placeholder="Ej: SUC-01">
                        @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <input type="text" name="address" id="address" value="{{ old('address', $branch->address) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('address') border-red-500 @enderror"
                            placeholder="Ej: Av. Principal 123">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Ciudad -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-[var(--primary-color)]">
                                Ciudad
                            </label>
                            <input type="text" name="city" id="city" value="{{ old('city', $branch->city) }}"
                                class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('city') border-red-500 @enderror"
                                placeholder="Ej: La Paz">
                            @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Departamento -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-[var(--primary-color)]">
                                Departamento
                            </label>
                            <input type="text" name="state" id="state" value="{{ old('state', $branch->state) }}"
                                class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('state') border-red-500 @enderror"
                                placeholder="Ej: La Paz">
                            @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('phone') border-red-500 @enderror"
                            placeholder="Ej: +591 2 1234567">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--primary-color)]">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('email') border-red-500 @enderror"
                            placeholder="Ej: sucursal@empresa.com">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            {{ old('is_main', $branch->is_main) ? 'checked' : '' }}
                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_main" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Establecer como sucursal principal
                        </label>
                    </div>

                    <!-- Está Activa -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $branch->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_active" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Sucursal activa
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex space-x-4 justify-end pt-4 border-t">
                <a href="{{ route('branches.index') }}"
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
@endsection