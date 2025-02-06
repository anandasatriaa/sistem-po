<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahCabang = DB::table('cabang')->count();
        $jumlahSupplier = DB::table('supplier')->count();
        $jumlahCategory = DB::table('category')->count();
        $jumlahUnit = DB::table('unit')->count();
        $jumlahBarang = DB::table('barang')->count();

        $jumlahPRMilenia = DB::table('purchase_request')
        ->where('pt', 'PT. Milenia Mega Mandiri')
        ->count();
        $jumlahPRMap = DB::table('purchase_request')
        ->where('pt', 'PT. Mega Auto Prima')
        ->count();

        $jumlahPOMilenia = DB::table('purchase_order_milenia')->count();
        $jumlahPOMap = DB::table('purchase_order_map')->count();

        // Ambil data PR Milenia per bulan
        $prMilenia = DB::table('purchase_request')
        ->selectRaw('MONTH(date_request) as month, COUNT(*) as total')
        ->where('pt', 'PT. Milenia Mega Mandiri')
        ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Ambil data PR MAP per bulan
        $prMap = DB::table('purchase_request')
        ->selectRaw('MONTH(date_request) as month, COUNT(*) as total')
        ->where('pt', 'PT. Mega Auto Prima')
        ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Ambil data PO Milenia per bulan
        $poMilenia = DB::table('purchase_order_milenia')
        ->selectRaw('MONTH(date) as month, COUNT(*) as total')
        ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Ambil data PO MAP per bulan
        $poMap = DB::table('purchase_order_map')
        ->selectRaw('MONTH(date) as month, COUNT(*) as total')
        ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        // Pastikan semua bulan (1-12) memiliki data, jika tidak ada isi dengan 0
        $months = range(1, 12);
        $dataPRMilenia = array_map(fn($m) => $prMilenia[$m] ?? 0, $months);
        $dataPRMap = array_map(fn($m) => $prMap[$m] ?? 0, $months);
        $dataPOMilenia = array_map(fn($m) => $poMilenia[$m] ?? 0, $months);
        $dataPOMap = array_map(fn($m) => $poMap[$m] ?? 0, $months);

        return view('admin.dashboard.index', compact(
            'jumlahCabang',
            'jumlahSupplier',
            'jumlahCategory',
            'jumlahUnit',
            'jumlahBarang',
            'jumlahPRMilenia',
            'jumlahPOMilenia',
            'jumlahPRMap',
            'jumlahPOMap',
            'dataPRMilenia',
            'dataPRMap',
            'dataPOMilenia',
            'dataPOMap'
        ));
    }
}
