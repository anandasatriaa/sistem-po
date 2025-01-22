@extends('admin.layouts.app')

@section('title', 'Unit | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Table Unit</h5>
                        <a href="" data-bs-toggle="modal" data-bs-target="#modalTambahUnit"
                            class="btn btn-primary"><i class="ri-add-circle-line me-2"></i>Tambah Unit</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-unit" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 15px">No</th>
                                <th style="width: 100px">Kode</th>
                                <th>Satuan</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($units as $index => $unit)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $unit->kode }}</td>
                                    <td>{{ $unit->satuan }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Edit yang membuka modal dan mengisi data -->
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalUpdateUnit" data-id="{{ $unit->id }}"
                                                data-kode="{{ $unit->kode }}" data-satuan="{{ $unit->satuan }}">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.unit-destroy', $unit->id) }}"
                                                method="POST" style="display:inline-block;" class="delete-unit-form">
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

    <!-- Modal Tambah Unit -->
    <div class="modal fade" id="modalTambahUnit" tabindex="-1" aria-labelledby="modalTambahUnit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahUnit">Tambah Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formTambahUnit">
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodeunit" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodeunit" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="satuanunit" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="satuanunit" name="satuanunit"
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

    <!-- Modal Update Unit -->
    <div class="modal fade" id="modalUpdateUnit" tabindex="-1" aria-labelledby="modalUpdateUnit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateUnit">Update Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formUpdateUnit" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodeunitupdate" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodeunitupdate" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="satuanunitupdate" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="satuanunitupdate"
                                        name="satuanunitupdate" required>
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

    {{-- ID Unit --}}
    <script>
        document.getElementById('modalTambahUnit').addEventListener('show.bs.modal', function() {
            fetch('{{ url('admin/unit/last-id') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kodeunit').value = data.kode;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    {{-- POST Unit --}}
    <script>
        document.getElementById('formTambahUnit').addEventListener('submit', function(e) {
            e.preventDefault();

            const kode = document.getElementById('kodeunit').value;
            const satuan = document.getElementById('satuanunit').value;

            fetch('{{ url('/admin/unit/store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        satuanunit: satuan
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
                        $('#modalTambahUnit').modal('hide');

                        // Reset the form
                        document.getElementById('formTambahUnit').reset();
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

    {{-- UPDATE Unit --}}
    <script>
        // Handle the modal data population
        $('#modalUpdateUnit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var unitId = button.data('id'); // Extract the data-id
            var unitKode = button.data('kode'); // Extract the data-kode
            var unitSatuan = button.data('satuan'); // Extract the data-satuan

            // Populate the modal with the unit data
            var modal = $(this);
            modal.find('#kodeunitupdate').val(unitKode); // Set the Kode
            modal.find('#satuanunitupdate').val(unitSatuan); // Set the Satuan

            // Set the form action URL using the url() helper in the blade template
            var updateUrl = '{{ url('/admin/unit/update') }}/' + unitId;
            modal.find('#formUpdateUnit').attr('action', updateUrl); // Set the form action URL
        });

        // Handle the form submission for updating unit
        document.getElementById('formUpdateUnit').addEventListener('submit', function(e) {
            e.preventDefault();

            const unitId = document.getElementById('kodeunitupdate').value;
            const satuan = document.getElementById('satuanunitupdate').value;

            fetch('{{ url('/admin/unit/update') }}/' + unitId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        satuanunitupdate: satuan
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
                        $('#modalUpdateUnit').modal('hide');

                        // Reset the form
                        document.getElementById('formUpdateUnit').reset();
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

    {{-- DELETE Unit --}}
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
