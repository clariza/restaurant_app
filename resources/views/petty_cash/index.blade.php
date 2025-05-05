@extends('layouts.app')
@section('content')
<style>
    /* Estilos mejorados para botones */
    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    
    .btn-view {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #2563eb;
    }
    
    .btn-edit {
        background-color: #10b981;
        color: white;
        border: 1px solid #059669;
    }
    
    .btn-delete {
        background-color: #ef4444;
        color: white;
        border: 1px solid #dc2626;
    }
    
    .btn-close {
        background-color: #8b5cf6;
        color: white;
        border: 1px solid #7c3aed;
    }
    
    .btn-print {
        background-color: #6b7280;
        color: white;
        border: 1px solid #4b5563;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Resto de estilos... */
    .input-group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
</style>

<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Cierres de Caja Chica</h2>

    <!-- Mensajes de alerta -->
    @if (session('warning'))
        <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
            <button onclick="closeOpenPettyCash()" class="ml-2 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                Cerrar caja abierta
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabla de cierres -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Fecha</th>
                    <th class="p-2 text-right">Monto Actual</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                    <th class="p-2 text-left">Reporte</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pettyCashes as $pettyCash)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 text-left">{{ $pettyCash->date }}</td>
                    <td class="p-2 text-right">${{ number_format($totalSales - $totalExpenses, 2) }}</td>
                    <td class="p-2 text-left">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $pettyCash->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $pettyCash->status === 'open' ? 'Abierta' : 'Cerrada' }}
                        </span>
                    </td>
                    <td class="p-2 text-left space-x-1">
                        <a href="{{ route('petty-cash.show', $pettyCash) }}" 
                           class="btn-action btn-view">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('petty-cash.edit', $pettyCash) }}" 
                           class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('petty-cash.destroy', $pettyCash) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-action btn-delete"
                                    onclick="return confirm('¿Estás seguro de eliminar esta caja chica?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                        @if ($pettyCash->status === 'open')
                            <button onclick="openModal('{{ $pettyCash->id }}')" 
                                    class="btn-action btn-close">
                                <i class="fas fa-lock"></i> Cerrar
                            </button>
                        @endif
                    </td>
                    <td class="p-2 text-left">
                        @if ($pettyCash->status === 'closed')
                            <a href="{{ route('petty-cash.print', $pettyCash) }}" 
                               target="_blank"
                               class="btn-action btn-print">
                                <i class="fas fa-print"></i> PDF
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de cierre -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-6 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <!-- Cabecera del modal -->
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900">Cierre de Caja Chica</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Contenido del modal -->
        <div class="modal-grid mt-4">
            <!-- Tabla de denominaciones -->
            <div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium mb-3">Conteo de Efectivo</h4>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 text-left">Denominación</th>
                                <th class="p-2">Cantidad</th>
                                <th class="p-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([0.1, 0.2, 0.3, 0.5, 1, 2, 5, 10, 20, 50, 100, 200] as $denominacion)
                            <tr class="border-b">
                                <td class="p-2">${{ number_format($denominacion, 2) }}</td>
                                <td class="p-2">
                                    <input type="number" min="0" 
                                           class="w-full border rounded p-1 text-sm contar-input" 
                                           data-denominacion="{{ $denominacion }}" 
                                           placeholder="0">
                                </td>
                                <td class="p-2 text-right">
                                    <span class="subtotal">$0.00</span>
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-100 font-medium">
                                <td colspan="2" class="p-2 text-right">Total Efectivo:</td>
                                <td class="p-2 text-right">
                                    <span id="total">$0.00</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de cierre -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-medium mb-3">Resumen de Cierre</h4>
                
                <div class="space-y-4">
                    <div class="input-group">
                        <label for="gastos" class="input-label">Gastos</label>
                        <input type="number" id="gastos" class="modal-input" 
                               value="{{ $totalExpenses }}" step="0.01">
                    </div>
                    
                    <div class="input-group">
                        <label for="ventas-efectivo" class="input-label">Ventas en Efectivo</label>
                        <input type="number" id="ventas-efectivo" class="modal-input" 
                               value="0" step="0.01" readonly>
                    </div>
                    
                    <div class="input-group">
                        <label for="ventas-qr" class="input-label">Ventas QR</label>
                        <input type="number" id="ventas-qr" class="modal-input" 
                               value="{{ $totalSalesQR }}" step="0.01">
                    </div>
                    
                    <div class="input-group">
                        <label for="ventas-tarjeta" class="input-label">Ventas Tarjeta</label>
                        <input type="number" id="ventas-tarjeta" class="modal-input" 
                               value="{{ $totalSalesCard }}" step="0.01">
                    </div>
                    
                    <div class="pt-4">
                        
                        <button onclick="saveClosure()" 
                                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            <i class="fas fa-save mr-1"></i> Guardar Cierre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para cierre -->
