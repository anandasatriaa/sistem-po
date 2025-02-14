@extends('ga.layouts.app')

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
            height: auto;
            /* A4 size */
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            /* display: flex; */
            /* flex-direction: column; */
            /* justify-content: space-between; */
            /* position: relative; */
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

        .signature-container {
            /* margin-top: auto; */
            /* Dorong elemen ini ke bagian bawah kontainer */
            text-align: left;
            /* padding-top: 20px; */
        }

        canvas {
            cursor: crosshair;
            border: 1px solid #000;
        }

        /* Membatasi lebar FilePond */
        .filepond--root {
            max-width: 300px;
            /* margin: 0 auto; */
        }
    </style>
@endsection

@section('content')
    <div class="container-custom mx-auto">
        <div class="badge bg-danger text-center mb-2 w-100">
            Untuk menghindari duplikat silahkan pilih barang yang sudah ada. Jika tidak ada barang yang dimaksud, silahkan tulis nama barang baru pada input.
        </div>
    </div>

    <div class="paper mx-auto">
        <div class="header mb-5">
            <div style="flex: 1;" class="text-start">
                <img id="headerLogo" src="{{ asset('assets/images/logo-milenia.png') }}" alt="Logo Milenia" style="width: 100px;">
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
                        <label for="dateInput" class="form-label"><span class="text-danger">**</span>Date</label>
                    </div>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <input type="date" class="form-control" data-provider="flatpickr" id="dateInput" required>
                            <span class="input-group-text" style="cursor:pointer;">
                                <i class="ri-calendar-todo-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="divisi" class="form-label"><span class="text-danger">**</span>Divisi</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="divisi" class="form-select" data-choices data-choices-sorting="true" required>
                            <option selected disabled>Pilih Divisi...</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->Divisi }}">{{ $division->Divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="nopr" class="form-label"><span class="text-danger">**</span>No. PR</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" placeholder="" id="nopr" disabled>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="ForminputState" class="form-label"><span class="text-danger">**</span>PT.</label>
                    </div>
                    <div class="col-lg-10">
                        <select id="ForminputState" class="form-select" data-choices data-choices-sorting="true" required>
                            <option selected disabled>Pilih PT...</option>
                            <option>PT. Milenia Mega Mandiri</option>
                            <option>PT. Mega Auto Prima</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-lg-2">
                        <label for="remarks" class="form-label"><span class="text-danger">**</span>Remarks</label>
                    </div>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" placeholder="ex: Untuk Perbaikan" id="remarks" required>
                    </div>
                </div>
            </div>

            <!-- Checkbox Group -->
            <div class="col-lg-4 checkbox-group">
                <small><span class="text-danger">**</span>Wajib dipilih</small>
                <div class="border border-danger rounded p-2">
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
        </div>

        <div class="table-section mt-3">
            <table id="barangTable">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th><span class="text-danger">**</span>Nama Barang</th>
                        <th><span class="text-danger">**</span>Qty</th>
                        <th><span class="text-danger">**</span>Satuan</th>
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
        <div class="d-flex justify-content-between align-items-start">
            <!-- Signature di sebelah kiri -->
            <div class="signature-container" style="flex: 1; margin-right: 20px;">
                <div class="fw-bold">TTD:</div>
                <small><span class="text-danger">**</span>Sign Pad wajib digores</small>
                <div class="d-flex align-content-center mt-2">
                    <canvas id="signatureCanvas" width="200" height="100"></canvas>
                    <button id="clearSignature" class="btn btn-outline-danger ms-2">
                        <i class="ri-delete-bin-2-line"></i>
                    </button>
                </div>
            </div>

            <!-- File Upload di sebelah kanan -->
            <div class="upload-container">
                <div class="fw-bold">Upload Lampiran Foto/PDF (Optional):</div>
                <small>**(Optional) Upload bukti foto / pdf (maks. file 10MB)</small>
                <input type="file" class="filepond filepond-input-multiple mt-2" multiple name="filepond"
                    data-allow-reorder="true" data-max-file-size="10MB" data-max-files="15">
            </div>
        </div>
        <div class="mt-3">
            <button type="button" class="btn btn-primary btnAjukan w-100"><i
                    class="ri-send-plane-line me-2"></i>Ajukan</button>
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
            fetch('{{ url('/ga/purchase-request/last-nopr') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nopr').value = data.no_pr;
                });
        });
    </script>

    {{-- POST Data Purchase Request --}}
    <script>
        document.querySelector(".btnAjukan").addEventListener("click", function() {
            const signatureCanvas = document.getElementById("signatureCanvas");
            const signatureData = signatureCanvas.toDataURL("image/png");

            // Ambil data dari form
            const dateRequest = document.getElementById("dateInput").value;
            const divisi = document.getElementById("divisi").value;
            const noPr = document.getElementById("nopr").value;
            const pt = document.getElementById("ForminputState").value;
            const remarks = document.getElementById("remarks").value;

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

            // Buat objek FormData
            let formData = new FormData();
            formData.append('user_id', userId);
            formData.append('date_request', dateRequest);
            formData.append('divisi', divisi);
            formData.append('no_pr', noPr);
            formData.append('pt', pt);
            formData.append('remarks', remarks);
            // Simpan array important dan barang_data dalam bentuk JSON string
            formData.append('important', JSON.stringify(important));
            formData.append('barang_data', JSON.stringify(barangData));
            formData.append('signature', signatureData);

            // Ambil file lampiran (opsional) dari input file yang berada di dalam .upload-container
            const filepondElements = document.querySelectorAll('.filepond');
            filepondElements.forEach(inputElement => {
                // Dapatkan instance FilePond terkait
                const pondInstance = FilePond.find(inputElement);
                if (pondInstance) {
                    const files = pondInstance.getFiles();
                    if (files.length > 0) {
                        console.log("Jumlah file lampiran:", files.length);
                        files.forEach((fileItem, index) => {
                            console.log(`File ${index + 1}:`, fileItem.file);
                            // Masukkan file asli ke FormData
                            formData.append('lampiran[]', fileItem.file);
                        });
                    } else {
                        console.log("Tidak ada file lampiran yang dipilih di FilePond.");
                    }
                } else {
                    console.log("Instance FilePond tidak ditemukan untuk input:", inputElement);
                }
            });

            // Opsional: Log semua entry FormData untuk memastikan data yang dikirim
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            // Kirim data ke backend dengan POST menggunakan fetch
            fetch('{{ url('/ga/purchase-request/store') }}', {
                    method: 'POST',
                    headers: {
                        // Jangan set Content-Type, biarkan browser mengatur boundary multipart-nya
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pastikan CSRF token disertakan
                    },
                    body: formData
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
                                window.location.href = "{{ route('ga.pr-status') }}";
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
                        text: 'Terjadi kesalahan saat mengirim data. Pastikan semua data telah terisi.',
                        showConfirmButton: true
                    });
                });
        });
    </script>

    {{-- Signature --}}
    <script>
        // Inisialisasi SignaturePad (menggantikan kode event manual)
        const canvas = document.getElementById('signatureCanvas');
        // Inisialisasi SignaturePad; atur backgroundColor jika diperlukan
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)' // atau gunakan 'white'
        });

        // (Opsional) Fungsi untuk mengupdate status atau melakukan sesuatu setelah tanda tangan selesai
        function updateButtonState() {
            // Contoh: jika ingin memeriksa apakah canvas sudah tidak kosong
            if (!signaturePad.isEmpty()) {
                console.log('Tanda tangan telah dibuat.');
            } else {
                console.log('Canvas kosong.');
            }
        }

        // Panggil updateButtonState setiap kali pengguna selesai menggambar tanda tangan
        signaturePad.onEnd = updateButtonState;

        // Tombol Clear Signature
        document.getElementById('clearSignature').addEventListener('click', () => {
            signaturePad.clear();
            updateButtonState();
        });
    </script>

    <!-- Script untuk mengganti logo header sesuai pilihan PT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ptDropdown = document.getElementById('ForminputState');
            const headerLogo = document.getElementById('headerLogo');

            ptDropdown.addEventListener('change', function() {
                const selectedPT = ptDropdown.value;

                if (selectedPT === "PT. Milenia Mega Mandiri") {
                    headerLogo.src = "{{ asset('assets/images/logo-milenia-2.png') }}";
                } else if (selectedPT === "PT. Mega Auto Prima") {
                    headerLogo.src = "{{ asset('assets/images/map-logo.png') }}";
                } else {
                    // Jika ingin mengembalikan ke logo default bila tidak ada pilihan yang cocok
                    headerLogo.src = "{{ asset('assets/images/logo-milenia.png') }}";
                }
            });
        });
    </script>

    {{-- Filepond --}}
    <script>
        // Inisialisasi setelah DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan semua plugin telah diregistrasi terlebih dahulu
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateSize,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileEncode
            );

            // Cari elemen input FilePond
            const fileInputs = document.querySelectorAll('.filepond');

            // Pastikan elemen input ditemukan sebelum melakukan inisialisasi
            if (fileInputs.length) {
                // Buat instance FilePond untuk setiap elemen yang ditemukan
                fileInputs.forEach(input => {
                    FilePond.create(input, {
                        instantUpload: false, // Menonaktifkan unggahan otomatis
                        allowMultiple: true // Jika mengizinkan beberapa file (sesuaikan kebutuhan)
                    });
                });
            }
        });
    </script>

    {{-- Icon Calendar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan flatpickr telah diinisialisasi
            const dateInput = document.getElementById('dateInput');
            const calendarIcon = document.querySelector('.input-group-text');

            // Jika menggunakan inisialisasi otomatis dengan data-provider, flatpickr instance akan tersimpan pada properti _flatpickr
            if (dateInput._flatpickr) {
                calendarIcon.addEventListener('click', function() {
                    dateInput._flatpickr.open();
                });
            } else {
                // Jika belum diinisialisasi, kamu bisa inisialisasi secara manual
                const fp = flatpickr(dateInput, {});
                calendarIcon.addEventListener('click', function() {
                    fp.open();
                });
            }
        });
    </script>


@endsection
