<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductAssignmentController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\Admin\RebateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
    // return view("welcome");
});

// Redirect authenticated users based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'super_admin') {
        return redirect('/super-admin/dashboard');
    } elseif ($user->role === 'admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'salesperson') {
        return redirect('/sales/dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Super Admin Routes (Smabgroup Owner)
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');

    // Business Management
    Route::resource('businesses', BusinessController::class)->names('super-admin.businesses');
    Route::patch('/businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('super-admin.businesses.toggle-status');
    Route::get('/businesses/{business}/users', [BusinessController::class, 'users'])->name('super-admin.businesses.users');
    Route::get('/businesses/{business}/balance-wallet', [BusinessController::class, 'balanceWallet'])->name('super-admin.businesses.walletBalance');
    Route::get('/businesses/{business}/creditors', [BusinessController::class, 'creditors'])->name('super-admin.businesses.creditors');
    Route::get('/businesses/{business}/wallet-transactions', [BusinessController::class, 'walletTransactions'])->name('super-admin.businesses.wallet-transactions');
    Route::get('/businesses/{business}/wallet-transactions/export', [BusinessController::class, 'exportWalletTransactions'])->name('super-admin.businesses.wallet-transactions.export');
    Route::get('/businesses/{business}/analytics', [BusinessController::class, 'analytics'])->name('super-admin.businesses.analytics');

    // Global User Management
    Route::get('/users', [SuperAdminController::class, 'allUsers'])->name('super-admin.users.index');

    // Global Reports
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('super-admin.reports');

    // System Settings
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('super-admin.settings');

    // Activity Log
    Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'superAdminIndex'])->name('super-admin.activity-log');

    // All Documents
    Route::get('/documents', [SuperAdminController::class, 'allDocuments'])->name('super-admin.documents.index');

    // Sales Management
    Route::get('/sales', [\App\Http\Controllers\SuperAdmin\SaleController::class, 'index'])->name('super-admin.sales.index');
    Route::get('/sales/export', [\App\Http\Controllers\SuperAdmin\SaleController::class, 'export'])->name('super-admin.sales.export');
    Route::get('/sales/{sale}', [\App\Http\Controllers\SuperAdmin\SaleController::class, 'show'])->name('super-admin.sales.show');

        // Business Capital Management
    Route::get('/businesses/{business}/capital/create', [\App\Http\Controllers\SuperAdmin\BusinessCapitalController::class, 'create'])->name('super-admin.capital.create');
    Route::post('/businesses/{business}/capital', [\App\Http\Controllers\SuperAdmin\BusinessCapitalController::class, 'store'])->name('super-admin.capital.store');
    // Wallet Management
    Route::get('/businesses/{business}/wallet/deposit', [\App\Http\Controllers\SuperAdmin\WalletController::class, 'createDeposit'])->name('super-admin.wallets.deposit');
    Route::post('/businesses/{business}/wallet/deposit', [\App\Http\Controllers\SuperAdmin\WalletController::class, 'storeDeposit'])->name('super-admin.wallets.deposit.store');
    Route::get('/businesses/{business}/wallet/withdraw', [\App\Http\Controllers\SuperAdmin\WalletController::class, 'createWithdrawal'])->name('super-admin.wallets.withdraw');
    Route::post('/businesses/{business}/wallet/withdraw', [\App\Http\Controllers\SuperAdmin\WalletController::class, 'storeWithdrawal'])->name('super-admin.wallets.withdraw.store');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Staff Management
    Route::get('/my-staffs', [DashboardController::class, 'myStaff'])->name('admin.myStaff');
    Route::get('/create/staff-account', [RegisteredUserController::class, 'createUser'])->name('admin.createUser');
    Route::post('/store/staff-account', [RegisteredUserController::class, 'storeUser'])->name('admin.storeUser');
    Route::get('/staff/{user}', [RegisteredUserController::class, 'showUser'])->name('admin.showUser');
    Route::get('/staff/{user}/edit', [RegisteredUserController::class, 'editUser'])->name('admin.editUser');

    // Product Assignments Management
    Route::resource('assignments', ProductAssignmentController::class)->names('admin.assignments');
    Route::get('/assignments/{assignment}/complete', [ProductAssignmentController::class, 'markAsComplete'])->name('admin.assignments.complete');
    Route::patch('/staff/{user}', [RegisteredUserController::class, 'updateUser'])->name('admin.updateUser');
    Route::patch('/staff/{user}/toggle-status', [RegisteredUserController::class, 'toggleStatus'])->name('admin.toggleUserStatus');

    // Product Assignment Routes (Admin only)
    Route::get('/assignments', [ProductAssignmentController::class, 'index'])->name('admin.assignments.index');
    Route::get('/assignments/create', [ProductAssignmentController::class, 'create'])->name('admin.assignments.create');
    Route::post('/assignments', [ProductAssignmentController::class, 'store'])->name('admin.assignments.store');
    Route::get('/assignments/{assignment}', [ProductAssignmentController::class, 'show'])->name('admin.assignments.show');
    Route::patch('/assignments/{assignment}/collect-return', [ProductAssignmentController::class, 'collectReturn'])->name('admin.assignments.collectReturn');

    // Inventory category management
    Route::get('/inventory/categories', [InventoryCategoryController::class, 'index'])->name('inventory.categories.index');
    Route::post('/inventory/categories', [InventoryCategoryController::class, 'store'])->name('inventory.categories.store');
    Route::get('/inventory/categories/{category}/edit', [InventoryCategoryController::class, 'edit'])->name('inventory.categories.edit');
    Route::put('/inventory/categories/{category}', [InventoryCategoryController::class, 'update'])->name('inventory.categories.update');
    Route::delete('/inventory/categories/{category}', [InventoryCategoryController::class, 'destroy'])->name('inventory.categories.destroy');

    // Inventory Management (Admin only)
    Route::resource('inventory', InventoryController::class);
    Route::post('/inventory/{product}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    Route::get('/inventory/{adjustment}/adjust-stock/delete', [InventoryController::class, 'adjustStockDelete'])->name('inventory.adjustStockDelete');

    // Purchase Management (Admin only)
    // Replace the single resource route with explicit routes
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::get('/purchases/restock/{purchase}', [PurchaseController::class, 'restock'])->name('purchases.restock');
    Route::post('/purchases/restock/store', [PurchaseController::class, 'storeRestock'])->name('purchases.storeRestock');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/update/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/destroy/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    // Rebate Management (Admin only)
    Route::resource('rebates', RebateController::class)->only([
        'index',
        'create',
        'store',
        'show',
        'destroy',
    ])->names('rebates');

    // Sales Management (Admin can view/edit all)
    // Route::resource('sales', SalesController::class);

    // Reports (Admin only)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/profit', [ReportController::class, 'profitReport'])->name('reports.profit');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/sales/pdf', [ReportController::class, 'exportSalesPDF'])->name('reports.sales.pdf');
    Route::get('/reports/profit/pdf', [ReportController::class, 'exportProfitPDF'])->name('reports.profit.pdf');


    // Expenses (Admin only)
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/inventory', [ExpensesController::class, 'inventoryReport'])->name('expenses.inventory');
    Route::get('/reports/expenses/pdf', [ReportController::class, 'exportExpensesPDF'])->name('reports.expenses.pdf');
    Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'adminIndex'])->name('admin.activity-log');

    // app settings (Admin only)

    Route::get('/gallery/app-settings', [AppSettingController::class, 'index'])->name('appSetting.index');
    Route::get('/gallery/app-settings/news', [AppSettingController::class, 'indexNews'])->name('appSetting.indexNews');
    Route::get('/app-setting/create', [AppSettingController::class, 'create'])->name('appSetting.create');
    Route::get('/app-setting/create/new', [AppSettingController::class, 'createNew'])->name('appSetting.createNew');
    Route::get('/app-setting/delete/{new}', [AppSettingController::class, 'newDelete'])->name('appSetting.newDelete');
    Route::get('/app-setting/gallery-delete/{gallery}', [AppSettingController::class, 'galleryDelete'])->name('appSetting.galleryDelete');
    Route::post('/app-setting/store', [AppSettingController::class, 'store'])->name('appSetting.store');
    Route::post('/app-setting/store/news', [AppSettingController::class, 'storeNews'])->name('appSetting.storeNews');
    // Route::get('/reports/expensive/pdf', [ReportController::class, 'exportExpensivePDF'])->name('reports.expensive.pdf');


    // Document Management
    Route::resource('documents', \App\Http\Controllers\DocumentController::class)->names('documents');
});

