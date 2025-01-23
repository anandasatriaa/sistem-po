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
            padding: 4px;
        }

        .container-custom {
            width: 21cm;
        }
    </style>
@endsection

@section('content')
    <div class="container-custom mx-auto">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary btnAjukan"><i class="ri-send-plane-line me-2"></i>Ajukan</button>
        </div>
        <div class="badge bg-danger text-center mb-2 w-100">
            Jika tidak ada barang yang dimaksud, silahkan tulis nama barang baru pada input.
        </div>
    </div>

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
            <div class="col-lg-8 info-section mb-3">
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
                        <label for="divisi" class="form-label">Divisi</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="divisi" class="form-select" data-choices data-choices-sorting="true">
                            <option selected disabled>Pilih Divisi...</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->Divisi }}">{{ $division->Divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="nopr" class="form-label">No. PR</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" placeholder="" id="nopr" disabled>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="ForminputState" class="form-label">PT.</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="ForminputState" class="form-select" data-choices data-choices-sorting="true">
                            <option selected disabled>Pilih PT...</option>
                            <option>PT. Milenia Mega Mandiri</option>
                            <option>PT. Mega Auto Prima</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Checkbox Group -->
            <div class="col-lg-4 checkbox-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rutinTidakSegera" value="Rutin, Tidak Segera"
                        name="exclusive-checkbox">
                    <label class="form-check-label" for="rutinTidakSegera">
                        Rutin, Tidak Segera
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rutinMendesak" value="Rutin, Mendesak"
                        name="exclusive-checkbox">
                    <label class="form-check-label" for="rutinMendesak">
                        Rutin, Mendesak
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tidakRutinTidakSegera"
                        value="Tidak Rutin, Tidak Segera" name="exclusive-checkbox">
                    <label class="form-check-label" for="tidakRutinTidakSegera">
                        Tidak Rutin, Tidak Segera
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tidakRutinSegera" value="Tidak Rutin, Segera"
                        name="exclusive-checkbox">
                    <label class="form-check-label" for="tidakRutinSegera">
                        Tidak Rutin, Segera
                    </label>
                </div>
            </div>
        </div>

        <div class="table-section mt-3">
            <table id="barangTable">
                <thead>
                    <tr class="text-center">
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
                        <td><input list="barangList" name="barang[]" class="form-control" />
                            <datalist id="barangList">
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->nama }}">{{ $barang->nama }}</option>
                                @endforeach
                            </datalist>
                        </td>
                        <td><input type="number" name="qty[]" class="form-control" placeholder="" style="width: 60px">
                        </td>
                        <td>
                            <select name="satuan[]" class="form-select" data-choices data-choices-sorting="true">
                                <option selected disabled>Pilih...</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->satuan }}">{{ $unit->satuan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="keterangan[]" id="" class="form-control" style="height: 40px"></textarea>
                        </td>
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

    {{-- Dropdown & Add Row --}}
    <script>
        // Function to update row numbers
        function updateRowNumbers() {
            const rows = document.querySelectorAll("#barangTable tbody tr");
            rows.forEach((row, index) => {
                row.querySelector("td:first-child").textContent = index + 1; // Update row number
            });
        }

        // Function to initialize Choices.js
        function initializeChoices() {
            // Select all dropdowns with `data-choices` attribute that are not initialized
            const selects = document.querySelectorAll('select[data-choices]:not(.choices__input)');
            selects.forEach(select => {
                new Choices(select, {
                    searchEnabled: true
                });
            });
        }

        // Add row functionality
        document.getElementById("addRowBtn").addEventListener("click", function() {
            const tableBody = document.querySelector("#barangTable tbody");
            const newRow = document.createElement("tr");

            newRow.innerHTML = `
                <td></td>
                <td>
                    <input list="barangList" name="barang[]" class="form-control" />
                    <datalist id="barangList">
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->nama }}">{{ $barang->nama }}</option>
                        @endforeach
                    </datalist>
                </td>
                <td><input type="number" name="qty[]" class="form-control" placeholder="" style="width: 60px"></td>
                <td>
                    <select name="satuan[]" class="form-select" data-choices data-choices-sorting="true">
                        <option selected disabled>Pilih...</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->satuan }}">{{ $unit->satuan }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <textarea name="keterangan[]" class="form-control" style="height: 40px"></textarea>
                </td>
                <td class="text-center">
                    <button type="button" class="removeRowBtn btn btn-danger">
                        <i class="ri-delete-bin-2-line"></i>
                    </button>
                </td>
            `;

            tableBody.appendChild(newRow);
            updateRowNumbers(); // Update row numbers after adding a new row
            initializeChoices();
        });

        // Remove row functionality
        document.querySelector("#barangTable tbody").addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRowBtn") || e.target.closest(".removeRowBtn")) {
                const row = e.target.closest("tr");
                row.remove();
                updateRowNumbers(); // Update row numbers after removing a row
            }
        });

        // Initialize Choices.js for existing dropdowns
        initializeChoices();
    </script>

    {{-- Checkbox --}}
    <script>
        // JavaScript to ensure only one checkbox is checked at a time
        document.querySelectorAll('input[name="exclusive-checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('input[name="exclusive-checkbox"]').forEach((
                        otherCheckbox) => {
                        if (otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>

    {{-- No PR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Panggil endpoint untuk mendapatkan no_pr terbaru
            fetch('{{ url('purchase-request/last-nopr') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nopr').value = data.no_pr;
                });
        });
    </script>

    {{-- POST Data Purchase Request --}}
    <script>
        document.querySelector(".btnAjukan").addEventListener("click", function() {
            // Ambil data dari form
            const dateRequest = document.getElementById("dateInput").value;
            const divisi = document.getElementById("divisi").value;
            const noPr = document.getElementById("nopr").value;
            const pt = document.getElementById("ForminputState").value;

            // Mengambil important berdasarkan checkbox yang terpilih
            const importantCheckboxes = document.querySelectorAll(".form-check-input");
            let important = [];
            importantCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    important.push(checkbox.value);
                }
            });

            // Ambil data barang
            const rows = document.querySelectorAll("#barangTable tbody tr");
            const barangData = [];
            rows.forEach(row => {
                const namaBarang = row.querySelector("input[name='barang[]']").value;
                const qty = row.querySelector("input[name='qty[]']").value;
                const satuan = row.querySelector("select[name='satuan[]']").value;
                const keterangan = row.querySelector("textarea[name='keterangan[]']").value;

                barangData.push({
                    nama_barang: namaBarang,
                    quantity: qty,
                    unit: satuan,
                    keterangan: keterangan
                });
            });

            const userId = @json(auth()->user()->ID);

            // Console log data yang diambil
            console.log("Purchase Request Data:");
            console.log("user_id: ", userId); // Ganti dengan nilai user_id yang sesuai
            console.log("date_request: ", dateRequest);
            console.log("divisi: ", divisi);
            console.log("no_pr: ", noPr);
            console.log("pt: ", pt);
            console.log("important: ", important);
            console.log("barang data: ", barangData);

            // Kirim data ke backend dengan POST menggunakan fetch
            fetch('{{ url('/purchase-request/store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pastikan CSRF token disertakan
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        date_request: dateRequest,
                        divisi: divisi,
                        no_pr: noPr,
                        pt: pt,
                        important: important,
                        barang_data: barangData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response from server:", data); // Debugging response
                    if (data.success) {
                        // SweetAlert for success
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('user.pr-status') }}";
                            }
                        });
                    } else {
                        // SweetAlert for failure
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message, // Display detailed error message
                            showConfirmButton: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // SweetAlert for unexpected error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan saat mengirim data. Pastikan data yang diinputkan valid.',
                        showConfirmButton: true
                    });
                });
        });
    </script>

@endsection
