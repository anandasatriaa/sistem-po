@extends('user.layouts.app')

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
                                                data-bs-target="#modalDetailPR" data-pdf-url="{{ route('user.pr-generatePDF', $pr->id) }}">
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
            $('#table-unit').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true,
                order: [[5, 'desc']],
            });
        });
    </script>

    {{-- Frame PDF --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Temukan semua tombol "Print"
            const printButtons = document.querySelectorAll('.btn-primary[data-bs-target="#modalDetailPR"]');

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
