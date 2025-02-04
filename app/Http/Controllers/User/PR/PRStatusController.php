<?php

namespace App\Http\Controllers\User\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF as PDF;

class PRStatusController extends Controller
{
    public function status()
    {
        // Ambil ID user yang sedang login
        $user = Auth::user();
        $userId = $user->ID;

        Log::info('User ID yang sedang login:', ['userId' => $userId]);

        // Ambil data dari database dengan join ke tabel users dan filter berdasarkan user_id
        $purchaseRequests = PurchaseRequest::join('users', 'users.ID', '=', 'purchase_request.user_id')
        ->where('purchase_request.user_id', $userId) // Filter berdasarkan user_id
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

        return view('user.pr.status', ['purchaseRequests' => $purchaseRequests]);
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

}
