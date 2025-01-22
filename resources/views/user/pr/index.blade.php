@extends('user.layouts.app')

@section('title', 'Purchase Request | Sistem Purchase Order General Affair')

@section('css')
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            padding: 20px;
            display: block;
        }

        .paper {
            background-color: #fff;
            width: 21cm;
            height: 29.7cm;
            /* A4 size */
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .header,
        .checkbox-group,
        .table-section {
            margin-bottom: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
        }

        .checkbox-group {
            /* Menghilangkan display flex agar checkbox berada satu per baris */
            display: block;
        }

        .checkbox-group .form-check {
            /* Setiap checkbox berada dalam satu baris */
            display: block;
            margin-bottom: 10px;
            /* Memberikan jarak antar checkbox */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="paper mx-auto">
        <div class="header mb-5">
            <div style="flex: 1;" class="text-start">
                <img src="{{ asset('assets/images/logo-milenia.png') }}" alt="Logo Milenia" style="width: 100px;">
            </div>
            <div style="flex: 1;" class="fw-bold text-center">
                <h3>Purchase Request (Permintaan Barang)</h3>
            </div>
            <div style="flex: 1;"></div>
        </div>

        <div class="row align-items-start">
            <!-- Info Section -->
            <div class="col-lg-8 info-section">
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="dateInput" class="form-label">Date</label>
                    </div>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <input type="date" class="form-control" data-provider="flatpickr" id="dateInput">
                            <span class="input-group-text">
                                <i class="ri-calendar-todo-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="ForminputState" class="form-label">Divisi</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="ForminputState" class="form-select" data-choices data-choices-sorting="true">
                            <option selected>Choose...</option>
                            <option>...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="firstNameinput" class="form-label">No. PR</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" placeholder="" id="firstNameinput" disabled>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="ForminputState" class="form-label">PT.</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="ForminputState" class="form-select" data-choices data-choices-sorting="true">
                            <option selected>Choose...</option>
                            <option>PT. Milenia Mega Mandiri</option>
                            <option>PT. Mega Auto Prima</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Checkbox Group -->
            <div class="col-lg-4 checkbox-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rutinTidakSegera">
                    <label class="form-check-label" for="rutinTidakSegera">
                        Rutin, Tidak Segera
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rutinMendesak">
                    <label class="form-check-label" for="rutinMendesak">
                        Rutin, Mendesak
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tidakRutinTidakSegera">
                    <label class="form-check-label" for="tidakRutinTidakSegera">
                        Tidak Rutin, Tidak Segera
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tidakRutinSegera">
                    <label class="form-check-label" for="tidakRutinSegera">
                        Tidak Rutin, Segera
                    </label>
                </div>
            </div>
        </div>

        <div class="table-section mt-3">
            <table id="barangTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><select id="ForminputState" name="barang[]" class="form-select" data-choices
                                data-choices-sorting="true">
                                <option selected>Choose...</option>
                                <option>Barang 1</option>
                                <option>Barang 2</option>
                            </select></td>
                        <td><input type="number" name="qty[]" placeholder="Qty" style="width: 50px"></td>
                        <td><select id="ForminputState" name="satuan[]" class="form-select" data-choices
                                data-choices-sorting="true">
                                <option selected>Choose...</option>
                                <option>PT. Milenia Mega Mandiri</option>
                                <option>PT. Mega Auto Prima</option>
                            </select></td>
                        <td><input type="text" name="keterangan[]" placeholder="Keterangan"></td>
                        <td><button type="button" class="removeRowBtn btn btn-danger"><i
                                    class="ri-delete-bin-2-line"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="addRowBtn" class="btn btn-primary mt-2"><i class="ri-add-line"></i></button>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let rowCount = 1;

        // Fungsi untuk menambah baris
        document.getElementById('addRowBtn').addEventListener('click', function() {
            rowCount++;
            const table = document.getElementById('barangTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="nama_barang[]" placeholder="Nama Barang"></td>
            <td><input type="number" name="qty[]" placeholder="Qty" style="width: 50px"></td>
            <td><input type="text" name="satuan[]" placeholder="Satuan" style="width: 150px"></td>
            <td><input type="text" name="keterangan[]" placeholder="Keterangan"></td>
            <td><button type="button" class="removeRowBtn btn btn-danger"><i class="ri-delete-bin-2-line"></i></button></td>
        `;
        });

        // Fungsi untuk menghapus baris
        document.querySelector('table').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('removeRowBtn')) {
                const row = e.target.closest('tr');
                row.parentNode.removeChild(row);
                rowCount--;
                updateRowNumbers();
            }
        });

        // Update nomor urut baris setelah penghapusan
        function updateRowNumbers() {
            const rows = document.querySelectorAll('#barangTable tbody tr');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1; // Update nomor di kolom pertama
            });
        }
    </script>
@endsection
