<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Purchase Order</title>
    <style>
        /* CSS inline untuk mendukung tampilan di berbagai email client */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            max-width: 650px;
            margin: auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f7f7f7;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            color: #000;
            font-size: 0.9em;
        }
        .bg-warning {
            background-color: #f0ad4e;
        }
        .approval {
            border: 2px solid #0275d8;
            padding: 15px;
            background-color: #e9f7fd;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .approval p {
            margin: 5px 0;
            font-size: 1.1em;
            color: #0275d8;
        }
        .approval a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
            border: 2px solid #0275d8;
            padding: 5px 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header dengan logo -->
        {{-- <div class="header">
            <img src="{{ $message->embed(public_path('assets/images/logo-milenia-2.png')) }}" alt="Logo Milenia">
        </div> --}}

        <h2>Detail Purchase Order</h2>
        <p>Purchase Order baru telah dibuat dengan detail sebagai berikut:</p>

        <table>
            <tr>
                <th>No PO</th>
                <td>{{ $po->no_po }}</td>
            </tr>
            <tr>
                <th>Cabang</th>
                <td>{{ $po->cabang }}</td>
            </tr>
            <tr>
                <th>Supplier</th>
                <td>{{ $po->supplier }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ \Carbon\Carbon::parse($po->date)->format('d M, Y') }}</td>
            </tr>
            <tr>
                <th>Remarks</th>
                <td>{{ $po->remarks }}</td>
            </tr>
            <tr>
                <th>Pembuat PO</th>
                <td>{{ $po->nama_2 }}</td>
            </tr>
            <tr>
                <th>PT</th>
                <td>PT. Milenia Mega Mandiri</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-warning">Need Approval by SPV GA / Direktur</span>
                </td>
            </tr>
            <tr>
                <th>Barang</th>
                <td>
                    @if ($po->barang && $po->barang->count() > 0)
                        <table border="1" cellpadding="5" cellspacing="0" width="100%">
                            <thead>
                                <tr style="background-color: #f2f2f2; text-align: left;">
                                    <th>Nama Barang</th>
                                    <th>Quantity Unit</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($po->barang as $barang)
                                    <tr>
                                        <td>{{ $barang->barang }}</td>
                                        <td>{{ $barang->qty }} {{ $barang->unit }}</td>
                                        <td>{{ $barang->category }}</td>
                                        <td>{{ $barang->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <div class="approval">
            <p><strong>Dibutuhkan Approval</strong> untuk Purchase Order di atas.</p>
            <p>
                Silakan lakukan pengecekan atau approval pada link berikut:
                <a href="{{ url('/approval-po-milenia/' . $po->id) }}"
                    style="color: #000000; font-weight: bold; text-decoration: none; border: 2px solid #0275d8; padding: 5px 10px; border-radius: 3px;">SISTEM GA</a>.
            </p>
        </div>

        <p>Terima kasih.</p>
        <p>Salam,<br>Admin GA</p>
    </div>
</body>
</html>
