@extends('admin.layouts.app')

@section('title', 'Barang | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Table Barang</h5>
                        {{-- <form action="{{ route('admin.barang-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Upload CSV</label>
                                <input type="file" name="file" id="file" class="form-control" accept=".csv"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Import</button>
                        </form> --}}
                        <a href="" data-bs-toggle="modal" data-bs-target="#modalTambahBarang"
                            class="btn btn-primary"><i class="ri-add-circle-line me-2"></i>Tambah Barang</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-barang" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 15px">No</th>
                                <th style="width: 100px">Kode</th>
                                <th>Nama</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $index => $barang)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $barang->kode }}</td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Edit yang membuka modal dan mengisi data -->
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalUpdateBarang" data-id="{{ $barang->id }}"
                                                data-kode="{{ $barang->kode }}" data-nama="{{ $barang->nama }}">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.barang-destroy', $barang->id) }}"
                                                method="POST" style="display:inline-block;" class="delete-barang-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger delete-button">
                                                    <i class="ri-delete-bin-fill"></i>
                                                </button>
                                            </form>
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

    <!-- Modal Tambah Barang -->
    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="modalTambahBarang" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahBarang">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formTambahBarang">
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodebarang" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodebarang" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namabarang" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control" id="namabarang" name="namabarang"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Barang -->
    <div class="modal fade" id="modalUpdateBarang" tabindex="-1" aria-labelledby="modalUpdateBarang" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateBarang">Update Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formUpdateBarang" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodebarangupdate" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodebarangupdate" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namabarangupdate" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control" id="namabarangupdate"
                                        name="namabarangupdate" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    {{-- Datatable --}}
    <script>
        $(document).ready(function() {
            $('#table-barang').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    {{-- ID Barang --}}
    <script>
        document.getElementById('modalTambahBarang').addEventListener('show.bs.modal', function() {
            fetch('{{ url('admin/barang/last-id') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kodebarang').value = data.kode;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    {{-- POST Barang --}}
    <script>
        document.getElementById('formTambahBarang').addEventListener('submit', function(e) {
            e.preventDefault();

            const kode = document.getElementById('kodebarang').value;
            const nama = document.getElementById('namabarang').value;

            fetch('{{ url('/admin/barang/store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        namabarang: nama
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
                                window.location.reload();
                            }
                        });

                        // Close the modal
                        $('#modalTambahBarang').modal('hide');

                        // Reset the form
                        document.getElementById('formTambahBarang').reset();
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

    {{-- UPDATE Barang --}}
    <script>
        // Handle the modal data population
        $('#modalUpdateBarang').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var barangId = button.data('id'); // Extract the data-id
            var barangKode = button.data('kode'); // Extract the data-kode
            var barangNama = button.data('nama'); // Extract the data-nama

            // Populate the modal with the barang data
            var modal = $(this);
            modal.find('#kodebarangupdate').val(barangKode); // Set the Kode
            modal.find('#namabarangupdate').val(barangNama); // Set the Nama

            // Set the form action URL using the url() helper in the blade template
            var updateUrl = '{{ url('/admin/barang/update') }}/' + barangId;
            modal.find('#formUpdateBarang').attr('action', updateUrl); // Set the form action URL
        });

        // Handle the form submission for updating barang
        document.getElementById('formUpdateBarang').addEventListener('submit', function(e) {
            e.preventDefault();

            const barangId = document.getElementById('kodebarangupdate').value;
            const nama = document.getElementById('namabarangupdate').value;

            fetch('{{ url('/admin/barang/update') }}/' + barangId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        namabarangupdate: nama
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
                                window.location.reload();
                            }
                        });

                        // Close the modal
                        $('#modalUpdateBarang').modal('hide');

                        // Reset the form
                        document.getElementById('formUpdateBarang').reset();
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

    {{-- DELETE Category --}}
    <script>
        // Attach SweetAlert confirmation to the delete button
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Show the SweetAlert confirmation
                Swal.fire({
                    icon: 'warning',
                    title: 'Yakin ingin menghapus?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the form
                        const form = button.closest('form');

                        // Perform AJAX request for form submission to avoid page reload
                        fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')
                                        .value
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE',
                                    _token: form.querySelector('input[name="_token"]')
                                        .value
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show SweetAlert success after successful deletion
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        showConfirmButton: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Optionally, reload the page or update the table
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    // Show SweetAlert error if deletion fails
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: data.message,
                                        showConfirmButton: true
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);

                                // Show SweetAlert error for unexpected errors
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan!',
                                    text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.',
                                    showConfirmButton: true
                                });
                            });
                    }
                });
            });
        });
    </script>
@endsection
