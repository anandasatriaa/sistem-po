<?php

namespace App\Http\Controllers\Admin\PO;

use App\Http\Controllers\Controller;
use App\Models\Cabang\Cabang;
use App\Models\Category\Category;
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

class LaporanPOController extends Controller
{
    public function index(Request $request)
    {
        $cabangs = Cabang::all();
        $categories = Category::all();

        $detail = $this->getDetail($request)->sortBy('no_po');
        $summary = $this->getSummary($request);
        $cabangList = DB::table('cabang')->pluck('nama');

        // Jika request AJAX untuk tabel
        if ($request->ajax()) {
            return response()->json([
                'table_detail' => view('admin.po-milenia.partials.table-detail', compact('detail'))->render()
            ]);
        }

        return view('admin.po-milenia.laporan', compact('detail', 'summary', 'cabangList', 'cabangs', 'categories'));
    }

    // **Method untuk mengambil data laporan detail**
    private function getDetail($request)
    {
        return DB::table('purchase_order_milenia')
        ->join('cabang', 'purchase_order_milenia.cabang_id', '=', 'cabang.id_cabang')
        ->join('purchase_order_barang_milenia', 'purchase_order_milenia.id', '=', 'purchase_order_barang_milenia.purchase_order_id')
        ->join('category', 'purchase_order_barang_milenia.category_id', '=', 'category.id')
        ->when($request->filled('cabang'), function ($query) use ($request) {
            if (!in_array('all', $request->cabang)) {
                $query->whereIn('cabang.id_cabang', $request->cabang);
            }
        })
            ->when($request->filled('category'), function ($query) use ($request) {
                if (!in_array('all', $request->category)) {
                    $query->whereIn('category.id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = date('Y-m-d', strtotime($dates[0]));
                $endDate = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('purchase_order_milenia.date', [$startDate, $endDate]);
            })
            ->select(
                'purchase_order_milenia.*',
                'cabang.nama as cabang_name',
                'category.nama as category_name',
                'purchase_order_barang_milenia.barang',
                'purchase_order_barang_milenia.qty',
                'purchase_order_barang_milenia.unit',
                'purchase_order_barang_milenia.unit_price',
                'purchase_order_barang_milenia.amount_price'
            )
            ->get();
    }

    public function exportPDFDetail(Request $request)
    {
        // Query utama untuk data laporan
        $laporan = DB::table('purchase_order_milenia')
        ->join('cabang', 'purchase_order_milenia.cabang_id', '=', 'cabang.id_cabang')
        ->join('purchase_order_barang_milenia', 'purchase_order_milenia.id', '=', 'purchase_order_barang_milenia.purchase_order_id')
        ->join('category', 'purchase_order_barang_milenia.category_id', '=', 'category.id')
        ->when($request->filled('cabang'), function ($query) use ($request) {
            if (!in_array('all', $request->cabang)) {
                $query->whereIn('cabang.id_cabang', $request->cabang);
            }
        })
            ->when($request->filled('category'), function ($query) use ($request) {
                if (!in_array('all', $request->category)) {
                    $query->whereIn('category.id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = date('Y-m-d', strtotime($dates[0]));
                $endDate   = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('purchase_order_milenia.date', [$startDate, $endDate]);
            })
            ->select(
                'purchase_order_milenia.*',
                'cabang.nama as cabang_name',
                'category.nama as category_name',
                'purchase_order_barang_milenia.barang',
                'purchase_order_barang_milenia.qty',
                'purchase_order_barang_milenia.unit',
                'purchase_order_barang_milenia.unit_price',
                'purchase_order_barang_milenia.amount_price'
            )
            ->orderBy('purchase_order_milenia.no_po', 'asc')
            ->get();

        // Proses filter untuk ditampilkan di PDF

        // Hitung total amount dari seluruh laporan
        $totalAmount = $laporan->sum('amount_price');

        // Konversi totalAmount ke dalam bentuk terbilang
        $grandtotalWords = $this->terbilang($totalAmount);

        // Filter Cabang
        if ($request->filled('cabang') && !in_array('all', $request->cabang)) {
            $cabangNames = DB::table('cabang')
            ->whereIn('id_cabang', $request->cabang)
                ->pluck('nama')
                ->toArray();
            $cabangText = implode(', ', $cabangNames);
        } else {
            $cabangText = 'All';
        }

        // Filter Category
        if ($request->filled('category') && !in_array('all', $request->category)) {
            $categoryNames = DB::table('category')
            ->whereIn('id', $request->category)
                ->pluck('nama')
                ->toArray();
            $categoryText = implode(', ', $categoryNames);
        } else {
            $categoryText = 'All';
        }

        // Filter Periode
        $dateText = $request->filled('date') ? $request->date : 'Semua Periode';

        // Kirim data laporan dan filter ke view PDF
        $pdf = PDF::loadView('pdf.laporan-po-detail-milenia', compact('laporan', 'cabangText', 'categoryText','dateText', 'grandtotalWords'));
        return $pdf->download('laporan_po_detail.pdf');
    }

    // **Method untuk mengambil data summary**
    private function getSummary($request)
    {
        // Subquery untuk mengambil data purchase_order_milenia.
        // Karena tiap purchase order sudah memiliki total, kita tidak perlu menjumlahkannya ulang.
        $subQuery = DB::table('purchase_order_milenia')
        ->select('id', 'cabang_id', 'date', 'total as total_amount');

        // Query utama dengan join subquery tersebut ke tabel purchase_order_barang_milenia
        $filteredQuery = DB::table('purchase_order_barang_milenia')
        ->joinSub($subQuery, 'po_milenia', function ($join) {
            $join->on('purchase_order_barang_milenia.purchase_order_id', '=', 'po_milenia.id');
        })
            ->join('cabang', 'po_milenia.cabang_id', '=', 'cabang.id_cabang')
            ->when($request->filled('cabang'), function ($query) use ($request) {
                if (!in_array('all', $request->cabang)) {
                    $query->whereIn('cabang.id_cabang', $request->cabang);
                }
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                // Hanya filter berdasarkan category jika bukan "all"
                if (!in_array('all', $request->category)) {
                    $query->whereIn('purchase_order_barang_milenia.category_id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = isset($dates[0]) ? date('Y-m-d', strtotime($dates[0])) : null;
                $endDate   = isset($dates[1]) ? date('Y-m-d', strtotime($dates[1])) : $startDate;

                if ($startDate && $endDate) {
                    $query->whereBetween('po_milenia.date', [$startDate, $endDate]);
                }
            });

        // Ambil rentang periode (start_date dan end_date) dari hasil query yang sudah difilter
        $periodeRange = clone $filteredQuery;
        $periodeRange = $periodeRange->select(
            DB::raw('MIN(po_milenia.date) as start_date'),
            DB::raw('MAX(po_milenia.date) as end_date')
        )->first();

        /**
         * Logika grouping:
         * - Jika tidak ada filter category (load pertama kali), atau
         * - jika filter category mengandung 'all', atau
         * - jika jumlah category yang dipilih > 1
         * maka grouping hanya berdasarkan cabang & periode.
         * 
         * Jika hanya 1 category yang dipilih, grouping berdasarkan cabang & category.
         */
        $groupByCategory = false;
        if ($request->filled('category')) {
            if (in_array('all', $request->category) || count($request->category) > 1) {
                $groupByCategory = false;
            } else {
                $groupByCategory = true;
            }
        } else {
            // Jika filter category tidak ada, kita anggap sebagai all.
            $groupByCategory = false;
        }

        if (!$groupByCategory) {
            // Grouping tanpa category (semua category dijumlahkan)
            $summary = $filteredQuery
                ->select(
                    DB::raw('MONTHNAME(po_milenia.date) as bulan'),
                    'cabang.nama as cabang_name',
                    DB::raw('SUM(DISTINCT po_milenia.total_amount) as total_amount'),
                    DB::raw('DATE_FORMAT(po_milenia.date, "%Y-%m") as periode')
                )
                ->groupBy('bulan', 'periode', 'cabang.id_cabang', 'cabang.nama')
                ->orderBy('periode', 'asc')
                ->get();
        } else {
            // Grouping berdasarkan category (hanya 1 category yang dipilih)
            $summary = $filteredQuery
                ->select(
                    DB::raw('MONTHNAME(po_milenia.date) as bulan'),
                    'cabang.nama as cabang_name',
                    'purchase_order_barang_milenia.category_id',
                    DB::raw('SUM(DISTINCT po_milenia.total_amount) as total_amount'),
                    DB::raw('DATE_FORMAT(po_milenia.date, "%Y-%m") as periode')
                )
                ->groupBy('bulan', 'periode', 'cabang.id_cabang', 'cabang.nama', 'purchase_order_barang_milenia.category_id')
                ->orderBy('periode', 'asc')
                ->get();
        }

        // Menambahkan start_date dan end_date ke setiap item summary
        foreach ($summary as $item) {
            $item->start_date = $periodeRange->start_date;
            $item->end_date   = $periodeRange->end_date;
        }

        return $summary;
    }

    public function getFilteredSummary(Request $request)
    {
        $summary = $this->getSummary($request);
        $cabangList = DB::table('cabang')->pluck('nama'); // Tambahkan ini

        if ($request->ajax()) {
            return response()->json([
                'table_summary' => view('admin.po-milenia.partials.table-summary', compact('summary', 'cabangList'))->render()
            ]);
        }

        return view('admin.po-milenia.partials.table-summary', compact('summary', 'cabangList'))->render();
    }

    public function exportPDFSummary(Request $request)
    {
        // Ambil data summary berdasarkan filter menggunakan fungsi yang sudah ada
        $summary = $this->getSummary($request);

        // Ambil daftar cabang (nama) untuk keperluan tabel di PDF
        $cabangList = DB::table('cabang')->pluck('nama');

        // Menentukan teks filter untuk ditampilkan di PDF
        // Filter Cabang
        $cabangText = 'All';
        if ($request->filled('cabang') && !in_array('all', $request->cabang)) {
            $cabangText = DB::table('cabang')
            ->whereIn('id_cabang', $request->cabang)
                ->pluck('nama')
                ->implode(', ');
        }

        // Filter Category
        $categoryText = 'All';
        if ($request->filled('category') && !in_array('all', $request->category)) {
            $categoryText = DB::table('category')
            ->whereIn('id', $request->category)
                ->pluck('nama')
                ->implode(', ');
        }

        // Filter Date
        $dateText = $request->date ? $request->date : 'All';

        // Hitung grand total dari summary (sesuaikan perhitungan jika diperlukan)
        $grandTotal = $summary->sum('total_amount');

        // Konversi grand total ke kata-kata (contoh sederhana; Anda bisa menggantinya dengan helper yang lebih canggih)
        $grandtotalWords = $this->terbilang($grandTotal);

        // Menggunakan DomPDF untuk generate PDF
        $pdf = PDF::loadView('pdf.laporan-po-summary-milenia', compact(
            'summary',
            'cabangList',
            'cabangText',
            'categoryText',
            'dateText',
            'grandtotalWords'
        ))->setPaper('a4');

        return $pdf->download('laporan_po_summary.pdf');
    }

    private function terbilang($angka)
    {
        $formatter = new NumberFormatter('id', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($angka));
    }

    public function indexMAP(Request $request)
    {
        $cabangs = Cabang::all();
        $categories = Category::all();

        $detail = $this->getDetailMAP($request)->sortBy('no_po');
        $summary = $this->getSummaryMAP($request);
        $cabangList = DB::table('cabang')->pluck('nama');

        // Jika request AJAX untuk tabel
        if ($request->ajax()) {
            return response()->json([
                'table_detail' => view('admin.po-map.partials.table-detail', compact('detail'))->render()
            ]);
        }

        return view('admin.po-map.laporan', compact('detail', 'summary', 'cabangList', 'cabangs', 'categories'));
    }

    // **Method untuk mengambil data laporan detail**
    private function getDetailMAP($request)
    {
        return DB::table('purchase_order_map')
        ->join('cabang', 'purchase_order_map.cabang_id', '=', 'cabang.id_cabang')
        ->join('purchase_order_barang_map', 'purchase_order_map.id', '=', 'purchase_order_barang_map.purchase_order_id')
        ->join('category', 'purchase_order_barang_map.category_id', '=', 'category.id')
        ->when($request->filled('cabang'), function ($query) use ($request) {
            if (!in_array('all', $request->cabang)) {
                $query->whereIn('cabang.id_cabang', $request->cabang);
            }
        })
            ->when($request->filled('category'), function ($query) use ($request) {
                if (!in_array('all', $request->category)) {
                    $query->whereIn('category.id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = date('Y-m-d', strtotime($dates[0]));
                $endDate = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('purchase_order_map.date', [$startDate, $endDate]);
            })
            ->select(
                'purchase_order_map.*',
                'cabang.nama as cabang_name',
                'category.nama as category_name',
                'purchase_order_barang_map.barang',
                'purchase_order_barang_map.qty',
                'purchase_order_barang_map.unit',
                'purchase_order_barang_map.unit_price',
                'purchase_order_barang_map.amount_price'
            )
            ->get();
    }

    public function exportPDFDetailMAP(Request $request)
    {
        // Query utama untuk data laporan
        $laporan = DB::table('purchase_order_map')
        ->join('cabang', 'purchase_order_map.cabang_id', '=', 'cabang.id_cabang')
        ->join('purchase_order_barang_map', 'purchase_order_map.id', '=', 'purchase_order_barang_map.purchase_order_id')
        ->join('category', 'purchase_order_barang_map.category_id', '=', 'category.id')
        ->when($request->filled('cabang'), function ($query) use ($request) {
            if (!in_array('all', $request->cabang)) {
                $query->whereIn('cabang.id_cabang', $request->cabang);
            }
        })
            ->when($request->filled('category'), function ($query) use ($request) {
                if (!in_array('all', $request->category)) {
                    $query->whereIn('category.id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = date('Y-m-d', strtotime($dates[0]));
                $endDate   = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('purchase_order_map.date', [$startDate, $endDate]);
            })
            ->select(
                'purchase_order_map.*',
                'cabang.nama as cabang_name',
                'category.nama as category_name',
                'purchase_order_barang_map.barang',
                'purchase_order_barang_map.qty',
                'purchase_order_barang_map.unit',
                'purchase_order_barang_map.unit_price',
                'purchase_order_barang_map.amount_price'
            )
            ->orderBy('purchase_order_map.no_po', 'asc')
            ->get();

        // Proses filter untuk ditampilkan di PDF

        // Hitung total amount dari seluruh laporan
        $totalAmount = $laporan->sum('amount_price');

        // Konversi totalAmount ke dalam bentuk terbilang
        $grandtotalWords = $this->terbilang($totalAmount);

        // Filter Cabang
        if ($request->filled('cabang') && !in_array('all', $request->cabang)) {
            $cabangNames = DB::table('cabang')
            ->whereIn('id_cabang', $request->cabang)
                ->pluck('nama')
                ->toArray();
            $cabangText = implode(', ', $cabangNames);
        } else {
            $cabangText = 'All';
        }

        // Filter Category
        if ($request->filled('category') && !in_array('all', $request->category)) {
            $categoryNames = DB::table('category')
            ->whereIn('id', $request->category)
                ->pluck('nama')
                ->toArray();
            $categoryText = implode(', ', $categoryNames);
        } else {
            $categoryText = 'All';
        }

        // Filter Periode
        $dateText = $request->filled('date') ? $request->date : 'Semua Periode';

        // Kirim data laporan dan filter ke view PDF
        $pdf = PDF::loadView('pdf.laporan-po-detail-map', compact('laporan', 'cabangText', 'categoryText', 'dateText', 'grandtotalWords'));
        return $pdf->download('laporan_po_detail.pdf');
    }

    // **Method untuk mengambil data summary**
    private function getSummaryMAP($request)
    {
        // Subquery untuk mengambil data purchase_order_map.
        // Karena tiap purchase order sudah memiliki total, kita tidak perlu menjumlahkannya ulang.
        $subQuery = DB::table('purchase_order_map')
        ->select('id', 'cabang_id', 'date', 'total as total_amount');

        // Query utama dengan join subquery tersebut ke tabel purchase_order_barang_map
        $filteredQuery = DB::table('purchase_order_barang_map')
        ->joinSub($subQuery, 'po_map', function ($join) {
            $join->on('purchase_order_barang_map.purchase_order_id', '=', 'po_map.id');
        })
            ->join('cabang', 'po_map.cabang_id', '=', 'cabang.id_cabang')
            ->when($request->filled('cabang'), function ($query) use ($request) {
                if (!in_array('all', $request->cabang)) {
                    $query->whereIn('cabang.id_cabang', $request->cabang);
                }
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                // Hanya filter berdasarkan category jika bukan "all"
                if (!in_array('all', $request->category)) {
                    $query->whereIn('purchase_order_barang_map.category_id', $request->category);
                }
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $dates = explode(' to ', $request->date);
                $startDate = isset($dates[0]) ? date('Y-m-d', strtotime($dates[0])) : null;
                $endDate   = isset($dates[1]) ? date('Y-m-d', strtotime($dates[1])) : $startDate;

                if ($startDate && $endDate) {
                    $query->whereBetween('po_map.date', [$startDate, $endDate]);
                }
            });

        // Ambil rentang periode (start_date dan end_date) dari hasil query yang sudah difilter
        $periodeRange = clone $filteredQuery;
        $periodeRange = $periodeRange->select(
            DB::raw('MIN(po_map.date) as start_date'),
            DB::raw('MAX(po_map.date) as end_date')
        )->first();

        /**
         * Logika grouping:
         * - Jika tidak ada filter category (load pertama kali), atau
         * - jika filter category mengandung 'all', atau
         * - jika jumlah category yang dipilih > 1
         * maka grouping hanya berdasarkan cabang & periode.
         * 
         * Jika hanya 1 category yang dipilih, grouping berdasarkan cabang & category.
         */
        $groupByCategory = false;
        if ($request->filled('category')) {
            if (in_array('all', $request->category) || count($request->category) > 1) {
                $groupByCategory = false;
            } else {
                $groupByCategory = true;
            }
        } else {
            // Jika filter category tidak ada, kita anggap sebagai all.
            $groupByCategory = false;
        }

        if (!$groupByCategory) {
            // Grouping tanpa category (semua category dijumlahkan)
            $summary = $filteredQuery
                ->select(
                    DB::raw('MONTHNAME(po_map.date) as bulan'),
                    'cabang.nama as cabang_name',
                    DB::raw('SUM(DISTINCT po_map.total_amount) as total_amount'),
                    DB::raw('DATE_FORMAT(po_map.date, "%Y-%m") as periode')
                )
                ->groupBy('bulan', 'periode', 'cabang.id_cabang', 'cabang.nama')
                ->orderBy('periode', 'asc')
                ->get();
        } else {
            // Grouping berdasarkan category (hanya 1 category yang dipilih)
            $summary = $filteredQuery
                ->select(
                    DB::raw('MONTHNAME(po_map.date) as bulan'),
                    'cabang.nama as cabang_name',
                    'purchase_order_barang_map.category_id',
                    DB::raw('SUM(DISTINCT po_map.total_amount) as total_amount'),
                    DB::raw('DATE_FORMAT(po_map.date, "%Y-%m") as periode')
                )
                ->groupBy('bulan', 'periode', 'cabang.id_cabang', 'cabang.nama', 'purchase_order_barang_map.category_id')
                ->orderBy('periode', 'asc')
                ->get();
        }

        // Menambahkan start_date dan end_date ke setiap item summary
        foreach ($summary as $item) {
            $item->start_date = $periodeRange->start_date;
            $item->end_date   = $periodeRange->end_date;
        }

        return $summary;
    }

    public function getFilteredSummaryMAP(Request $request)
    {
        $summary = $this->getSummaryMAP($request);
        $cabangList = DB::table('cabang')->pluck('nama'); // Tambahkan ini

        if ($request->ajax()) {
            return response()->json([
                'table_summary' => view('admin.po-map.partials.table-summary', compact('summary', 'cabangList'))->render()
            ]);
        }

        return view('admin.po-map.partials.table-summary', compact('summary', 'cabangList'))->render();
    }

    public function exportPDFSummaryMAP(Request $request)
    {
        // Ambil data summary berdasarkan filter menggunakan fungsi yang sudah ada
        $summary = $this->getSummaryMAP($request);

        // Ambil daftar cabang (nama) untuk keperluan tabel di PDF
        $cabangList = DB::table('cabang')->pluck('nama');

        // Menentukan teks filter untuk ditampilkan di PDF
        // Filter Cabang
        $cabangText = 'All';
        if ($request->filled('cabang') && !in_array('all', $request->cabang)) {
            $cabangText = DB::table('cabang')
            ->whereIn('id_cabang', $request->cabang)
                ->pluck('nama')
                ->implode(', ');
        }

        // Filter Category
        $categoryText = 'All';
        if ($request->filled('category') && !in_array('all', $request->category)) {
            $categoryText = DB::table('category')
            ->whereIn('id', $request->category)
                ->pluck('nama')
                ->implode(', ');
        }

        // Filter Date
        $dateText = $request->date ? $request->date : 'All';

        // Hitung grand total dari summary (sesuaikan perhitungan jika diperlukan)
        $grandTotal = $summary->sum('total_amount');

        // Konversi grand total ke kata-kata (contoh sederhana; Anda bisa menggantinya dengan helper yang lebih canggih)
        $grandtotalWords = $this->terbilang($grandTotal);

        // Menggunakan DomPDF untuk generate PDF
        $pdf = PDF::loadView('pdf.laporan-po-summary-map', compact(
            'summary',
            'cabangList',
            'cabangText',
            'categoryText',
            'dateText',
            'grandtotalWords',
            'grandTotal'
        ))->setPaper('a4');

        return $pdf->download('laporan_po_summary.pdf');
    }
}
