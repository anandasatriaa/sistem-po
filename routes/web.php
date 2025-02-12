<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Input\CabangController;
use App\Http\Controllers\Admin\Input\CategoryController;
use App\Http\Controllers\Admin\Input\UnitController;
use App\Http\Controllers\Admin\Input\SupplierController;
use App\Http\Controllers\Admin\Input\BarangController;
use App\Http\Controllers\Admin\PR\AdminPRStatusController;
use App\Http\Controllers\Admin\PO\POController;
use App\Http\Controllers\Admin\PO\LaporanPOController;
use App\Http\Controllers\User\PR\PRController;
use App\Http\Controllers\User\PR\PRStatusController;
use App\Http\Controllers\SPV\PR\SPVPRController;
use App\Http\Controllers\SPV\PR\SPVPRStatusController;
use App\Http\Controllers\GA\PR\GAPRController;
use App\Http\Controllers\GA\PR\GAPRStatusController;
use App\Http\Controllers\GA\PO\GAPOStatusController;
use App\Models\PO\PurchaseOrderMAP;
use App\Models\PO\PurchaseOrderMilenia;
use App\Models\PR\PurchaseRequest;
use App\Models\User;
use setasign\Fpdi\Fpdi;
use Barryvdh\DomPDF\Facade\PDF as PDF;

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

Route::get('/approval-pr/{id}', function ($id) {
    // Ambil data purchase request beserta lampiran
    $pr = \App\Models\PR\PurchaseRequest::with('lampiran')->findOrFail($id);
    $users = \App\Models\User::where('Aktif', 1)->get();
    return view('approved.index', compact('pr', 'users'));
});

Route::get('/pdf-view/{id}', function ($id) {
    // Ambil data purchase request (tanpa lampiran, atau cukup user untuk format file)
    $purchaseRequest = \App\Models\PR\PurchaseRequest::with('user')->findOrFail($id);

    // Format nama file PDF
    $userName = $purchaseRequest->user->Nama;
    $today = \Carbon\Carbon::now()->format('Y-m-d');
    $fileName = 'PR_' . strtolower(str_replace(' ', '_', $userName)) . '_' . $today . '.pdf';

    // Generate PDF utama dari view (misalnya: resources/views/pdf/pr.blade.php)
    $dompdf = Pdf::loadView('pdf.pr', compact('purchaseRequest'));
    $mainPdfContent = $dompdf->output();

    return response($mainPdfContent, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
});

// Approval PR tanpa login
Route::post('/pr/approved', [SPVPRStatusController::class, 'saveSignature'])->name('pr.approved');
Route::post('/pr/rejected', [SPVPRStatusController::class, 'rejectPR'])->name('pr.rejected');

Route::get('/approval-po-milenia/{id}', function ($id) {
    // Mengambil data purchase request berdasarkan id
    $po = PurchaseOrderMilenia::findOrFail($id);
    $users = User::where('Aktif', 1)->get();
    return view('approved.po-milenia', compact('po', 'users'));
});

Route::get('/pdf-view-milenia/{id}', function ($id) {
    $purchaseOrder = PurchaseOrderMilenia::findOrFail($id);
    $category = $purchaseOrder->barang->first()->category;
    $grandtotal = $purchaseOrder->total;
    $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
    $grandtotalWords = ucfirst($formatter->format($grandtotal));

    // Menghasilkan PDF dari view 'pr' dengan data purchase order
    $pdf = PDF::loadView('pdf.po-milenia-final', compact('purchaseOrder', 'grandtotalWords', 'category'));

    // Mengembalikan stream PDF
    return $pdf->stream('purchase-order-' . $id . '.pdf');
});

Route::get('/approval-po-map/{id}', function ($id) {
    // Mengambil data purchase request berdasarkan id
    $po = PurchaseOrderMAP::findOrFail($id);
    $users = User::where('Aktif', 1)->get();
    return view('approved.po-map', compact('po', 'users'));
});

Route::get('/pdf-view-map/{id}', function ($id) {
    $purchaseOrder = PurchaseOrderMAP::findOrFail($id);
    $category = $purchaseOrder->barang->first()->category;
    $grandtotal = $purchaseOrder->total;
    $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
    $grandtotalWords = ucfirst($formatter->format($grandtotal));

    // Menghasilkan PDF dari view 'pr' dengan data purchase order
    $pdf = PDF::loadView('pdf.po-map-final', compact('purchaseOrder', 'grandtotalWords', 'category'));

    // Mengembalikan stream PDF
    return $pdf->stream('purchase-order-' . $id . '.pdf');
});

// Approval PO tanpa login
Route::post('/po/approved', [GAPOStatusController::class, 'saveSignatureNotLogin'])->name('po.approved');
Route::post('/po/rejected', [GAPOStatusController::class, 'rejectPONotLogin'])->name('po.rejected');


// Grup Admin
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard-index');

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
    Route::get('/admin/status-purchase-request-milenia/pdf/{id}', [AdminPRStatusController::class, 'generatePDFMilenia'])->name('admin.pr-generatePDFMilenia');
    Route::post('/admin/status-purchase-request-milenia/update', [AdminPRStatusController::class, 'updateStatusMilenia'])->name('admin.pr-updateStatusMilenia');
    
    Route::get('/admin/status-purchase-request-map', [AdminPRStatusController::class, 'map'])->name('admin.pr-map');
    Route::get('/admin/status-purchase-request-map/pdf/{id}', [AdminPRStatusController::class, 'generatePDFMAP'])->name('admin.pr-generatePDFMAP');
    Route::post('/admin/status-purchase-request-map/update', [AdminPRStatusController::class, 'updateStatusMAP'])->name('admin.pr-updateStatusMAP');

    Route::get('/admin/input-po-milenia', [POController::class, 'poMilenia'])->name('admin.po-milenia');
    Route::post('/admin/input-po-milenia/preview', [POController::class, 'previewPDFMilenia']);
    Route::post('/admin/input-po-milenia/store', [POController::class, 'storeMilenia'])->name('admin.po-milenia-store');

    Route::get('/admin/input-po-map', [POController::class, 'poMap'])->name('admin.po-map');
    Route::post('/admin/input-po-map/preview', [POController::class, 'previewPDFMap']);
    Route::post('/admin/input-po-map/store', [POController::class, 'storeMap'])->name('admin.po-map-store');

    Route::get('/admin/status-po-milenia', [POController::class, 'statusPOMilenia'])->name('admin.statuspo-milenia');
    Route::get('/admin/status-po-milenia/pdf/{id}', [POController::class, 'generatePDFMilenia'])->name('admin.po-generatePDFMilenia');

    Route::get('/admin/status-po-map', [POController::class, 'statusPOMap'])->name('admin.statuspo-map');
    Route::get('/admin/status-po-map/pdf/{id}', [POController::class, 'generatePDFMap'])->name('admin.po-generatePDFMap');

    Route::get('/admin/laporan-po-milenia', [LaporanPOController::class, 'index'])->name('admin.laporanpo-milenia');
    Route::get('/admin/laporan-po-milenia/pdf-detail', [LaporanPOController::class, 'exportPDFDetail'])->name('admin.laporanpo-milenia.pdf-detail');
    Route::get('/admin/laporan-po-milenia/pdf-summary', [LaporanPOController::class, 'exportPDFSummary'])->name('admin.laporanpo-milenia.pdf-summary');
    Route::get('/admin/laporan-po-milenia/summary', [LaporanPOController::class, 'getFilteredSummary'])->name('admin.laporanpo-milenia.summary');

    Route::get('/admin/laporan-po-map', [LaporanPOController::class, 'indexMAP'])->name('admin.laporanpo-map');
    Route::get('/admin/laporan-po-map/pdf-detail', [LaporanPOController::class, 'exportPDFDetailMAP'])->name('admin.laporanpo-map.pdf-detail');
    Route::get('/admin/laporan-po-map/pdf-summary', [LaporanPOController::class, 'exportPDFSummaryMAP'])->name('admin.laporanpo-map.pdf-summary');
    Route::get('/admin/laporan-po-map/summary', [LaporanPOController::class, 'getFilteredSummaryMAP'])->name('admin.laporanpo-map.summary');
});

