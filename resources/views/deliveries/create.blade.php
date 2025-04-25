@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ isset($delivery) ? 'Editar' : 'Crear' }} Servicio de Delivery</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($delivery) ? route('deliveries.update', $delivery->id) : route('deliveries.store') }}" method="POST">
                @csrf
                @if(isset($delivery))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $delivery->name ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $delivery->description ?? '') }}</textarea>
                </div>

                @if(isset($delivery))
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $delivery->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Activo</label>
                </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>
@endsection