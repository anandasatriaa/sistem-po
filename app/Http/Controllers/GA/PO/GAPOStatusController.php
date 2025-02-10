<?php

namespace App\Http\Controllers\GA\PO;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PO\PurchaseOrderMilenia;
use App\Models\PO\PurchaseOrderBarangMilenia;
use App\Models\PO\PurchaseOrderMAP;
use App\Models\PO\PurchaseOrderBarangMAP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF as PDF;
use NumberFormatter;

class GAPOStatusController extends Controller
{
    public function status()
    {
        $purchaseOrder = PurchaseOrderMilenia::with('barang')->get();
        $purchaseOrderMAP = PurchaseOrderMAP::with('barang')->get();
        return view('ga.po.status', compact('purchaseOrder', 'purchaseOrderMAP'));
    }

    public function generatePDFMilenia($id)
    {
        $purchaseOrder = PurchaseOrderMilenia::with('barang')->findOrFail($id);

        // Ambil kategori pertama yang terkait dengan purchase_order_id
        $category = $purchaseOrder->barang->first()->category;

        // Pastikan 'grandtotal' diambil dari model dan dikonversi menjadi terbilang
        $grandtotal = $purchaseOrder->total;
        $grandtotalWords = $this->terbilang($grandtotal);

        // Ambil nomor PO
        $noPO = $purchaseOrder->no_po;

        // Ambil tanggal hari ini
        $today = \Carbon\Carbon::now()->format('Y-m-d'); // Format: YYYY-MM-DD

        // Format nama file PDF
        $fileName = 'PO_' . strtolower(str_replace(' ', '_', $noPO)) . '_' . $today . '.pdf';

        // Load view untuk PDF
        $pdf = PDF::loadView('pdf.po-milenia-final', compact('purchaseOrder', 'grandtotalWords', 'category'));

        // Return file PDF
        return $pdf->stream($fileName);
    }

    public function generatePDFMAP($id)
    {
        $purchaseOrder = PurchaseOrderMAP::with('barang')->findOrFail($id);

        // Ambil kategori pertama yang terkait dengan purchase_order_id
        $category = $purchaseOrder->barang->first()->category;

        // Pastikan 'grandtotal' diambil dari model dan dikonversi menjadi terbilang
        $grandtotal = $purchaseOrder->total;
        $grandtotalWords = $this->terbilang($grandtotal);

        // Ambil nomor PO
        $noPO = $purchaseOrder->no_po;

        // Ambil tanggal hari ini
        $today = \Carbon\Carbon::now()->format('Y-m-d'); // Format: YYYY-MM-DD

        // Format nama file PDF
        $fileName = 'PO_' . strtolower(str_replace(' ', '_', $noPO)) . '_' . $today . '.pdf';

        // Load view untuk PDF
        $pdf = PDF::loadView('pdf.po-map-final', compact('purchaseOrder', 'grandtotalWords', 'category'));

        // Return file PDF
        return $pdf->stream($fileName);
    }