<form id="closureForm" action="{{ route('petty-cash.save-closure') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="petty_cash_id" id="petty_cash_id">
    <input type="hidden" name="total_sales_cash" id="total_sales_cash">
    <input type="hidden" name="total_sales_qr" id="total_sales_qr">
    <input type="hidden" name="total_sales_card" id="total_sales_card">
    <input type="hidden" name="total_expenses" id="total_expenses">
</form>

<script>
    // Función para abrir el modal con el ID correcto
    function openModal(id) {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('petty_cash_id').value = id;
        
        // Resetear los inputs al abrir el modal
        document.querySelectorAll('.contar-input').forEach(input => {
            input.value = '';
        });
        document.querySelectorAll('.subtotal').forEach(span => {
            span.textContent = '$0.00';
        });
        document.getElementById('total').textContent = '$0.00';
        document.getElementById('ventas-efectivo').value = '0';
    }

    // Función para cerrar el modal
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    // Calcular subtotales y total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.contar-input').forEach(input => {
            const denominacion = parseFloat(input.getAttribute('data-denominacion'));
            const cantidad = parseFloat(input.value) || 0;
            const subtotal = denominacion * cantidad;
            
            const subtotalElement = input.closest('tr').querySelector('.subtotal');
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
            
            total += subtotal;
        });
        
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;
        document.getElementById('ventas-efectivo').value = total.toFixed(2);
        document.getElementById('total_sales_cash').value = total.toFixed(2);
    }

    // Guardar el cierre
    function saveClosure() {
        const pettyCashId = document.getElementById('petty_cash_id').value;
        const totalSalesCash = parseFloat(document.getElementById('ventas-efectivo').value) || 0;
        const totalSalesQR = parseFloat(document.getElementById('ventas-qr').value) || 0;
        const totalSalesCard = parseFloat(document.getElementById('ventas-tarjeta').value) || 0;
        const totalExpenses = parseFloat(document.getElementById('gastos').value) || 0;

        if (!pettyCashId) {
            alert('Error: No se ha seleccionado una caja chica');
            return;
        }

        // Validar que al menos haya un valor ingresado
        if (totalSalesCash === 0 && totalSalesQR === 0 && totalSalesCard === 0 && totalExpenses === 0) {
            if (!confirm('¿Estás seguro de cerrar la caja sin registrar movimientos?')) {
                return;
            }
        }

        // Configurar los datos del formulario
        document.getElementById('total_sales_cash').value = totalSalesCash;
        document.getElementById('total_sales_qr').value = totalSalesQR;
        document.getElementById('total_sales_card').value = totalSalesCard;
        document.getElementById('total_expenses').value = totalExpenses;

        // Enviar el formulario
        fetch("{{ route('petty-cash.save-closure') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                petty_cash_id: pettyCashId,
                total_sales_cash: totalSalesCash,
                total_sales_qr: totalSalesQR,
                total_sales_card: totalSalesCard,
                total_expenses: totalExpenses
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cierre guardado correctamente');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar el cierre'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el formulario');
        });
    }

    // Cerrar todas las cajas abiertas
    function closeOpenPettyCash() {
        if (confirm('¿Estás seguro de cerrar todas las cajas chicas abiertas?')) {
            fetch("{{ route('petty-cash.close-all-open') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudieron cerrar las cajas'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cerrar las cajas');
            });
        }
    }

    // Event listeners para cálculos automáticos
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.contar-input').forEach(input => {
            input.addEventListener('input', calcularTotal);
        });
    });
</script>
@endsection