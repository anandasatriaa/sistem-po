@extends('admin.layouts.app')

@section('title', 'Status Purchase Order Milenia | Sistem Purchase Order General Affair')

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
                    <table id="table-status-po-milenia" class="table table-bordered table-striped align-middle"
                        style="width:100%">
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
                            @foreach ($purchaseOrders as $key => $po)
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
                                    <td>{{ \Carbon\Carbon::parse($po->date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->estimate_date)->format('d M, Y') }}</td>
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
                                                data-bs-target="#modalDetailPO" data-pdf-url="{{ route('admin.po-generatePDFMilenia', $po->id) }}">
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
            $('#table-status-po-milenia').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true,
                order: [[1, 'desc']],
            });
        });
    </script>

    {{-- Frame PDF --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Temukan semua tombol "Print"
            const printButtons = document.querySelectorAll('.btn-milenia[data-bs-target="#modalDetailPO"]');

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

@endsection