// Admin and Salesperson shared routes
Route::middleware(['auth', 'role:admin,salesperson'])->prefix('admin')->group(function () {
    // Creditor Management
    Route::resource('creditors', \App\Http\Controllers\Admin\CreditorController::class)->names('admin.creditors');
    Route::post('creditors/{creditor}/record-payment', [\App\Http\Controllers\Admin\CreditorController::class, 'recordPayment'])->name('admin.creditors.record-payment');
    Route::get('creditors/{creditor}/print', [\App\Http\Controllers\Admin\CreditorController::class, 'print'])->name('admin.creditors.print');
    Route::get('creditors/transactions/{transaction}/print', [\App\Http\Controllers\Admin\CreditorController::class, 'printTransaction'])->name('admin.creditors.print-transaction');
});

// Admin and Salesperson shared sales routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Admin sales management
    Route::get('/sales/report', [SalesController::class, 'report'])->name('admin.sales.report');
    Route::get('/sales/export', [SalesController::class, 'export'])->name('admin.sales.export');
    Route::get('/sales/export-pdf', [SalesController::class, 'exportPdf'])->name('admin.sales.export-pdf');
});

// Sales Routes (Admin & Salesperson)
Route::middleware(['auth', 'role:admin,salesperson'])->prefix('sales')->group(function () {
    // Dashboard access based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return app(DashboardController::class)->salesDashboard();
    })->name('sales.dashboard');

    // Sales management - accessible by both admin and salesperson
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/my-sales', [SalesController::class, 'mySales'])->name('sales.my-sales');
    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report');
    Route::get('/sales/export', [SalesController::class, 'export'])->name('sales.export');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/success', [SalesController::class, 'success'])->name('sales.success');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
    Route::get('/sales/{sale}/print-receipt', [SalesController::class, 'printReceipt'])->name('sales.print-receipt');
    // Add this with your other sales routes
    Route::get('/print-multiple-receipts', [SalesController::class, 'printMultipleReceipts'])
        ->name('sales.print-multiple-receipts');


    // View available inventory (read-only)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('sales.inventory');
    Route::get('/inventory/{product}', [InventoryController::class, 'show'])->name('sales.inventory.show');

    // Product Assignments for Staff
    Route::get('/my-assignments', [ProductAssignmentController::class, 'myAssignments'])->name('sales.assignments');
});

// Shared Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    // Profile management - accessible by all roles (super_admin, admin, salesperson)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Sales Routes (Admin & Salesperson)
Route::middleware(['auth'])->prefix('sales')->group(function () {
    Route::get('/sales/salesperson/{sale}', [SalesController::class, 'show'])->name('salesperson.show');
    Route::get('/sales/salesperson/{sale}/print-receipt', [SalesController::class, 'printReceipt'])->name('salesperson.print-receipt');
});





Route::get('/app-setting/fetch/api', [AppSettingController::class, 'fetchApi'])->name('appSetting.fetchApi');
Route::get('/app-setting/fetch/api/news', [AppSettingController::class, 'fetchNewApi'])->name('appSetting.fetchNewApi');
require __DIR__.'/auth.php';
