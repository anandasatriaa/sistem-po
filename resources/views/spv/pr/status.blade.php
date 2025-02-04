@extends('spv.layouts.app')

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
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseRequests as $key => $pr)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $pr->user_name }}</td>
                                    <td>{{ $pr->date_request }}</td>
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
                                                data-pdf-url="{{ route('spv.pr-generatePDF', $pr->id) }}">
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
                    <button type="button" class="btn btn-danger" onclick="rejectPR(event)"
                        data-id="{{ $pr->id }}">Rejected</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTTD">Approved</button>
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
                    <button id="saveSignature" class="btn btn-primary" data-id="{{ $pr->id }}"
                        data-user-name="{{ Auth::user()->Nama }}"><i class="ri-checkbox-circle-line"></i>
                        Simpan</button>
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
                info: true
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
        // Setup Canvas for Signature
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;

        canvas.addEventListener('mousedown', () => {
            isDrawing = true;
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
        });

        canvas.addEventListener('mouseout', () => {
            isDrawing = false;
        });

        // Clear Signature
        document.getElementById('clearSignature').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Save Signature
        document.getElementById('saveSignature').addEventListener('click', () => {
            const canvas = document.getElementById('signatureCanvas');
            const signatureData = canvas.toDataURL('image/png');
            const prId = event.target.getAttribute('data-id');
            const userName = event.target.getAttribute('data-user-name');

            fetch('{{ url('/spv/status-purchase-request/save-signature') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: prId,
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

            fetch('{{ url('/spv/status-purchase-request/reject') }}', {
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
