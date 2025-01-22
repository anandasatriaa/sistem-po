<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Input\CabangController;
use App\Http\Controllers\Admin\Input\CategoryController;
use App\Http\Controllers\Admin\Input\UnitController;

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
});

// Grup User
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase-request', function () {
        return view('user.pr.index');
    })->name('user.pr-index');

    Route::get('/status-purchase-request', function () {
        return view('user.pr.status');
    })->name('user.pr-status');
});