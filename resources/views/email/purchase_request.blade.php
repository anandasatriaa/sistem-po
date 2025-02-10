<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Purchase Request</title>
    <style>
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

        h2 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
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
            color: #000000;
            font-size: 0.9em;
        }

        .bg-danger {
            background-color: #d9534f;
        }

        .bg-warning {
            background-color: #f0ad4e;
        }

        .bg-primary {
            background-color: #0275d8;
        }

        .bg-info {
            background-color: #5bc0de;
        }

        .bg-success {
            background-color: #5cb85c;
        }

        .signature {
            margin-top: 20px;
            text-align: center;
        }

        .signature img {
            max-width: 200px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 3px;
        }

        a {
            color: #0275d8;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Detail Purchase Request</h2>
        <p>Purchase Request baru telah dibuat dengan detail sebagai berikut:</p>

        <table>
            <tr>
                <th>No PR</th>
                <td>{{ $pr->no_pr }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ \Carbon\Carbon::parse($pr->date_request)->format('d M, Y') }}</td>
            </tr>
            <tr>
                <th>Nama Pemohon</th>
                <!-- Jika Anda memiliki relasi user atau properti user_name -->
                <td>{{ $user->Nama ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Divisi</th>
                <td>{{ $pr->divisi }}</td>
            </tr>
            <tr>
                <th>PT</th>
                <td>{{ $pr->pt }}</td>
            </tr>
            <tr>
                <th>Kebutuhan</th>
                <td>{{ $pr->important }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-warning">
                        Need Approval
                    </span>
                </td>
            </tr>
            <tr>
                <th>Barang</th>
                <td>
                    @if ($pr->barang && $pr->barang->count() > 0)
                        <table border="1" cellpadding="5" cellspacing="0" width="100%">
                            <thead>
                                <tr style="background-color: #f2f2f2; text-align: left;">
                                    <th>Nama Barang</th>
                                    <th>Quantity Unit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pr->barang as $barang)
                                    <tr>
                                        <td>{{ $barang->nama_barang }}</td>
                                        <td>{{ $barang->quantity }} {{ $barang->unit }}</td>
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

        <div
            style="border: 2px solid #0275d8; padding: 15px; background-color: #e9f7fd; border-radius: 5px; text-align: center; margin: 20px 0;">
            <p style="font-size: 1.2em; font-weight: bold; color: #0275d8; margin: 0;">
                Dibutuhkan <span style="color: #d9534f;">Approval</span> untuk melanjutkan ke tahapan Purchase Order.
            </p>
            <p style="font-size: 1.1em; color: #0275d8; margin: 5px 0 0;">
                Silakan lakukan pengecekan atau approval pada link berikut:
                <a href="{{ url('/approval-pr/' . $pr->id) }}"
                    style="color: #000000; font-weight: bold; text-decoration: none; border: 2px solid #0275d8; padding: 5px 10px; border-radius: 3px;">SISTEM GA</a>.
            </p>
        </div>

        <p>Terima kasih.</p>
        <p>Salam,<br>Admin GA</p>
    </div>
</body>

</html>
