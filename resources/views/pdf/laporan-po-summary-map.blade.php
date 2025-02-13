<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Purchase Order - PT. Mega Auto Prima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0px;
        }

        table {
            width: 100%;
            table-layout: fixed;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
        }

        .po-title {
            font-size: 20px;
            font-weight: bold;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .details-table th {
            background-color: #ddd;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
            page-break-inside: avoid;
            font-size: 12px;
        }

        .ttd-table td {
            border: 1px solid #000;
            padding: 2px;
        }
    </style>
</head>

<body>
    <table style="margin-bottom: 10px">
        <tr>
            <td style="text-align: left; width: 30%;"></td>
            <td style="text-align: center; width: 39%;">
                <div class="po-title">LAPORAN PO SUMMARY</div>
            </td>
            <td style="text-align: right; width: 30%;">
                <img src="{{ asset('assets/images/logo-map-rm.png') }}" alt="Company Logo" style="height: 60px;">
            </td>
        </tr>
    </table>

    <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Cabang: </strong></td>
            <td>{{ $cabangText }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Category: </strong></td>
            <td>{{ $categoryText }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Periode: </strong></td>
            <td>{{ $dateText }}</td>
        </tr>
    </table>

    <table class="details-table" cellpadding="10" cellspacing="0"
        style="width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 20px;">
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 10%">Bulan</th>
                @foreach ($cabangList as $cabang)
                    <th>{{ $cabang }}</th>
                @endforeach
                <th style="width: 15%">Periode</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                // Inisialisasi array untuk menyimpan total per cabang
                $totalPerCabang = array_fill_keys($cabangList->toArray(), 0);
                $grandTotal = 0;

                // Ambil tanggal awal dan akhir dari filter atau data
                $filterStartDate = request()->filled('date') ? explode(' to ', request('date'))[0] : null;
                $filterEndDate = request()->filled('date')
                    ? explode(' to ', request('date'))[1] ?? $filterStartDate
                    : null;
                $periodeFormatted = $filterStartDate
                    ? date('d M, Y', strtotime($filterStartDate)) . ' s/d ' . date('d M, Y', strtotime($filterEndDate))
                    : ($summary->isNotEmpty()
                        ? date('d M, Y', strtotime($summary->min('start_date'))) .
                            ' s/d ' .
                            date('d M, Y', strtotime($summary->max('end_date')))
                        : '');
            @endphp

            @foreach ($summary->groupBy('periode') as $periode => $dataBulan)
                @foreach ($dataBulan->groupBy('bulan') as $bulan => $dataCabang)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $bulan }}</td>
                        @foreach ($cabangList as $cabang)
                            @php
                                $cabangData = $dataCabang->where('cabang_name', $cabang)->first();
                                $amount = $cabangData ? $cabangData->total_amount : 0;
                                // Akumulasi total per cabang
                                $totalPerCabang[$cabang] += $amount;
                                $grandTotal += $amount;
                            @endphp
                            <td style="text-align: right">
                                Rp. {{ $cabangData ? number_format($amount, 0, ',', '.') : '-' }},-
                            </td>
                        @endforeach
                        <td>{{ $periodeFormatted }}</td>
                    </tr>
                @endforeach
            @endforeach

            <!-- Baris Total Akhir -->
            <tr>
                <td colspan="2"><strong>Total:</strong></td>
                @foreach ($cabangList as $cabang)
                    <td style="text-align: right">
                        <strong>
                            Rp. {{ number_format($totalPerCabang[$cabang], 0, ',', '.') }},-
                        </strong>
                    </td>
                @endforeach
                <td></td>
            </tr>

            <!-- Baris Grand Total (opsional) -->
            <tr>
                <td colspan="{{ 2 + count($cabangList) }}"><strong>Total Semua:</strong></td>
                <td style="text-align: right">
                    <strong>
                        Rp. {{ number_format($grandTotal, 0, ',', '.') }},-
                    </strong>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="border: 1px solid #000; padding: 2px; margin-top: 10px; font-size: 12px;">
        <strong>Terbilang:</strong>
        {{ strtoupper($grandtotalWords) }} RUPIAH
    </div>
</body>


</html>
