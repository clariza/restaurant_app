<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PettyCashController;

// Redirección raíz
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// ============================================
// RUTAS DE AUTENTICACIÓN (Sin autenticación)
// ============================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('web')
    ->name('logout');

// ============================================
// RUTAS CON AUTENTICACIÓN (Sin caja chica)
// ============================================
Route::middleware(['auth'])->group(function () {

    // --- PETTY CASH (Caja Chica) ---
    Route::get('/petty-cash/create', [PettyCashController::class, 'create'])
        ->name('petty-cash.create');
    Route::post('/petty-cash', [PettyCashController::class, 'store'])
        ->name('petty-cash.store');

    // 🔥 NUEVA RUTA: Para obtener datos JSON del cierre
    Route::get('/petty-cash/closure-data', [PettyCashController::class, 'getClosureData'])
        ->name('petty-cash.closure-data');

    // Esta ruta sigue retornando HTML para el modal principal
    Route::get('/petty-cash/modal-content', [PettyCashController::class, 'modalContent'])
        ->name('petty-cash.modal-content');

    Route::get('/petty-cash/check-status', [PettyCashController::class, 'checkStatus'])
        ->name('petty-cash.check-status');
    Route::get('/petty-cash/check-open', [PettyCashController::class, 'checkOpen'])
        ->name('petty-cash.check-open');
    Route::get('/petty-cash/export/excel', [PettyCashController::class, 'exportExcel'])
        ->name('petty-cash.export.excel');
    Route::get('/petty-cash/export/pdf', [PettyCashController::class, 'exportPdf'])
        ->name('petty-cash.export.pdf');
    Route::post('/petty-cash/save-closure', [PettyCashController::class, 'saveClosure'])
        ->name('petty-cash.save-closure');
    Route::post('/petty-cash/close-all-open', [PettyCashController::class, 'closeAllOpen'])
        ->name('petty-cash.close-all-open');
    Route::get('/petty-cash/{pettyCash}/print', [PettyCashController::class, 'print'])
        ->name('petty-cash.print');
    Route::resource('petty-cash', PettyCashController::class)->except(['store', 'create']);

    // --- EXPENSES (Gastos - Solo lectura) ---
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');

    // --- CUSTOMER DETAILS ---
    Route::get('/customer-details', function () {
        return view('customer-details');
    })->name('customer.details');

    // --- ORDERS (Órdenes) ---
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // --- PROFORMAS ---
    Route::get('/proformas/{proforma}', [ProformaController::class, 'show'])->name('proformas.show');
    Route::get('/proformas/{proforma}/print', [ProformaController::class, 'print'])->name('proformas.print');

    // --- INVENTORY (Inventario) ---
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('/inventory/{id}/movements', [InventoryController::class, 'itemMovements'])->name('inventory.item-movements');

    // --- TABLES (Mesas - Consulta) ---

    Route::get('/tables/available', [TableController::class, 'available'])->name('tables.available');
    Route::get('/tables/stats', [TableController::class, 'getTablesStats'])->name('tables.stats');
    Route::get('/tables/{id}/status', [TableController::class, 'getTableStatus'])->name('tables.status');

    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/tables/{table}', [TableController::class, 'show'])->name('tables.show');
    // --- SALES (Ventas - Solo consulta) ---
    Route::get('/api/sales/next-order-number', [SaleController::class, 'getNextOrderNumber'])
        ->name('sales.nextOrderNumber');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    // --- ITEMS (Productos) ---
    Route::resource('items', ItemsController::class);
    Route::get('/items/search', [ItemsController::class, 'search'])->name('items.search');

    // --- CATEGORIES ---
    Route::resource('categories', CategoryController::class);

    // --- SUPPLIERS ---
    Route::resource('suppliers', SupplierController::class);
});

// ============================================
// RUTAS QUE REQUIEREN CAJA CHICA ABIERTA
// ============================================
Route::middleware(['auth', 'check.pettycash'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/admin/dashboard', [SaleController::class, 'dashboard'])->name('admin.dashboard');

    // --- MENU ---
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

    // --- SALES (Ventas - Creación) ---
    Route::post('/sales', [SaleController::class, 'store'])
        ->middleware('api')
        ->name('sales.store');
    Route::post('/check-stock', [SaleController::class, 'checkStock']);
    Route::post('/calculate-total-cash', [SaleController::class, 'calculateTotalCash']);

    // --- EXPENSES (Gastos - Creación/Edición/Eliminación) ---
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // --- PROFORMAS (Creación y conversión) ---
    Route::post('/proformas', [ProformaController::class, 'store']);
    Route::post('/proformas/{proforma}/convert', [ProformaController::class, 'convertToOrder'])
        ->name('proformas.convert');

    // --- INVENTORY (Actualización de stock) ---
    Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])
        ->name('inventory.update-stock');

    // --- TABLES (Mesas - Modificación) ---
    Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
    Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
    Route::post('/tables/{id}/change-availability', [TableController::class, 'changeAvailability'])
        ->name('tables.change-availability');
    Route::post('/tables/{table}/state', [TableController::class, 'updateState'])
        ->name('tables.update-state');
    Route::post('/tables/bulk-state', [TableController::class, 'bulkChangeState'])
        ->name('tables.bulk-state');

    // --- DELIVERY ---
    Route::resource('deliveries', DeliveryServiceController::class);

    // --- SETTINGS ---
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
});

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // --- USERS ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // --- PURCHASES ---
    // Dentro del grupo de rutas de purchases
    Route::get('/purchases/search-products', [PurchaseController::class, 'searchProducts'])->name('purchases.searchProducts');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/purchases/product-details/{id}', [PurchaseController::class, 'getProductDetails'])
        ->name('purchases.productDetails');
});
