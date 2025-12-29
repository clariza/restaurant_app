
/**
 * Cargar proforma en el sistema de pedidos
 * @param {number} proformaId - ID de la proforma a cargar
 */
function loadProformaToOrder(proformaId) {
    console.log('🔄 Cargando proforma:', proformaId);

    // Mostrar loading
    Swal.fire({
        title: 'Cargando proforma...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Obtener datos de la proforma
    fetch(`/proformas/${proformaId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener la proforma');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.proforma) {
                const proforma = data.proforma;

                if (!proforma.items || proforma.items.length === 0) {
                    throw new Error('La proforma no tiene items');
                }

                // Verificar si ya tiene items en el pedido
                const currentOrder = localStorage.getItem('order');
                if (currentOrder && JSON.parse(currentOrder).length > 0) {
                    Swal.fire({
                        title: '¿Reemplazar pedido actual?',
                        text: 'Ya tienes items en el pedido. ¿Deseas reemplazarlos con la proforma?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#203363',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, reemplazar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processProformaConversion(proforma, proformaId);
                        }
                    });
                } else {
                    processProformaConversion(proforma, proformaId);
                }

            } else {
                throw new Error(data.message || 'Error al cargar la proforma');
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo cargar la proforma',
                confirmButtonColor: '#EF476F'
            });
        });
}

/**
 * Procesar la conversión de proforma a orden
 * @param {Object} proforma - Datos de la proforma
 * @param {number} proformaId - ID de la proforma
 */
function processProformaConversion(proforma, proformaId) {
    try {
        const orderItems = proforma.items.map(item => {
            const menuItem = item.menu_item || {};

            return {
                id: item.menu_item_id,
                name: item.item_name,
                price: parseFloat(item.price),
                quantity: parseFloat(item.quantity),
                stock: menuItem.stock || 999,
                stock_type: menuItem.stock_type || 'discrete',
                stock_unit: menuItem.stock_unit || 'unidades',
                min_stock: menuItem.min_stock || 0,
                manage_inventory: menuItem.manage_inventory || false
            };
        });

        localStorage.setItem('order', JSON.stringify(orderItems));
        localStorage.setItem('converting_proforma_id', proformaId);

        const proformaInfo = {
            id: proformaId,
            customer_name: proforma.customer_name,
            customer_phone: proforma.customer_phone,
            notes: proforma.notes,
            order_type: proforma.order_type,
            loaded_at: new Date().toISOString()
        };
        localStorage.setItem('proforma_info', JSON.stringify(proformaInfo));

        if (typeof updateOrderDetails === 'function') {
            updateOrderDetails();
        } else {
            setTimeout(() => {
                if (typeof updateOrderDetails === 'function') {
                    updateOrderDetails();
                }
            }, 500);
        }

        Swal.fire({
            icon: 'success',
            title: '¡Proforma cargada!',
            html: `
                <p>La proforma <strong>#PROF-${proformaId}</strong> ha sido cargada.</p>
                <p class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-info-circle"></i>
                    Puedes modificar los items y proceder con el pago.
                </p>
            `,
            confirmButtonText: 'Continuar',
            confirmButtonColor: '#203363'
        });

        scrollToOrderPanel();

    } catch (error) {
        console.error('❌ Error en processProformaConversion:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo procesar la proforma',
            confirmButtonColor: '#EF476F'
        });
    }
}
/**
 * Scroll al panel de pedidos en móviles
 */
function scrollToOrderPanel() {
    const orderPanel = document.getElementById('order-panel');
    if (orderPanel && window.innerWidth < 768) {
        orderPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Marcar proforma como convertida en el backend
 * @param {number} proformaId - ID de la proforma
 * @param {number} orderId - ID de la orden creada
 */
function markProformaAsConverted(proformaId, orderId) {
    return fetch(`/proformas/${proformaId}/mark-converted`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Proforma marcada como convertida:', data);
            }
            return data;
        })
        .catch(error => {
            console.error('⚠️ Error al marcar proforma:', error);
            return { success: false, error: error.message };
        });
}

/**
 * Limpiar datos de conversión de proforma
 */
function clearProformaConversion() {
    localStorage.removeItem('converting_proforma_id');
    localStorage.removeItem('proforma_info');
    console.log('🧹 Datos de proforma limpiados');
}

/**
 * Obtener información de la proforma en conversión
 * @returns {Object|null} Información de la proforma o null
 */
function getConvertingProformaInfo() {
    const proformaId = localStorage.getItem('converting_proforma_id');
    const proformaInfoJson = localStorage.getItem('proforma_info');

    if (proformaId && proformaInfoJson) {
        try {
            return JSON.parse(proformaInfoJson);
        } catch (e) {
            console.error('Error al parsear proforma_info:', e);
            return { id: proformaId };
        }
    }

    return null;
}

// Exportar funciones si se usa módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        loadProformaToOrder,
        markProformaAsConverted,
        clearProformaConversion,
        getConvertingProformaInfo
    };
}