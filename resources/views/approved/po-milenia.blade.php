<!DOCTYPE html>
<html>

<head>
    <title>Approved Purchase Order | Sistem Purchase Order General Affair</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- (Opsional) Memuat Bootstrap untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Tambahkan Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
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

        .card-header {
            font-size: 1.1rem;
        }

        .form-select-lg {
            border: 2px solid #198754;
        }
    </style>
</head>

<body>
    <div class="mx-2 mt-2 bg-white rounded-3 shadow-lg p-4">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">Approved Purchase Order Milenia</h1>
            <img src="{{ asset('assets/images/logo-milenia-2.png') }}" alt="" width="100px">
            <div class="border-bottom border-2 border-primary w-25 mx-auto my-3"></div>
        </div>

        <!-- PDF Preview Card -->
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white">
                <i class="ri-file-pdf-line"></i> Preview Dokumen
            </div>
            <div class="card-body p-0 text-center">
                <div id="pdf-container" style="height:55vh; overflow: auto; border: none;"></div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <i class="ri-quill-pen-line"></i> Area Tanda Tangan
            </div>
            <div class="card-body">
                <div class="signature-container text-center">
                    <div class="mb-3">
                        <label class="fw-bold text-danger">Tanda Tangan:</label>
                        <p class="text-muted small mb-1">
                            <i class="ri-alert-line"></i> Geser jari/mouse untuk membuat tanda tangan di area berikut
                        </p>
                    </div>
                    <!-- Canvas ditampilkan di tengah -->
                    <canvas id="signatureCanvas" class="border rounded bg-light d-block mx-auto"
                        style="border: 2px dashed #dc3545!important" width="200" height="100">
                    </canvas>
                    <!-- Tombol Clear berada di bawah canvas -->
                    <div class="mt-3">
                        <button id="clearSignature" class="btn btn-danger">
                            <i class="ri-delete-bin-5-line"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Ambil user dengan jabatan "SPV GA" dan status aktif = 1
            $spvGA = \App\Models\User::where('Jabatan', 'SPV GA')->where('Aktif', 1)->first();
        @endphp

        <!-- Name Selection -->
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="ri-user-line"></i> Penanda Tangan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nameInput" class="form-label fw-bold">Nama Penanggung Jawab:</label>
                    <input type="text" id="nameInput" name="nama" class="form-control form-control-lg"
                        value="{{ $spvGA ? $spvGA->Nama : '' }}">
                </div>
            </div>
        </div>

        <!-- Tombol Approved & Rejected -->
        <div class="mt-4 text-center">
            <button id="btnApproved" class="btn btn-success me-3" disabled>Approved</button>
            <button id="btnRejected" class="btn btn-danger">Rejected</button>
        </div>
    </div>

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- Tambahkan Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Library Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@5.0.4/dist/signature_pad.umd.min.js"></script>

    <!-- Sertakan PDF.js dan worker-nya -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    </script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 (tetap sama)
            $('#nameDropdown').select2({
                placeholder: "Silahkan pilih nama...",
                allowClear: true,
                width: '100%'
            });

            // Inisialisasi SignaturePad (menggantikan kode event manual)
            const canvas = document.getElementById('signatureCanvas');
            // Pastikan canvas telah disesuaikan ukurannya (opsional)
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)' // atau bisa diset ke 'white'
            });

            // Fungsi untuk mengupdate status tombol Approved
            function updateButtonState() {
                var selectedName = $('#nameDropdown').val();
                // Gunakan method isEmpty() dari SignaturePad untuk cek apakah sudah ada tanda tangan
                if (!signaturePad.isEmpty() && selectedName) {
                    $('#btnApproved').prop('disabled', false);
                } else {
                    $('#btnApproved').prop('disabled', true);
                }
            }

            // Panggil updateButtonState setiap kali pengguna selesai menggambar tanda tangan
            signaturePad.onEnd = updateButtonState;

            // Tombol Clear Signature
            document.getElementById('clearSignature').addEventListener('click', () => {
                signaturePad.clear();
                updateButtonState();
            });

            // Update tombol saat dropdown berubah
            $('#nameDropdown').on('change', function() {
                updateButtonState();
            });

            // Event klik tombol Approved
            $('#btnApproved').on('click', function() {
                // Ambil data tanda tangan dari canvas sebagai data URL
                var signatureData = signaturePad.toDataURL();
                var userName = $('#nameDropdown').val();
                // Ambil jabatan dari option yang dipilih
                var jabatan = $('#nameDropdown option:selected').data('jabatan');
                var poId = {{ $po->id }};

                $.ajax({
                    url: '{{ route('po.approved') }}',
                    type: 'POST',
                    data: {
                        id: poId,
                        signature: signatureData,
                        user_name: userName,
                        jabatan: jabatan,
                        type: 'milenia',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success').then(function() {
                                location.reload(); // Atau redirect sesuai kebutuhan
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                    }
                });
            });

            // Event klik tombol Rejected
            $('#btnRejected').on('click', function() {
                // Cek apakah dropdown nama sudah terisi
                var selectedName = $('#nameDropdown').val();
                if (!selectedName) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Nama penanggung jawab (yang menolak) harus diisi!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                // Jika sudah terisi, tampilkan konfirmasi
                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah anda yakin untuk menolak Purchase Request ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ambil jabatan dari option yang dipilih
                        var jabatan = $('#nameDropdown option:selected').data('jabatan');
                        var poId = {{ $po->id }};
                        $.ajax({
                            url: '{{ route('po.rejected') }}',
                            type: 'POST',
                            data: {
                                id: poId,
                                jabatan: jabatan,
                                type: 'milenia',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Success', response.message, 'success')
                                        .then(function() {
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Terjadi kesalahan pada server',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        // URL PDF yang akan ditampilkan (disesuaikan dengan route Anda)
        const url = "{{ url('/pdf-view-milenia/' . $po->id) }}";

        // Fungsi untuk merender halaman PDF ke dalam canvas
        function renderPage(page) {
            const scale = 1.5;
            const viewport = page.getViewport({
                scale: scale
            });

            // Buat canvas untuk halaman PDF
            const canvas = document.createElement("canvas");
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            document.getElementById('pdf-container').appendChild(canvas);

            // Render halaman ke canvas
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext);
        }

        // Memuat dokumen PDF
        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            // Render halaman pertama (Anda dapat mengubah ini untuk merender semua halaman)
            pdf.getPage(1).then(renderPage);
        }).catch(function(error) {
            console.error('Error saat memuat PDF: ', error);
        });
    </script>

</body>

</html>
