@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-file-invoice me-2"></i>Detalle de Compra
            </h2>
            <p class="text-muted">Referencia: {{ $purchase->reference_number }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            @if(auth()->user()->role === 'admin' && $purchase->status === 'pending')
                <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información de la Compra -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información de la Compra
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Número de Referencia</label>
                            <p class="fw-semibold mb-0">{{ $purchase->reference_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fecha de Compra</label>
                            <p class="fw-semibold mb-0">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Estado</label>
                            <div>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pendiente'],
                                        'completed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Completado'],
                                        'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelado']
                                    ];
                                    $config = $statusConfig[$purchase->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }}">
                                    <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                    {{ $config['text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Monto Total</label>
                            <p class="fw-bold text-success mb-0 fs-4">
                                Bs. {{ number_format($purchase->total_amount, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos de la Compra -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>Productos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchase->stocks as $stock)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-secondary text-white me-2">
                                                    {{ substr($stock->item->name ?? 'P', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $stock->item->name ?? 'Producto sin nombre' }}
                                                    </div>
                                                    @if($stock->item->category)
                                                        <small class="text-muted">
                                                            {{ $stock->item->category->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            Bs. {{ number_format($stock->unit_price, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Bs. {{ number_format($stock->quantity * $stock->unit_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No hay productos registrados en esta compra
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($purchase->stocks->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold text-success fs-5">
                                            Bs. {{ number_format($purchase->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Proveedor -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Proveedor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle-large bg-success text-white mx-auto mb-2">
                            {{ substr($purchase->supplier->name, 0, 2) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $purchase->supplier->name }}</h5>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="text-muted small d-block">
                            <i class="fas fa-id-card me-1"></i>NIT
                        </label>
                        <p class="mb-0">{{ $purchase->supplier->nit ?? 'No especificado' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">
                            <i class="fas fa-user me-1"></i>Contacto
                        </label>
                        <p class="mb-0">{{ $purchase->supplier->contact }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">
                            <i class="fas fa-phone me-1"></i>Teléfono
                        </label>
                        <p class="mb-0">
                            <a href="tel:{{ $purchase->supplier->phone }}" class="text-decoration-none">
                                {{ $purchase->supplier->phone }}
                            </a>
                        </p>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small d-block">
                            <i class="fas fa-map-marker-alt me-1"></i>Dirección
                        </label>
                        <p class="mb-0">{{ $purchase->supplier->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Resumen
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Productos:</span>
                        <span class="fw-semibold">{{ $purchase->stocks->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Unidades:</span>
                        <span class="fw-semibold">{{ $purchase->stocks->sum('quantity') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Compra:</span>
                        <span class="fw-bold text-success">
                            Bs. {{ number_format($purchase->total_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.avatar-circle-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
}
</style>
@endsection