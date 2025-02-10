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
                <div class="po-title">PURCHASE ORDER</div>
            </td>
            <td style="text-align: right; width: 22%;">
                <div class="company-name"
                    style="border: 2px solid #000; padding: 5px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);">
                    MEGA AUTO PRIMA
                </div>
            </td>
        </tr>
    </table>

    <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>To: </strong></td>
            <td colspan="3">{{ $purchaseOrder->supplier }}</td>
            <td style="width: 11%;"><strong>From: </strong></td>
            <td>Mega Auto Prima</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Address: </strong></td>
            <td colspan="3">{{ $purchaseOrder->address }}</td>
            <td style="width: 11%;"><strong>Date: </strong></td>
            <td>{{ \Carbon\Carbon::parse($purchaseOrder->date)->format('F d, Y') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Phone: </strong></td>
            <td>{{ $purchaseOrder->phone }}</td>
            <td style="width: 11%; text-align: right;"><strong>Fax: </strong></td>
            <td>{{ $purchaseOrder->fax }}</td>
            <td style="width: 11%;"><strong>Page: </strong></td>
            <td>1</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>UP: </strong></td>
            <td colspan="3">{{ $purchaseOrder->up }}</td>
            <td style="width: 11%;"><strong>NO: </strong></td>
            <td>{{ $purchaseOrder->no_po }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 11%;"><strong>Cabang: </strong></td>
            <td colspan="3">{{ $purchaseOrder->cabang }}</td>
            <td style="width: 11%;"><strong>Category: </strong></td>
            <td>{{ $category }}</td>
        </tr>
    </table>

    <p style="margin-top: 15px; margin-bottom: 15px;">Bersama dengan surat ini kami memesan :</p>

    <table class="details-table">
        <thead style="font-size: 14px;">
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 30%; text-align: center;">Barang</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: center;">Harga</th>
                <th style="width: 15%; text-align: center;">Amount</th>
                <th style="width: 25%; text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody style="font-size: 14px;">
            @foreach ($purchaseOrder->barang as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->barang }}</td>
                    <td>{{ $item->qty }} {{ $item->unit }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($item->amount_price, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="4">Sub Total</td>
                <td style="text-align: right">{{ number_format($purchaseOrder->sub_total, 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Pajak
                    ({{ number_format(($purchaseOrder->pajak / $purchaseOrder->sub_total) * 100) }}%)</td>
                <td style="text-align: right">+ {{ number_format($purchaseOrder->pajak, 0, ',', '.') }}
                </td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Diskon</td>
                <td style="text-align: right">- {{ number_format($purchaseOrder->discount, 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Total</td>
                <td style="text-align: right">{{ number_format($purchaseOrder->total, 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
        </tbody>
    </table>

    <div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
        <strong>Terbilang:</strong><br>
        {{ strtoupper($grandtotalWords) }} RUPIAH
    </div>

    <div style="margin-bottom: 10px;">
        <strong>Tanggal Pengiriman:
            {{ \Carbon\Carbon::parse($purchaseOrder->estimate_date)->format('F d, Y') }}</strong>
    </div>

    <div>
        <u><strong>REMARKS:</strong> {{ $purchaseOrder->remarks }}</u>
    </div>

    <div>
        <p>Demikianlah pesanan dari kami, kiranya dapat diproses dengan segera.<br>
            Atas bantuan dan kerjasamanya, kami ucapkan terima kasih.</p>
    </div>

    <table style="margin-top: 10px;" class="ttd-table">
        <tr style="text-align: center;">
            <td>
                Mengetahui,
            </td>
            <td>
                Yang Membuat,
            </td>
            <td>
                Disetujui,
            </td>
        </tr>
        <tr style="text-align: center;">
            <td style="border-bottom: none; height: 50px;">
                @if ($purchaseOrder->ttd_1)
                    @if ($purchaseOrder->ttd_1 === 'REJECTED')
                        <div
                            style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                            REJECTED
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $purchaseOrder->ttd_1) }}" alt="Signature-GA" width="150">
                    @endif
                @endif
            </td>
            <td style="border-bottom: none; height: 50px;">
                @if ($purchaseOrder->ttd_2)
                    @if ($purchaseOrder->ttd_2 === 'REJECTED')
                        <div
                            style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                            REJECTED
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $purchaseOrder->ttd_2) }}" alt="Signature-ADMIN"
                            width="150">
                    @endif
                @endif
            </td>
            <td style="border-bottom: none; height: 50px;">
                @if ($purchaseOrder->ttd_3)
                    @if ($purchaseOrder->ttd_3 === 'REJECTED')
                        <div
                            style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                            REJECTED
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $purchaseOrder->ttd_3) }}" alt="Signature-DIRECTOR"
                            width="150">
                    @endif
                @endif
            </td>
        </tr>
        <tr style="text-align: center;">
            <td style="border-top: none;">
                {{ $purchaseOrder->nama_1 }}
            </td>
            <td style="border-top: none;">
                {{ strtoupper($purchaseOrder->nama_2) }}
            </td>
            <td style="border-top: none;">
                {{ $purchaseOrder->nama_3 }}
            </td>
        </tr>
        <tr style="text-align: center;">
            <td>
                <strong>GA Dept</strong>
            </td>
            <td>
                <strong>PO Lokal</strong>
            </td>
            <td>
                <strong>Senior Adm Officer</strong>
            </td>
        </tr>
    </table>
</body>

</html>
