<?php

namespace App\Http\Controllers\Admin\Input;

use App\Http\Controllers\Controller;
use App\Models\Unit\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('admin.unit.index', compact('units'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'satuanunit' => 'required|string|max:255',
        ]);

        try {
            // Generate a new unit code
            $lastUnit = Unit::orderBy('id', 'desc')->first();
            $newCode = $lastUnit
                ? 'UNIT' . ((int) substr($lastUnit->kode, 4) + 1)
                : 'UNIT1';

            // Create a new unit
            Unit::create([
                'kode' => $newCode,
                'satuan' => $request->satuanunit,
            ]);

            // Return success response
            return response()->json(['success' => true, 'message' => 'Unit berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan unit: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $kode)
    {
        // Validate the data
        $validated = $request->validate([
            'satuanunitupdate' => 'required|string|max:255',
        ]);

        try {
            // Find the unit by its 'kode' instead of 'id'
            $unit = Unit::where('kode', $kode)->firstOrFail();

            // Update the unit's name
            $unit->satuan = $request->satuanunitupdate;
            $unit->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Unit berhasil diupdate'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate unit: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the unit by its ID and delete it
            $unit = Unit::findOrFail($id);
            $unit->delete();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Unit berhasil dihapus'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus unit: ' . $e->getMessage()], 500);
        }
    }

    public function lastUnitId()
    {
        $lastUnit = Unit::orderBy('id', 'desc')->first();
        $newCode = $lastUnit
            ? 'UNIT' . ((int) substr($lastUnit->kode, 4) + 1)
            : 'UNIT1';

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

        // Inisialisasi kode unit
        $latestUnit = Unit::orderBy('id', 'desc')->first();
        $nextId = $latestUnit ? $latestUnit->id + 1 : 1;

        $units = [];
        foreach ($data as $index => $row) {
            // Lewati baris kosong
            if (empty($row[0])) {
                continue;
            }

            $units[] = [
                'kode' => 'UNIT' . ($nextId + $index),
                'satuan' => $row[0] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Simpan ke database
        Unit::insert($units);

        return back()->with('success', 'Data berhasil diimport!');
    }
}
