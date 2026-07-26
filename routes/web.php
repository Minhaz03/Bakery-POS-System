<?php

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomOrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Saas\AuthController;
use App\Http\Controllers\Saas\ImpersonationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockLedgerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $plans = [];
    try {
        $plans = Plan::all();
    } catch (Exception $e) {
        // Fallback to empty if db is not migrated yet
    }

    return view('welcome', compact('plans'));
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'subscribed'])
    ->name('dashboard');

// Billing & Subscription Routes (Exempt from 'subscribed' middleware)
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
});

// Dashboard Subpages (Protected by 'subscribed' middleware)
Route::middleware(['auth', 'verified', 'subscribed'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::patch('products/{product}/toggle-stock', [ProductController::class, 'toggleStock'])->name('products.toggle-stock');
    Route::get('pos-items', [ProductController::class, 'posItems'])->name('pos-items');
    Route::get('pos-items/create', [ProductController::class, 'posItemCreate'])->name('pos-items.create');
    Route::post('pos-items', [ProductController::class, 'posItemStore'])->name('pos-items.store');
    Route::get('pos-items/{product}/edit', [ProductController::class, 'posItemEdit'])->name('pos-items.edit');
    Route::put('pos-items/{product}', [ProductController::class, 'posItemUpdate'])->name('pos-items.update');

    // CRUD Resources with custom route names matching the UI sidebar
    Route::resource('products', ProductController::class)->parameters([
        'products' => 'product',
    ])->names([
        'index' => 'products',
        'create' => 'products.create',
        'store' => 'products.store',
        'show' => 'products.show',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy',
    ]);

    Route::resource('categories', CategoryController::class)->parameters([
        'categories' => 'category',
    ])->names([
        'index' => 'categories',
        'create' => 'categories.create',
        'store' => 'categories.store',
        'show' => 'categories.show',
        'edit' => 'categories.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    Route::resource('suppliers', SupplierController::class)->parameters([
        'suppliers' => 'supplier',
    ])->names([
        'index' => 'suppliers',
        'create' => 'suppliers.create',
        'store' => 'suppliers.store',
        'show' => 'suppliers.show',
        'edit' => 'suppliers.edit',
        'update' => 'suppliers.update',
        'destroy' => 'suppliers.destroy',
    ]);

    Route::resource('customers', CustomerController::class)->parameters([
        'customers' => 'customer',
    ])->names([
        'index' => 'customers',
        'create' => 'customers.create',
        'store' => 'customers.store',
        'show' => 'customers.show',
        'edit' => 'customers.edit',
        'update' => 'customers.update',
        'destroy' => 'customers.destroy',
    ]);

    Route::resource('units', UnitController::class)->parameters([
        'units' => 'unit',
    ])->names([
        'index' => 'units',
        'create' => 'units.create',
        'store' => 'units.store',
        'show' => 'units.show',
        'edit' => 'units.edit',
        'update' => 'units.update',
        'destroy' => 'units.destroy',
    ]);

    Route::resource('brands', BrandController::class)->parameters([
        'brands' => 'brand',
    ])->names([
        'index' => 'brands',
        'create' => 'brands.create',
        'store' => 'brands.store',
        'show' => 'brands.show',
        'edit' => 'brands.edit',
        'update' => 'brands.update',
        'destroy' => 'brands.destroy',
    ]);

    Route::get('/stock-ledger', [StockLedgerController::class, 'stockLedger'])->name('stock-ledger');
    Route::post('/stock-ledger/adjust', [StockLedgerController::class, 'adjustStock'])->name('stock-ledger.adjust');
    Route::get('/stock-ledger/export', [StockLedgerController::class, 'exportExcel'])->name('stock-ledger.export');
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::resource('purchases', PurchaseController::class)->parameters([
        'purchases' => 'purchase',
    ])->names([
        'index' => 'purchases',
        'create' => 'purchases.create',
        'store' => 'purchases.store',
        'show' => 'purchases.show',
        'edit' => 'purchases.edit',
        'update' => 'purchases.update',
        'destroy' => 'purchases.destroy',
    ]);
    Route::get('/pos-terminal', [PosController::class, 'posTerminal'])->name('pos-terminal');
    Route::post('/pos-terminal/checkout', [PosController::class, 'checkout'])->name('pos-terminal.checkout');
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::post('sales/{sale}/collect-payment', [SaleController::class, 'collectPayment'])->name('sales.collect-payment');
    Route::resource('sales', SaleController::class)->parameters(['sales' => 'sale'])->except(['create', 'store'])->names([
        'index' => 'sales',
        'show' => 'sales.show',
        'edit' => 'sales.edit',
        'update' => 'sales.update',
        'destroy' => 'sales.destroy',
    ]);
    Route::resource('recipes', RecipeController::class)->parameters([
        'recipes' => 'recipe',
    ])->names([
        'index' => 'recipes',
        'create' => 'recipes.create',
        'store' => 'recipes.store',
        'show' => 'recipes.show',
        'edit' => 'recipes.edit',
        'update' => 'recipes.update',
        'destroy' => 'recipes.destroy',
    ]);
    Route::get('/production', [ProductionController::class, 'production'])->name('production');
    Route::get('/production/{batch}', [ProductionController::class, 'show'])->name('production.show');
    Route::post('/production', [ProductionController::class, 'store'])->name('production.store');
    Route::patch('/production/{batch}/complete', [ProductionController::class, 'complete'])->name('production.complete');
    Route::patch('/production/{batch}/cancel', [ProductionController::class, 'cancel'])->name('production.cancel');
    Route::get('/custom-orders', [CustomOrderController::class, 'customOrders'])->name('custom-orders');
    Route::post('/custom-orders', [CustomOrderController::class, 'store'])->name('custom-orders.store');
    Route::get('/custom-orders/{order}', [CustomOrderController::class, 'show'])->name('custom-orders.show');
    Route::get('/custom-orders/{order}/print', [CustomOrderController::class, 'print'])->name('custom-orders.print');
    Route::patch('/custom-orders/{order}/cancel', [CustomOrderController::class, 'cancel'])->name('custom-orders.cancel');
    Route::patch('/custom-orders/{order}/status', [CustomOrderController::class, 'updateStatus'])->name('custom-orders.status');
    Route::post('/custom-orders/{order}/payment', [CustomOrderController::class, 'addPayment'])->name('custom-orders.payment');
    Route::get('/analytics', [AnalyticsController::class, 'analytics'])->name('analytics');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
        Route::get('/purchases', [ReportController::class, 'purchasesReport'])->name('purchases');
        Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
        Route::get('/production', [ReportController::class, 'productionReport'])->name('production');
        Route::get('/profit-loss', [ReportController::class, 'profitLossReport'])->name('profit-loss');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/impersonate/leave', [ImpersonationController::class, 'leave'])->name('impersonation.leave');
});

// Admin Routes (now merged into dashboard prefix)
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
    // General Settings & Profile
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Module Control Panel
    Route::middleware('can:modules.manage')->group(function () {
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::post('/modules/infrastructure/{module}', [ModuleController::class, 'toggleInfrastructure'])->name('modules.toggle-infrastructure');
        Route::post('/modules/business-type', [ModuleController::class, 'setBusinessType'])->name('modules.set-business-type');
    });

    // ── User Management ──
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ── Roles & Permissions ──
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.sync-permissions');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

// ── SaaS Super Admin Routes ──
Route::prefix('saas')->name('saas.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('saas.subscriptions.index');
        });
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::resource('plans', PlanController::class)->except(['show']);
        Route::get('users', [App\Http\Controllers\Saas\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [App\Http\Controllers\Saas\UserController::class, 'show'])->name('users.show');
        Route::post('impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');
    });
});

require __DIR__.'/auth.php';
