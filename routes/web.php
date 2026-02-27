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
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;

// ============================================
// REDIRECCIÓN RAÍZ
// ============================================
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

    // ─── BRANCHES (Solo admin) ── ESTÁTICAS PRIMERO ──────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
        Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
            ->name('branches.toggle-status');
    });

    // ─── BRANCHES (Lectura pública) ── DINÁMICAS DESPUÉS ─────────
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('branches.show');

    // ─── PETTY CASH (No requieren caja abierta) ───────────────────
    Route::get('/petty-cash/create', [PettyCashController::class, 'create'])
        ->name('petty-cash.create');
    Route::post('/petty-cash', [PettyCashController::class, 'store'])
        ->name('petty-cash.store');
    Route::get('/petty-cash/get-open', [PettyCashController::class, 'getOpenPettyCash'])
        ->name('petty-cash.get-open');
    Route::get('/petty-cash/modal-closure/{id}', [PettyCashController::class, 'getModalClosure'])
        ->name('petty-cash.modal-closure');
    Route::get('/petty-cash/closure-modal-content', [PettyCashController::class, 'closureModalContent'])
        ->name('petty-cash.closure-modal-content');
    Route::get('/petty-cash/check-status', [PettyCashController::class, 'checkStatus'])
        ->name('petty-cash.check-status');
    Route::get('/petty-cash/check-open', [PettyCashController::class, 'checkOpen'])
        ->name('petty-cash.check-open');
    Route::get('/petty-cash/closure-data', [PettyCashController::class, 'getClosureData'])
        ->name('petty-cash.closure-data');
    Route::get('/petty-cash/modal-content', [PettyCashController::class, 'modalContent'])
        ->name('petty-cash.modal-content');
    Route::get('/petty-cash/print-previous', [PettyCashController::class, 'printPrevious'])
        ->name('petty-cash.print-previous');
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
    Route::resource('petty-cash', PettyCashController::class)
        ->except(['store', 'create']);

    // ─── DELIVERY API ─────────────────────────────────────────────
    Route::get('/api/delivery-services', [DeliveryServiceController::class, 'getActiveServices'])
        ->name('deliveries.api.active');

    // ─── INVENTORY ── ESTÁTICAS PRIMERO ──────────────────────────
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/movements', [InventoryController::class, 'movements'])->name('movements');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('lowStock');
        Route::get('/report', [InventoryController::class, 'report'])->name('report');
        Route::get('/proformas', [ProformaController::class, 'index'])->name('proformas.index');
        Route::get('/proformas/{id}/can-convert', [ProformaController::class, 'canBeConverted'])
            ->name('proformas.can-convert');
        Route::post('/proformas/{id}/mark-converted', [ProformaController::class, 'markAsConverted'])
            ->name('proformas.mark-converted');
        Route::get('/proformas/{id}', [ProformaController::class, 'show'])->name('proformas.show');
        Route::get('/{id}/movements', [InventoryController::class, 'itemMovements'])->name('itemMovements');
        Route::get('/', [InventoryController::class, 'index'])->name('index');
    });

    // ─── EXPENSES ── ESTÁTICAS PRIMERO ───────────────────────────
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');

    // ─── ORDERS ── ESTÁTICAS PRIMERO ─────────────────────────────
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // ─── PROFORMAS ── ESTÁTICAS PRIMERO ──────────────────────────
    Route::get('/proformas/{proforma}/print', [ProformaController::class, 'print'])->name('proformas.print');
    Route::get('/proformas/{proforma}', [ProformaController::class, 'show'])->name('proformas.show');

    // ─── TABLES ── ESTÁTICAS PRIMERO ─────────────────────────────
    Route::get('/tables/available', [TableController::class, 'available'])->name('tables.available');
    Route::get('/tables/stats', [TableController::class, 'getTablesStats'])->name('tables.stats');
    Route::get('/tables/{id}/status', [TableController::class, 'getTableStatus'])->name('tables.status');
    Route::get('/tables/{table}', [TableController::class, 'show'])->name('tables.show');

    // ─── SALES ── ESTÁTICAS PRIMERO ──────────────────────────────
    Route::get('/api/sales/next-order-number', [SaleController::class, 'getNextOrderNumber'])
        ->name('sales.nextOrderNumber');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    // ─── ITEMS ───────────────────────────────────────────────────
    Route::get('/items/search', [ItemsController::class, 'search'])->name('items.search');
    Route::resource('items', ItemsController::class);

    // ─── CATEGORIES ──────────────────────────────────────────────
    Route::resource('categories', CategoryController::class);

    // ─── SUPPLIERS ───────────────────────────────────────────────
    Route::resource('suppliers', SupplierController::class);

    // ─── CLIENTS ─────────────────────────────────────────────────
    Route::post('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
        ->name('clients.toggle-status');
    Route::resource('clients', ClientController::class);

    // ─── CUSTOMER DETAILS ────────────────────────────────────────
    Route::get('/customer-details', function () {
        return view('customer-details');
    })->name('customer.details');

    // ─── SETTINGS (Lectura) ───────────────────────────────────────
    Route::get('/settings/tables-status', [SettingController::class, 'getTablesStatus'])
        ->name('settings.tables.status');
});

