<?php

namespace App\Http\Controllers\GA\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use App\Models\Barang\Barang;
use App\Mail\PurchaseRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GAPRController extends Controller
{
    public function index()
    {
        $divisions = DB::table('users')->select('Divisi')->distinct()->whereNotNull('Divisi')->get();
        $units = DB::table('unit')->select('satuan')->get();
        $barangs = DB::table('barang')->select('nama')->get();
        return view('ga.pr.index', compact('divisions', 'units', 'barangs'));
    }

    public function store(Request $request)
    {
        // Validasi data input; lampiran tidak wajib jadi tidak divalidasi sebagai file
        $request->validate([
            'user_id'      => 'required|integer',
            'date_request' => 'required|date',
            'divisi'       => 'required|string',
            'no_pr'        => 'required|string',
            'pt'           => 'required|string',
            'important'    => 'required|string', // Berisi JSON string array
            'barang_data'  => 'required|string', // Berisi JSON string array
            'signature'    => 'required|string',
        ]);

        try {
            // Simpan signature sebagai file gambar
            $signatureData = $request->input('signature');
            $signaturePath = null;
            if ($signatureData) {
                // Decode data base64 gambar
                $signatureFile = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
                $signatureFileName = 'signature_' . time() . '.png';
                $signaturePath = 'signatures/' . $signatureFileName;
                Storage::put('public/' . $signaturePath, $signatureFile);
            }

            // Simpan data purchase request
            $purchaseRequest = new PurchaseRequest();
            $purchaseRequest->user_id      = $request->user_id;
            $purchaseRequest->date_request = $request->date_request;
            $purchaseRequest->divisi       = $request->divisi;
            $purchaseRequest->no_pr        = $request->no_pr;
            $purchaseRequest->pt           = $request->pt;
            // Decode JSON string dan gabungkan menjadi string (sesuai kebutuhan)
            $importantArray = json_decode($request->important, true);
            $purchaseRequest->important = implode(", ", $importantArray);
            $purchaseRequest->status     = 1; // Status awal
            $purchaseRequest->signature  = $signaturePath;
            $purchaseRequest->save();

            // Simpan data barang (decode JSON string)
            $barangData = json_decode($request->barang_data, true);
            foreach ($barangData as $barang) {
                $existingBarang = Barang::where('nama', $barang['nama_barang'])->first();
                if (!$existingBarang) {
                    // Jika barang tidak ditemukan, buat barang baru
                    $lastBarang = Barang::orderBy('id', 'desc')->first();
                    $newCode = $lastBarang ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1) : 'ITEM1';

                    $existingBarang = Barang::create([
                        'kode' => $newCode,
                        'nama' => $barang['nama_barang'],
                    ]);
                }

                $purchaseRequestBarang = new PurchaseRequestBarang();
                $purchaseRequestBarang->purchase_request_id = $purchaseRequest->id;
                $purchaseRequestBarang->no_pr               = $request->no_pr;
                $purchaseRequestBarang->nama_barang         = $barang['nama_barang'];
                $purchaseRequestBarang->quantity            = $barang['quantity'];
                $purchaseRequestBarang->unit                = $barang['unit'];
                $purchaseRequestBarang->keterangan          = $barang['keterangan'];
                $purchaseRequestBarang->save();
            }
            $purchaseRequest->load('barang');

            // Simpan file lampiran jika ada
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    // Simpan file ke folder 'lampiran_pr' pada disk 'public'
                    $lampiranPath = $file->store('lampiran_pr', 'public');
                    // Buat record di tabel purchase_request_lampiran
                    \App\Models\PR\PurchaseRequestLampiran::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'file_path'           => $lampiranPath,
                    ]);
                }
            }

            // Mengirim email ke atasan
            $user = User::find($request->user_id);
            if (!$user || !$user->email_atasan) {
                return response()->json(['success' => false, 'message' => 'Email atasan tidak ditemukan.']);
            }
            $emailAtasan = $user->email_karyawan;
            $ccAdmin = 'admin.ga@ccas.co.id';
            $ccUser  = $user->email_karyawan;
            Mail::to($emailAtasan)->cc([$ccAdmin, $ccUser])->send(new PurchaseRequestMail($purchaseRequest, $user));

            return response()->json(['success' => true, 'message' => 'Purchase Request berhasil diajukan'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateNoPr()
    {
        $currentMonth = Carbon::now()->format('m'); // Current month
        $currentYear = Carbon::now()->format('Y');  // Current year

        // Check if there are any PRs
        $lastPr = PurchaseRequest::orderByDesc('id')->first();

        if ($lastPr) {
            // Extract the last PR number and month/year from no_pr
            $lastNoPr = explode('/', $lastPr->no_pr);
            $lastNumber = (int)$lastNoPr[0];
            $lastMonth = $lastNoPr[1];
            $lastYear = $lastNoPr[2];

            // Check if the year has changed; reset PR number if it has
            if ($currentYear != $lastYear) {
                $newNumber = '001';
            } else {
                // Continue the sequence from the last PR number
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }
        } else {
            // If there are no PRs, start from 001
            $newNumber = '001';
        }

        // Generate new no_pr with the current month and year
        $noPr = $newNumber . '/' . $currentMonth . '/' . $currentYear;

        return response()->json(['no_pr' => $noPr]);
    }
}
