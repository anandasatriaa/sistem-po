<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Purchase Order - PT. Mega Auto Prima</title>
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
            padding: 2px;
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
            <td style="text-align: left; width: 33%;"></td>
            <td style="text-align: center; width: 33%;">
                <div class="po-title">LAPORAN PO DETAIL</div>
            </td>
            <td style="text-align: right; width: 33%;">
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

    <table class="details-table" style="margin-top: 20px;">
        <thead style="font-size: 12px">
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>No PO</th>
                <th>Cabang</th>
                <th>Category</th>
                <th>Barang</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody style="font-size: 10px">
            @foreach ($laporan as $key => $item)
                <tr>
                    <td style="width: 4%">{{ $key + 1 }}</td>
                    <td style="width: 10%">{{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}</td>
                    <td>{{ $item->no_po }}</td>
                    <td style="width: 12%">{{ $item->cabang_name }}</td>
                    <td>{{ $item->category_name }}</td>
                    <td style="width: 20%">{{ $item->barang }}</td>
                    <td style="width: 7%; text-align: center;">{{ $item->qty }} {{ $item->unit }}</td>
                    <td style="text-align: right">Rp. {{ number_format($item->unit_price, 0, ',', '.') }},-</td>
                    <td style="text-align: right">Rp. {{ number_format($item->amount_price, 0, ',', '.') }},-</td>
                </tr>
            @endforeach

            @php
                $totalAmount = $laporan->sum('amount_price');
            @endphp
            <tr style="font-weight: bold;">
                <td colspan="8" style="text-align: left">Total:</td>
                <td style="text-align: right">Rp. {{ number_format($totalAmount, 0, ',', '.') }},-</td>
            </tr>
        </tbody>
    </table>

    <div style="border: 1px solid #000; padding: 2px; margin-top: 10px; font-size: 12px;">
        <strong>Terbilang:</strong>
        {{ strtoupper($grandtotalWords) }} RUPIAH
    </div>
</body>

</html>
