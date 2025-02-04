<?php

namespace App\Http\Controllers\SPV\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF as PDF;

class SPVPRStatusController extends Controller
{
    public function status()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();
        $userDivisi = $user->Divisi;

        Log::info('Divisi user yang sedang login:', ['divisi' => $userDivisi]);

        // Ambil data dari database dengan join ke tabel users dan filter berdasarkan divisi
        $purchaseRequests = PurchaseRequest::join('users', 'users.ID', '=', 'purchase_request.user_id')
            ->where('purchase_request.divisi', $userDivisi) // Filter berdasarkan divisi
            ->select(
                'purchase_request.*',
                'users.Nama as user_name'
            )
            ->get()
            ->map(function ($pr) {
                // Mapping status ke dalam bentuk label
                $statusLabels = [
                    0 => 'Rejected',
                    1 => 'Waiting Approved by Supervisor',
                    2 => 'Waiting Purchase Order by Admin',
                    3 => 'Waiting Approved by GA / Director',
                    4 => 'Done'
                ];

                $pr->status_label = $statusLabels[$pr->status] ?? 'Unknown';

                // Ambil barang terkait untuk setiap purchase request
                $pr->barang_list = PurchaseRequestBarang::where('purchase_request_id', $pr->id)
                    ->get()
                    ->map(function ($barang) {
                        return "{$barang->nama_barang} <span class='badge text-bg-secondary'>({$barang->quantity} {$barang->unit})</span>";
                    })
                    ->toArray();

                return $pr;
            });

        return view('spv.pr.status', ['purchaseRequests' => $purchaseRequests]);
    }


    public function generatePDF($id)
    {
        $purchaseRequest = PurchaseRequest::with('barang', 'user')->findOrFail($id);

        $userName = $purchaseRequest->user->Nama; // Sesuaikan jika field nama user berbeda

        // Ambil tanggal hari ini
        $today = \Carbon\Carbon::now()->format('Y-m-d'); // Format: YYYY-MM-DD

        // Format nama file PDF
        $fileName = 'PR_' . strtolower(str_replace(' ', '_', $userName)) . '_' . $today . '.pdf';

        // Load view untuk PDF
        $pdf = PDF::loadView('pdf.pr', compact('purchaseRequest'));

        // Return file PDF
        return $pdf->stream($fileName);
    }

    public function rejectPR(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchase_request,id',
        ]);

        try {
            $purchaseRequest = PurchaseRequest::find($request->id);

            // Update status menjadi 0
            $purchaseRequest->update([
                'status' => 0,
                'acc_sign' => null,
                'acc_by' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Purchase Request berhasil ditolak'], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menolak purchase request: ' . $e->getMessage()], 500);
        }
    }


    public function saveSignature(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchase_request,id',
            'signature' => 'required',
            'user_name' => 'required|string',
        ]);

        try {
            $purchaseRequest = PurchaseRequest::find($request->id);

            // Simpan tanda tangan ke storage
            $signaturePath = 'signature-spv/signature_' . time() . '.png';
            $signatureData = explode(',', $request->signature)[1];
            Storage::put('public/' . $signaturePath, base64_decode($signatureData));

            // Update data di database
            $purchaseRequest->update([
                'status' => 2,
                'acc_sign' => $signaturePath,
                'acc_by' => $request->user_name,
            ]);

            return response()->json(['success' => true, 'message' => 'Approval Purchase Request berhasil dilakukan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat approval purchase request: ' . $e->getMessage()], 500);
        }
    }
}
