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
            padding: 1px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    {{-- HEADER & DETAIL PO (Halaman 1) --}}
    <table style="margin-bottom: 10px">
        <tr>
            <td style="text-align: left; width: 33%;"></td>
            <td style="text-align: center; width: 33%;">
                <div class="po-title">PURCHASE ORDER</div>
            </td>
            <td style="text-align: right; width: 33%;">
                <img src="{{ asset('assets/images/logo-map-rm.png') }}" alt="Company Logo" style="height: 60px;">
            </td>
        </tr>
    </table>

    <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>To: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['supplier'] }}</td>
            <td style="width: 10%; padding: 1;"><strong>From: </strong></td>
            <td style="width: 40%; padding: 1;">Mega Auto Prima</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['address'] }}</td>
            <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $formData['cabang_alamat'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
            <td style="width: 17%;">{{ $formData['phone'] }}</td>
            <td style="width: 10%; padding: 1; text-align: right;"><strong>Fax: </strong></td>
            <td style="width: 15%;">{{ $formData['fax'] }}</td>
            <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
            <td style="width: 35%;">{{ $formData['cabang_telepon'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>UP: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['up'] }}</td>
            <td style="width: 10%; padding: 1;"><strong>NO: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $formData['no_po'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Cabang: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['cabang'] }}</td>
            <td style="width: 10%; padding: 1;"><strong>Kategori: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $formData['category'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Date: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">
                {{ \Carbon\Carbon::parse($formData['date'])->format('F d, Y') }}
            </td>
            <td style="width: 10%; padding: 1;"><strong>Page: </strong></td>
            <td style="width: 40%; padding: 1;">1</td>
        </tr>
    </table>

    <p style="margin-top: 15px; margin-bottom: 10px; font-size: 12px;">
        Bersama dengan surat ini kami memesan :
    </p>

    @php
        $maxItemsOnFirstPage = 23;
        $totalItems = count($barang);
    @endphp

    @if ($totalItems <= $maxItemsOnFirstPage)
        {{-- Jika total barang kurang atau sama dengan 25, tampilkan semua di 1 halaman --}}
        <table class="details-table">
            <thead style="font-size: 12px;">
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 30%; text-align: center;">Barang</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: center;">Harga</th>
                    <th style="width: 15%; text-align: center;">Amount</th>
                    <th style="width: 25%; text-align: center;">Keterangan</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
                @foreach ($barang as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['barang'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                        <td style="text-align: right;">{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach
                {{-- Bagian Total --}}
                <tr style="font-weight: bold;">
                    <td colspan="4">Sub Total</td>
                    <td style="text-align: right">{{ number_format($formData['subtotal'], 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
                @if ($formData['tax'] > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Pajak ({{ $formData['tax'] }}%)</td>
                        <td style="text-align: right">
                            {{ number_format($formData['subtotal'] * ($formData['tax'] / 100), 0, ',', '.') }}
                        </td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                @if ($formData['discount'] > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Diskon</td>
                        <td style="text-align: right">
                            {{ number_format($formData['discount'], 0, ',', '.') }}
                        </td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                <tr style="font-weight: bold;">
                    <td colspan="4">Total</td>
                    <td style="text-align: right">{{ number_format($formData['grandtotal'], 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
            </tbody>
        </table>
    @else
        {{-- Halaman 1: Tampilkan 25 baris pertama --}}
        <table class="details-table">
            <thead style="font-size: 12px;">
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 30%; text-align: center;">Barang</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: center;">Harga</th>
                    <th style="width: 15%; text-align: center;">Amount</th>
                    <th style="width: 25%; text-align: center;">Keterangan</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
                @foreach (array_slice($barang, 0, $maxItemsOnFirstPage) as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['barang'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                        <td style="text-align: right;">{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Page Break --}}
        <div style="page-break-before: always;"></div>

        {{-- HEADER & DETAIL PO (Halaman 1) --}}
        <table style="margin-bottom: 10px">
            <tr>
                <td style="text-align: left; width: 33%;"></td>
                <td style="text-align: center; width: 33%;">
                    <div class="po-title">PURCHASE ORDER</div>
                </td>
                <td style="text-align: right; width: 33%;">
                    <img src="{{ asset('assets/images/logo-map-rm.png') }}" alt="Company Logo"
                        style="height: 60px;">
                </td>
            </tr>
        </table>

        <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>To: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['supplier'] }}</td>
                <td style="width: 10%; padding: 1;"><strong>From: </strong></td>
                <td style="width: 40%; padding: 1;">Mega Auto Prima</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['address'] }}</td>
                <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $formData['cabang_alamat'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
                <td style="width: 17%;">{{ $formData['phone'] }}</td>
                <td style="width: 10%; padding: 1; text-align: right;"><strong>Fax: </strong></td>
                <td style="width: 15%;">{{ $formData['fax'] }}</td>
                <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
                <td style="width: 35%;">{{ $formData['cabang_telepon'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>UP: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['up'] }}</td>
                <td style="width: 10%; padding: 1;"><strong>NO: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $formData['no_po'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Cabang: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $formData['cabang'] }}</td>
                <td style="width: 10%; padding: 1;"><strong>Kategori: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $formData['category'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Date: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">
                    {{ \Carbon\Carbon::parse($formData['date'])->format('F d, Y') }}
                </td>
                <td style="width: 10%; padding: 1;"><strong>Page: </strong></td>
                <td style="width: 40%; padding: 1;">1</td>
            </tr>
        </table>

        <p style="margin-top: 15px; margin-bottom: 10px; font-size: 12px;">
            Bersama dengan surat ini kami memesan :
        </p>

        {{-- Halaman 2: Tampilkan sisa barang beserta bagian total --}}
        <table class="details-table">
            <thead style="font-size: 12px;">
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 30%; text-align: center;">Barang</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: center;">Harga</th>
                    <th style="width: 15%; text-align: center;">Amount</th>
                    <th style="width: 25%; text-align: center;">Keterangan</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
                @foreach (array_slice($barang, $maxItemsOnFirstPage) as $index => $item)
                    <tr>
                        <td>{{ $maxItemsOnFirstPage + $index + 1 }}</td>
                        <td>{{ $item['barang'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                        <td style="text-align: right;">{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    </tr>
                @endforeach

                {{-- Bagian Total --}}
                <tr style="font-weight: bold;">
                    <td colspan="4">Sub Total</td>
                    <td style="text-align: right">{{ number_format($formData['subtotal'], 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
                @if ($formData['tax'] > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Pajak ({{ $formData['tax'] }}%)</td>
                        <td style="text-align: right">
                            {{ number_format($formData['subtotal'] * ($formData['tax'] / 100), 0, ',', '.') }}
                        </td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                @if ($formData['discount'] > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Diskon</td>
                        <td style="text-align: right">
                            {{ number_format($formData['discount'], 0, ',', '.') }}
                        </td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                <tr style="font-weight: bold;">
                    <td colspan="4">Total</td>
                    <td style="text-align: right">{{ number_format($formData['grandtotal'], 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Konten Lainnya --}}
    <div style="border: 1px solid #000; padding: 2px; margin: 2px 0; font-size: 12px;">
        <strong>Terbilang:</strong>
        {{ strtoupper($formData['grandtotal_words']) }} RUPIAH
    </div>

    <div style="margin-bottom: 10px; font-size: 12px;">
        <strong>Tanggal Pengiriman: {{ \Carbon\Carbon::parse($formData['estimate_date'])->format('F d, Y') }}</strong>
    </div>

    <div style="font-size: 12px;">
        <u><strong>REMARKS:</strong> {{ $formData['remarks'] }}</u>
    </div>

    <div style="font-size: 12px;">
        <p style="margin-top: 10px; margin-bottom: 10px;">
            Demikianlah pesanan dari kami, kiranya dapat diproses dengan segera.<br>
            Atas bantuan dan kerjasamanya, kami ucapkan terima kasih.
        </p>
    </div>

    <table class="ttd-table">
        <tr style="text-align: center;">
            <td>Mengetahui,</td>
            <td>Yang Membuat,</td>
            <td>Disetujui,</td>
        </tr>
        <tr style="text-align: center;">
            <td style="border-bottom: none; height: 30px;"></td>
            <td style="border-bottom: none; height: 30px;">
                @if (!empty($signature))
                    <img src="{{ $signature }}" width="100">
                @else
                    <span>Tidak ada tanda tangan</span>
                @endif
            </td>
            <td style="border-bottom: none; height: 30px;"></td>
        </tr>
        <tr style="text-align: center;">
            <td style="border-top: none;"></td>
            <td style="border-top: none;">{{ strtoupper($formData['nama_pembuat']) }}</td>
            <td style="border-top: none;"></td>
        </tr>
        <tr style="text-align: center;">
            <td><strong>GA Dept</strong></td>
            <td><strong>PO Lokal</strong></td>
            <td><strong>Senior Adm Officer</strong></td>
        </tr>
    </table>
</body>

</html>
