@extends('admin.layouts.app')

@section('title', 'Purchase Request | Sistem Purchase Order General Affair')

@section('css')
    <style>
        .card-map {
            border-top: 4px solid #0d6efd;
        }

        .card-map:hover {
            box-shadow: 0 0 15px rgba(13, 110, 253, 0.2);
        }

        .btn-map {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .btn-map:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .card-map .card-header {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-map">
                <div class="card-header">
                    <div class="row align-items-center">
                        <!-- Konten Utama -->
                        <div class="col-12 col-md-9"> <!-- Full width di mobile, 9 columns di desktop -->
                            <h5 class="card-title mb-0">Table Status Approved (Purchase Request) MAP</h5>
                            <div class="mt-3">
                                <h6>Tahapan Status:</h6>
                                <span class="badge bg-danger">Rejected</span>
                                <span class="badge bg-warning">Waiting Approved by Supervisor</span>
                                <span class="badge bg-primary">Waiting Purchase Order by Admin</span>
                                <span class="badge bg-info">Waiting Approved by GA / Director</span>
                                <span class="badge bg-success">Done</span>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-12 col-md-3 text-center text-md-end mt-3 mt-md-0">
                            <!-- Full width di mobile, 3 columns di desktop -->
                            <img src="{{ asset('assets/images/map-logo.png') }}" class="img-fluid"
                                style="max-width: 120px" alt="Logo Milenia">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-pr-map" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>PO</th>
                                <th>Done</th>
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
                            @foreach ($purchaseRequests as $pr)
                                <tr>
                                    <td>
                                        <div class="form-check text-center">
                                            <input type="checkbox" class="form-check-input po-checkbox"
                                                data-id="{{ $pr->id }}" {{ $pr->status >= 3 ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check text-center">
                                            <input type="checkbox" class="form-check-input done-checkbox"
                                                data-id="{{ $pr->id }}" {{ $pr->status == 4 ? 'checked' : '' }}
                                                {{ $pr->status < 3 ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pr->user->Nama ?? '-' }}</td>
                                    <td>{{ $pr->date_request }}</td>
                                    <td>
                                        @if ($pr->barang->count() > 0)
                                            <ul>
                                                @foreach ($pr->barang as $barang)
                                                    <li>{{ $barang->nama_barang }} <span
                                                            class='badge text-bg-secondary'>{{ $barang->quantity }}
                                                            ({{ $barang->unit }})
                                                        </span></li>
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
                                        <span id="badge-{{ $pr->id }}"
                                            class="badge 
                                            {{ $pr->status == 0 ? 'bg-danger' : '' }}
                                            {{ $pr->status == 1 ? 'bg-warning' : '' }}
                                            {{ $pr->status == 2 ? 'bg-primary' : '' }}
                                            {{ $pr->status == 3 ? 'bg-info' : '' }}
                                            {{ $pr->status == 4 ? 'bg-success' : '' }}">
                                            {{ $statusLabels[$pr->status] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-map me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailPRMAP" data-id="{{ $pr->id }}"
                                                data-pdf-url="{{ route('admin.pr-generatePDFMAP', $pr->id) }}">
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
    <div id="modalDetailPRMAP" class="modal fade" tabindex="-1" aria-labelledby="modalDetailPRMAPLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailPRMAPLabel">Detail Purchase Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src="" style="width: 100%; height: 800px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- DataTable --}}
    <script>
        $(document).ready(function() {
            $('#table-pr-map').DataTable({
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
            const modalDetailPRMAP = document.getElementById('modalDetailPRMAP');
            const pdfFrame = document.getElementById('pdfFrame');

            // Event ketika modal ditampilkan
            modalDetailPRMAP.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // Tombol yang memicu modal
                const pdfUrl = button.getAttribute('data-pdf-url');

                // Set iframe source
                pdfFrame.src = pdfUrl;
            });

            // Reset iframe saat modal ditutup
            modalDetailPRMAP.addEventListener('hidden.bs.modal', function() {
                pdfFrame.src = '';
            });
        });
    </script>

        {{-- Checkbox PO & Done --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle PO Checkbox
            document.querySelectorAll('.po-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const prId = this.dataset.id;
                    const newStatus = this.checked ? 3 : 2;

                    // Update status via AJAX
                    updateStatus(prId, newStatus).then(() => {
                        // Enable/disable Done checkbox
                        const doneCheckbox = this.closest('tr').querySelector(
                            '.done-checkbox');
                        doneCheckbox.disabled = !this.checked;

                        // Jika PO diuncheck, uncheck Done juga
                        if (!this.checked) {
                            doneCheckbox.checked = false;
                        }
                    });
                });
            });

            // Handle Done Checkbox
            document.querySelectorAll('.done-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const prId = this.dataset.id;
                    const newStatus = this.checked ? 4 : 3;

                    // Update status via AJAX
                    updateStatus(prId, newStatus);
                });
            });

            function updateStatus(prId, status) {
                return fetch('{{ url('/admin/status-purchase-request-map/update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: prId,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge status
                            const badge = document.getElementById(`badge-${prId}`);
                            badge.className = `badge bg-${getStatusColor(status)}`;
                            badge.textContent = getStatusLabel(status);
                        }
                    });
            }

            function getStatusColor(status) {
                const colors = {
                    0: 'danger',
                    1: 'warning',
                    2: 'primary',
                    3: 'info',
                    4: 'success'
                };
                return colors[status] || 'secondary';
            }

            function getStatusLabel(status) {
                const labels = {
                    0: 'Rejected',
                    1: 'Waiting Approved by Supervisor',
                    2: 'Waiting Purchase Order by Admin',
                    3: 'Waiting Approved by GA / Director',
                    4: 'Done'
                };
                return labels[status] || 'Unknown';
            }
        });
    </script>


@endsection
