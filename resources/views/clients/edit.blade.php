@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-semibold text-[var(--primary-color)]">Editar Cliente</h1>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
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
                        <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Ej: Juan">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-[var(--primary-color)]">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $client->last_name) }}" required
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('last_name') border-red-500 @enderror"
                            placeholder="Ej: Pérez">
                        @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('document_type') border-red-500 @enderror">
                            <option value="CI" {{ old('document_type', $client->document_type) == 'CI' ? 'selected' : '' }}>CI - Carnet de Identidad</option>
                            <option value="NIT" {{ old('document_type', $client->document_type) == 'NIT' ? 'selected' : '' }}>NIT</option>
                            <option value="Pasaporte" {{ old('document_type', $client->document_type) == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                        @error('document_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número de Documento -->
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-[var(--primary-color)]">
                            Número de Documento
                        </label>
                        <input type="text" name="document_number" id="document_number" value="{{ old('document_number', $client->document_number) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 font-mono @error('document_number') border-red-500 @enderror"
                            placeholder="Ej: 1234567">
                        @error('document_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $client->phone) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('phone') border-red-500 @enderror"
                            placeholder="Ej: +591 71234567">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--primary-color)]">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('email') border-red-500 @enderror"
                            placeholder="Ej: cliente@email.com">
                        @error('email')
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
                        <input type="text" name="address" id="address" value="{{ old('address', $client->address) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('address') border-red-500 @enderror"
                            placeholder="Ej: Calle 123, Zona Centro">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-[var(--primary-color)]">
                            Ciudad
                        </label>
                        <input type="text" name="city" id="city" value="{{ old('city', $client->city) }}"
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('city') border-red-500 @enderror"
                            placeholder="Ej: La Paz">
                        @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            class="mt-1 block w-full rounded-md border border-[var(--tertiary-color)] shadow-sm p-2 focus:border-[var(--primary-color)] focus:ring focus:ring-[var(--primary-color)] focus:ring-opacity-50 @error('notes') border-red-500 @enderror"
                            placeholder="Observaciones o notas adicionales sobre el cliente">{{ old('notes', $client->notes) }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cliente Activo -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 text-[var(--primary-color)] border-gray-300 rounded focus:ring-[var(--primary-color)]">
                        <label for="is_active" class="ml-2 text-sm font-medium text-[var(--primary-color)]">
                            Cliente activo
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex space-x-4 justify-end pt-4 border-t">
                <a href="{{ route('clients.index') }}"
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