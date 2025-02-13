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
use setasign\Fpdi\Fpdi;
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
            $maxRows = 3;            // Jumlah baris per halaman
            $imagesPerPage = $maxColumns * $maxRows; // Maksimal gambar per halaman

            // Gunakan ukuran halaman A4 portrait
            $pageWidth  = 210;  // lebar A4 dalam mm
            $pageHeight = 297;  // tinggi A4 dalam mm
            $margin     = 10;   // margin dalam mm
            $headerHeight = 10; // tinggi header "Lampiran" dalam mm

            // Hitung dimensi cell (area untuk tiap gambar)
            $availableWidth  = $pageWidth - 2 * $margin;  // misal, 210 - 20 = 190 mm
            $availableHeight = $pageHeight - $margin - $headerHeight - $margin; // misal, 297 - 10 - 10 - 10 = 267 mm
            $cellWidth  = $availableWidth / $maxColumns;   // misal, 190 / 2 = 95 mm
            $cellHeight = $availableHeight / $maxRows;      // misal, 267 / 3 ≈ 89 mm

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
                $fpdi->SetTextColor(0, 0, 0);
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
                    list($origWidth, $origHeight) = getimagesize($imgPath);

                    // Hitung skala agar gambar muat di dalam cell (tanpa mengubah aspek rasio)
                    $scale = min($cellWidth / $origWidth, $cellHeight / $origHeight);
                    $displayWidth  = $origWidth  * $scale;
                    $displayHeight = $origHeight * $scale;

                    // Hitung posisi (offset) agar gambar di-center dalam cell
                    $cellX = $startX + $col * $cellWidth;
                    $cellY = $startY + $row * $cellHeight;
                    $offsetX = $cellX + ($cellWidth - $displayWidth) / 2;
                    $offsetY = $cellY + ($cellHeight - $displayHeight) / 2;

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

    public function generatePDFMAP($id)
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
            $maxRows = 3;            // Jumlah baris per halaman
            $imagesPerPage = $maxColumns * $maxRows; // Maksimal gambar per halaman

            // Gunakan ukuran halaman A4 portrait
            $pageWidth  = 210;  // lebar A4 dalam mm
            $pageHeight = 297;  // tinggi A4 dalam mm
            $margin     = 10;   // margin dalam mm
            $headerHeight = 10; // tinggi header "Lampiran" dalam mm

            // Hitung dimensi cell (area untuk tiap gambar)
            $availableWidth  = $pageWidth - 2 * $margin;  // misal, 210 - 20 = 190 mm
            $availableHeight = $pageHeight - $margin - $headerHeight - $margin; // misal, 297 - 10 - 10 - 10 = 267 mm
            $cellWidth  = $availableWidth / $maxColumns;   // misal, 190 / 2 = 95 mm
            $cellHeight = $availableHeight / $maxRows;      // misal, 267 / 3 ≈ 89 mm

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
                $fpdi->SetTextColor(0, 0, 0);
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
                    list($origWidth, $origHeight) = getimagesize($imgPath);

                    // Hitung skala agar gambar muat di dalam cell (tanpa mengubah aspek rasio)
                    $scale = min($cellWidth / $origWidth, $cellHeight / $origHeight);
                    $displayWidth  = $origWidth  * $scale;
                    $displayHeight = $origHeight * $scale;

                    // Hitung posisi (offset) agar gambar di-center dalam cell
                    $cellX = $startX + $col * $cellWidth;
                    $cellY = $startY + $row * $cellHeight;
                    $offsetX = $cellX + ($cellWidth - $displayWidth) / 2;
                    $offsetY = $cellY + ($cellHeight - $displayHeight) / 2;

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
