@extends('ga.layouts.app')

@section('title', 'Approval Purchase Order | Sistem Purchase Order General Affair')

@section('css')
    <style>
        .bg-milenia {
            background-color: #6f42c1;
        }

        .card-milenia {
            border-top: 4px solid #6f42c1;
        }

        .card-milenia:hover {
            box-shadow: 0 0 15px rgba(111, 66, 193, 0.2);
        }

        .btn-milenia {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }

        .btn-milenia:hover {
            background-color: #5e34b0;
            border-color: #5e34b0;
            color: white;
        }

        .card-milenia .card-header {
            background-color: rgba(111, 66, 193, 0.1);
            color: #6f42c1;
        }


        .bg-map {
            background-color: #0d6efd;
        }

        .card-map {
            border-top: 4px solid #0d6efd;
        }

        .card-map:hover {
            box-shadow: 0 0 15px rgba(66, 68, 193, 0.1);
        }

        .btn-map {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .btn-map:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        .card-map .card-header {
            background-color: rgba(66, 68, 193, 0.1);
            color: #0d6efd;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-milenia">
                <div class="card-header">
                    <div class="row align-items-center">
                        <!-- Konten Utama -->
                        <div class="col-12 col-md-9"> <!-- Full width di mobile, 9 columns di desktop -->
                            <h5 class="card-title mb-0">Table Status Approved (Purchase Order) Milenia</h5>
                            <div class="mt-3">
                                <h6>Tahapan Status:</h6>
                                <span class="badge bg-danger">Rejected</span>
                                <span class="badge bg-info">Waiting Approved by GA / Director</span>
                                <span class="badge bg-success">Accepted</span>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-12 col-md-3 text-center text-md-end mt-3 mt-md-0">
                            <!-- Full width di mobile, 3 columns di desktop -->
                            <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="img-fluid"
                                style="max-width: 120px" alt="Logo Milenia">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-po-milenia" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No PO</th>
                                <th>Cabang</th>
                                <th>Supplier</th>
                                <th>Barang</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Estimate Date</th>
                                <th>Total</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder as $key => $po)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $po->no_po }}</td>
                                    <td>{{ $po->cabang }}</td>
                                    <td>{{ $po->supplier }}</td>
                                    <td>
                                        @if ($po->barang->count() > 0)
                                            <ul>
                                                @foreach ($po->barang as $barang)
                                                    <li>{{ $barang->barang }} <span
                                                            class='badge text-bg-secondary'>{{ $barang->qty }}
                                                            ({{ $barang->unit }})
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ $po->remarks }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->date)->format('F d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->estimate_date)->format('F d, Y') }}</td>
                                    <td>{{ 'Rp. ' . number_format($po->total, 0, ',', '.') . ',-' }}</td>
                                    <td>{{ $po->nama_2 }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            {{ $po->status == 0 ? 'bg-danger' : '' }}
                                            {{ $po->status == 1 ? 'bg-info' : '' }}
                                            {{ $po->status == 2 ? 'bg-success' : '' }}">
                                            {{ $po->status == 0 ? 'Rejected' : ($po->status == 1 ? 'Waiting Approved by GA / Director' : 'Accepted') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-milenia me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailPO" data-id="{{ $po->id }}"
                                                data-pdf-url="{{ route('ga.po-generatePDFMilenia', $po->id) }}">
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-map">
                <div class="card-header">
                    <div class="row align-items-center">
                        <!-- Konten Utama -->
                        <div class="col-12 col-md-9"> <!-- Full width di mobile, 9 columns di desktop -->
                            <h5 class="card-title mb-0">Table Status Approved (Purchase Order) MAP</h5>
                            <div class="mt-3">
                                <h6>Tahapan Status:</h6>
                                <span class="badge bg-danger">Rejected</span>
                                <span class="badge bg-info">Waiting Approved by GA / Director</span>
                                <span class="badge bg-success">Accepted</span>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-12 col-md-3 text-center text-md-end mt-3 mt-md-0">
                            <!-- Full width di mobile, 3 columns di desktop -->
                            <img src="{{ asset('assets/images/map-logo.png') }}" class="img-fluid" style="max-width: 120px"
                                alt="Logo MAP">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-po-map" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No PO</th>
                                <th>Cabang</th>
                                <th>Supplier</th>
                                <th>Barang</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Estimate Date</th>
                                <th>Total</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrderMAP as $key => $poMAP)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $poMAP->no_po }}</td>
                                    <td>{{ $poMAP->cabang }}</td>
                                    <td>{{ $poMAP->supplier }}</td>
                                    <td>
                                        @if ($poMAP->barang->count() > 0)
                                            <ul>
                                                @foreach ($poMAP->barang as $barang)
                                                    <li>{{ $barang->barang }} <span
                                                            class='badge text-bg-secondary'>{{ $barang->qty }}
                                                            ({{ $barang->unit }})
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ $poMAP->remarks }}</td>
                                    <td>{{ \Carbon\Carbon::parse($poMAP->date)->format('F d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($poMAP->estimate_date)->format('F d, Y') }}</td>
                                    <td>{{ 'Rp. ' . number_format($poMAP->total, 0, ',', '.') . ',-' }}</td>
                                    <td>{{ $poMAP->nama_2 }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            {{ $poMAP->status == 0 ? 'bg-danger' : '' }}
                                            {{ $poMAP->status == 1 ? 'bg-info' : '' }}
                                            {{ $poMAP->status == 2 ? 'bg-success' : '' }}">
                                            {{ $poMAP->status == 0 ? 'Rejected' : ($poMAP->status == 1 ? 'Waiting Approved by GA / Director' : 'Accepted') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-map me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailPO" data-id="{{ $poMAP->id }}"
                                                data-pdf-url="{{ route('ga.po-generatePDFMAP', $poMAP->id) }}">
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
    <div id="modalDetailPO" class="modal fade" tabindex="-1" aria-labelledby="modalDetailPOLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailPOLabel">Detail Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src="" style="width: 100%; height: 700px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="rejectPO(event)">
                        Rejected
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" onclick="consoleApproved(this)"
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
                    <button id="saveSignature" class="btn btn-primary"
                        data-id="{{ isset($po) ? $po->id : (isset($poMAP) ? $poMAP->id : '') }}"
                        data-user-name="{{ Auth::user()->Nama }}">
                        <i class="ri-checkbox-circle-line"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- DataTable --}}
    <script>
        $(document).ready(function() {
            $('#table-po-milenia').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });

        $(document).ready(function() {
            $('#table-po-map').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Ambil semua tombol print yang membuka modal detail
            const printButtons = document.querySelectorAll('[data-bs-target="#modalDetailPO"]');

            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const poId = this.getAttribute("data-id");
                    const pdfUrl = this.getAttribute("data-pdf-url");
                    
                    let type = "";
                    if (pdfUrl.toLowerCase().includes("map")) {
                        console.log("Modal Detail is for MAP");
                        type = "map";
                    } else if (pdfUrl.toLowerCase().includes("milenia")) {
                        console.log("Modal Detail is for Milenia");
                        type = "milenia";
                    } else {
                        console.log("Modal Detail type unknown");
                        type = "unknown";
                    }

                    console.log("Print button clicked. ID:", poId);
                    console.log("PDF URL:", pdfUrl);
                    console.log("Type:", type); // Debug log untuk type

                    // Update PDF di modal detail
                    const pdfFrame = document.getElementById("pdfFrame");
                    pdfFrame.src = pdfUrl;

                    // Update tombol Rejected dan Approved di modal detail
                    const modalDetail = document.getElementById("modalDetailPO");
                    const rejectButton = modalDetail.querySelector("button.btn-danger");
                    const approvedButton = modalDetail.querySelector("button.btn-primary");

                    // Set data-id dan data-type pada tombol reject dan approved
                    rejectButton.setAttribute("data-id", poId);
                    rejectButton.setAttribute("data-type",
                    type); // Tambahkan data-type ke tombol reject

                    approvedButton.setAttribute("data-id", poId);
                });
            });
        });
    </script>

    <script>
        function consoleApproved(button) {
            const id = button.getAttribute('data-id');
            console.log("Approved button clicked. ID:", id);

            // Update tombol "Simpan" di modal tanda tangan dengan id yang sama
            const saveSignatureBtn = document.getElementById('saveSignature');
            saveSignatureBtn.setAttribute('data-id', id);
            console.log("SaveSignature button updated with ID:", saveSignatureBtn.getAttribute('data-id'));

            // Cek apakah modal detail untuk MAP atau Milenia berdasarkan URL di pdfFrame
            const pdfFrame = document.getElementById("pdfFrame");
            const pdfUrl = pdfFrame.src;
            console.log("PDF URL in modal:", pdfUrl);

            let type = "";
            if (pdfUrl.toLowerCase().includes("map")) {
                console.log("Modal Detail is for MAP");
                type = "map";
            } else if (pdfUrl.toLowerCase().includes("milenia")) {
                console.log("Modal Detail is for Milenia");
                type = "milenia";
            } else {
                console.log("Modal Detail type unknown");
                type = "unknown";
            }
            // Set data-type di tombol SaveSignature untuk dikirim ke server
            saveSignatureBtn.setAttribute('data-type', type);
            console.log("SaveSignature button updated with TYPE:", saveSignatureBtn.getAttribute('data-type'));
        }
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
        document.getElementById('saveSignature').addEventListener('click', (e) => {
            const canvas = document.getElementById('signatureCanvas');
            const signatureData = canvas.toDataURL('image/png');
            const prId = e.currentTarget.getAttribute('data-id');
            const userName = e.currentTarget.getAttribute('data-user-name');
            const type = e.currentTarget.getAttribute('data-type');

            // Debugging: Log all data before sending
            console.log("Data to be sent:");
            console.log("ID:", prId);
            console.log("TYPE:", type);
            console.log("Signature:", signatureData ? "[Signature Captured]" : "[No Signature]");
            console.log("User Name:", userName);

            if (!prId || !signatureData || !userName || !type) {
                console.error("Missing required data!");
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Lengkap!',
                    text: 'Harap pastikan semua data telah terisi sebelum mengirim tanda tangan.',
                    showConfirmButton: true
                });
                return;
            }

            fetch('{{ url('/ga/status-purchase-order/save-signature') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: prId,
                        signature: signatureData,
                        user_name: userName,
                        type: type
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

    {{-- Reject PO --}}
    <script>
        function rejectPO(event) {
            const purchaseOrderId = event.target.getAttribute('data-id');
            const type = event.target.getAttribute('data-type');

            console.log("ID:", purchaseOrderId, "Type:", type);

            fetch('{{ url('/ga/status-purchase-order/reject') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        id: purchaseOrderId,
                        type: type
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
                                const modalDetailPO = new bootstrap.Modal(document.getElementById(
                                    'modalDetailPO'));
                                modalDetailPO.hide();
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
