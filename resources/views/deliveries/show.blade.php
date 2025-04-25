@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Detalles del Servicio de Delivery</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> {{ $delivery->name }}</p>
                    <p><strong>Descripción:</strong> {{ $delivery->description ?? 'N/A' }}</p>
                    <p>
                        <strong>Estado:</strong>
                        <span class="badge bg-{{ $delivery->is_active ? 'success' : 'danger' }}">
                            {{ $delivery->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Creado:</strong> {{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $delivery->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection