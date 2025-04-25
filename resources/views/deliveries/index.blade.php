@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Servicios de Delivery</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Delivery
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->name }}</td>
                            <td>{{ $delivery->description ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $delivery->is_active ? 'success' : 'danger' }}">
                                    {{ $delivery->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este servicio?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection