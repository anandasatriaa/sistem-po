<?php

namespace App\Http\Controllers\User\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use App\Models\Barang\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PRController extends Controller
{
    public function index()
    {
        $divisions = DB::table('users')->select('Divisi')->distinct()->whereNotNull('Divisi')->get();
        $units = DB::table('unit')->select('satuan')->get();
        $barangs = DB::table('barang')->select('nama')->get();
        return view('user.pr.index', compact('divisions', 'units', 'barangs'));
    }

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'user_id' => 'required|integer',
            'date_request' => 'required|date',
            'divisi' => 'required|string',
            'no_pr' => 'required|string',
            'pt' => 'required|string',
            'important' => 'required|array',
            'barang_data' => 'required|array',
        ]);

        try {
            // Simpan data di tabel purchase_request
            $purchaseRequest = new PurchaseRequest();
            $purchaseRequest->user_id = $request->user_id;
            $purchaseRequest->date_request = $request->date_request;
            $purchaseRequest->divisi = $request->divisi;
            $purchaseRequest->no_pr = $request->no_pr;
            $purchaseRequest->pt = $request->pt;
            $purchaseRequest->important = implode(", ", $request->important); // Menggabungkan status menjadi string
            $purchaseRequest->status = 1; // Set status ke 1 saat membuat purchase request
            $purchaseRequest->save();

            // Simpan data barang di tabel purchase_request_barang
            foreach ($request->barang_data as $barang) {
                $existingBarang = Barang::where('nama', $barang['nama_barang'])->first();
                if (!$existingBarang) {
                    // Jika barang tidak ada, tambahkan barang baru ke tabel barang
                    $lastBarang = Barang::orderBy('id', 'desc')->first();
                    $newCode = $lastBarang
                        ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1)
                        : 'ITEM1';

                    $existingBarang = Barang::create([
                        'kode' => $newCode,
                        'nama' => $barang['nama_barang'],
                    ]);
                }

                $purchaseRequestBarang = new PurchaseRequestBarang();
                $purchaseRequestBarang->purchase_request_id = $purchaseRequest->id;
                $purchaseRequestBarang->no_pr = $request->no_pr;
                $purchaseRequestBarang->nama_barang = $barang['nama_barang'];
                $purchaseRequestBarang->quantity = $barang['quantity'];
                $purchaseRequestBarang->unit = $barang['unit'];
                $purchaseRequestBarang->keterangan = $barang['keterangan'];
                $purchaseRequestBarang->save();
            }

            return response()->json(['success' => true, 'message' => 'Purchase Request berhasil diajukan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()], 500);
        }
    }


    public function generateNoPr()
    {
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        // Cek apakah ada PR di bulan ini
        $lastPr = PurchaseRequest::whereYear('date_request', $currentYear)
            ->whereMonth('date_request', $currentMonth)
            ->orderByDesc('no_pr')
            ->first();

        if ($lastPr) {
            // Ambil nomor PR terakhir, kemudian increment 1
            $lastNoPr = explode('/', $lastPr->no_pr);
            $lastNumber = (int)$lastNoPr[0];
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika tidak ada PR, mulai dari 001
            $newNumber = '001';
        }

        // Generate no_pr baru
        $noPr = $newNumber . '/' . $currentMonth . '/' . $currentYear;

        return response()->json(['no_pr' => $noPr]);
    }
}
