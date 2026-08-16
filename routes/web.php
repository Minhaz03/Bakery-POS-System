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
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupportTicketController;
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

// SSLCommerz Callbacks (Exempt from auth middleware due to cross-site POST)
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::post('/billing/payment/success', [BillingController::class, 'paymentSuccess'])->name('billing.payment.success');
    Route::post('/billing/payment/fail', [BillingController::class, 'paymentFail'])->name('billing.payment.fail');
    Route::post('/billing/payment/cancel', [BillingController::class, 'paymentCancel'])->name('billing.payment.cancel');
    Route::post('/billing/payment/ipn', [BillingController::class, 'paymentIpn'])->name('billing.payment.ipn');
});

// Dashboard Subpages (Protected by 'subscribed' middleware)
Route::middleware(['auth', 'verified', 'subscribed'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Products & POS Items
    Route::middleware('can:products.view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('pos-items', [ProductController::class, 'posItems'])->name('pos-items');
    });
    Route::middleware('can:products.create')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('pos-items/create', [ProductController::class, 'posItemCreate'])->name('pos-items.create');
        Route::post('pos-items', [ProductController::class, 'posItemStore'])->name('pos-items.store');
    });
    Route::middleware('can:products.edit')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('products/{product}/toggle-stock', [ProductController::class, 'toggleStock'])->name('products.toggle-stock');
        Route::get('pos-items/{product}/edit', [ProductController::class, 'posItemEdit'])->name('pos-items.edit');
        Route::put('pos-items/{product}', [ProductController::class, 'posItemUpdate'])->name('pos-items.update');
    });
    Route::middleware('can:products.delete')->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Support Tickets
    Route::get('tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [SupportTicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/close', [SupportTicketController::class, 'close'])->name('tickets.close');
    Route::patch('tickets/{ticket}/reopen', [SupportTicketController::class, 'reopen'])->name('tickets.reopen');
    Route::get('tickets/{ticket}/messages/{message}/attachments/{index}', [SupportTicketController::class, 'downloadAttachment'])->name('tickets.downloadAttachment');

    // Categories
    Route::middleware('can:categories.manage')->group(function () {
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
    });

    // Suppliers & Supplier Payments
    Route::middleware('can:suppliers.manage')->group(function () {
        Route::post('suppliers/{supplier}/pay', [SupplierController::class, 'payBalance'])->name('suppliers.pay');
        Route::get('suppliers-payments', [SupplierController::class, 'paymentIndex'])->name('suppliers.payments.index');
        Route::put('suppliers/payments/{payment}', [SupplierController::class, 'updatePayment'])->name('suppliers.payments.update');
        Route::delete('suppliers/payments/{payment}', [SupplierController::class, 'destroyPayment'])->name('suppliers.payments.destroy');

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
    });

    // Expenses & Expense Categories
    Route::middleware('can:expenses.view')->group(function () {
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    });
    Route::middleware('can:expenses.create')->group(function () {
        Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    });
    Route::middleware('can:expenses.manage')->group(function () {
        Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        Route::resource('expense-categories', ExpenseCategoryController::class)->except(['create', 'show', 'edit'])->parameters([
            'expense-categories' => 'expenseCategory',
        ])->names([
            'index' => 'expense-categories.index',
            'store' => 'expense-categories.store',
            'update' => 'expense-categories.update',
            'destroy' => 'expense-categories.destroy',
        ]);
    });

    // Customers
    Route::middleware('can:customers.manage')->group(function () {
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
    });

    // Units & Brands
    Route::middleware('can:units.manage')->group(function () {
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
    });

    Route::middleware('can:brands.manage')->group(function () {
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
    });

    // Stock Ledger
    Route::middleware('can:stock.view')->group(function () {
        Route::get('/stock-ledger', [StockLedgerController::class, 'stockLedger'])->name('stock-ledger');
        Route::get('/stock-ledger/export', [StockLedgerController::class, 'exportExcel'])->name('stock-ledger.export');
    });
    Route::middleware('can:stock.adjust')->group(function () {
        Route::post('/stock-ledger/adjust', [StockLedgerController::class, 'adjustStock'])->name('stock-ledger.adjust');
    });

    // Purchases
    Route::middleware('can:purchases.view')->group(function () {
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    });
    Route::middleware('can:purchases.create')->group(function () {
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    });
    Route::middleware('can:purchases.manage')->group(function () {
        Route::get('purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
        Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    });

    // POS & Sales
    Route::middleware('can:pos.access')->group(function () {
        Route::get('/pos-terminal', [PosController::class, 'posTerminal'])->name('pos-terminal');
        Route::post('/pos-terminal/checkout', [PosController::class, 'checkout'])->name('pos-terminal.checkout');
    });
    Route::middleware('can:sales.view')->group(function () {
        Route::get('sales', [SaleController::class, 'index'])->name('sales');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    });
    Route::middleware('can:sales.edit')->group(function () {
        Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::post('sales/{sale}/collect-payment', [SaleController::class, 'collectPayment'])->name('sales.collect-payment');
    });
    Route::middleware('can:sales.return')->group(function () {
        Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    });

    // Recipes
    Route::middleware('can:recipes.manage')->group(function () {
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
    });

    // Production Orders
    Route::middleware('can:production.view')->group(function () {
        Route::get('/production', [ProductionController::class, 'production'])->name('production');
        Route::get('/production/{order}', [ProductionController::class, 'show'])->name('production.show');
    });
    Route::middleware('can:production.create')->group(function () {
        Route::post('/production', [ProductionController::class, 'store'])->name('production.store');
    });
    Route::middleware('can:production.manage')->group(function () {
        Route::get('/production/{order}/edit', [ProductionController::class, 'edit'])->name('production.edit');
        Route::put('/production/{order}', [ProductionController::class, 'update'])->name('production.update');
        Route::delete('/production/{order}', [ProductionController::class, 'destroy'])->name('production.destroy');
        Route::patch('/production/{order}/start', [ProductionController::class, 'start'])->name('production.start');
        Route::patch('/production/{order}/complete', [ProductionController::class, 'complete'])->name('production.complete');
        Route::patch('/production/{order}/cancel', [ProductionController::class, 'cancel'])->name('production.cancel');
    });

    // Custom Orders
    Route::middleware('can:custom.orders.manage')->group(function () {
        Route::get('/custom-orders', [CustomOrderController::class, 'customOrders'])->name('custom-orders');
        Route::post('/custom-orders', [CustomOrderController::class, 'store'])->name('custom-orders.store');
        Route::get('/custom-orders/{order}', [CustomOrderController::class, 'show'])->name('custom-orders.show');
        Route::get('/custom-orders/{order}/print', [CustomOrderController::class, 'print'])->name('custom-orders.print');
        Route::patch('/custom-orders/{order}/cancel', [CustomOrderController::class, 'cancel'])->name('custom-orders.cancel');
        Route::patch('/custom-orders/{order}/status', [CustomOrderController::class, 'updateStatus'])->name('custom-orders.status');
        Route::post('/custom-orders/{order}/payment', [CustomOrderController::class, 'addPayment'])->name('custom-orders.payment');
    });

    // Analytics
    Route::middleware('can:analytics.view')->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'analytics'])->name('analytics');
    });

    // Reports
    Route::middleware('can:reports.view')->prefix('reports')->name('reports.')->group(function () {
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
    Route::middleware('can:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('can:users.create')->group(function () {
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('can:users.edit')->group(function () {
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::put('/users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');
    });
    Route::middleware('can:users.delete')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ── Roles & Permissions ──
    Route::middleware('can:roles.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::middleware('can:roles.create')->group(function () {
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('can:roles.edit')->group(function () {
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.sync-permissions');
    });
    Route::middleware('can:roles.delete')->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});

// ── SaaS Super Admin Routes ──
Route::prefix('superadmin')->name('saas.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Saas\DashboardController::class, 'index'])->name('dashboard');
        
        // SaaS Global Settings
        Route::get('settings', [App\Http\Controllers\Saas\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [App\Http\Controllers\Saas\SettingController::class, 'store'])->name('settings.store');
        
        Route::resource('tenants', App\Http\Controllers\Saas\TenantController::class)->except(['create', 'edit']);
        Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'show', 'update']);
        Route::resource('plans', PlanController::class)->except(['show']);
        Route::get('users', [App\Http\Controllers\Saas\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [App\Http\Controllers\Saas\UserController::class, 'show'])->name('users.show');
        Route::post('impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');

        // Support Tickets
        Route::get('tickets', [App\Http\Controllers\Saas\SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/{ticket}', [App\Http\Controllers\Saas\SupportTicketController::class, 'show'])->name('tickets.show');
        Route::post('tickets/{ticket}/reply', [App\Http\Controllers\Saas\SupportTicketController::class, 'reply'])->name('tickets.reply');
        Route::patch('tickets/{ticket}/status', [App\Http\Controllers\Saas\SupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');
        Route::patch('tickets/{ticket}/priority', [App\Http\Controllers\Saas\SupportTicketController::class, 'updatePriority'])->name('tickets.updatePriority');
        Route::patch('tickets/{ticket}/assign', [App\Http\Controllers\Saas\SupportTicketController::class, 'assign'])->name('tickets.assign');
        Route::delete('tickets/{ticket}', [App\Http\Controllers\Saas\SupportTicketController::class, 'destroy'])->name('tickets.destroy');
        Route::get('tickets/{ticket}/messages/{message}/attachments/{index}', [App\Http\Controllers\Saas\SupportTicketController::class, 'downloadAttachment'])->name('tickets.downloadAttachment');
    });
});

require __DIR__.'/auth.php';
