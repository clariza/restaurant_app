@extends('layouts.app')

@section('content')
<style>
    /* Estilos personalizados para la vista de apertura */
    :root {
        --primary-color: #203363;
        --secondary-color: #6380a6;
        --tertiary-color: #a4b6ce;
        --background-color: #fafafa;
        --table-data-color: #7c7b90;
    }
    
    .card {
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background-color: var(--primary-color);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-label {
        font-size: 14px;
        color: var(--table-data-color);
        margin-bottom: 8px;
        display: block;
        text-align: start;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 2px solid var(--tertiary-color);
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        outline: none;
        background-color: white;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 6px rgba(32, 51, 99, 0.2);
    }
    
    textarea.form-control {
        min-height: 100px;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        padding: 10px 16px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #47517c;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-white">Apertura de Caja Chica</h4>
                </div>
                <div class="card-body">
                    @if (session('warning'))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('warning') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('petty-cash.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="notes" class="form-label">Notas (Opcional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Puede agregar alguna observación relevante"></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-cash-register mr-2"></i> Abrir Caja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Deshabilitar todos los enlaces del sidebar excepto logout
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(link => {
            if (!link.href.includes('logout')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Apertura de Caja Requerida',
                        text: 'Debe abrir una caja chica antes de acceder a esta sección.',
                        confirmButtonColor: '#203363'
                    });
                });
            }
        });
    });
</script>
@endsection