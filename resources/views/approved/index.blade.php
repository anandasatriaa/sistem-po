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
    <div class="mx-2 mt-2 mb-5 bg-white rounded-3 shadow-lg p-4">
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
            <div class="card-body p-0 text-center">
                <div id="pdf-container" style="height:55vh; overflow: auto; border: none;"></div>
            </div>
        </div>

        <!-- Lampiran Preview Card -->
        <div class="card border-secondary mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="ri-attachment-line"></i> Lampiran
            </div>
            <div class="card-body p-0 text-center" id="attachment-container"
                style="height:55vh; overflow: auto; border: none;">
                @if ($pr->lampiran->count() > 0)
                    @foreach ($pr->lampiran as $lampiran)
                        @php
                            $extension = strtolower(pathinfo($lampiran->file_path, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                            <div class="attachment-image mb-3">
                                <img src="{{ asset('storage/' . $lampiran->file_path) }}" alt="Lampiran Image"
                                    width="50%">
                            </div>
                        @elseif($extension === 'pdf')
                            <div class="attachment-pdf mb-3" data-url="{{ asset('storage/' . $lampiran->file_path) }}">
                                <!-- PDF attachment akan dirender ke dalam canvas oleh pdf.js -->
                                <p class="text-muted">Memuat lampiran PDF...</p>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p>Tidak ada lampiran.</p>
                @endif
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
            // Ambil user yang mengajukan PR
            $creator = \App\Models\User::find($pr->user_id);

            // Cari data atasan berdasarkan email karyawan yang sesuai dengan email atasan user yang mengajukan PR
            $atasan = $creator
                ? \App\Models\User::where('email_karyawan', $creator->email_atasan)->where('Aktif', 1)->first()
                : null;
        @endphp

        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="ri-user-line"></i> Penanda Tangan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="namaPenandatangan" class="form-label fw-bold">Nama Penanggung Jawab:</label>
                    <input type="text" id="namaPenandatangan" name="nama" class="form-control form-control-lg"
                        value="{{ $atasan ? $atasan->Nama : '' }}">
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
                // Ambil data tanda tangan dari canvas sebagai data URL menggunakan SignaturePad
                var signatureData = signaturePad.toDataURL();
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

    <!-- Sertakan pdf.js dari CDN dengan versi yang sama -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
    <script>
        // Set workerSrc agar cocok dengan versi pdf.js yang digunakan
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.worker.min.js';

        // Render PDF utama ke dalam #pdf-container (kode ini tetap sama)
        const mainPdfUrl = "{{ url('/pdf-view/' . $pr->id) }}";
        pdfjsLib.getDocument(mainPdfUrl).promise.then(function(pdf) {
            const container = document.getElementById('pdf-container');
            container.innerHTML = ''; // Bersihkan container
            // Render seluruh halaman PDF utama
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(function(page) {
                    const scale = 1.5;
                    const viewport = page.getViewport({
                        scale: scale
                    });
                    const canvas = document.createElement("canvas");
                    canvas.className = "pdf-page";
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    container.appendChild(canvas);
                    const context = canvas.getContext('2d');
                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                });
            }
        }).catch(function(error) {
            console.error('Error saat memuat PDF utama: ', error);
        });

        // Render PDF attachment (jika ada) ke dalam div attachment masing-masing
        document.querySelectorAll('.attachment-pdf').forEach(function(div) {
            const url = div.getAttribute('data-url');
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                // Bersihkan container attachment (hapus teks placeholder)
                div.innerHTML = '';

                // Untuk membuat thumbnail, tentukan lebar yang diinginkan (misalnya 100px)
                const desiredWidth = 900;

                // Loop untuk merender semua halaman lampiran PDF
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        // Dapatkan viewport default pada scale=1 untuk menghitung ukuran asli
                        const defaultViewport = page.getViewport({
                            scale: 1
                        });
                        // Hitung skala agar lebar canvas sama dengan desiredWidth
                        const scale = desiredWidth / defaultViewport.width;
                        const viewport = page.getViewport({
                            scale: scale
                        });

                        // Buat canvas untuk thumbnail halaman ini
                        const canvas = document.createElement("canvas");
                        canvas.className = "pdf-attachment-page";
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        // Bungkus canvas dalam div kecil agar bisa diberi margin
                        const canvasWrapper = document.createElement("div");
                        canvasWrapper.style.display = "inline-block";
                        canvasWrapper.style.margin = "5px";
                        canvasWrapper.appendChild(canvas);

                        // Tambahkan wrapper ke container attachment
                        div.appendChild(canvasWrapper);

                        // Render halaman ke canvas
                        const context = canvas.getContext('2d');
                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });
                    });
                }
            }).catch(function(error) {
                console.error('Error saat memuat lampiran PDF: ', error);
                div.innerHTML = '<p class="text-danger">Gagal memuat lampiran PDF.</p>';
            });
        });
    </script>



</body>

</html>
