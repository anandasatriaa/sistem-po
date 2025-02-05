<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - PT. Mega Auto Prima</title>
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
            font-size: 26px;
            font-weight: bold;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .details-table th {
            background-color: #f5f5f5;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
            page-break-inside: avoid;
        }

        .ttd-table td {
            border: 1px solid #000;
            padding: 2px;
        }

        .total-section {
            margin-left: auto;
            width: 300px;
            padding: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <table style="margin-bottom: 20px">
        <tr>
            <td style="text-align: left; width: 20%;">
                <img src="{{ asset('assets/images/map-logo.png') }}" alt="Company Logo" style="height: 80px;">
            </td>
            <td style="text-align: center; width: 50%;">
                <div class="po-title">Laporan PO Detail</div>
            </td>
            <td style="text-align: right; width: 22%;">
                <div class="company-name"
                    style="border: 2px solid #000; padding: 5px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);">
                    MEGA AUTO PRIMA
                </div>
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
                    <td style="width: 10%">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                    <td>{{ $item->no_po }}</td>
                    <td style="width: 12%">{{ $item->cabang_name }}</td>
                    <td>{{ $item->category_name }}</td>
                    <td style="width: 20%">{{ $item->barang }}</td>
                    <td style="width: 7%">{{ $item->qty }} {{ $item->unit }}</td>
                    <td>Rp. {{ number_format($item->unit_price, 0, ',', '.') }},-</td>
                    <td>Rp. {{ number_format($item->amount_price, 0, ',', '.') }},-</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
