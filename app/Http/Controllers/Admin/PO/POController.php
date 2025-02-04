<?php

namespace App\Http\Controllers\Admin\PO;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PR\PurchaseRequest;
use App\Models\PR\PurchaseRequestBarang;
use App\Models\PO\PurchaseOrderMilenia;
use App\Models\PO\PurchaseOrderBarangMilenia;
use App\Models\PO\PurchaseOrderMAP;
use App\Models\PO\PurchaseOrderBarangMAP;
use App\Models\Supplier\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF as PDF;
use NumberFormatter;

class POController extends Controller
{
    public function poMilenia()
    {
        $cabang = DB::table('cabang')->where('aktif', 1)->get();
        $supplier = DB::table('supplier')->get();
        $category = DB::table('category')->get();
        $unit = DB::table('unit')->get();
        $barang = DB::table('barang')->get();
        $nopomilenia = DB::table('purchase_order_milenia')->get();

        // Ambil nomor PO terbesar (terakhir) dari database
        $lastPo = DB::table('purchase_order_milenia')
        ->orderBy('no_po', 'desc')
        ->first();

        // Jika ada nomor PO, tampilkan nomor PO terbesar, jika tidak, set dengan 'PO1'
        $noPoTerakhir = $lastPo ? $lastPo->no_po : 'PLO000000';

        return view('admin.po-milenia.index', compact('cabang', 'supplier', 'category', 'unit', 'barang', 'nopomilenia', 'noPoTerakhir'));
    }

    public function previewPDFMilenia(Request $request)
    {
        // Mengambil data dari request
        $formData = $request->all();

        // Konversi grandtotal menjadi terbilang
        $formData['grandtotal_words'] = $this->terbilang($formData['grandtotal']);

        // Data barang
        $barang = $formData['barang'] ?? [];

        // Mengirim data ke view untuk diolah dan menghasilkan PDF
        $pdf = Pdf::loadView('pdf.po-milenia', [
            'formData' => $formData,
            'barang' => $barang,
            'signature' => $formData['signature'] ?? null,
        ]);

        return $pdf->stream('po_preview.pdf');
    }

    public function storeMilenia(Request $request)
    {
        // Log data request sebelum diproses
        Log::info('Data yang diterima di backend:', $request->all());

        try {
            DB::beginTransaction();

            // Ambil semua data dari request tanpa validasi
            $data = $request->all();

            // Proses tanda tangan (signature) jika ada
            $signaturePath = null;
            if (!empty($data['signature'])) {
                // Menghapus prefix base64 jika ada
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['signature']));

                if ($imageData === false) {
                    throw new \Exception("Gagal mendekode tanda tangan.");
                }

                // Nama file gambar
                $imageName = 'signature_' . time() . '.png';
                $directoryPath = 'public/signature-admin'; // Path di dalam storage

                // Simpan gambar ke storage dengan menggunakan Storage facade
                $signaturePath = 'signature-admin/' . $imageName;

                // Simpan gambar ke dalam folder storage yang sudah ditentukan
                Storage::put($directoryPath . '/' . $imageName, $imageData);
            }

