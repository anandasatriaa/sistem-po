<!DOCTYPE html>
<html>

<head>
    <title>Approved Purchase Request | Sistem Purchase Order General Affair</title>
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
    <div class="container mt-4 bg-white rounded-3 shadow-lg p-4">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">Approved Purchase Request</h1>
            <div class="border-bottom border-2 border-primary w-25 mx-auto my-3"></div>
        </div>

        <!-- PDF Preview Card -->
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white">
                <i class="ri-file-pdf-line"></i> Preview Dokumen
            </div>
            <div class="card-body p-0">
                <iframe src="{{ url('/pdf-view/' . $pr->id) }}" class="w-100" style="height: 75vh; border: none"
                    title="PDF Viewer">
                </iframe>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <i class="ri-quill-pen-line"></i> Area Tanda Tangan
            </div>
            <div class="card-body">
                <div class="signature-container">
                    <div class="mb-3">
                        <label class="fw-bold text-danger">Tanda Tangan:</label>
                        <p class="text-muted small mb-1">
                            <i class="ri-alert-line"></i> Geser jari/mouse untuk membuat tanda tangan di area berikut
                        </p>
                    </div>

                    <div class="d-flex align-items-center">
                        <canvas id="signatureCanvas" class="border rounded bg-light"
                            style="border: 2px dashed #dc3545!important" width="200" height="100">
                        </canvas>
                        <button id="clearSignature" class="btn btn-danger ms-3" style="height: fit-content">
                            <i class="ri-delete-bin-5-line"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Name Selection -->
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="ri-user-line"></i> Pilih Penanda Tangan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nameDropdown" class="form-label fw-bold">Nama Penanggung Jawab:</label>
                    <select name="nama" id="nameDropdown" class="form-select form-select-lg select2" required>
                        <option value="" disabled selected>Silahkan pilih nama...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->Nama }}">{{ $user->Nama }} -
                                {{ $user->Jabatan ?? 'Tidak Ada Jabatan' }}</option>
                        @endforeach
                    </select>
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

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#nameDropdown').select2({
                placeholder: "Silahkan pilih nama...",
                allowClear: true,
                width: '100%'
            });

            // Inisialisasi custom SignaturePad dengan canvas native
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let isSigned = false; // flag untuk mengecek apakah sudah ada tanda tangan

            // Fungsi untuk meng-update status tombol Approved
            function updateButtonState() {
                var selectedName = $('#nameDropdown').val();
                if (isSigned && selectedName) {
                    $('#btnApproved').prop('disabled', false);
                } else {
                    $('#btnApproved').prop('disabled', true);
                }
            }

            // Event listener untuk menggambar tanda tangan
            canvas.addEventListener('mousedown', () => {
                isDrawing = true;
                isSigned = true; // mulai menggambar → tandai sudah ada tanda tangan
                ctx.beginPath();
            });

            canvas.addEventListener('mousemove', (event) => {
                if (isDrawing) {
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;
                    ctx.lineTo(x, y);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', () => {
                isDrawing = false;
                ctx.closePath();
                updateButtonState();
            });

            canvas.addEventListener('mouseout', () => {
                isDrawing = false;
                ctx.closePath();
                updateButtonState();
            });

            // Tombol Clear Signature
            document.getElementById('clearSignature').addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                isSigned = false;
                updateButtonState();
            });

            // Update tombol saat dropdown berubah
            $('#nameDropdown').on('change', function() {
                updateButtonState();
            });

            // Event klik tombol Approved
            $('#btnApproved').on('click', function() {
                // Ambil data tanda tangan dari canvas sebagai data URL
                var signatureData = canvas.toDataURL();
                var userName = $('#nameDropdown').val();
                var prId = {{ $pr->id }};

                $.ajax({
                    url: '{{ route('pr.approved') }}',
                    type: 'POST',
                    data: {
                        id: prId,
                        signature: signatureData,
                        user_name: userName,
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
                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah anda yakin untuk menolak Purchase Request ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var prId = {{ $pr->id }};
                        $.ajax({
                            url: '{{ route('pr.rejected') }}',
                            type: 'POST',
                            data: {
                                id: prId,
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
</body>

</html>
