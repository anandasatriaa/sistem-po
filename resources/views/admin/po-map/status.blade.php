@extends('admin.layouts.app')

@section('title', 'Status Purchase Order MAP | Sistem Purchase Order General Affair')

@section('css')
    <style>
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
                                <span class="badge bg-primary">Submission Cost / Purchase Stuff</span>
                                <span class="badge bg-success">Done</span>
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
                    <table id="table-status-po-map" class="table table-bordered table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Done</th>
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
                            @foreach ($purchaseOrders as $key => $po)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <div class="form-check text-center">
                                            <input type="checkbox" class="form-check-input done-checkbox"
                                                data-id="{{ $po->id }}"
                                                {{ $po->status == 3 ? 'checked' : ($po->status == 2 ? '' : 'disabled') }}>
                                        </div>
                                    </td>
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
                                        <span id="badge-{{ $po->id }}"
                                            class="badge 
                                            {{ $po->status == 0 ? 'bg-danger' : '' }}
                                            {{ $po->status == 1 ? 'bg-info' : '' }}
                                            {{ $po->status == 2 ? 'bg-primary' : '' }}
                                            {{ $po->status == 3 ? 'bg-success' : '' }}">
                                            {{ $po->status == 0
                                                ? 'Rejected'
                                                : ($po->status == 1
                                                    ? 'Waiting Approved by GA / Director'
                                                    : ($po->status == 2
                                                        ? 'Submission Cost / Purchase Stuff'
                                                        : ($po->status == 3
                                                            ? 'Done'
                                                            : ''))) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" class="btn btn-map me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailPO"
                                                data-pdf-url="{{ route('admin.po-generatePDFMap', $po->id) }}">
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
            $('#table-status-po-map').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true,
                order: [
                    [1, 'desc']
                ],
            });
        });
    </script>

    {{-- Frame PDF --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Temukan semua tombol "Print"
            const printButtons = document.querySelectorAll('.btn-map[data-bs-target="#modalDetailPO"]');

            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Ambil URL file PDF dari atribut data-url
                    const pdfUrl = this.getAttribute('data-pdf-url');

                    // Setel URL ke iframe dalam modal
                    const pdfFrame = document.getElementById('pdfFrame');
                    pdfFrame.src = pdfUrl;
                });
            });
        });
    </script>

    <script>
        function getStatusColor(status) {
            const colors = {
                0: 'danger',
                1: 'info',
                2: 'primary',
                3: 'success'
            };
            return colors[status] || 'secondary';
        }

        function getStatusLabel(status) {
            const labels = {
                0: 'Rejected',
                1: 'Waiting Approved by GA / Director',
                2: 'Submission Cost / Purchase Stuff',
                3: 'Done'
            };
            return labels[status] || 'Unknown';
        }

        // Tambahkan event listener untuk setiap checkbox dengan kelas .done-checkbox
        document.querySelectorAll('.done-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const poId = this.getAttribute('data-id');
                // Jika diceklis, newStatus = 3; jika tidak diceklis, newStatus = 2.
                const newStatus = this.checked ? 3 : 2;

                // Kirim request untuk mengupdate status PO
                fetch("{{ route('admin.updatestatuspo-map') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: poId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge status menggunakan fungsi getStatusColor dan getStatusLabel
                            const badge = document.getElementById(`badge-${poId}`);
                            if (badge) {
                                badge.className = `badge bg-${getStatusColor(newStatus)}`;
                                badge.textContent = getStatusLabel(newStatus);
                            }
                        }
                    })
            });
        });
    </script>

@endsection
