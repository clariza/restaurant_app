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
        border-radius: 6px;
    }
    
    .btn-primary:hover {
        background-color: #47517c;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
    }
    
    /* 🔥 NUEVO: Estilos para el botón de reporte anterior */
    .btn-print-previous {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        border: none;
        padding: 10px 16px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 6px;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(107, 114, 128, 0.2);
    }
    
    .btn-print-previous:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(107, 114, 128, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .btn-print-previous i {
        font-size: 16px;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .alert-danger {
        background-color: #fee;
        border-color: #fcc;
        color: #c33;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }
    
    /* 🔥 NUEVO: Grid para botones lado a lado */
    .buttons-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    /* 🔥 Responsivo: en móviles se apilan */
    @media (max-width: 576px) {
        .buttons-grid {
            grid-template-columns: 1fr;
        }
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
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle mr-2"></i>
                            {{ session('error') }}
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
                        
                        {{-- 🔥 Grid con botones lado a lado --}}
                        <div class="buttons-grid">
                            {{-- Botón principal para abrir caja --}}
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-cash-register mr-2"></i> Abrir Caja
                            </button>
                            
                            {{-- 🔥 BOTÓN PARA IMPRIMIR REPORTE ANTERIOR --}}
                            <a href="{{ route('petty-cash.print-previous') }}" 
                               class="btn-print-previous py-2"
                               target="_blank"
                               title="Ver reporte de la última caja cerrada">
                                <i class="fas fa-file-pdf"></i>
                                <span>Reporte Anterior</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definir las rutas que deben bloquearse
        const blockedRoutes = [
            'menu',  // Ruta del Menú
            'sales'   // Ruta de Lista de Ventas
        ];
        
        // Obtener todos los enlaces del sidebar
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        
        sidebarLinks.forEach(link => {
            // Verificar si el enlace coincide con alguna ruta bloqueada
            const shouldBlock = blockedRoutes.some(route => {
                // Comprobar si la URL del enlace contiene la ruta
                return link.href.includes(route);
            });

            if (shouldBlock) {
                // Agregar evento para bloquear el clic
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Apertura de Caja Requerida',
                        text: 'Debe abrir una caja chica antes de acceder a las funciones de ventas.',
                        confirmButtonColor: '#203363',
                        confirmButtonText: 'Entendido'
                    });
                });
                
                // Cambiar estilo visual para indicar que está bloqueado
                link.style.opacity = '0.6';
                link.style.cursor = 'not-allowed';
                link.title = 'Se requiere apertura de caja';
            }
        });
    });
</script>
@endsection