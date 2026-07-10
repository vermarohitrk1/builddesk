<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\LeadFollowupController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExpenseCategoryController;


/**
 * --------------------------------------------
 * Tenant Routes
 * --------------------------------------------
 */
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    /**
     * Employee Routes
     */
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/datatable', [EmployeeController::class, 'getEmployeesData'])->name('employees.datatable');
    });
    Route::resource('employees', EmployeeController::class);
    Route::resource('users', UserController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /**
     * Settings Routes
     */
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/partial/basic', [SettingsController::class, 'getBasic'])->name('settings.basic');
    Route::get('/settings/partial/categories', [SettingsController::class, 'getExpenseCategories'])->name('settings.expense_categories');
    Route::get('/settings/partial/suppliers', [SettingsController::class, 'getSuppliers'])->name('settings.suppliers');
    Route::get('/settings/partial/modules', [SettingsController::class, 'getModules'])->name('settings.modules');
    Route::post('/settings/modules/{module}/toggle', [SettingsController::class, 'toggleModule'])->name('settings.module.toggle');
    Route::post('/settings/logo', [SettingsController::class, 'updateOrganisationLogo'])->name('settings.logo.update');
    Route::post('/settings/info', [SettingsController::class, 'updateOrganisationInfo'])->name('settings.info.update');
    
    Route::get('/settings/partial/billing', [SettingsController::class, 'getBilling'])->name('settings.billing');
    Route::post('/settings/subscription/start', [SettingsController::class, 'startSubscription'])->name('settings.subscription.start');
    Route::post('/settings/subscription/cancel', [SettingsController::class, 'cancelSubscription'])->name('settings.subscription.cancel');
    /**
     * Lead Routes
     */
    Route::group(['prefix' => 'leads'], function () {
        Route::get('/datatable', [LeadController::class, 'getLeadsData'])->name('leads.datatable');
        Route::get('/check-mobile', [LeadController::class, 'checkMobile'])->name('leads.check-mobile');
    });
    Route::resource('leads', LeadController::class);

    /**
     * Measurement Routes
     */
    Route::group(['prefix' => 'measurements'], function () {
        Route::get('/add-item-row', [MeasurementController::class, 'addItemRow'])->name('measurements.add-item-row');
    });
    Route::resource('measurements', MeasurementController::class);

    /**
     * Quotation Routes
     */
    Route::group(['prefix' => 'quotations'], function () {
        Route::get('/add-item-row', [QuotationController::class, 'addItemRow'])->name('quotations.add-item-row');
        Route::get('/get-measurement-suggestions', [QuotationController::class, 'getMeasurementSuggestions'])->name('quotations.get-measurement-suggestions');
        Route::get('/{id}/pdf/download', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf.download');
        Route::get('/{id}/pdf/preview', [QuotationController::class, 'previewPdf'])->name('quotations.pdf.preview');
    });
    Route::get('/quotations/datatable', [QuotationController::class, 'getQuotationsData'])->name('quotations.datatable');
    Route::resource('quotations', QuotationController::class);

    /**
     * Follow-up Routes
     */
    Route::post('/followups/{id}/complete', [LeadFollowupController::class, 'complete'])->name('followups.complete');
    Route::resource('followups', LeadFollowupController::class)->except(['index', 'show']);

    /**
     * Project Routes
     */
    Route::get('/projects/datatable', [ProjectController::class, 'getProjectsData'])->name('projects.datatable');
    Route::resource('projects', ProjectController::class);

    /**
     * Expense & Supplier Routes
     */
    Route::get('/expenses/datatable', [ExpenseController::class, 'getExpensesData'])->name('expenses.datatable');
    Route::resource('expenses', ExpenseController::class);

    /**
     * Supplier Routes (managed from Settings)
     */
    Route::get('/suppliers/data', [SupplierController::class, 'getData'])->name('suppliers.data');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::resource('suppliers', SupplierController::class)->except(['index', 'show', 'create']);

    /**
     * Expense Category Routes (managed from Settings)
     */
    Route::prefix('expenses/categories')->name('expenses.categories.')->group(function () {
        Route::get('/data', [ExpenseCategoryController::class, 'getData'])->name('data');
        Route::get('/create', [ExpenseCategoryController::class, 'create'])->name('create');
        Route::post('/', [ExpenseCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ExpenseCategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });

    /**
     * Customer Routes
     */
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/datatable', [CustomerController::class, 'getCustomersData'])->name('customers.datatable');
    });
    Route::resource('customers', CustomerController::class);

    /**
     * Reports Routes
     */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/revenue/pdf', [ReportController::class, 'revenuePdf'])->name('revenue.pdf');
        Route::get('/revenue/excel', [ReportController::class, 'revenueExcel'])->name('revenue.excel');

        Route::get('/expense', [ReportController::class, 'expense'])->name('expense');
        Route::get('/expense/pdf', [ReportController::class, 'expensePdf'])->name('expense.pdf');
        Route::get('/expense/excel', [ReportController::class, 'expenseExcel'])->name('expense.excel');

        Route::get('/financial-statement', [ReportController::class, 'cashFlow'])->name('cashflow');
        Route::get('/financial-statement/pdf', [ReportController::class, 'cashFlowPdf'])->name('cashflow.pdf');
        Route::get('/financial-statement/excel', [ReportController::class, 'cashFlowExcel'])->name('cashflow.excel');
    });

    /**
     * Payroll Routes
     */
    Route::prefix('payrolls')->name('payrolls.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/data', [PayrollController::class, 'getData'])->name('data');
        Route::get('/generate-modal', [PayrollController::class, 'showGenerateModal'])->name('generate.modal');
        Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
        Route::post('/mark-paid', [PayrollController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{id}/cancel', [PayrollController::class, 'cancel'])->name('cancel');
        Route::get('/export', [PayrollController::class, 'export'])->name('export');
    });

    /**
     * Attendance Routes
     */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/get-daily', [AttendanceController::class, 'getDaily'])->name('daily.get');
        Route::post('/store-daily', [AttendanceController::class, 'saveDaily'])->name('daily.store');
        Route::post('/matrix', [AttendanceController::class, 'getMatrix'])->name('matrix.get');
    });

});


/**
 * --------------------------------------------
 * Super Admin Routes
 * --------------------------------------------
 */
use App\Http\Middleware\SuperAdminAuthenticate;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    Route::middleware([SuperAdminAuthenticate::class])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index']);
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Let Super Admin assume control of an organisation
        Route::get('/impersonate/{id}', [\App\Http\Controllers\TenantController::class, 'impersonate'])->name('impersonate');
        Route::get('/stop-impersonation', [\App\Http\Controllers\TenantController::class, 'stopImpersonation'])->name('stop-impersonation');


        //Organisation Routes
        Route::group(['prefix' => 'organisations'], function () {
            Route::get('/datatable', [OrganisationController::class, 'getOrganisationsData'])->name('organisations.datatable');
            Route::post('/{organisation}/modules', [OrganisationController::class, 'updateModules'])->name('organisations.modules.update');
        });
        Route::resource('organisations', OrganisationController::class);

        //Settings Routes
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/datatable', [SettingController::class, 'getSettingsData'])->name('settings.datatable');
        });
        Route::resource('settings', SettingController::class);
    });
});



/**
 * -----------------------------------------------
 * Dual Guard Routes (Web + Super Admin)
 * -----------------------------------------------
 */
Route::middleware(['auth:web,super_admin'])->group(function () {
    //
});