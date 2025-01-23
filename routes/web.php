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
use App\Http\Controllers\User\PR\PRController;

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

});

// Grup User
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase-request', [PRController::class, 'index'])->name('user.pr-index');
    Route::post('/purchase-request/store', [PRController::class, 'store']);
    Route::get('/purchase-request/last-nopr', [PRController::class, 'generateNoPr']);

    Route::get('/status-purchase-request', function () {
        return view('user.pr.status');
    })->name('user.pr-status');
});