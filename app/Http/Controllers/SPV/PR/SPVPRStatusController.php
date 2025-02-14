<?php

namespace App\Http\Controllers\SPV\PR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use App\Mail\PrApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;
use Barryvdh\DomPDF\Facade\PDF as PDF;

class SPVPRStatusController extends Controller
{
    public function status()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();
        $userId = $user->ID;
        $userDivisi = $user->Divisi;
        $userNama   = $user->Nama;

        Log::info('Data user yang sedang login:', ['divisi' => $userDivisi, 'nama' => $userNama]);

        // Ambil data dari database dengan join ke tabel users dan filter berdasarkan divisi
        $purchaseRequests = PurchaseRequest::join('users', 'users.ID', '=', 'purchase_request.user_id')
            ->where(function ($query) use ($userDivisi, $userId) {
                $query->where('purchase_request.divisi', $userDivisi)
                    ->orWhere('purchase_request.user_id', $userId);
            })
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
        // Ambil data Purchase Request beserta relasi (barang, user, lampiran)
        $purchaseRequest = PurchaseRequest::with('barang', 'user', 'lampiran')->findOrFail($id);

        // Format nama file PDF berdasarkan nama user dan tanggal hari ini
        $userName = $purchaseRequest->user->Nama; // Sesuaikan jika field nama user berbeda
        $today = \Carbon\Carbon::now()->format('Y-m-d'); // Format: YYYY-MM-DD
        $fileName = 'PR_' . strtolower(str_replace(' ', '_', $userName)) . '_' . $today . '.pdf';

        // 1. Generate PDF utama dari view (misalnya: resources/views/pdf/pr.blade.php)
        $dompdf = Pdf::loadView('pdf.pr', compact('purchaseRequest'));
        $mainPdfContent = $dompdf->output();

        // Simpan PDF utama ke file sementara
        $tempMainPath = tempnam(sys_get_temp_dir(), 'main') . '.pdf';
        file_put_contents($tempMainPath, $mainPdfContent);

        // 2. Buat instance FPDI untuk menggabungkan PDF
        $fpdi = new Fpdi();

        // Import setiap halaman dari PDF utama ke FPDI
        $pageCountMain = $fpdi->setSourceFile($tempMainPath);
        for ($pageNo = 1; $pageNo <= $pageCountMain; $pageNo++) {
            $templateId = $fpdi->importPage($pageNo);
            $size = $fpdi->getTemplateSize($templateId);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($templateId);
        }

        // Variabel untuk mengumpulkan lampiran gambar
        $imageAttachments = [];

