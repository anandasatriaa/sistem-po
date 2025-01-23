@extends('admin.layouts.app')

@section('title', 'Supplier | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Table Supplier</h5>
                        {{-- <form action="{{ route('admin.supplier-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Upload CSV</label>
                                <input type="file" name="file" id="file" class="form-control" accept=".csv"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Import</button>
                        </form> --}}
                        <a href="" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier"
                            class="btn btn-primary"><i class="ri-add-circle-line me-2"></i>Tambah supplier</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-supplier" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Fax</th>
                                <th>UP</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $index => $supplier)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $supplier->kode }}</td>
                                    <td>{{ $supplier->nama }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->fax }}</td>
                                    <td>{{ $supplier->up }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Edit yang membuka modal dan mengisi data -->
                                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#modalUpdateSupplier" data-id="{{ $supplier->id }}"
                                                data-kode="{{ $supplier->kode }}" data-nama="{{ $supplier->nama }}"
                                                data-address="{{ $supplier->address }}"
                                                data-phone="{{ $supplier->phone }}" data-fax="{{ $supplier->fax }}"
                                                data-up="{{ $supplier->up }}">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.supplier-destroy', $supplier->id) }}"
                                                method="POST" style="display:inline-block;" class="delete-supplier-form">
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

    <!-- Modal Tambah Supplier -->
    <div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-labelledby="modalTambahSupplier" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahSupplier">Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formTambahSupplier">
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodesupplier" class="form-label"><span
                                            class="text-danger">*</span>Kode</label>
                                    <input type="text" class="form-control" id="kodesupplier" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namasupplier" class="form-label"><span class="text-danger">*</span>Nama
                                        Supplier</label>
                                    <input type="text" class="form-control" id="namasupplier" name="namasupplier"
                                        required>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="addresssupplier" class="form-label">Address</label>
                                    <textarea name="addresssupplier" id="addresssupplier" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="phonesupplier" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phonesupplier" name="phonesupplier">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="faxsupplier" class="form-label">Fax</label>
                                    <input type="text" class="form-control" id="faxsupplier" name="faxsupplier">
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="upsupplier" class="form-label">UP</label>
                                    <input type="text" class="form-control" id="upsupplier" name="upsupplier">
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

    <!-- Modal Update Supplier -->
    <div class="modal fade" id="modalUpdateSupplier" tabindex="-1" aria-labelledby="modalUpdateSupplier"
        aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateSupplier">Update Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formUpdateSupplier" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="kodesupplierupdate" class="form-label"><span
                                            class="text-danger">*</span>Kode</label>
                                    <input type="text" class="form-control" id="kodesupplierupdate" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namasupplierupdate" class="form-label"><span
                                            class="text-danger">*</span>Nama Supplier</label>
                                    <input type="text" class="form-control" id="namasupplierupdate"
                                        name="namasupplierupdate" required>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="addresssupplierupdate" class="form-label">Address</label>
                                    <textarea name="addresssupplierupdate" id="addresssupplierupdate" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="phonesupplierupdate" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phonesupplierupdate"
                                        name="phonesupplierupdate">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="faxsupplierupdate" class="form-label">Fax</label>
                                    <input type="text" class="form-control" id="faxsupplierupdate"
                                        name="faxsupplierupdate">
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="upsupplierupdate" class="form-label">UP</label>
                                    <input type="text" class="form-control" id="upsupplierupdate"
                                        name="upsupplierupdate">
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
            $('#table-supplier').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    {{-- ID Supplier --}}
    <script>
        document.getElementById('modalTambahSupplier').addEventListener('show.bs.modal', function() {
            fetch('{{ url('admin/supplier/last-id') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('kodesupplier').value = data.kode;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    {{-- POST Supplier --}}
    <script>
        document.getElementById('formTambahSupplier').addEventListener('submit', function(e) {
            e.preventDefault();

            const kode = document.getElementById('kodesupplier').value;
            const nama = document.getElementById('namasupplier').value;
            const address = document.getElementById('addresssupplier').value;
            const phone = document.getElementById('phonesupplier').value;
            const fax = document.getElementById('faxsupplier').value;
            const up = document.getElementById('upsupplier').value;

            fetch('{{ url('/admin/supplier/store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        kodesupplier: kode,
                        namasupplier: nama,
                        addresssupplier: address,
                        phonesupplier: phone,
                        faxsupplier: fax,
                        upsupplier: up,
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
                        $('#modalTambahSupplier').modal('hide');

                        // Reset the form
                        document.getElementById('formTambahSupplier').reset();
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

    {{-- UPDATE Supplier --}}
    <script>
        // Handle the modal data population
        $('#modalUpdateSupplier').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var supplierId = button.data('id'); // Extract the data-id
            var supplierKode = button.data('kode'); // Extract the data-kode
            var supplierNama = button.data('nama');
            var supplierAddress = button.data('address');
            var supplierPhone = button.data('phone');
            var supplierFax = button.data('fax');
            var supplierUp = button.data('up');

            // Populate the modal with the supplier data
            var modal = $(this);
            modal.find('#kodesupplierupdate').val(supplierKode); // Set the Kode
            modal.find('#namasupplierupdate').val(supplierNama); // Set the Nama
            modal.find('#addresssupplierupdate').val(supplierAddress); // Set the Address
            modal.find('#phonesupplierupdate').val(supplierPhone); // Set the Phone
            modal.find('#faxsupplierupdate').val(supplierFax); // Set the Fax
            modal.find('#upsupplierupdate').val(supplierUp); // Set the Up

            // Set the form action URL using the url() helper in the blade template
            var updateUrl = '{{ url('/admin/supplier/update') }}/' + supplierId;
            modal.find('#formUpdateSupplier').attr('action', updateUrl); // Set the form action URL
        });

        // Handle the form submission for updating supplier
        document.getElementById('formUpdateSupplier').addEventListener('submit', function(e) {
            e.preventDefault();

            const supplierId = document.getElementById('kodesupplierupdate').value;
            const nama = document.getElementById('namasupplierupdate').value;
            const address = document.getElementById('addresssupplierupdate').value;
            const phone = document.getElementById('phonesupplierupdate').value;
            const fax = document.getElementById('faxsupplierupdate').value;
            const up = document.getElementById('upsupplierupdate').value;

            fetch('{{ url('/admin/supplier/update') }}/' + supplierId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        namasupplierupdate: nama,
                        addresssupplierupdate: address,
                        phonesupplierupdate: phone,
                        faxsupplierupdate: fax,
                        upsupplierupdate: up,
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
                        $('#modalUpdateSupplier').modal('hide');

                        // Reset the form
                        document.getElementById('formUpdateSupplier').reset();
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

    {{-- DELETE Supplier --}}
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
