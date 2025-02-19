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
use App\Models\Barang\Barang;
use App\Mail\PoCreatedMail;
use App\Mail\PoCreatedMailMAP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $noPoTerakhir = $lastPo ? $lastPo->no_po : 'PL000000';

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
            // Jika terdapat po_id maka lakukan update
            if (!empty($request->po_id)) {
                $purchaseOrder = PurchaseOrderMilenia::find($request->po_id);
                if (!$purchaseOrder) {
                    return response()->json(['success' => false, 'message' => 'PO tidak ditemukan.'], 404);
                }
                $purchaseOrder->update([
                    'no_po'       => $request->no_po,
                    'cabang'      => $request->cabang_po,
                    'cabang_id'   => $request->cabang_id,
                    'supplier'    => $request->supplier_po,
                    'supplier_id' => $request->supplier_id,
                    'address'     => $request->address_po,
                    'phone'       => $request->phone_po,
                    'fax'         => $request->fax_po,
                    'up'          => $request->up_po,
                    'date'        => $request->date_po,
                    'estimate_date' => $request->estimatedate_po,
                    'remarks'     => $request->remarks_po,
                    'sub_total'   => $request->sub_total,
                    'pajak'       => $request->tax_amount,
                    'discount'    => $request->discount,
                    'total'       => $request->total,
                    'ttd_2'       => $signaturePath,
                    'nama_2'      => $request->namapembuat_po,
                ]);

                // Untuk data barang, misalnya kita hapus yang lama dan masukkan data baru:
                PurchaseOrderBarangMilenia::where('purchase_order_id', $purchaseOrder->id)->delete();
            } else {
                // Jika tidak ada po_id, buat data baru
                $purchaseOrder = PurchaseOrderMilenia::create([
                    'no_po'       => $request->no_po,
                    'cabang'      => $request->cabang_po,
                    'cabang_id'   => $request->cabang_id,
                    'supplier'    => $request->supplier_po,
                    'supplier_id' => $request->supplier_id,
                    'address'     => $request->address_po,
                    'phone'       => $request->phone_po,
                    'fax'         => $request->fax_po,
                    'up'          => $request->up_po,
                    'date'        => $request->date_po,
                    'estimate_date' => $request->estimatedate_po,
                    'remarks'     => $request->remarks_po,
                    'sub_total'   => $request->sub_total,
                    'pajak'       => $request->tax_amount,
                    'discount'    => $request->discount,
                    'total'       => $request->total,
                    'ttd_2'       => $signaturePath,
                    'nama_2'      => $request->namapembuat_po,
                    'status'      => 1,
                ]);
            }

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

            // Proses data barang
            if (!empty($data['barang'])) {
                // Jika data barang berupa string JSON, decode menjadi array
                if (is_string($data['barang'])) {
                    $barangArray = json_decode($data['barang'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Format data barang tidak valid.");
                    }
                } elseif (is_array($data['barang'])) {
                    $barangArray = $data['barang'];
                } else {
                    throw new \Exception("Format data barang tidak sesuai.");
                }

                Log::info('Barang diterima dari frontend:', $barangArray);

                foreach ($barangArray as $barang) {
                    // Ambil nama barang dari data (gunakan key 'barang' atau 'nama_barang')
                    $barangName = $barang['barang'] ?? $barang['nama_barang'] ?? null;
                    if (!$barangName) {
                        continue; // Lewati jika nama barang tidak ada
                    }

                    // Cek apakah barang sudah ada di tabel Barang (berdasarkan nama)
                    $existingBarang = Barang::where('nama', $barangName)->first();
                    if (!$existingBarang) {
                        // Jika barang tidak ditemukan, buat barang baru
                        $lastBarang = Barang::orderBy('id', 'desc')->first();
                        $newCode = $lastBarang ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1) : 'ITEM1';

                        $existingBarang = Barang::create([
                            'kode' => $newCode,
                            'nama' => $barangName,
                        ]);
                        Log::info('Barang baru dibuat:', ['id' => $existingBarang->id, 'nama' => $barangName]);
                    }

                    // Simpan data barang ke tabel purchase_order_barang_milenia
                    $barangEntry = PurchaseOrderBarangMilenia::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'category_id'       => $barang['category_id'] ?? null,
                        'category'          => $barang['category'] ?? null,
                        // Gunakan ID dan nama barang dari tabel Barang
                        'barang_id'         => $existingBarang->id,
                        'barang'            => $existingBarang->nama,
                        'qty'               => $barang['qty'] ?? 1,
                        'unit_id'           => $barang['unit_id'] ?? null,
                        'unit'              => $barang['unit'] ?? null,
                        'keterangan'        => $barang['keterangan'] ?? null,
                        'unit_price'        => $barang['unit_price'] ?? 0,
                        'amount_price'      => $barang['amount_price'] ?? 0,
                    ]);

                    Log::info('Data barang berhasil disimpan ke tabel purchase_order_barang_milenia:', ['id' => $barangEntry->id]);
                }
            } else {
                Log::warning('Data barang tidak ditemukan di request.');
            }

            DB::commit();
            Log::info('Data berhasil disimpan ke database.');

            // Kirim email ke SPV GA (saat ini ari.darma@ccas.co.id)
            Mail::to('ari.darma@ccas.co.id')
            ->cc('it.web2@ccas.co.id')
            ->send(new PoCreatedMail($purchaseOrder));

            return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dibuat'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()], 500);
        }
    }

    public function statusPOMilenia()
    {
        $purchaseOrders = PurchaseOrderMilenia::with('barang')->get();
        return view('admin.po-milenia.status', compact('purchaseOrders'));
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

        // Query data cabang berdasarkan nama cabang yang disimpan di purchaseOrder.
        // Jika purchaseOrder->cabang berisi nama cabang, kita asumsikan itu sesuai dengan field 'nama' di tabel cabang.
        $cabangData = \App\Models\Cabang\Cabang::where('nama', $purchaseOrder->cabang)->first();

        // Load view untuk PDF dengan meneruskan data tambahan (misalnya, $cabangData)
        $pdf = PDF::loadView('pdf.po-milenia-final', compact('purchaseOrder', 'grandtotalWords', 'category', 'cabangData'));

        // Return file PDF
        return $pdf->stream($fileName);
    }

    public function getPoDataMilenia(Request $request)
    {
        $noPo = $request->query('no_po');
        // Cari PO berdasarkan no_po
        $po = PurchaseOrderMilenia::with('barang')->where('no_po', $noPo)->first();

        if ($po) {
            return response()->json([
                'success' => true,
                'po' => $po
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'PO tidak ditemukan'
            ]);
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
        $noPoTerakhir = $lastPo ? $lastPo->no_po : 'PL000000';

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
            // Jika terdapat po_id maka lakukan update
            if (!empty($request->po_id)) {
                $purchaseOrder = PurchaseOrderMAP::find($request->po_id);
                if (!$purchaseOrder) {
                    return response()->json(['success' => false, 'message' => 'PO tidak ditemukan.'], 404);
                }
                $purchaseOrder->update([
                    'no_po'       => $request->no_po,
                    'cabang'      => $request->cabang_po,
                    'cabang_id'   => $request->cabang_id,
                    'supplier'    => $request->supplier_po,
                    'supplier_id' => $request->supplier_id,
                    'address'     => $request->address_po,
                    'phone'       => $request->phone_po,
                    'fax'         => $request->fax_po,
                    'up'          => $request->up_po,
                    'date'        => $request->date_po,
                    'estimate_date' => $request->estimatedate_po,
                    'remarks'     => $request->remarks_po,
                    'sub_total'   => $request->sub_total,
                    'pajak'       => $request->tax_amount,
                    'discount'    => $request->discount,
                    'total'       => $request->total,
                    'ttd_2'       => $signaturePath,
                    'nama_2'      => $request->namapembuat_po,
                ]);

                // Untuk data barang, misalnya kita hapus yang lama dan masukkan data baru:
                PurchaseOrderBarangMAP::where('purchase_order_id', $purchaseOrder->id)->delete();
            } else {
                // Jika tidak ada po_id, buat data baru
                $purchaseOrder = PurchaseOrderMAP::create([
                    'no_po'       => $request->no_po,
                    'cabang'      => $request->cabang_po,
                    'cabang_id'   => $request->cabang_id,
                    'supplier'    => $request->supplier_po,
                    'supplier_id' => $request->supplier_id,
                    'address'     => $request->address_po,
                    'phone'       => $request->phone_po,
                    'fax'         => $request->fax_po,
                    'up'          => $request->up_po,
                    'date'        => $request->date_po,
                    'estimate_date' => $request->estimatedate_po,
                    'remarks'     => $request->remarks_po,
                    'sub_total'   => $request->sub_total,
                    'pajak'       => $request->tax_amount,
                    'discount'    => $request->discount,
                    'total'       => $request->total,
                    'ttd_2'       => $signaturePath,
                    'nama_2'      => $request->namapembuat_po,
                    'status'      => 1,
                ]);
            }

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

            // Proses data barang
            if (!empty($data['barang'])) {
                // Jika data barang berupa string JSON, decode menjadi array
                if (is_string($data['barang'])) {
                    $barangArray = json_decode($data['barang'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Format data barang tidak valid.");
                    }
                } elseif (is_array($data['barang'])) {
                    $barangArray = $data['barang'];
                } else {
                    throw new \Exception("Format data barang tidak sesuai.");
                }

                Log::info('Barang diterima dari frontend:', $barangArray);

                foreach ($barangArray as $barang) {
                    // Ambil nama barang dari data (gunakan key 'barang' atau 'nama_barang')
                    $barangName = $barang['barang'] ?? $barang['nama_barang'] ?? null;
                    if (!$barangName) {
                        continue; // Lewati jika nama barang tidak ada
                    }

                    // Cek apakah barang sudah ada di tabel Barang (berdasarkan nama)
                    $existingBarang = Barang::where('nama', $barangName)->first();
                    if (!$existingBarang) {
                        // Jika barang tidak ditemukan, buat barang baru
                        $lastBarang = Barang::orderBy('id', 'desc')->first();
                        $newCode = $lastBarang ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1) : 'ITEM1';

                        $existingBarang = Barang::create([
                            'kode' => $newCode,
                            'nama' => $barangName,
                        ]);
                        Log::info('Barang baru dibuat:', ['id' => $existingBarang->id, 'nama' => $barangName]);
                    }

                    // Simpan data barang ke tabel purchase_order_barang_milenia
                    $barangEntry = PurchaseOrderBarangMAP::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'category_id'       => $barang['category_id'] ?? null,
                        'category'          => $barang['category'] ?? null,
                        // Gunakan ID dan nama barang dari tabel Barang
                        'barang_id'         => $existingBarang->id,
                        'barang'            => $existingBarang->nama,
                        'qty'               => $barang['qty'] ?? 1,
                        'unit_id'           => $barang['unit_id'] ?? null,
                        'unit'              => $barang['unit'] ?? null,
                        'keterangan'        => $barang['keterangan'] ?? null,
                        'unit_price'        => $barang['unit_price'] ?? 0,
                        'amount_price'      => $barang['amount_price'] ?? 0,
                    ]);

                    Log::info('Data barang berhasil disimpan ke tabel purchase_order_barang_milenia:', ['id' => $barangEntry->id]);
                }
            } else {
                Log::warning('Data barang tidak ditemukan di request.');
            }

            DB::commit();
            Log::info('Data berhasil disimpan ke database.');

            // Kirim email ke SPV GA (saat ini ari.darma@ccas.co.id)
            Mail::to('ari.darma@ccas.co.id')
            ->cc('it.web2@ccas.co.id')
            ->send(new PoCreatedMailMAP($purchaseOrder));

            return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dibuat'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan Purchase Request: ' . $e->getMessage()], 500);
        }
    }

    public function statusPOMap()
    {
        $purchaseOrders = PurchaseOrderMAP::with('barang')->get();
        return view('admin.po-map.status', compact('purchaseOrders'));
    }

    public function generatePDFMap($id)
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

        // Query data cabang berdasarkan nama cabang yang disimpan di purchaseOrder.
        // Jika purchaseOrder->cabang berisi nama cabang, kita asumsikan itu sesuai dengan field 'nama' di tabel cabang.
        $cabangData = \App\Models\Cabang\Cabang::where('nama', $purchaseOrder->cabang)->first();

        // Load view untuk PDF dengan meneruskan data tambahan (misalnya, $cabangData)
        $pdf = PDF::loadView('pdf.po-map-final', compact('purchaseOrder', 'grandtotalWords', 'category', 'cabangData'));

        // Return file PDF
        return $pdf->stream($fileName);
    }

    public function getPoDataMap(Request $request)
    {
        $noPo = $request->query('no_po');
        // Cari PO berdasarkan no_po
        $po = PurchaseOrderMAP::with('barang')->where('no_po', $noPo)->first();

        if ($po) {
            return response()->json([
                'success' => true,
                'po' => $po
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'PO tidak ditemukan'
            ]);
        }
    }

    private function terbilang($angka)
    {
        $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($angka));
    }
}
