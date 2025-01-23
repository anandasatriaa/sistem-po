<?php

namespace App\Http\Controllers\Admin\Input;

use App\Http\Controllers\Controller;
use App\Models\Barang\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('admin.barang.index', compact('barangs'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'namabarang' => 'required|string|max:255',
        ]);

        try {
            // Generate a new barang code
            $lastBarang = Barang::orderBy('id', 'desc')->first();
            $newCode = $lastBarang
                ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1)
                : 'ITEM1';

            // Create a new barang
            Barang::create([
                'kode' => $newCode,
                'nama' => $request->namabarang,
            ]);

            // Return success response
            return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan barang: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $kode)
    {
        // Validate the data
        $validated = $request->validate([
            'namabarangupdate' => 'required|string|max:255',
        ]);

        try {
            // Find the barang by its 'kode' instead of 'id'
            $barang = Barang::where('kode', $kode)->firstOrFail();

            // Update the barang's name
            $barang->nama = $request->namabarangupdate;
            $barang->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Barang berhasil diupdate'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate barang: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the barang by its ID and delete it
            $barang = Barang::findOrFail($id);
            $barang->delete();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus barang: ' . $e->getMessage()], 500);
        }
    }

    public function lastBarangId()
    {
        $lastBarang = Barang::orderBy('id', 'desc')->first();
        $newCode = $lastBarang
            ? 'ITEM' . ((int) substr($lastBarang->kode, 4) + 1)
            : 'ITEM1';

        return response()->json(['kode' => $newCode]);
    }

    public function importCsv(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        // Baca file CSV
        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Ambil header dan hapus baris pertama (header)
        $headers = array_shift($data);

        // Inisialisasi kode barang
        $latestBarang = Barang::orderBy('id', 'desc')->first();
        $nextId = $latestBarang ? $latestBarang->id + 1 : 1;

        $barangs = [];
        foreach ($data as $index => $row) {
            // Lewati baris kosong
            if (empty($row[0])) {
                continue;
            }

            $barangs[] = [
                'kode' => 'ITEM' . ($nextId + $index),
                'nama' => $row[0] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Simpan ke database
        Barang::insert($barangs);

        return back()->with('success', 'Data berhasil diimport!');
    }
}