    private function terbilang($angka)
    {
        $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($angka));
    }

    public function rejectPO(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|string|in:milenia,map',
        ]);

        try {
            // Tentukan model berdasarkan type
            $purchaseOrder = $request->type === 'milenia'
            ? PurchaseOrderMilenia::find($request->id)
                : PurchaseOrderMAP::find($request->id);

            if (!$purchaseOrder) {
                return response()->json(['success' => false, 'message' => 'Purchase Order tidak ditemukan'], 404);
            }

            // Dapatkan jabatan user yang sedang login
            $user = auth()->user(); // Pastikan autentikasi pengguna sudah berjalan
            $jabatan = $user->Jabatan; // Asumsi kolom jabatan ada pada tabel user

            // Tentukan kolom yang akan diupdate berdasarkan jabatan
            $ttdField = ($jabatan === 'COO') ? 'ttd_3' : 'ttd_1';
            $namaField = ($jabatan === 'COO') ? 'nama_3' : 'nama_1';

            // Update status dan kolom ttd sesuai dengan jabatan
            $purchaseOrder->update([
                'status' => 0,
                $ttdField => 'REJECTED',  // Update kolom ttd yang sesuai
                $namaField => null, // Update kolom nama yang sesuai
            ]);

            return response()->json(['success' => true, 'message' => 'Purchase Request berhasil ditolak'], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menolak purchase request: ' . $e->getMessage()], 500);
        }
    }

    public function saveSignature(Request $request)
    {
        Log::info('Request Data:', $request->all());

        $request->validate([
            'id' => 'required',
            'signature' => 'required',
            'user_name' => 'required|string',
            'type' => 'required|string'
        ]);

        try {
            if ($request->type === 'milenia') {
                $purchaseOrder = PurchaseOrderMilenia::find($request->id);
            } elseif ($request->type === 'map') {
                $purchaseOrder = PurchaseOrderMAP::find($request->id);
            } else {
                return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
            }

            if (!$purchaseOrder) {
                return response()->json(['success' => false, 'message' => 'Purchase Order tidak ditemukan'], 404);
            }

            // Simpan tanda tangan ke storage
            $signaturePath = 'signature-ga-po/signature_' . time() . '.png';
            $signatureData = explode(',', $request->signature)[1];
            Storage::put('public/' . $signaturePath, base64_decode($signatureData));

            // Dapatkan jabatan user yang sedang login
            $user = auth()->user(); // Pastikan autentikasi pengguna sudah berjalan
            $jabatan = $user->Jabatan; // Asumsi kolom jabatan ada pada tabel user

            // Tentukan kolom yang akan diupdate berdasarkan jabatan
            $ttdField = ($jabatan === 'COO') ? 'ttd_3' : 'ttd_1';
            $namaField = ($jabatan === 'COO') ? 'nama_3' : 'nama_1';

            // Update status dan kolom ttd sesuai dengan jabatan
            $purchaseOrder->update([
                'status' => 2,
                $ttdField => $signaturePath,  // Update kolom ttd yang sesuai
                $namaField => $request->user_name, // Update kolom nama yang sesuai
            ]);

            return response()->json(['success' => true, 'message' => 'Approval Purchase Request berhasil dilakukan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat approval purchase request: ' . $e->getMessage()], 500);
        }
    }

    public function rejectPONotLogin(Request $request)
    {
        $request->validate([
            'id'       => 'required',
            'type'     => 'required|string|in:milenia,map',
            'jabatan'  => 'required|string', // informasi jabatan dikirim via request
        ]);

        try {
            // Tentukan model berdasarkan type
            $purchaseOrder = $request->type === 'milenia'
            ? PurchaseOrderMilenia::find($request->id)
                : PurchaseOrderMAP::find($request->id);

            if (!$purchaseOrder) {
                return response()->json(['success' => false, 'message' => 'Purchase Order tidak ditemukan'], 404);
            }

            // Gunakan jabatan yang dikirim melalui request
            $jabatan = $request->jabatan;

            // Tentukan kolom yang akan diupdate berdasarkan jabatan
            $ttdField = ($jabatan === 'COO') ? 'ttd_3' : 'ttd_1';
            $namaField = ($jabatan === 'COO') ? 'nama_3' : 'nama_1';

            // Update status dan kolom ttd sesuai dengan jabatan
            $purchaseOrder->update([
                'status'   => 0,
                $ttdField  => 'REJECTED',  // Update kolom ttd yang sesuai dengan nilai 'REJECTED'
                $namaField => null,        // Reset kolom nama
            ]);

            return response()->json(['success' => true, 'message' => 'Purchase Request berhasil ditolak'], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menolak purchase request: ' . $e->getMessage()], 500);
        }
    }

    public function saveSignatureNotLogin(Request $request)
    {
        Log::info('Request Data:', $request->all());

        $request->validate([
            'id'        => 'required',
            'signature' => 'required',
            'user_name' => 'required|string',
            'type'      => 'required|string',
            'jabatan'   => 'required|string', // informasi jabatan dikirim via request
        ]);

        try {
            if ($request->type === 'milenia') {
                $purchaseOrder = PurchaseOrderMilenia::find($request->id);
            } elseif ($request->type === 'map') {
                $purchaseOrder = PurchaseOrderMAP::find($request->id);
            } else {
                return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
            }

            if (!$purchaseOrder) {
                return response()->json(['success' => false, 'message' => 'Purchase Order tidak ditemukan'], 404);
            }

            // Simpan tanda tangan ke storage
            $signaturePath = 'signature-ga-po/signature_' . time() . '.png';
            $signatureData = explode(',', $request->signature)[1];
            Storage::put('public/' . $signaturePath, base64_decode($signatureData));

            // Gunakan jabatan yang dikirim melalui request
            $jabatan = $request->jabatan;

            // Tentukan kolom yang akan diupdate berdasarkan jabatan
            $ttdField = ($jabatan === 'COO') ? 'ttd_3' : 'ttd_1';
            $namaField = ($jabatan === 'COO') ? 'nama_3' : 'nama_1';

            // Update status dan kolom ttd sesuai dengan jabatan
            $purchaseOrder->update([
                'status'   => 2,
                $ttdField  => $signaturePath,      // simpan path tanda tangan
                $namaField => $request->user_name, // simpan nama user yang meng-approve
            ]);

            return response()->json(['success' => true, 'message' => 'Approval Purchase Request berhasil dilakukan'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat approval purchase request: ' . $e->getMessage()], 500);
        }
    }

}
