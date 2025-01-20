@extends('admin.layouts.app')

@section('title', 'Employee | Sistem Purchase Order General Affair')

@section('css')
    <style>
        .loading-sync {
            position: fixed;
            bottom: 20px;
            /* Jarak dari bawah layar */
            right: 20px;
            /* Jarak dari kanan layar */
            z-index: 1050;
            /* Supaya elemen berada di atas elemen lain */
            width: 300px;
            /* Lebar kotak progress */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Tambahkan efek bayangan */
            border-radius: 8px;
            /* Membuat sudut lebih melengkung */
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Employee Data Milenia Group</h5>
                    <button id="syncBtn" type="button" class="btn btn-primary btn-label rounded-pill"><i
                            class="ri-refresh-line label-icon align-middle rounded-pill fs-16 me-2"></i> Sync</button>
                </div>
                <div class="card-body">
                    <table id="table-employee" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 1%">No</th>
                                <th class="text-center">ID</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Jabatan</th>
                                <th class="text-center">Cabang</th>
                                <th class="text-center">Divisi</th>
                                <th class="text-center">Golongan</th>
                                <th class="text-center">Status Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $index => $employee)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $employee->ID }}</td>
                                    <td class="text-center">
                                        @php
                                            $formattedFoto = str_pad($employee->ID, 5, '0', STR_PAD_LEFT);
                                            $fotoUrl = "http://192.168.0.8/hrd-milenia/foto/{$formattedFoto}.JPG";
                                        @endphp
                                        <div class="avatar">
                                            <img src="{{ $fotoUrl }}" class="avatar-img rounded"
                                                alt="Foto {{ $employee->Nama }}" width="100" height="100">
                                        </div>
                                    </td>
                                    <td>{{ $employee->Nama }}</td>
                                    <td>{{ $employee->Jabatan }}</td>
                                    <td>{{ $employee->Cabang }}</td>
                                    <td>{{ $employee->Divisi }}</td>
                                    <td>{{ $employee->Golongan }}</td>
                                    <td><span
                                            class="badge {{ $employee->statuskar == 'MAP' ? 'bg-primary' : 'bg-info' }}">{{ $employee->statuskar }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="loadingSync" class="card bg-light overflow-hidden loading-sync" style="display: none;">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <h6 class="mb-0"><b id="syncPercent" class="text-danger">0%</b> Update in progress...</h6>
                </div>
                <div class="flex-shrink-0">
                    <h6 class="mb-0" id="timeLeft">0s left</h6>
                </div>
            </div>
        </div>
        <div class="progress bg-danger-subtle rounded-0">
            <div id="progressBar" class="progress-bar bg-danger" role="progressbar" style="width: 0%" aria-valuenow="0"
                aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#table-employee').DataTable({
                responsive: true,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    <script>
        $('#syncBtn').click(function() {
            // Menampilkan elemen loading sync
            $('#loadingSync').show();

            // Menginisialisasi variabel
            var totalTime = 10; // Total waktu dalam detik (misalnya 25 detik)
            var elapsedTime = 0; // Waktu yang telah berlalu
            var progress = 0; // Progress bar mulai dari 0%
            var interval = setInterval(function() {
                elapsedTime++;
                progress = Math.min(100, (elapsedTime / totalTime) * 100); // Menghitung persentase progress

                // Mengupdate progress bar, persen, dan detik yang tersisa
                $('#progressBar').css('width', progress + '%');
                $('#syncPercent').text(Math.round(progress) + '%');
                $('#timeLeft').text((totalTime - elapsedTime) + 's left');

                // Jika progress mencapai 100%, hentikan interval
                if (elapsedTime >= totalTime) {
                    clearInterval(interval);
                }
            }, 1000); // Interval 1 detik

            // Melakukan AJAX request untuk sinkronisasi data
            $.ajax({
                url: '{{ url('/admin/syncEmployee') }}', // Menggunakan helper URL untuk membuat URL dinamis
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    // Menyembunyikan elemen loading setelah request selesai
                    $('#loadingSync').hide();
                    location.reload(); // Memuat ulang halaman untuk menampilkan data yang baru
                },
                error: function(xhr, status, error) {
                    // Menyembunyikan elemen loading jika terjadi error
                    $('#loadingSync').hide();
                    alert('Terjadi kesalahan saat sinkronisasi data');
                }
            });
        });
    </script>
@endsection
