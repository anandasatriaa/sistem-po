@extends('admin.layouts.app')

@section('title', 'Cabang | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Table Kantor Cabang</h5>
                        <a href="" data-bs-toggle="modal" data-bs-target="#modalTambahCabang" class="btn btn-primary"><i
                                class="ri-add-circle-line me-2"></i>Tambah cabang</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-cabang" class="table table-bordered table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Cabang</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th>Telepon</th>
                                <th>PIC</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cabang as $index => $cabangItem)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $cabangItem->id_cabang }}</td>
                                    <td>{{ $cabangItem->nama }}</td>
                                    <td>{{ $cabangItem->alamat }}</td>
                                    <td>{{ $cabangItem->kota }}</td>
                                    <td>{{ $cabangItem->provinsi }}</td>
                                    <td>{{ $cabangItem->telepon }}</td>
                                    <td>{{ $cabangItem->pic }}</td>
                                    <td>{{ $cabangItem->aktif ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Edit yang membuka modal dan mengisi data -->
                                            <button type="button" class="btn btn-primary me-2 btn-edit-cabang"
                                                data-bs-toggle="modal" data-bs-target="#modalUpdateCabang"
                                                data-id="{{ $cabangItem->id_cabang }}" data-nama="{{ $cabangItem->nama }}"
                                                data-alamat="{{ $cabangItem->alamat }}"
                                                data-kota="{{ $cabangItem->kota }}"
                                                data-provinsi="{{ $cabangItem->provinsi }}"
                                                data-telepon="{{ $cabangItem->telepon }}"
                                                data-pic="{{ $cabangItem->pic }}">
                                                <i class="ri-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('admin.cabang-destroy', $cabangItem->id_cabang) }}"
                                                method="POST" style="display:inline-block;"
                                                id="deleteForm-{{ $cabangItem->id_cabang }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger"
                                                    onclick="confirmDelete('{{ $cabangItem->id_cabang }}')">
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

    <!-- Modal Tambah Cabang -->
    <div class="modal fade" id="modalTambahCabang" tabindex="-1" aria-labelledby="modalTambahCabang" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCabang">Tambah Cabang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="formTambahCabang">
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="idcabang" class="form-label">ID Cabang</label>
                                    <input type="text" class="form-control" id="idcabang" required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namacabang" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="namacabang" required>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="provinsicabang" class="form-label">Provinsi</label>
                                    <select id="provinsiCabang" class="form-select">
                                        <option selected>Choose...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="kotacabang" class="form-label">Kota</label>
                                    <select id="kotaCabang" class="form-select">
                                        <option selected>Choose...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="alamatcabang" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamatcabang" required>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="teleponcabang" class="form-label">Telepon</label>
                                    <input type="number" class="form-control" id="teleponcabang">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="piccabang" class="form-label">PIC</label>
                                    <input type="text" class="form-control" id="piccabang">
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

    <!-- Modal Update Cabang -->
    <div class="modal fade" id="modalUpdateCabang" tabindex="-1" aria-labelledby="modalUpdateCabang"
        aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUpdateCabang">Update Cabang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formUpdateCabang">
                        @method('POST')
                        @csrf
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="idcabangupdate" class="form-label">ID Cabang</label>
                                    <input type="text" class="form-control" id="idcabangupdate" name="idcabangupdate"
                                        required disabled>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namacabangupdate" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="namacabangupdate"
                                        name="namacabangupdate">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="provinsicabangupdate" class="form-label">Provinsi</label>
                                    <select id="provinsiCabangupdate" class="form-select" name="provinsiCabangupdate">
                                        <option selected>Choose...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="kotacabangupdate" class="form-label">Kota</label>
                                    <select id="kotaCabangupdate" class="form-select" name="kotaCabangupdate">
                                        <option selected>Choose...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div>
                                    <label for="alamatcabangupdate" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamatcabangupdate"
                                        name="alamatcabangupdate">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="teleponcabangupdate" class="form-label">Telepon</label>
                                    <input type="number" class="form-control" id="teleponcabangupdate"
                                        name="teleponcabangupdate">
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div>
                                    <label for="piccabangupdate" class="form-label">PIC</label>
                                    <input type="text" class="form-control" id="piccabangupdate"
                                        name="piccabangupdate">
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
    <script>
        $(document).ready(function() {
            $('#table-cabang').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });
        });
    </script>

    {{-- Dropdown Kota dan Provinsi --}}
    <script>
        // Menyimpan data provinsi dan kota
        let provinsiData = [];
        let kotaData = {};

        // Memuat file CSV provinsi dan kota
        document.addEventListener('DOMContentLoaded', function() {
            // Memuat provinsi
            fetch('{{ url('assets/provinsi.csv') }}') // Menggunakan url() untuk path file CSV
                .then(response => response.text())
                .then(data => {
                    // Parsing file CSV provinsi
                    const rows = data.split('\n');
                    const provinsiSelect = document.getElementById('provinsiCabang');

                    rows.forEach(row => {
                        // Memisahkan kolom dengan koma
                        const columns = row.split(',');
                        if (columns.length === 2) {
                            const provinsiId = columns[0].trim();
                            const provinsiName = columns[1].trim();

                            // Menyimpan data provinsi
                            provinsiData.push({
                                id: provinsiId,
                                name: provinsiName
                            });

                            // Menambah option provinsi ke dropdown
                            const option = document.createElement('option');
                            option.value = provinsiId;
                            option.textContent = provinsiName;
                            provinsiSelect.appendChild(option);

                            // Menyimpan data kota berdasarkan provinsi
                            kotaData[provinsiId] = [];
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading CSV file for provinsi:', error);
                });

            // Memuat kota
            fetch('{{ url('assets/kota.csv') }}') // Menggunakan url() untuk path file CSV
                .then(response => response.text())
                .then(data => {
                    // Parsing file CSV kota
                    const rows = data.split('\n');

                    rows.forEach(row => {
                        const columns = row.split(',');
                        if (columns.length === 3) { // Format: ID Kota, ID Provinsi, Nama Kota
                            const kotaId = columns[0].trim();
                            const provinsiId = columns[1].trim();
                            const kotaName = columns[2].trim();

                            // Menyimpan data kota untuk provinsi terkait
                            if (kotaData[provinsiId]) {
                                kotaData[provinsiId].push({
                                    id: kotaId,
                                    name: kotaName
                                });
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading CSV file for kota:', error);
                });
        });

        // Event listener untuk provinsi, mengisi dropdown kota berdasarkan provinsi yang dipilih
        document.getElementById('provinsiCabang').addEventListener('change', function() {
            const provinsiId = this.value;
            const kotaSelect = document.getElementById('kotaCabang');
            kotaSelect.innerHTML = '<option selected>Choose...</option>'; // Reset pilihan kota

            if (provinsiId && kotaData[provinsiId]) {
                // Menambah pilihan kota berdasarkan provinsi yang dipilih
                kotaData[provinsiId].forEach(kota => {
                    const option = document.createElement('option');
                    option.value = kota.id;
                    option.textContent = kota.name;
                    kotaSelect.appendChild(option);
                });
            }
        });
    </script>

    {{-- ID Cabang --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menghasilkan ID Cabang
            function generateCabangId() {
                // Ambil ID cabang terakhir dari tabel
                fetch(
                        '{{ url('admin/cabang/last-id') }}'
                    ) // Gantilah dengan URL API untuk mendapatkan ID cabang terakhir
                    .then(response => response.json())
                    .then(data => {
                        let newCabangId;
                        if (data && data.lastId) {
                            // Mendapatkan ID cabang terakhir dan menambah 1
                            let lastId = data.lastId;
                            let numberPart = parseInt(lastId.substring(1)); // Mengambil angka setelah huruf C
                            let newNumber = numberPart + 1;
                            newCabangId = 'C' + newNumber.toString().padStart(3, '0');
                        } else {
                            // Jika belum ada data, set ID pertama C001
                            newCabangId = 'C001';
                        }

                        // Set nilai ID cabang ke input
                        document.getElementById('idcabang').value = newCabangId;
                    })
                    .catch(error => {
                        console.error('Error fetching last cabang ID:', error);
                        document.getElementById('idcabang').value = 'C001'; // Default to C001 jika gagal
                    });
            }

            // Generate ID Cabang saat halaman dimuat
            generateCabangId();
        });
    </script>

    {{-- POST Cabang --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('formTambahCabang');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah form dikirimkan secara default

                // Ambil data dari inputan form
                const provinsiId = document.getElementById('provinsiCabang').value;
                const kotaId = document.getElementById('kotaCabang').value;

                // Menemukan nama provinsi berdasarkan ID provinsi yang dipilih
                const provinsiName = provinsiData.find(provinsi => provinsi.id === provinsiId).name;

                // Menemukan nama kota berdasarkan ID kota yang dipilih
                const kotaName = kotaData[provinsiId].find(kota => kota.id === kotaId).name;

                // Ambil data dari inputan form
                const formData = {
                    id_cabang: document.getElementById('idcabang').value,
                    nama: document.getElementById('namacabang').value,
                    alamat: document.getElementById('alamatcabang').value,
                    kota: kotaName,
                    provinsi: provinsiName,
                    telepon: document.getElementById('teleponcabang').value,
                    pic: document.getElementById('piccabang').value,
                };

                console.log("Form Data:", formData); // Verifikasi data form

                // Kirim data ke server
                fetch('{{ url('/admin/cabang/store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Untuk CSRF token di Laravel
                        },
                        body: JSON.stringify(formData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Response from server:", data); // Debugging response
                        let alertMessage = '';
                        if (data.success) {
                            // Tampilkan alert sukses
                            alertMessage = `<div class="alert alert-success alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                            <i class="ri-notification-off-line label-icon"></i><strong>Success!</strong> Cabang berhasil ditambahkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;

                            $('#modalTambahCabang').modal('hide');
                        } else {
                            // Tampilkan alert error jika gagal
                            alertMessage = `<div class="alert alert-danger alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                            <i class="ri-error-warning-line label-icon"></i><strong>Failed!</strong> Gagal menambahkan cabang: ${data.message}.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;
                        }

                        // Menyisipkan alert ke atas row
                        document.querySelector('.row').insertAdjacentHTML('beforebegin', alertMessage);

                        // Hapus alert setelah 2 detik
                        setTimeout(function() {
                            const alert = document.querySelector('.alert');
                            if (alert) {
                                alert.remove();
                            }
                        }, 1000);

                        // Reset form setelah berhasil
                        if (data.success) form.reset();

                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorAlert = `<div class="alert alert-danger alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                        <i class="ri-error-warning-line label-icon"></i><strong>Failed!</strong> Terjadi kesalahan saat mengirim data.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                        // Menyisipkan alert error ke atas row
                        document.querySelector('.row').insertAdjacentHTML('beforebegin', errorAlert);

                        // Hapus alert setelah 2 detik
                        setTimeout(function() {
                            const alert = document.querySelector('.alert');
                            if (alert) {
                                alert.remove();
                            }
                        }, 1000);
                    });
            });
        });
    </script>

    {{-- UPDATE Cabang --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit-cabang');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Ambil data dari atribut tombol
                    const idCabang = button.getAttribute('data-id');
                    const nama = button.getAttribute('data-nama');
                    const alamat = button.getAttribute('data-alamat');
                    const kota = button.getAttribute('data-kota');
                    const provinsi = button.getAttribute('data-provinsi');
                    const telepon = button.getAttribute('data-telepon');
                    const pic = button.getAttribute('data-pic');

                    console.log("Data yang diambil:");
                    console.log({
                        idCabang,
                        nama,
                        alamat,
                        kota,
                        provinsi,
                        telepon,
                        pic
                    });

                    // Isi form modal dengan data
                    document.getElementById('idcabangupdate').value = idCabang;
                    document.getElementById('namacabangupdate').value = nama;
                    document.getElementById('alamatcabangupdate').value = alamat;
                    document.getElementById('teleponcabangupdate').value = telepon;
                    document.getElementById('piccabangupdate').value = pic;

                    // Set dropdown provinsi
                    const provinsiSelect = document.getElementById('provinsiCabangupdate');
                    provinsiSelect.innerHTML =
                        '<option selected>Choose...</option>'; // Reset dropdown
                    provinsiData.forEach(provinsiItem => {
                        const option = document.createElement('option');
                        option.value = provinsiItem.name;
                        option.textContent = provinsiItem.name;
                        if (provinsiItem.name === provinsi) {
                            option.selected = true; // Set selected berdasarkan database
                        }
                        provinsiSelect.appendChild(option);
                    });

                    // Log untuk memastikan provinsi yang dipilih
                    console.log("Selected Provinsi:", provinsi);

                    // Set dropdown kota
                    const kotaSelect = document.getElementById('kotaCabangupdate');
                    kotaSelect.innerHTML = '<option selected>Choose...</option>'; // Reset dropdown

                    if (provinsi) {
                        // Mencari ID provinsi berdasarkan nama yang dipilih
                        const provinsiId = provinsiData.find(p => p.name === provinsi)?.id;
                        if (provinsiId && kotaData[provinsiId]) {
                            console.log("Available Kota for selected Provinsi:", kotaData[
                                provinsiId]);

                            kotaData[provinsiId].forEach(kotaItem => {
                                const option = document.createElement('option');
                                option.value = kotaItem
                                    .id; // Menggunakan ID kota untuk value
                                option.textContent = kotaItem.name;
                                if (kotaItem.name === kota) {
                                    option.selected =
                                        true; // Set berdasarkan nama kota dari database
                                }
                                kotaSelect.appendChild(option);
                            });
                        } else {
                            console.log("Tidak ada data kota untuk provinsi:", provinsi);
                        }
                    }

                    // Log kota yang sudah dipilih
                    console.log("Selected Kota:", kota);
                });
            });

            // Event listener untuk perubahan dropdown provinsi pada update
            document.getElementById('provinsiCabangupdate').addEventListener('change', function() {
                const provinsiName = this.value; // Nama provinsi yang dipilih
                const kotaSelect = document.getElementById('kotaCabangupdate');
                kotaSelect.innerHTML = '<option selected>Choose...</option>'; // Reset dropdown kota

                // Mencari ID provinsi berdasarkan nama yang dipilih
                const provinsiId = provinsiData.find(p => p.name === provinsiName)?.id;

                if (provinsiId && kotaData[provinsiId]) {
                    console.log("Available Kota for selected Provinsi:", kotaData[provinsiId]);

                    kotaData[provinsiId].forEach(kotaItem => {
                        const option = document.createElement('option');
                        option.value = kotaItem.id; // Menggunakan ID kota untuk value
                        option.textContent = kotaItem.name;
                        kotaSelect.appendChild(option);
                    });
                } else {
                    console.log("Tidak ada data kota untuk provinsi:", provinsiId);
                }
            });

            // Tangani submit form update cabang
            document.getElementById('formUpdateCabang').addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah reload halaman

                const form = new FormData(this);

                const idCabang = document.getElementById('idcabangupdate').value;
                form.append('id', idCabang);

                const kotaId = document.getElementById('kotaCabangupdate').value;
                let namaKota = null;

                for (const provinsiId in kotaData) {
                    const kotaList = kotaData[provinsiId];
                    const kotaItem = kotaList.find(kota => kota.id === kotaId);
                    if (kotaItem) {
                        namaKota = kotaItem.name;
                        break;
                    }
                }

                if (!namaKota) {
                    alert('Kota tidak valid. Pastikan memilih kota yang benar.');
                    return;
                }

                // Tambahkan nama kota ke FormData, dan hapus jika sudah ada
                form.delete('kotaCabangupdate'); // Hapus ID kota sebelumnya
                form.append('kotaCabangupdate', namaKota); // Masukkan nama kota

                console.log("Form data yang akan dikirim:");
                form.forEach((value, key) => console.log(`${key}: ${value}`));

                // Kirim data melalui AJAX
                fetch(`{{ url('/admin/cabang/update') }}/${idCabang}`, {
                        method: 'POST',
                        body: form,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Pastikan token CSRF dikirim
                        }
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
                            $('#modalTambahCabang').modal('hide');

                            // Reset the form
                            document.getElementById('formTambahCabang').reset();
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

        });
    </script>

    {{-- Delete Cabang --}}
    <script>
        function confirmDelete(cabangId) {
            console.log(cabangId);
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika konfirmasi diterima, kirimkan form penghapusan
                    document.getElementById('deleteForm-' + cabangId).submit();
                }
            });
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif


@endsection