// Grup User
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase-request', [PRController::class, 'index'])->name('user.pr-index');
    Route::post('/purchase-request/store', [PRController::class, 'store']);
    Route::get('/purchase-request/last-nopr', [PRController::class, 'generateNoPr']);

    Route::get('/status-purchase-request', [PRStatusController::class, 'status'])->name('user.pr-status');
    Route::get('/status-purchase-request/pdf/{id}', [PRStatusController::class, 'generatePDF'])->name('user.pr-generatePDF');
});

// Grup GA/Director
Route::middleware(['auth', 'isGa'])->group(function () {
    Route::get('/ga/purchase-request', [GAPRController::class, 'index'])->name('ga.pr-index');
    Route::post('/ga/purchase-request/store', [GAPRController::class, 'store']);
    Route::get('/ga/purchase-request/last-nopr', [GAPRController::class, 'generateNoPr']);

    Route::get('/ga/status-purchase-request', [GAPRStatusController::class, 'status'])->name('ga.pr-status');
    Route::get('/ga/status-purchase-request/pdf/{id}', [GAPRStatusController::class, 'generatePDF'])->name('ga.pr-generatePDF');
    Route::post('/ga/status-purchase-request/save-signature', [GAPRStatusController::class, 'saveSignature'])->name('ga.pr-saveSignature');
    Route::post('/ga/status-purchase-request/reject', [GAPRStatusController::class, 'rejectPR'])->name('ga.pr-rejectPR');

    Route::get('/ga/status-purchase-order', [GAPOStatusController::class, 'status'])->name('ga.po-status');
    Route::get('/ga/status-purchase-order/pdf-milenia/{id}', [GAPOStatusController::class, 'generatePDFMilenia'])->name('ga.po-generatePDFMilenia');
    Route::get('/ga/status-purchase-order/pdf-map/{id}', [GAPOStatusController::class, 'generatePDFMAP'])->name('ga.po-generatePDFMAP');
    Route::post('/ga/status-purchase-order/save-signature', [GAPOStatusController::class, 'saveSignature'])->name('ga.po-saveSignature');
    Route::post('/ga/status-purchase-order/reject', [GAPOStatusController::class, 'rejectPO'])->name('ga.po-rejectPO');
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