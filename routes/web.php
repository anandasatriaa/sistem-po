<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Input\CabangController;
use App\Http\Controllers\Admin\Input\CategoryController;
use App\Http\Controllers\Admin\Input\UnitController;
use App\Http\Controllers\Admin\Input\SupplierController;
use App\Http\Controllers\Admin\Input\BarangController;
use App\Http\Controllers\Admin\PR\AdminPRStatusController;
use App\Http\Controllers\User\PR\PRController;
use App\Http\Controllers\User\PR\PRStatusController;
use App\Http\Controllers\SPV\PR\SPVPRController;
use App\Http\Controllers\SPV\PR\SPVPRStatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Grup Admin
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard-index');

    Route::get('/admin/employee', [EmployeeController::class, 'index'])->name('admin.employee-index');
    Route::post('/admin/syncEmployee', [EmployeeController::class, 'APIgetAllEmployee'])->name('api-sync');

    Route::get('/admin/cabang', [CabangController::class, 'index'])->name('admin.cabang-index');
    Route::post('/admin/cabang/store', [CabangController::class, 'store']);
    Route::post('/admin/cabang/update/{id}', [CabangController::class, 'update'])->name('admin.cabang-update');
    Route::delete('/admin/cabang/destroy/{id}', [CabangController::class, 'destroy'])->name('admin.cabang-destroy');
    Route::get('/admin/cabang/last-id', [CabangController::class, 'lastCabangId']);

    Route::get('/admin/category', [CategoryController::class, 'index'])->name('admin.category-index');
    Route::post('/admin/category/store', [CategoryController::class, 'store']);
    Route::post('/admin/category/update/{id}', [CategoryController::class, 'update'])->name('admin.category-update');
    Route::delete('/admin/category/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category-destroy');
    Route::get('/admin/category/last-id', [CategoryController::class, 'lastCategoryId']);

    Route::get('/admin/unit', [UnitController::class, 'index'])->name('admin.unit-index');
    Route::post('/admin/unit/store', [UnitController::class, 'store']);
    Route::post('/admin/unit/update/{id}', [UnitController::class, 'update'])->name('admin.unit-update');
    Route::delete('/admin/unit/destroy/{id}', [UnitController::class, 'destroy'])->name('admin.unit-destroy');
    Route::get('/admin/unit/last-id', [UnitController::class, 'lastUnitId']);
    Route::post('/admin/unit/import', [UnitController::class, 'importCsv'])->name('admin.unit-import');

    Route::get('/admin/supplier', [SupplierController::class, 'index'])->name('admin.supplier-index');
    Route::post('/admin/supplier/store', [SupplierController::class, 'store']);
    Route::post('/admin/supplier/update/{id}', [SupplierController::class, 'update'])->name('admin.supplier-update');
    Route::delete('/admin/supplier/destroy/{id}', [SupplierController::class, 'destroy'])->name('admin.supplier-destroy');
    Route::get('/admin/supplier/last-id', [SupplierController::class, 'lastSupplierId']);
    Route::post('/admin/supplier/import', [SupplierController::class, 'importCsv'])->name('admin.supplier-import');

    Route::get('/admin/barang', [BarangController::class, 'index'])->name('admin.barang-index');
    Route::post('/admin/barang/store', [BarangController::class, 'store']);
    Route::post('/admin/barang/update/{id}', [BarangController::class, 'update'])->name('admin.barang-update');
    Route::delete('/admin/barang/destroy/{id}', [BarangController::class, 'destroy'])->name('admin.barang-destroy');
    Route::get('/admin/barang/last-id', [BarangController::class, 'lastBarangId']);
    Route::post('/admin/barang/import', [BarangController::class, 'importCsv'])->name('admin.barang-import');

    Route::get('/admin/status-purchase-request-milenia', [AdminPRStatusController::class, 'milenia'])->name('admin.pr-milenia');
    Route::get('/admin/status-purchase-request-map', [AdminPRStatusController::class, 'map'])->name('admin.pr-map');
    Route::get('/admin/status-purchase-request-milenia/pdf/{id}', [AdminPRStatusController::class, 'generatePDFMilenia'])->name('admin.pr-generatePDFMilenia');
    Route::get('/admin/status-purchase-request-map/pdf/{id}', [AdminPRStatusController::class, 'generatePDFMAP'])->name('admin.pr-generatePDFMAP');
    Route::post('/admin/status-purchase-request-milenia/update', [AdminPRStatusController::class, 'updateStatusMilenia'])->name('admin.pr-updateStatusMilenia');
    Route::post('/admin/status-purchase-request-map/update', [AdminPRStatusController::class, 'updateStatusMAP'])->name('admin.pr-updateStatusMAP');
});

// Grup User
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase-request', [PRController::class, 'index'])->name('user.pr-index');
    Route::post('/purchase-request/store', [PRController::class, 'store']);
    Route::get('/purchase-request/last-nopr', [PRController::class, 'generateNoPr']);

    Route::get('/status-purchase-request', [PRStatusController::class, 'status'])->name('user.pr-status');
    Route::get('/status-purchase-request/pdf/{id}', [PRStatusController::class, 'generatePDF'])->name('user.pr-generatePDF');
});

// Grup SPV
Route::middleware(['auth', 'isSpv'])->group(function () {
    Route::get('/spv/purchase-request', [SPVPRController::class, 'index'])->name('spv.pr-index');
    Route::post('/spv/purchase-request/store', [SPVPRController::class, 'store']);
    Route::get('/spv/purchase-request/last-nopr', [SPVPRController::class, 'generateNoPr']);

    Route::get('/spv/status-purchase-request', [SPVPRStatusController::class, 'status'])->name('spv.pr-status');
    Route::get('/spv/status-purchase-request/pdf/{id}', [SPVPRStatusController::class, 'generatePDF'])->name('spv.pr-generatePDF');
    Route::post('/spv/status-purchase-request/save-signature', [SPVPRStatusController::class, 'saveSignature'])->name('spv.pr-saveSignature');
    Route::post('/spv/status-purchase-request/reject', [SPVPRStatusController::class, 'rejectPR'])->name('spv.pr-rejectPR');
});
