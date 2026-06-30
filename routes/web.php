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

});

