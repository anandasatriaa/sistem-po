<?php

namespace App\Http\Controllers\Admin\Input;

use App\Http\Controllers\Controller;
use App\Models\Supplier\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('admin.supplier.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'namasupplier' => 'required|string|max:255',
            'addresssupplier' => 'nullable|string|max:500',
            'phonesupplier' => 'nullable|string|max:15',
            'faxsupplier' => 'nullable|string|max:15',
            'upsupplier' => 'nullable|string|max:255',
        ]);

        try {
            // Generate a new supplier code
            $lastSupplier = Supplier::orderBy('id', 'desc')->first();
            $newCode = $lastSupplier
                ? 'SUP' . ((int) substr($lastSupplier->kode, 3) + 1)
                : 'SUP1';

            // Create a new supplier
            Supplier::create([
                'kode' => $newCode,
                'nama' => $request->namasupplier,
                'address' => $request->addresssupplier,
                'phone' => $request->phonesupplier,
                'fax' => $request->faxsupplier,
                'up' => $request->upsupplier,
            ]);

            // Return success response
            return response()->json(['success' => true, 'message' => 'Supplier berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan supplier: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $kode)
    {
        $validated = $request->validate([
            'namasupplierupdate' => 'required|string|max:255',
            'addresssupplierupdate' => 'nullable|string|max:500',
            'phonesupplierupdate' => 'nullable|string|max:15',
            'faxsupplierupdate' => 'nullable|string|max:15',
            'upsupplierupdate' => 'nullable|string|max:255',
        ]);

        try {
            // Find the supplier by its 'kode' instead of 'id'
            $supplier = Supplier::where('kode', $kode)->firstOrFail();

            // Update the supplier's name
            $supplier->nama = $request->namasupplierupdate;
            $supplier->address = $request->addresssupplierupdate;
            $supplier->phone = $request->phonesupplierupdate;
            $supplier->fax = $request->faxsupplierupdate;
            $supplier->up = $request->upsupplierupdate;
            $supplier->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Supplier berhasil diupdate'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate supplier: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the supplier by its ID and delete it
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Supplier berhasil dihapus'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus supplier: ' . $e->getMessage()], 500);
        }
    }

    public function lastSupplierId()
    {
        $lastSupplier = Supplier::orderBy('id', 'desc')->first();
        $newCode = $lastSupplier
            ? 'SUP' . ((int) substr($lastSupplier->kode, 3) + 1)
            : 'SUP1';

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

        // Inisialisasi kode supplier
        $latestSupplier = Supplier::orderBy('id', 'desc')->first();
        $nextId = $latestSupplier ? $latestSupplier->id + 1 : 1;

        $suppliers = [];
        foreach ($data as $index => $row) {
            // Lewati baris kosong
            if (empty($row[0])) {
                continue;
            }

            $suppliers[] = [
                'kode' => 'SUP' . ($nextId + $index),
                'nama' => $row[0] ?? null,
                'address' => $row[1] ?? null,
                'phone' => $row[2] ?? null,
                'fax' => $row[4] ?? null,
                'up' => $row[3] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Simpan ke database
        Supplier::insert($suppliers);

        return back()->with('success', 'Data berhasil diimport!');
    }
}