// ============================================
// RUTAS QUE REQUIEREN CAJA CHICA ABIERTA
// ============================================
Route::middleware(['auth', 'check.pettycash'])->group(function () {

    // ─── DASHBOARD ───────────────────────────────────────────────
    Route::get('/admin/dashboard', [SaleController::class, 'dashboard'])
        ->name('admin.dashboard');

    // ─── MENU ────────────────────────────────────────────────────
    Route::get('/menu', [MenuController::class, 'index'])
        ->name('menu.index');

    // ─── ORDERS (Lista) ──────────────────────────────────────────
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    // ─── TABLES (Lista) ──────────────────────────────────────────
    Route::get('/tables', [TableController::class, 'index'])
        ->name('tables.index');

    // ─── SALES ── ESTÁTICAS PRIMERO ──────────────────────────────
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])
        ->middleware('api')
        ->name('sales.store');
    Route::post('/check-stock', [SaleController::class, 'checkStock']);
    Route::post('/calculate-total-cash', [SaleController::class, 'calculateTotalCash']);

    // ─── EXPENSES (Creación/Edición/Eliminación) ──────────────────
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // ─── PROFORMAS (Creación y conversión) ───────────────────────
    Route::post('/proformas', [ProformaController::class, 'store']);
    Route::post('/proformas/{proforma}/convert', [ProformaController::class, 'convertToOrder'])
        ->name('proformas.convert');

    // ─── INVENTORY (Actualización) ────────────────────────────────
    Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])
        ->name('inventory.update-stock');

    // ─── TABLES (Modificación) ── ESTÁTICAS PRIMERO ───────────────
    Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::post('/tables/bulk-state', [TableController::class, 'bulkChangeState'])
        ->name('tables.bulk-state');
    Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
    Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
    Route::post('/tables/{id}/change-availability', [TableController::class, 'changeAvailability'])
        ->name('tables.change-availability');
    Route::post('/tables/{table}/state', [TableController::class, 'updateState'])
        ->name('tables.update-state');

    // ─── DELIVERY ────────────────────────────────────────────────
    Route::resource('deliveries', DeliveryServiceController::class);

    // ─── SETTINGS (Escritura) ─────────────────────────────────────
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
});

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // ─── USERS ───────────────────────────────────────────────────
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ─── PURCHASES ── ESTÁTICAS PRIMERO ──────────────────────────
    Route::get('/purchases/search-products', [PurchaseController::class, 'searchProducts'])
        ->name('purchases.searchProducts');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::get('/purchases/product-details/{id}', [PurchaseController::class, 'getProductDetails'])
        ->name('purchases.productDetails');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
});