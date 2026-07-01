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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    /**
     * Organisation Routes
     */
    Route::group(['prefix' => 'organisations'], function () {
        Route::get('/datatable', [OrganisationController::class, 'getOrganisationsData'])->name('organisations.datatable');
    });
    Route::resource('organisations', OrganisationController::class);
    Route::get('/impersonate/{id}', [TenantController::class, 'impersonate'])->name('tenant.impersonate');
    Route::get('/stop-impersonation', [TenantController::class, 'stopImpersonation'])->name('tenant.stop-impersonation');

    /**
     * Employee Routes
     */
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/datatable', [EmployeeController::class, 'getEmployeesData'])->name('employees.datatable');
    });
    Route::resource('employees', EmployeeController::class);
    Route::resource('users', UserController::class);
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    /**
     * Settings Routes
     */
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/logo', [SettingsController::class, 'updateOrganisationLogo'])->name('settings.logo.update');

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
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

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
        Route::get('/daily', [AttendanceController::class, 'getDaily'])->name('daily.get');
        Route::post('/daily', [AttendanceController::class, 'saveDaily'])->name('daily.save');
        Route::get('/matrix', [AttendanceController::class, 'getMatrix'])->name('matrix.get');
    });

});

