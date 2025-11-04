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

// Route::get('/', function () {
//     return view('dashboard');
// });
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('web')
    ->name('logout');


// Rutas de apertura de caja (sin middleware de caja abierta)
Route::middleware(['auth'])->group(function () {
    Route::get('/petty-cash/create', [PettyCashController::class, 'create'])
        ->name('petty-cash.create');
    Route::post('/petty-cash', [PettyCashController::class, 'store'])
        ->name('petty-cash.store');
    Route::get('/petty-cash/modal-content', [PettyCashController::class, 'modalContent'])
        ->name('petty-cash.modal-content');

    // Rutas de reportes
    Route::get('/petty-cash/export/excel', [PettyCashController::class, 'exportExcel'])
        ->name('petty-cash.export.excel');
    Route::get('/petty-cash/export/pdf', [PettyCashController::class, 'exportPdf'])
        ->name('petty-cash.export.pdf');

    Route::post('/petty-cash/save-closure', [PettyCashController::class, 'saveClosure'])
        ->name('petty-cash.save-closure');
    Route::post('/petty-cash/close-all-open', [PettyCashController::class, 'closeAllOpen'])
        ->name('petty-cash.close-all-open');
    Route::get('/petty-cash/check-open', [PettyCashController::class, 'checkOpen'])->name('petty-cash.check-open');
    Route::get('/petty-cash/{pettyCash}/print', [PettyCashController::class, 'print'])
        ->name('petty-cash.print');
    Route::resource('petty-cash', PettyCashController::class);
});

// Todas las demás rutas requieren caja abierta
Route::middleware(['auth', 'check.pettycash'])->group(function () {
    Route::get('/admin/dashboard', [SaleController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    // Rutas para Delivery
    Route::resource('deliveries', DeliveryServiceController::class);
});



//Route::get('/admin/dashboard', [SaleController::class, 'dashboard'])->name('admin.dashboard');
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin/dashboard', [SaleController::class, 'dashboard'])->name('admin.dashboard');
// });

// Ruta para el menú (submenú de Ventas)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');


// Ruta para mostrar la lista de ventas
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');

// Ruta para mostrar los detalles de las ventas
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

Route::post('/sales', [SaleController::class, 'store'])
    ->middleware('api') // Usar el middleware 'api' en lugar de 'web'
    ->name('sales.store');

// Ruta para la vista de detalles del cliente
Route::get('/customer-details', function () {
    return view('customer-details');
})->name('customer.details');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Rutas para la búsqueda de productos (Items)

    // Rutas para compras
    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->name('purchases.store');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');


    // Rutas para la búsqueda de productos
    Route::get('/purchases/search-products', [PurchaseController::class, 'searchProducts'])
        ->name('purchases.searchProducts');
    Route::get('/purchases/product-details/{id}', [PurchaseController::class, 'getProductDetails'])
        ->name('purchases.productDetails');
});

// Rutas para compras (solo admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('purchases', PurchaseController::class);
});


// Rutas para Producto (MenuItem)
Route::resource('items', ItemsController::class);
Route::get('/items/search', [ItemsController::class, 'search'])->name('items.search');

// Rutas para category
Route::resource('categories', CategoryController::class);

// Ruta para procesar la actualización de una categoría
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
// Ruta para mostrar el formulario de edición
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

Route::get('/items/create', [ItemsController::class, 'create'])->name('items.create');
Route::get('/items/{item}/edit', [ItemsController::class, 'edit'])->name('items.edit'); // Mostrar formulario de edición

// Route::resource('purchases', PurchaseController::class);
Route::resource('suppliers', SupplierController::class);

Route::resource('expenses', ExpenseController::class);
//Ruta para procesar calcular el total efectivo de denominaciones

Route::post('/proformas/{proforma}/convert', [ProformaController::class, 'convertToOrder'])->name('proformas.convert');
// Rutas para órdenes
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

// Rutas para proformas
Route::post('/proformas', [ProformaController::class, 'store']);
Route::get('/proformas/{proforma}', [ProformaController::class, 'show'])->name('proformas.show');
Route::get('/proformas/{proforma}/print', [ProformaController::class, 'print'])->name('proformas.print');
Route::post('/proformas/{proforma}/convert', [ProformaController::class, 'convertToOrder'])
    ->middleware('auth')
    ->name('proformas.convert');
// Agrega esta ruta
Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
// Rutas para inventario
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
Route::get('/inventory/{id}/movements', [InventoryController::class, 'itemMovements'])->name('inventory.item-movements');
// En routes/api.php
Route::post('/check-stock', [SaleController::class, 'checkStock']);
Route::post('/calculate-total-cash', [SaleController::class, 'calculateTotalCash']);

// Rutas para Table

Route::get('/tables/available', [TableController::class, 'available'])
    ->name('tables.available');
Route::get('/tables/stats', [TableController::class, 'getTablesStats'])
    ->name('tables.stats');
Route::get('/tables/{id}/status', [TableController::class, 'getTableStatus'])
    ->name('tables.status');
Route::post('/tables/{id}/change-availability', [TableController::class, 'changeAvailability'])
    ->name('tables.change-availability');
Route::post('/tables/{table}/state', [TableController::class, 'updateState'])
    ->name('tables.update-state');
Route::post('/tables/bulk-state', [TableController::class, 'bulkChangeState'])
    ->name('tables.bulk-state');
Route::resource('tables', TableController::class);
Route::get('/petty-cash/modal-content', [PettyCashController::class, 'modalContent'])
    ->name('petty-cash.modal-content');
