<?php

namespace App\Http\Controllers\Admin\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF as PDF;

class AdminPRStatusController extends Controller
{
    public function milenia()
    {
        $purchaseRequests = PurchaseRequest::with('barang')
        ->where('pt', 'PT. Milenia Mega Mandiri')
        ->whereIn('status', [2, 3, 4])
            ->orderBy('created_at', 'desc')
            ->get();

        $statusLabels = [
            0 => 'Rejected',
            1 => 'Waiting Approved by Supervisor',
            2 => 'Waiting Purchase Order by Admin',
            3 => 'Waiting Approved by GA / Director',
            4 => 'Done'
        ];

        return view('admin.pr-milenia.status', compact('purchaseRequests', 'statusLabels'));
    }

    public function map()
    {
        $purchaseRequests = PurchaseRequest::with('barang')
        ->where('pt', 'PT. Mega Auto Prima')
        ->whereIn('status', [2, 3, 4])
            ->orderBy('created_at', 'desc')
            ->get();

        $statusLabels = [
            0 => 'Rejected',
            1 => 'Waiting Approved by Supervisor',
            2 => 'Waiting Purchase Order by Admin',
            3 => 'Waiting Approved by GA / Director',
            4 => 'Done'
        ];

        return view('admin.pr-map.status', compact('purchaseRequests', 'statusLabels'));
    }

    public function generatePDFMilenia($id)
    {
        $purchaseRequest = PurchaseRequest::with(['barang', 'user'])
        ->findOrFail($id);

        $fileName = 'PR_' . Str::slug($purchaseRequest->user->Nama) . '_' . now()->format('Y-m-d') . '.pdf';

        $pdf = PDF::loadView('pdf.pr', compact('purchaseRequest'))
        ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function generatePDFMAP($id)
    {
        $purchaseRequest = PurchaseRequest::with(['barang', 'user'])
            ->findOrFail($id);

        $fileName = 'PR_' . Str::slug($purchaseRequest->user->Nama) . '_' . now()->format('Y-m-d') . '.pdf';

        $pdf = PDF::loadView('pdf.pr', compact('purchaseRequest'))
        ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function updateStatusMilenia(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchase_request,id',
            'status' => 'required|integer|between:0,4'
        ]);

        try {
            $pr = PurchaseRequest::findOrFail($request->id);
            $pr->status = $request->status;
            $pr->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function updateStatusMAP(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchase_request,id',
            'status' => 'required|integer|between:0,4'
        ]);

        try {
            $pr = PurchaseRequest::findOrFail($request->id);
            $pr->status = $request->status;
            $pr->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }
}
