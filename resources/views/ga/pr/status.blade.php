@extends('ga.layouts.app')

@section('title', 'Purchase Request | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Table Status Approved (Purchase Request)</h5>
                    <div class="mt-3">
                        <div>Tahapan Status:</div>
                        <span class="badge bg-danger">Rejected</span>
                        <span class="badge bg-warning">Waiting Approved by Supervisor</span>
                        <span class="badge bg-primary">Waiting Purchase Order by Admin</span>
                        <span class="badge bg-info">Waiting Approved by GA / Director</span>
                        <span class="badge bg-success">Done</span>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-unit" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Barang</th>
                                <th>Divisi</th>
                                <th>No PR</th>
                                <th>PT</th>
                                <th>Kebutuhan</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseRequests as $key => $pr)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $pr->user_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pr->date_request)->format('d M, Y') }}</td>
                                    <td>
                                        @if (!empty($pr->barang_list))
                                            <ul>
                                                @foreach ($pr->barang_list as $barang)
                                                    <li>{!! $barang !!}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ $pr->divisi }}</td>
                                    <td>{{ $pr->no_pr }}</td>
                                    <td>{{ $pr->pt }}</td>
                                    <td>{{ $pr->important }}</td>
                                    <td>{{ $pr->remarks }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            {{ $pr->status == 0 ? 'bg-danger' : '' }}
                                            {{ $pr->status == 1 ? 'bg-warning' : '' }}
                                            {{ $pr->status == 2 ? 'bg-primary' : '' }}
                                            {{ $pr->status == 3 ? 'bg-info' : '' }}
                                            {{ $pr->status == 4 ? 'bg-success' : '' }}">
                                            {{ $pr->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailPR" data-id="{{ $pr->id }}"
                                                data-pdf-url="{{ route('ga.pr-generatePDF', $pr->id) }}">
                                                <i class="ri-printer-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="modalDetailPR" class="modal fade" tabindex="-1" aria-labelledby="modalDetailPRLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailPRLabel">Detail Purchase Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src="" style="width: 100%; height: 700px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    @if (isset($pr) && $pr)
                        <button type="button" class="btn btn-danger" onclick="rejectPR(event)"
                            data-id="{{ $pr->id }}">Rejected</button>
                    @else
                        <button type="button" class="btn btn-danger" disabled>Rejected</button>
                    @endif

                    @if (isset($pr) && $pr)
                        <button id="approvedSignature" type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-id="{{ $pr->id }}" data-bs-target="#modalTTD">Approved</button>
                    @else
                        <button type="button" class="btn btn-primary" disabled>Approved</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tanda Tangan -->
    <div id="modalTTD" class="modal fade" tabindex="-1" aria-labelledby="modalTTDLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTTDLabel">Silahkan Tanda Tangan Approved di sini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="signature-container">
                        <div class="d-flex align-items-center justify-content-center">
                            <canvas id="signatureCanvas" width="200" height="100"
                                style="border: 1px solid #ddd;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="clearSignature" class="btn btn-danger">
                        <i class="ri-delete-bin-2-line"></i> Hapus
                    </button>
                    @if (isset($pr) && $pr)
                        <button id="saveSignature" class="btn btn-primary" data-id="{{ $pr->id }}"
                            data-user-name="{{ Auth::user()->Nama }}"><i class="ri-checkbox-circle-line"></i>
                            Simpan</button>
                    @else
                        <button type="button" class="btn btn-primary" disabled>Simpan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- DataTable --}}
    <script>
        $(document).ready(function() {
            $('#table-unit').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true,
                order: [
                    [5, 'desc']
                ],
            });
        });
    </script>

    {{-- Frame PDF --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const printButtons = document.querySelectorAll('.btn-primary[data-bs-target="#modalDetailPR"]');
            const rejectButton = document.querySelector('#modalDetailPR .btn-danger');

            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const prId = this.getAttribute('data-id'); // Ambil ID dari tombol

                    // Update data-id pada tombol Reject
                    rejectButton.setAttribute('data-id', prId);

                    // Update URL PDF jika diperlukan
                    const pdfUrl = this.getAttribute('data-pdf-url');
                    const pdfFrame = document.getElementById('pdfFrame');
                    pdfFrame.src = pdfUrl;
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

        // Menambahkan event listener untuk tombol yang ada di dalam tabel
        document.querySelectorAll('button[data-bs-target="#modalDetailPR"]').forEach(button => {
            button.addEventListener('click', (event) => {
                // Pastikan kita mengambil elemen button yang benar, meskipun icon di dalamnya diklik
                const clickedButton = event.target.closest('button'); // Mengambil elemen button
                const prId = clickedButton.getAttribute('data-id'); // Ambil ID dari atribut data-id
                console.log("Tombol dengan ID:", prId, "diklik!");

                purchaseRequestId = prId;
            });
        });

        // Save Signature
        document.getElementById('saveSignature').addEventListener('click', () => {
            if (!purchaseRequestId) {
                alert('ID tidak valid, silakan coba lagi.');
                return;
            }

            const canvas = document.getElementById('signatureCanvas');
            const signatureData = canvas.toDataURL('image/png');
            const userName = event.target.getAttribute('data-user-name');

            console.log("Data to be sent:");
            console.log("ID:", purchaseRequestId);

            fetch('{{ url('/ga/status-purchase-request/save-signature') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: purchaseRequestId,
                        signature: signatureData,
                        user_name: userName,
                    }),
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
                                const modalTTD = new bootstrap.Modal(document.getElementById(
                                    'modalTTD'));
                                modalTTD.hide();
                                window.location.reload();
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
                        text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.',
                        showConfirmButton: true
                    });
                });
        });
    </script>

    {{-- Reject PR --}}
    <script>
        function rejectPR(event) {
            const purchaseRequestId = event.target.getAttribute('data-id');

            fetch('{{ url('/ga/status-purchase-request/reject') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: purchaseRequestId
                    }),
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
                                const modalDetailPR = new bootstrap.Modal(document.getElementById(
                                    'modalDetailPR'));
                                modalDetailPR.hide();
                                window.location.reload();
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
                        text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.',
                        showConfirmButton: true
                    });
                });
        }
    </script>


@endsection
