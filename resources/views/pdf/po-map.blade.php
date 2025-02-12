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
                    style="border: 2px solid #000; padding: 5px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);">MEGA AUTO PRIMA</div>
            </td>
        </tr>
    </table>

    <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>To: </strong></td>
            <td colspan="3" style="width: 40%;">{{ $formData['supplier'] }}</td>
            <td style="width: 10%;"><strong>From: </strong></td>
            <td style="width: 40%;">Milenia Mega Mandiri</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>Address: </strong></td>
            <td colspan="3" style="width: 40%;">{{ $formData['address'] }}</td>
            <td style="width: 10%;"><strong>Address: </strong></td>
            <td style="width: 40%;">{{ $formData['cabang_alamat'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>Phone: </strong></td>
            <td style="width: 17%;">{{ $formData['phone'] }}</td>
            <td style="width: 10%; text-align: right;"><strong>Fax: </strong></td>
            <td style="width: 15%;">{{ $formData['fax'] }}</td>
            <td style="width: 10%;"><strong>Phone: </strong></td>
            <td style="width: 35%;">{{ $formData['cabang_telepon'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>UP: </strong></td>
            <td colspan="3" style="width: 40%;">{{ $formData['up'] }}</td>
            <td style="width: 10%;"><strong>NO: </strong></td>
            <td style="width: 40%;">{{ $formData['no_po'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>Cabang: </strong></td>
            <td colspan="3" style="width: 40%;">{{ $formData['cabang'] }}</td>
            <td style="width: 10%;"><strong>Kategori: </strong></td>
            <td style="width: 40%;">{{ $formData['category'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%;"><strong>Date: </strong></td>
            <td colspan="3" style="width: 40%;">{{ \Carbon\Carbon::parse($formData['date'])->format('F d, Y') }}
            </td>
            <td style="width: 10%;"><strong>Page: </strong></td>
            <td style="width: 40%;">1</td>
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
            @foreach ($barang as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['barang'] }}</td>
                    <td>{{ $item['qty'] }} {{ $item['unit'] }}</td>
                    <td style="text-align: right;">{{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="4">Sub Total</td>
                <td style="text-align: right">{{ number_format($formData['subtotal'], 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Pajak ({{ $formData['tax'] }}%)</td>
                <td style="text-align: right">+
                    {{ number_format($formData['subtotal'] * ($formData['tax'] / 100), 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Diskon</td>
                <td style="text-align: right">- {{ number_format($formData['discount'], 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="4">Total</td>
                <td style="text-align: right">{{ number_format($formData['grandtotal'], 0, ',', '.') }}</td>
                <td style="border: none;"></td>
            </tr>
        </tbody>
    </table>

    <div style="border: 1px solid #000; padding: 10px; margin: 10px 0;">
        <strong>Terbilang:</strong><br>
        {{ strtoupper($formData['grandtotal_words']) }} RUPIAH
    </div>

    <div style="margin-bottom: 10px;">
        <strong>Tanggal Pengiriman: {{ \Carbon\Carbon::parse($formData['estimate_date'])->format('F d, Y') }}</strong>
    </div>

    <div>
        <u><strong>REMARKS:</strong> {{ $formData['remarks'] }}</u>
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
                
            </td>
            <td style="border-bottom: none; height: 50px;">
                @if (!empty($signature))
                    <img src="{{ $signature }}" width="150">
                @else
                    <span>Tidak ada tanda tangan</span>
                @endif
            </td>
            <td style="border-bottom: none; height: 50px;">
                
            </td>
        </tr>
        <tr style="text-align: center;">
            <td style="border-top: none;">
                
            </td>
            <td style="border-top: none;">
                {{ strtoupper($formData['nama_pembuat']) }}
            </td>
            <td style="border-top: none;">
                
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
