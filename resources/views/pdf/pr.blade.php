<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 5px;
            position: relative;
            border: 2px solid #000;
            padding-left: 10px;
            padding-right: 10px;
        }

        .paper {
            margin: auto;
            width: 100%;
            max-width: 800px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        thead th {
            background-color: #ddd;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .header-table td {
            border: none;
            padding: 5px;
        }

        .info-section td {
            /* padding: 5px; */
            border: none;
        }

        .signature {
            position: absolute;
            bottom: 20px;
            left: 10px;
        }

        .signature-spv {
            position: absolute;
            bottom: 20px;
            right: 10px;
        }

        .line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="paper">
        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    @if ($purchaseRequest->pt === 'PT. Milenia Mega Mandiri')
                        <img src="{{ asset('assets/images/logo-milenia-2.png') }}" alt="Logo Milenia" width="80">
                    @elseif ($purchaseRequest->pt === 'PT. Mega Auto Prima')
                        <img src="{{ asset('assets/images/map-logo.png') }}" alt="Logo MAP" width="80">
                    @else
                        <!-- Jika tidak memenuhi salah satu kondisi, bisa ditampilkan logo default -->
                        <img src="{{ asset('assets/images/logo-milenia.png') }}" alt="Logo Default" width="80">
                    @endif
                </td>
                <td style="width: 60%; text-align: center; font-weight: bold; font-size: 20px;">
                    <h3>Purchase Request <br> (Permintaan Barang)</h3>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>

        <!-- Info Section -->
        <table class="info-section" cellpadding="10" cellspacing="0" style="width: 100%;">
            <tr>
                <td style="width: 100px; border-bottom: 1px solid #000;"><strong>Nama</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->user->Nama }}</td>
                <td style="width: 208px;">[{{ $purchaseRequest->important == 'Rutin, Tidak Segera' ? 'x' : ' ' }}]
                    Rutin,
                    Tidak Segera</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000;"><strong>Tanggal</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->date_request }}</td>
                <td>[{{ $purchaseRequest->important == 'Rutin, Mendesak' ? 'x' : ' ' }}] Rutin, Mendesak</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000;"><strong>Divisi</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->divisi }}</td>
                <td>[{{ $purchaseRequest->important == 'Tidak Rutin, Tidak Segera' ? 'x' : ' ' }}] Tidak Rutin,
                    Tidak
                    Segera</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000;"><strong>No. PR</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->no_pr }}</td>
                <td>[{{ $purchaseRequest->important == 'Tidak Rutin, Segera' ? 'x' : ' ' }}] Tidak Rutin, Segera
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000;"><strong>PT</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->pt }}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000;"><strong>Remarks</strong></td>
                <td style="border-bottom: 1px solid #000;">: {{ $purchaseRequest->remarks }}</td>
            </tr>
        </table>

        <!-- Items Section -->
        <h3>Items:</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseRequest->barang as $index => $barang)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->quantity }}</td>
                        <td>{{ $barang->unit }}</td>
                        <td>{{ $barang->keterangan ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Signature Section -->
    <div class="signature">
        <div class="title">Diminta oleh,</div>
        <img src="{{ asset('storage/' . $purchaseRequest->signature) }}" alt="Signature" width="150">
        <div class="name">{{ $purchaseRequest->user->Nama }}</div>
        <div class="line"></div>
    </div>

    <!-- Signature SPV Section -->
    <div class="signature-spv @if (empty($purchaseRequest->acc_sign)) empty-signature @endif">
        <div class="title">Disetujui oleh,</div>
        @if ($purchaseRequest->acc_sign)
            @if ($purchaseRequest->acc_sign === 'REJECTED')
                <div
                    style="margin-top: 25px; margin-bottom: 10px; text-align: center; border: 2px solid red; color: red; padding: 10px;">
                    REJECTED
                </div>
            @else
                <img src="{{ asset('storage/' . $purchaseRequest->acc_sign) }}" alt="Signature-SPV" width="150">
            @endif
        @else
            <div style="height: 70px;"></div> <!-- Memberikan ruang jika tidak ada tanda tangan -->
        @endif
        <div class="name">
            @if ($purchaseRequest->acc_by)
                {{ $purchaseRequest->acc_by }}
            @else
                <div style="height: 20px;"></div> <!-- Memberikan ruang jika tidak ada tanda tangan -->
            @endif
        </div>
        <div class="line"></div>
    </div>
</body>

</html>