            // Simpan data ke tabel purchase_order_milenia
            $purchaseOrder = PurchaseOrderMilenia::create([
                'no_po' => $data['no_po'] ?? null,
                'cabang' => $data['cabang_po'] ?? null,
                'cabang_id' => $data['cabang_id'] ?? null,
                'supplier' => $data['supplier_po'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'address' => $data['address_po'] ?? null,
                'phone' => $data['phone_po'] ?? null,
                'fax' => $data['fax_po'] ?? null,
                'up' => $data['up_po'] ?? null,
                'date' => $data['date_po'] ?? null,
                'estimate_date' => $data['estimatedate_po'] ?? null,
                'remarks' => $data['remarks_po'] ?? null,
                'sub_total' => $data['sub_total'] ?? 0,
                'pajak' => $data['tax_amount'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'] ?? 0,
                'ttd_2' => $signaturePath,
                'nama_2' => $data['namapembuat_po'] ?? null,
            ]);

            Log::info('Data purchase order berhasil disimpan dengan ID:', ['id' => $purchaseOrder->id]);

            // Cek apakah supplier_id ada dan update data supplier jika ada perubahan
            if (!empty($data['supplier_id'])) {
                $supplier = Supplier::find($data['supplier_id']); // Cari supplier berdasarkan ID

                if ($supplier) {
                    // Update informasi supplier jika ditemukan
                    $supplier->update([
                        'address' => $data['address_po'] ?? $supplier->address,
                        'phone' => $data['phone_po'] ?? $supplier->phone,
                        'fax' => $data['fax_po'] ?? $supplier->fax,
                        'up' => $data['up_po'] ?? $supplier->up,
                    ]);
                    Log::info('Data supplier berhasil diperbarui', ['supplier_id' => $supplier->id]);
                } else {
                    Log::warning('Supplier dengan ID ' . $data['supplier_id'] . ' tidak ditemukan.');
                }
            }

            // Memastikan format data barang valid
            if (!empty($data['barang'])) {
                // Jika data barang berupa string JSON, decode menjadi array
                if (is_string($data['barang'])) {
                    $barangArray = json_decode($data['barang'], true);

                    // Jika JSON tidak valid, tampilkan error
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Format data barang tidak valid.");
                    }
                } elseif (is_array($data['barang'])) {
                    // Jika data barang sudah berupa array, gunakan langsung
                    $barangArray = $data['barang'];
                } else {
                    // Jika format tidak sesuai, tampilkan error
                    throw new \Exception("Format data barang tidak sesuai.");
                }

                Log::info('Barang diterima dari frontend:', $barangArray);

                foreach ($barangArray as $barang) {
                    // Simpan data barang ke tabel purchase_order_barang_milenia
                    $barangEntry = PurchaseOrderBarangMilenia::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'category_id' => $barang['category_id'] ?? null,
                        'category' => $barang['category'] ?? null,
                        'barang_id' => $barang['barang_id'] ?? null,
                        'barang' => $barang['barang'] ?? null,
                        'qty' => $barang['qty'] ?? 1,
                        'unit_id' => $barang['unit_id'] ?? null,
                        'unit' => $barang['unit'] ?? null,
                        'keterangan' => $barang['keterangan'] ?? null,
                        'unit_price' => $barang['unit_price'] ?? 0,
                        'amount_price' => $barang['amount_price'] ?? 0,
                    ]);

                    // Log jika barang berhasil disimpan
                    Log::info(
                        'Data barang berhasil disimpan ke tabel purchase_order_barang_milenia:',
                        ['id' => $barangEntry->id]
                    );
                }
            } else {
                Log::warning('Data barang tidak ditemukan di request.');
            }

            DB::commit();
            Log::info('Data berhasil disimpan ke database.');

            return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dibuat'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()], 500);
        }
    }

    public function poMap()
    {
        $cabang = DB::table('cabang')->where('aktif', 1)->get();
        $supplier = DB::table('supplier')->get();
        $category = DB::table('category')->get();
        $unit = DB::table('unit')->get();
        $barang = DB::table('barang')->get();
        $nopomap = DB::table('purchase_order_map')->get();

        // Ambil nomor PO terbesar (terakhir) dari database
        $lastPo = DB::table('purchase_order_map')
        ->orderBy('no_po', 'desc')
        ->first();

        // Jika ada nomor PO, tampilkan nomor PO terbesar, jika tidak, set dengan 'PO1'
        $noPoTerakhir = $lastPo ? $lastPo->no_po : 'PLO000000';

        return view('admin.po-map.index', compact('cabang', 'supplier', 'category', 'unit', 'barang', 'nopomap', 'noPoTerakhir'));
    }

    public function previewPDFMap(Request $request)
    {
        // Mengambil data dari request
        $formData = $request->all();

        // Konversi grandtotal menjadi terbilang
        $formData['grandtotal_words'] = $this->terbilang($formData['grandtotal']);

        // Data barang
        $barang = $formData['barang'] ?? [];

        // Mengirim data ke view untuk diolah dan menghasilkan PDF
        $pdf = Pdf::loadView('pdf.po-map', [
            'formData' => $formData,
            'barang' => $barang,
            'signature' => $formData['signature'] ?? null,
        ]);

        return $pdf->stream('po_preview.pdf');
    }

    public function storeMap(Request $request)
    {
        // Log data request sebelum diproses
        Log::info('Data yang diterima di backend:', $request->all());

        try {
            DB::beginTransaction();

            // Ambil semua data dari request tanpa validasi
            $data = $request->all();

            // Proses tanda tangan (signature) jika ada
            $signaturePath = null;
            if (!empty($data['signature'])) {
                // Menghapus prefix base64 jika ada
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['signature']));

                if ($imageData === false) {
                    throw new \Exception("Gagal mendekode tanda tangan.");
                }

                // Nama file gambar
                $imageName = 'signature_' . time() . '.png';
                $directoryPath = 'public/signature-admin'; // Path di dalam storage

                // Simpan gambar ke storage dengan menggunakan Storage facade
                $signaturePath = 'signature-admin/' . $imageName;

                // Simpan gambar ke dalam folder storage yang sudah ditentukan
                Storage::put($directoryPath . '/' . $imageName, $imageData);
            }

            // Simpan data ke tabel purchase_order_map
            $purchaseOrder = PurchaseOrderMAP::create([
                'no_po' => $data['no_po'] ?? null,
                'cabang' => $data['cabang_po'] ?? null,
                'cabang_id' => $data['cabang_id'] ?? null,
                'supplier' => $data['supplier_po'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'address' => $data['address_po'] ?? null,
                'phone' => $data['phone_po'] ?? null,
                'fax' => $data['fax_po'] ?? null,
                'up' => $data['up_po'] ?? null,
                'date' => $data['date_po'] ?? null,
                'estimate_date' => $data['estimatedate_po'] ?? null,
                'remarks' => $data['remarks_po'] ?? null,
                'sub_total' => $data['sub_total'] ?? 0,
                'pajak' => $data['tax_amount'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'] ?? 0,
                'ttd_2' => $signaturePath,
                'nama_2' => $data['namapembuat_po'] ?? null,
            ]);

            Log::info('Data purchase order berhasil disimpan dengan ID:', ['id' => $purchaseOrder->id]);

            // Cek apakah supplier_id ada dan update data supplier jika ada perubahan
            if (!empty($data['supplier_id'])) {
                $supplier = Supplier::find($data['supplier_id']); // Cari supplier berdasarkan ID

                if ($supplier) {
                    // Update informasi supplier jika ditemukan
                    $supplier->update([
                        'address' => $data['address_po'] ?? $supplier->address,
                        'phone' => $data['phone_po'] ?? $supplier->phone,
                        'fax' => $data['fax_po'] ?? $supplier->fax,
                        'up' => $data['up_po'] ?? $supplier->up,
                    ]);
                    Log::info('Data supplier berhasil diperbarui', ['supplier_id' => $supplier->id]);
                } else {
                    Log::warning('Supplier dengan ID ' . $data['supplier_id'] . ' tidak ditemukan.');
                }
            }

            // Memastikan format data barang valid
            if (!empty($data['barang'])) {
                // Jika data barang berupa string JSON, decode menjadi array
                if (is_string($data['barang'])) {
                    $barangArray = json_decode($data['barang'], true);

                    // Jika JSON tidak valid, tampilkan error
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Format data barang tidak valid.");
                    }
                } elseif (is_array($data['barang'])) {
                    // Jika data barang sudah berupa array, gunakan langsung
                    $barangArray = $data['barang'];
                } else {
                    // Jika format tidak sesuai, tampilkan error
                    throw new \Exception("Format data barang tidak sesuai.");
                }

                Log::info('Barang diterima dari frontend:', $barangArray);

                foreach ($barangArray as $barang) {
                    // Simpan data barang ke tabel purchase_order_barang_map
                    $barangEntry = PurchaseOrderBarangMAP::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'category_id' => $barang['category_id'] ?? null,
                        'category' => $barang['category'] ?? null,
                        'barang_id' => $barang['barang_id'] ?? null,
                        'barang' => $barang['barang'] ?? null,
                        'qty' => $barang['qty'] ?? 1,
                        'unit_id' => $barang['unit_id'] ?? null,
                        'unit' => $barang['unit'] ?? null,
                        'keterangan' => $barang['keterangan'] ?? null,
                        'unit_price' => $barang['unit_price'] ?? 0,
                        'amount_price' => $barang['amount_price'] ?? 0,
                    ]);

                    // Log jika barang berhasil disimpan
                    Log::info(
                        'Data barang berhasil disimpan ke tabel purchase_order_barang_map:',
                        ['id' => $barangEntry->id]
                    );
                }
            } else {
                Log::warning('Data barang tidak ditemukan di request.');
            }

            DB::commit();
            Log::info('Data berhasil disimpan ke database.');

            return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dibuat'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()], 500);
        }
    }

    private function terbilang($angka)
    {
        $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($angka));
    }
}
