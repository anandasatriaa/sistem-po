@extends('admin.layouts.app')

@section('title', 'Category | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Table Category</h5>
                        <a href="" data-bs-toggle="modal" data-bs-target="#modalTambahCategory"
                            class="btn btn-primary"><i class="ri-add-circle-line me-2"></i>Tambah category</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-category" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 15px">No</th>
                                <th style="width: 100px">Kode</th>
                                <th>Nama</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->kode }}</td>
                                    <td>{{ $category->nama }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Edit yang membuka modal dan mengisi data -->
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalUpdateCategory" data-id="{{ $category->id }}"
                                                data-kode="{{ $category->kode }}" data-nama="{{ $category->nama }}">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.category-destroy', $category->id) }}"
                                                method="POST" style="display:inline-block;" class="delete-category-form">
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

    <!-- Modal Tambah Category -->
    <div class="modal fade" id="modalTambahCategory" tabindex="-1" aria-labelledby="modalTambahCategory" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCategory">Tambah Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formTambahCategory">
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodecategory" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodecategory" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namacategory" class="form-label">Nama Category</label>
                                    <input type="text" class="form-control" id="namacategory" name="namacategory"
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

    <!-- Modal Update Category -->
    <div class="modal fade" id="modalUpdateCategory" tabindex="-1" aria-labelledby="modalUpdateCategory" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateCategory">Update Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formUpdateCategory" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodecategoryupdate" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kodecategoryupdate" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namacategoryupdate" class="form-label">Nama Category</label>
                                    <input type="text" class="form-control" id="namacategoryupdate"
                                        name="namacategoryupdate" required>
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
            $('#table-category').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    {{-- ID Category --}}
    <script>
        document.getElementById('modalTambahCategory').addEventListener('show.bs.modal', function() {
            fetch('{{ url('admin/category/last-id') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kodecategory').value = data.kode;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    {{-- POST Category --}}
    <script>
        document.getElementById('formTambahCategory').addEventListener('submit', function(e) {
            e.preventDefault();

            const kode = document.getElementById('kodecategory').value;
            const nama = document.getElementById('namacategory').value;

            fetch('{{ url('/admin/category/store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        namacategory: nama
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
                        $('#modalTambahCategory').modal('hide');

                        // Reset the form
                        document.getElementById('formTambahCategory').reset();
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

    {{-- UPDATE Category --}}
    <script>
        // Handle the modal data population
        $('#modalUpdateCategory').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var categoryId = button.data('id'); // Extract the data-id
            var categoryKode = button.data('kode'); // Extract the data-kode
            var categoryNama = button.data('nama'); // Extract the data-nama

            // Populate the modal with the category data
            var modal = $(this);
            modal.find('#kodecategoryupdate').val(categoryKode); // Set the Kode
            modal.find('#namacategoryupdate').val(categoryNama); // Set the Nama

            // Set the form action URL using the url() helper in the blade template
            var updateUrl = '{{ url('/admin/category/update') }}/' + categoryId;
            modal.find('#formUpdateCategory').attr('action', updateUrl); // Set the form action URL
        });

        // Handle the form submission for updating category
        document.getElementById('formUpdateCategory').addEventListener('submit', function(e) {
            e.preventDefault();

            const categoryId = document.getElementById('kodecategoryupdate').value;
            const nama = document.getElementById('namacategoryupdate').value;

            fetch('{{ url('/admin/category/update') }}/' + categoryId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        namacategoryupdate: nama
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
                        $('#modalUpdateCategory').modal('hide');

                        // Reset the form
                        document.getElementById('formUpdateCategory').reset();
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