        // 3. Tambahkan lampiran dari Purchase Request
        foreach ($purchaseRequest->lampiran as $lampiran) {
            $extension = strtolower(pathinfo($lampiran->file_path, PATHINFO_EXTENSION));
            $lampiranPath = storage_path('app/public/' . $lampiran->file_path);

            // Jika lampiran berupa PDF, impor setiap halamannya
            if ($extension === 'pdf') {
                if (file_exists($lampiranPath)) {
                    $pageCountAttach = $fpdi->setSourceFile($lampiranPath);
                    for ($page = 1; $page <= $pageCountAttach; $page++) {
                        $templateId = $fpdi->importPage($page);
                        $size = $fpdi->getTemplateSize($templateId);
                        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);

                        // Tambahkan header teks "Lampiran" di atas konten lampiran
                        // $fpdi->SetFont('Arial', 'B', 16);
                        // $fpdi->SetTextColor(0, 0, 0);
                        // // Cell dengan lebar halaman; gunakan tinggi 10 mm untuk header
                        // $fpdi->Cell($size['width'], 10, 'Lampiran', 0, 1, 'C');

                        // Tempatkan template lampiran dengan offset vertikal agar tidak tertutup header
                        $fpdi->useTemplate($templateId, 0, 10, $size['width'], $size['height'] - 10);
                    }
                }
            }
            // Jika lampiran berupa gambar, simpan ke array untuk diproses secara terpisah
            elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (file_exists($lampiranPath)) {
                    $imageAttachments[] = $lampiranPath;
                }
            }
            // Format lain dapat ditangani sesuai kebutuhan
        }

        // 4. Jika terdapat lampiran gambar, tampilkan dalam grid pada halaman tersendiri
        if (count($imageAttachments) > 0) {
            // Definisikan layout grid:
            $maxColumns = 2;         // Jumlah kolom per halaman
            $maxRows = 2;            // Jumlah baris per halaman
            $imagesPerPage = $maxColumns * $maxRows; // Maksimal gambar per halaman

            // Gunakan ukuran halaman A4 portrait
            $pageWidth    = 210;  // lebar A4 dalam mm
            $pageHeight   = 297;  // tinggi A4 dalam mm
            $margin       = 10;   // margin dalam mm
            $headerHeight = 10;   // tinggi header "Lampiran" dalam mm

            // Hitung dimensi cell (area untuk tiap gambar)
            $availableWidth  = $pageWidth - 2 * $margin;  // misal, 210 - 20 = 190 mm
            $availableHeight = $pageHeight - $margin - $headerHeight - $margin; // misal, 297 - 10 - 10 - 10 = 267 mm
            $cellWidth  = $availableWidth / $maxColumns;   // misal, 190 / 2 = 95 mm
            $cellHeight = $availableHeight / $maxRows;      // misal, 267 / 2 ≈ 133.5 mm

            // Tentukan gap/padding untuk tiap gambar (misalnya 3 mm di setiap sisi)
            $gap = 3; // dalam mm

            // Bagi gambar menjadi beberapa halaman jika perlu
            $chunks = array_chunk($imageAttachments, $imagesPerPage);
            foreach ($chunks as $chunk) {
                // Tambahkan halaman baru dengan ukuran A4 portrait
                $fpdi->AddPage(
                    'P',
                    'A4'
                );
                // Tambahkan header teks "Lampiran"
                $fpdi->SetFont('Arial', 'B', 16);
                $fpdi->SetTextColor(0,
                    0,
                    0
                );
                $fpdi->Cell(
                    0,
                    $headerHeight,
                    'Lampiran',
                    0,
                    1,
                    'C'
                );

                // Posisi awal untuk grid gambar
                $startX = $margin;
                $startY = $margin + $headerHeight; // header menggunakan 10 mm

                $row = 0;
                $col = 0;
                foreach ($chunk as $imgPath) {
                    // Dapatkan ukuran asli gambar
                    list($origWidth,
                        $origHeight
                    ) = getimagesize($imgPath);

                    // Hitung effective cell dimensions dengan gap (padding) di semua sisi
                    $effectiveCellWidth  = $cellWidth - 2 * $gap;
                    $effectiveCellHeight = $cellHeight - 2 * $gap;

                    // Hitung skala agar gambar muat di dalam effective cell (tanpa mengubah aspek rasio)
                    $scale = min($effectiveCellWidth / $origWidth, $effectiveCellHeight / $origHeight);
                    $displayWidth  = $origWidth * $scale;
                    $displayHeight = $origHeight * $scale;

                    // Hitung posisi cell
                    $cellX = $startX + $col * $cellWidth;
                    $cellY = $startY + $row * $cellHeight;
                    // Hitung offset di dalam cell: mulai dari cellX + gap, dan center dalam effective area
                    $offsetX = $cellX + $gap + (($effectiveCellWidth - $displayWidth) / 2);
                    $offsetY = $cellY + $gap + (($effectiveCellHeight - $displayHeight) / 2);

                    // Tempatkan gambar dengan ukuran yang telah dihitung
                    $fpdi->Image($imgPath, $offsetX, $offsetY, $displayWidth, $displayHeight);

                    // Pindah ke cell berikutnya
                    $col++;
                    if (
                        $col >= $maxColumns
                    ) {
                        $col = 0;
                        $row++;
                    }
                }
            }
        }

        // Hapus file PDF utama sementara
        @unlink($tempMainPath);

        // 5. Output PDF gabungan
        $mergedPdfContent = $fpdi->Output('S', $fileName);

        return response($mergedPdfContent, 200)
            ->header('Content-Type', 'application/pdf');
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
                'acc_sign' => 'REJECTED',
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

            // Mengambil data user dari kolom user_id pada purchase_request
            $user = User::find($purchaseRequest->user_id);

            // Mengirim email ke admin dan cc ke email karyawan dari user
            Mail::to('admin.ga@ccas.co.id')
            ->cc($user->email_karyawan)
            ->send(new PrApprovedMail($purchaseRequest, $user));

            return response()->json(['success' => true, 'message' => 'Approval Purchase Request berhasil dilakukan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat approval purchase request: ' . $e->getMessage()], 500);
        }
    }
}
