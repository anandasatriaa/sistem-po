<?php

namespace App\Http\Controllers\Admin\Input;

use App\Http\Controllers\Controller;
use App\Models\Cabang\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CabangController extends Controller
{
    public function index()
    {
        $cabang = Cabang::all();
        $provinsi = Cabang::select('provinsi')->distinct()->get();
        $kota = Cabang::select('kota')->distinct()->get();
        return view('admin.cabang.index', ['cabang' => $cabang, 'provinsi' => $provinsi, 'kota' => $kota]);
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'id_cabang' => 'required|string',
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'telepon' => 'nullable|string',
            'pic' => 'nullable|string',
        ]);

        // Membuat data cabang baru
        Cabang::create([
            'id_cabang' => $request->id_cabang,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'telepon' => $request->telepon,
            'pic' => $request->pic,
            'aktif' => true,
        ]);

        return response()->json(['success' => 'Cabang berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        try {
            // Log data yang diterima
            Log::info('Data received for update:', $request->all());

            // Validasi data
            $validatedData = $request->validate([
                'namacabangupdate' => 'required|string|max:255',
                'alamatcabangupdate' => 'required|string',
                'kotaCabangupdate' => 'required|string|max:255',
                'provinsiCabangupdate' => 'required|string|max:255',
                'teleponcabangupdate' => 'nullable|string|max:255',
                'piccabangupdate' => 'nullable|string|max:255',
            ]);

            // Cari cabang berdasarkan ID
            $cabang = Cabang::findOrFail($id);

            // Update data cabang
            $cabang->update([
                'nama' => $validatedData['namacabangupdate'],
                'alamat' => $validatedData['alamatcabangupdate'],
                'kota' => $validatedData['kotaCabangupdate'],
                'provinsi' => $validatedData['provinsiCabangupdate'],
                'telepon' => $validatedData['teleponcabangupdate'],
                'pic' => $validatedData['piccabangupdate'],
            ]);

            // Berikan respons berhasil
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Tangani error lain
            Log::error('Error updating cabang:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Mencari cabang berdasarkan ID
        $cabang = Cabang::findOrFail($id);

        // Menghapus cabang
        $cabang->delete();

        // Mengalihkan ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('admin.cabang-index')->with('success', 'Cabang berhasil dihapus.');
    }

    public function lastCabangId()
    {
        $lastCabang = Cabang::orderBy('id_cabang', 'desc')->first();
        $lastId = $lastCabang ? $lastCabang->id_cabang : null;

        return response()->json(['lastId' => $lastId]);
    }
}
