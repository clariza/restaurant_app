// js/init.js - Cargar después de todos los scripts
document.addEventListener('DOMContentLoaded', function () {
    console.log('🔧 Inicializando aplicación...');

    // Verificar que todas las funciones esenciales estén disponibles
    const essentialFunctions = [
        'updateOrderDetails',
        'updateStockBadge',
        'openPaymentModal',
        'initializeOrderSystem'
    ];

    essentialFunctions.forEach(funcName => {
        if (typeof window[funcName] !== 'function') {
            console.error(`❌ Función esencial no disponible: ${funcName}`);
        } else {
            console.log(`✅ ${funcName} disponible`);
        }
    });

    // Inicializar sistema de pedidos si está disponible
    if (typeof initializeOrderSystem === 'function') {
        initializeOrderSystem();
    }
});