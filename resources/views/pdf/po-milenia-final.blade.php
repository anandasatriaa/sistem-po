<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - PT. Milenia Mega Mandiri</title>
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
    @php
        // Batas maksimal baris yang ditampilkan di halaman pertama
        $maxItemsOnFirstPage = 23;
        $items = $purchaseOrder->barang;
        $itemsCount = $items->count();
    @endphp

    {{-- HEADER & DETAIL PO (Halaman 1) --}}
    <table style="margin-bottom: 10px">
        <tr>
            <td style="text-align: left; width: 33%;"></td>
            <td style="text-align: center; width: 33%;">
                <div class="po-title">PURCHASE ORDER</div>
            </td>
            <td style="text-align: right; width: 33%;">
                <img src="{{ asset('assets/images/logo-milenia-rm.png') }}" alt="Company Logo" style="height: 60px;">
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 12px;">
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>To: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->supplier }}</td>
            <td style="width: 10%; padding: 1;"><strong>From: </strong></td>
            <td style="width: 40%; padding: 1;">Milenia Mega Mandiri</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->address }}</td>
            <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $cabangData ? $cabangData->alamat : '-' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
            <td style="width: 17%; padding: 1;">{{ $purchaseOrder->phone }}</td>
            <td style="width: 10%; text-align: right; padding: 1;"><strong>Fax: </strong></td>
            <td style="width: 15%; padding: 1;">{{ $purchaseOrder->fax }}</td>
            <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
            <td style="width: 35%; padding: 1;">{{ $cabangData ? $cabangData->telepon : '-' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>UP: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->up }}</td>
            <td style="width: 10%; padding: 1;"><strong>NO: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $purchaseOrder->no_po }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Cabang: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->cabang }}</td>
            <td style="width: 10%; padding: 1;"><strong>Kategori: </strong></td>
            <td style="width: 40%; padding: 1;">{{ $category }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #000;">
            <td style="width: 10%; padding: 1;"><strong>Date: </strong></td>
            <td colspan="3" style="width: 40%; padding: 1;">
                {{ \Carbon\Carbon::parse($purchaseOrder->date)->format('F d, Y') }}</td>
            <td style="width: 10%; padding: 1;"><strong>Page: </strong></td>
            <td style="width: 40%; padding: 1;">1</td>
        </tr>
    </table>

    <p style="margin-top: 15px; margin-bottom: 10px; font-size: 12px;">
        Bersama dengan surat ini kami memesan :
    </p>

    {{-- TABEL BARANG HALAMAN 1 --}}
    @if ($itemsCount > 0)
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
                @foreach ($items->take($maxItemsOnFirstPage) as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->barang }}</td>
                        <td style="text-align: center;">{{ $item->qty }} {{ $item->unit }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item->amount_price, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @endforeach

                {{-- Jika semua barang muat di halaman 1, tampilkan bagian total di halaman yang sama --}}
                @if ($itemsCount <= $maxItemsOnFirstPage)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Sub Total</td>
                        <td style="text-align: right;">{{ number_format($purchaseOrder->sub_total, 0, ',', '.') }}</td>
                        <td style="border: none;"></td>
                    </tr>
                    @if ($purchaseOrder->pajak > 0)
                        <tr style="font-weight: bold;">
                            <td colspan="4">
                                Pajak ({{ number_format(($purchaseOrder->pajak / $purchaseOrder->sub_total) * 100) }}%)
                            </td>
                            <td style="text-align: right;">{{ number_format($purchaseOrder->pajak, 0, ',', '.') }}</td>
                            <td style="border: none;"></td>
                        </tr>
                    @endif
                    @if ($purchaseOrder->discount > 0)
                        <tr style="font-weight: bold;">
                            <td colspan="4">Diskon</td>
                            <td style="text-align: right;">{{ number_format($purchaseOrder->discount, 0, ',', '.') }}
                            </td>
                            <td style="border: none;"></td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold;">
                        <td colspan="4">Total</td>
                        <td style="text-align: right;">{{ number_format($purchaseOrder->total, 0, ',', '.') }}</td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- JIKA BARANG LEBIH DARI 30, TAMPILKAN HALAMAN 2 --}}
    @if ($itemsCount > $maxItemsOnFirstPage)
        <div class="page-break"></div>

        {{-- HEADER ULANG UNTUK HALAMAN 2 --}}
        <table style="margin-bottom: 10px">
            <tr>
                <td style="text-align: left; width: 33%;"></td>
                <td style="text-align: center; width: 33%;">
                    <div class="po-title">PURCHASE ORDER</div>
                </td>
                <td style="text-align: right; width: 33%;">
                    <img src="{{ asset('assets/images/logo-milenia-rm.png') }}" alt="Company Logo"
                        style="height: 60px;">
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 12px;">
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>To: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->supplier }}</td>
                <td style="width: 10%; padding: 1;"><strong>From: </strong></td>
                <td style="width: 40%; padding: 1;">Milenia Mega Mandiri</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->address }}</td>
                <td style="width: 10%; padding: 1;"><strong>Address: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $cabangData ? $cabangData->alamat : '-' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
                <td style="width: 17%; padding: 1;">{{ $purchaseOrder->phone }}</td>
                <td style="width: 10%; text-align: right; padding: 1;"><strong>Fax: </strong></td>
                <td style="width: 15%; padding: 1;">{{ $purchaseOrder->fax }}</td>
                <td style="width: 10%; padding: 1;"><strong>Phone: </strong></td>
                <td style="width: 35%; padding: 1;">{{ $cabangData ? $cabangData->telepon : '-' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>UP: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->up }}</td>
                <td style="width: 10%; padding: 1;"><strong>NO: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $purchaseOrder->no_po }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Cabang: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">{{ $purchaseOrder->cabang }}</td>
                <td style="width: 10%; padding: 1;"><strong>Kategori: </strong></td>
                <td style="width: 40%; padding: 1;">{{ $category }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #000;">
                <td style="width: 10%; padding: 1;"><strong>Date: </strong></td>
                <td colspan="3" style="width: 40%; padding: 1;">
                    {{ \Carbon\Carbon::parse($purchaseOrder->date)->format('F d, Y') }}</td>
                <td style="width: 10%; padding: 1;"><strong>Page: </strong></td>
                <td style="width: 40%; padding: 1;">2</td>
            </tr>
        </table>

        <p style="margin-top: 15px; margin-bottom: 10px; font-size: 12px;">
            Bersama dengan surat ini kami memesan :
        </p>

        {{-- TABEL BARANG HALAMAN 2 (Sisa Barang) --}}
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
                @foreach ($items->slice($maxItemsOnFirstPage)->values() as $index => $item)
                    <tr>
                        <td>{{ $maxItemsOnFirstPage + $index + 1 }}</td>
                        <td>{{ $item->barang }}</td>
                        <td style="text-align: center;">{{ $item->qty }} {{ $item->unit }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($item->amount_price, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @endforeach

                {{-- Tampilkan bagian total di halaman 2 --}}
                <tr style="font-weight: bold;">
                    <td colspan="4">Sub Total</td>
                    <td style="text-align: right;">{{ number_format($purchaseOrder->sub_total, 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
                @if ($purchaseOrder->pajak > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">
                            Pajak ({{ number_format(($purchaseOrder->pajak / $purchaseOrder->sub_total) * 100) }}%)
                        </td>
                        <td style="text-align: right;">{{ number_format($purchaseOrder->pajak, 0, ',', '.') }}</td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                @if ($purchaseOrder->discount > 0)
                    <tr style="font-weight: bold;">
                        <td colspan="4">Diskon</td>
                        <td style="text-align: right;">{{ number_format($purchaseOrder->discount, 0, ',', '.') }}</td>
                        <td style="border: none;"></td>
                    </tr>
                @endif
                <tr style="font-weight: bold;">
                    <td colspan="4">Total</td>
                    <td style="text-align: right;">{{ number_format($purchaseOrder->total, 0, ',', '.') }}</td>
                    <td style="border: none;"></td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- KONTEN LAINNYA --}}
        <div style="border: 1px solid #000; padding: 2px; margin: 2px 0; font-size: 12px;">
            <strong>Terbilang:</strong> {{ strtoupper($grandtotalWords) }} RUPIAH
        </div>
        <div style="margin-bottom: 10px; font-size: 12px;">
            <strong>Tanggal Pengiriman:
                {{ \Carbon\Carbon::parse($purchaseOrder->estimate_date)->format('F d, Y') }}</strong>
        </div>
        <div style="font-size: 12px;">
            <u><strong>REMARKS:</strong> {{ $purchaseOrder->remarks }}</u>
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
                <td style="border-bottom: none; height: 30px;">
                    @if ($purchaseOrder->ttd_1)
                        @if ($purchaseOrder->ttd_1 === 'REJECTED')
                            <div
                                style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                                REJECTED
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $purchaseOrder->ttd_1) }}" alt="Signature-GA"
                                width="100">
                        @endif
                    @endif
                </td>
                <td style="border-bottom: none; height: 30px;">
                    @if ($purchaseOrder->ttd_2)
                        @if ($purchaseOrder->ttd_2 === 'REJECTED')
                            <div
                                style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                                REJECTED
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $purchaseOrder->ttd_2) }}" alt="Signature-ADMIN"
                                width="100">
                        @endif
                    @endif
                </td>
                <td style="border-bottom: none; height: 30px;">
                    @if ($purchaseOrder->ttd_3)
                        @if ($purchaseOrder->ttd_3 === 'REJECTED')
                            <div
                                style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                                REJECTED
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $purchaseOrder->ttd_3) }}" alt="Signature-DIRECTOR"
                                width="100">
                        @endif
                    @endif
                </td>
            </tr>
            <tr style="text-align: center;">
                <td style="border-top: none;">{{ $purchaseOrder->nama_1 }}</td>
                <td style="border-top: none;">{{ strtoupper($purchaseOrder->nama_2) }}</td>
                <td style="border-top: none;">{{ $purchaseOrder->nama_3 }}</td>
            </tr>
            <tr style="text-align: center;">
                <td><strong>GA Dept</strong></td>
                <td><strong>PO Lokal</strong></td>
                <td><strong>Senior Adm Officer</strong></td>
            </tr>
        </table>
</body>

</html>
