@extends('admin.layouts.app')

@section('title', 'Purchase Order MAP | Sistem Purchase Order General Affair')

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

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-left: 0.5em;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .delete-row {
            transition: all 0.3s ease;
        }

        .delete-row:hover {
            transform: scale(1.1);
        }

        .input-row {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
        }

        canvas {
            cursor: crosshair;
            border: 1px solid #000;
        }
    </style>
@endsection

@section('content')
    <div class="card card-map">
        <div class="card-header">
            <div class="row align-items-center">
                <!-- Konten Utama -->
                <div class="col-12 col-md-9"> <!-- Full width di mobile, 9 columns di desktop -->
                    <h5 class="card-title mb-0">Input Purchase Order MAP</h5>
                </div>
                <!-- Logo -->
                <div class="col-12 col-md-3 text-center text-md-end mt-3 mt-md-0">
                    <!-- Full width di mobile, 3 columns di desktop -->
                    <img src="{{ asset('assets/images/map-logo.png') }}" class="img-fluid" style="max-width: 60px"
                        alt="Logo MAP">
                </div>
            </div>
        </div>
        <div class="card-body bg-light">
            <form id="po-form" action="" class="row g-3">
                <div class="col-md-12">
                    <label for="no_po" class="form-label"><span class="text-danger">*</span>No. PO <br> <small class="text-muted">Last PO:
                            {{ $noPoTerakhir }}</small></label>
                    <div class="input-group">
                        <input list="poList" id="no_po" name="no_po" class="form-control" required />
                        <button type="button" class="btn btn-map" id="newPoBtn">New PO</button>
                    </div>
                    <datalist id="poList">
                        @foreach ($nopomap as $n)
                            <option value="{{ $n->no_po }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-12">
                    <label for="cabang_po" class="form-label"><span class="text-danger">*</span>Cabang</label>
                    <select id="cabang_po" name="cabang_po" class="form-select"
                        required>
                        <option selected disabled>Choose...</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->nama }}" data-id="{{ $c->id_cabang }}"
                                data-alamat="{{ $c->alamat }}" data-telepon="{{ $c->telepon }}">
                                {{ $c->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="supplier_po" class="form-label"><span class="text-danger">*</span>Supplier</label>
                    <input list="supplierList" id="supplier_po" name="supplier_po" class="form-control" required>
                    <datalist id="supplierList">
                        @foreach ($supplier as $s)
                            <option value="{{ $s->nama }}" data-id="{{ $s->id }}"
                                data-address="{{ $s->address }}" data-phone="{{ $s->phone }}"
                                data-fax="{{ $s->fax }}" data-up="{{ $s->up }}">
                            </option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label for="address_po" class="form-label">Address</label>
                    <input type="text" name="address_po" class="form-control" id="address_po">
                </div>
                <div class="col-md-4">
                    <label for="phone_po" class="form-label">Phone</label>
                    <input type="text" name="phone_po" class="form-control" id="phone_po">
                </div>
                <div class="col-md-4">
                    <label for="fax_po" class="form-label">Fax</label>
                    <input type="text" name="fax_po" class="form-control" id="fax_po">
                </div>
                <div class="col-md-4">
                    <label for="up_po" class="form-label">UP</label>
                    <input type="text" name="up_po" class="form-control" id="up_po">
                </div>
                <div class="col-md-6">
                    <label for="date_po" class="form-label"><span class="text-danger">*</span>Date</label>
                    <div class="input-group">
                        <input type="date" name="date_po" class="form-control" data-provider="flatpickr" id="date_po" required>
                        <span class="input-group-text bg-map text-white">
                            <i class="ri-calendar-todo-line"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="estimatedate_po" class="form-label"><span class="text-danger">*</span>Estimate Date</label>
                    <div class="input-group">
                        <input type="date" name="estimatedate_po" class="form-control" data-provider="flatpickr"
                            id="estimatedate_po" required>
                        <span class="input-group-text bg-map text-white">
                            <i class="ri-calendar-todo-line"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="remarks_po" class="form-label"><span class="text-danger">*</span>Remarks</label>
                    <input type="text" name="remarks_po" class="form-control" id="remarks_po">
                </div>
                <div class="col-md-6">
                    <label for="tax-input" class="form-label">Pajak</label>
                    <div class="input-group">
                        <input type="number" name="tax_input" class="form-control" id="tax-input">
                        <span class="input-group-text bg-map text-white">
                            <i class="ri-percent-line"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="discount-input" class="form-label">Discount</label>
                    <input type="text" name="discount_input" class="form-control" id="discount-input">
                </div>

                <hr style="color: #6f42c1; border: 1px solid;">

                <div class="col-md-12">
                    <label for="category_po" class="form-label"><span class="text-danger">*</span>Category</label>
                    <select id="category_po" name="category_po" class="form-select" data-choices
                        data-choices-sorting="true" required>
                        <option selected disabled>Choose...</option>
                        @foreach ($category as $cat)
                            <option value="{{ $cat->nama }}" data-id="{{ $cat->id }}">{{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="input-rows">
                    <!-- Baris Pertama -->
                    <div class="row input-row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label"><span class="text-danger">*</span>Barang</label>
                            <input list="barangList" name="barang[]" class="form-control barang-input" required>
                            <datalist id="barangList">
                                @foreach ($barang as $b)
                                    <option value="{{ $b->nama }}" data-id="{{ $b->id }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"><span class="text-danger">*</span>Qty</label>
                            <input type="number" name="qty[]" class="form-control qty-input" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><span class="text-danger">*</span>Unit</label>
                            <select name="unit[]" class="form-select unit-select" data-choices
                                data-choices-sorting="true" required>
                                <option selected disabled>Choose...</option>
                                @foreach ($unit as $units)
                                    <option value="{{ $units->satuan }}" data-id="{{ $units->id }}">
                                        {{ $units->satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan[]" class="form-control keterangan-input">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"><span class="text-danger">*</span>Unit Price</label>
                            <input type="text" name="price[]" class="form-control price-input" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Amount Price</label>
                            <input type="text" class="form-control amount-price" disabled>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-danger delete-row" type="button"><i
                                    class="ri-delete-bin-2-line"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-map" id="add-row" type="button"><i class="ri-add-line"></i> Tambah
                            Baris</button>
                    </div>
                </div>

                <hr style="color: #0b5ed7; border: 1px solid;">

                <div class="row justify-content-between mt-3">
                    <!-- Bagian TTD -->
                    <div class="col-md-6">
                        <div class="fw-bold">TTD:</div>
                        <small><span class="text-danger">**</span>Sign Pad wajib digores</small>
                        <div class="d-flex align-content-center justify-content-start">
                            <canvas id="signatureCanvas" width="200" height="100"></canvas>
                            <button id="clearSignature" type="button" class="btn btn-outline-danger ms-2">
                                <i class="ri-delete-bin-2-line"></i>
                            </button>
                        </div>
                        <div class="col-md-4 mt-2">
                            <input type="text" name="namapembuat_po" class="form-control" id="namapembuat_po" value="Windy Wulandari"
                                placeholder="Nama Pembuat PO" required>
                        </div>
                    </div>

                    <!-- Bagian Total -->
                    <div class="col-md-6">
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Sub Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="subtotal">0</span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Pajak (<span id="tax-percentage">0</span>%):</strong>
                            </div>
                            <div class="col-6 text-end">
                                + <span id="tax-amount" class="text-danger">0</span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Diskon:</strong>
                            </div>
                            <div class="col-6 text-end">
                                - <span id="discount-amount" class="text-primary">0</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <h5><strong>Total:</strong></h5>
                            </div>
                            <div class="col-6 text-end">
                                <h5><strong id="grand-total">0</strong></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="preview-btn">
                            <i class="ri-eye-line me-2"></i>Preview
                        </button>
                        <button type="submit" class="btn btn-map" id="create-btn">
                            <i class="ri-save-3-line me-2"></i>Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdf-frame" src="" width="100%" height="800px"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    {{-- Form PO Supplier --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen input dan input target
            const supplierInput = document.getElementById('supplier_po');
            const supplierList = document.getElementById('supplierList');

            const addressField = document.getElementById('address_po');
            const phoneField = document.getElementById('phone_po');
            const faxField = document.getElementById('fax_po');
            const upField = document.getElementById('up_po');

            supplierInput.addEventListener('input', function() {
                // Ambil semua opsi dalam datalist
                const options = supplierList.getElementsByTagName('option');
                let found = false;

                for (let option of options) {
                    if (option.value === supplierInput.value) {
                        found = true;

                        // Set nilai input berdasarkan dataset
                        addressField.value = option.dataset.address || '';
                        phoneField.value = option.dataset.phone || '';
                        faxField.value = option.dataset.fax || '';
                        upField.value = option.dataset.up || '';
                        break;
                    }
                }

                // Jika tidak ditemukan, kosongkan field
                if (!found) {
                    addressField.value = '';
                    phoneField.value = '';
                    faxField.value = '';
                    upField.value = '';
                }
            });
        });
    </script>

    {{-- Perhitungan Pajak, Discount, Amount Price, dll --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputRows = document.getElementById('input-rows');
            const addButton = document.getElementById('add-row');
            const taxInput = document.getElementById('tax-input');
            const discountInput = document.getElementById('discount-input');
            const subtotalEl = document.getElementById('subtotal');
            const taxPercentageEl = document.getElementById('tax-percentage');
            const taxAmountEl = document.getElementById('tax-amount');
            const discountAmountEl = document.getElementById('discount-amount');
            const grandTotalEl = document.getElementById('grand-total');

            // Fungsi untuk menghitung amount price
            function calculateAmount(row) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value.replace(/\./g, '')) || 0;
                const amount = qty * price;
                row.querySelector('.amount-price').value = amount.toLocaleString('id-ID');
                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.amount-price').forEach(amountField => {
                    subtotal += parseFloat(amountField.value.replace(/\./g, '')) || 0;
                });

                subtotalEl.textContent = subtotal.toLocaleString('id-ID');
                const taxPercentage = parseFloat(taxInput.value) || 0;
                const taxAmount = (subtotal * taxPercentage) / 100;
                taxPercentageEl.textContent = taxPercentage;
                taxAmountEl.textContent = taxAmount.toLocaleString('id-ID');

                const discount = parseFloat(discountInput.value.replace(/\./g, '')) || 0;
                discountAmountEl.textContent = discount.toLocaleString('id-ID');

                const grandTotal = subtotal + taxAmount - discount;
                grandTotalEl.textContent = grandTotal.toLocaleString('id-ID');
            }

            // Fungsi untuk memformat angka pada discount-input
            function formatDiscountInput(input) {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/\./g, "").replace(/\D/g,
                        ""); // Hapus titik & karakter non-angka
                    if (value) {
                        this.value = new Intl.NumberFormat("id-ID").format(
                            value); // Format angka dengan titik
                    }
                    calculateTotals(); // Hitung ulang total setelah discount berubah
                });
            }

            // Format discount saat input berubah
            formatDiscountInput(discountInput);

            // Fungsi untuk format angka dengan titik sebagai pemisah ribuan
            function formatPriceInput(input) {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, ''); // Hanya ambil angka
                    if (value) {
                        this.value = parseInt(value, 10).toLocaleString('id-ID');
                    } else {
                        this.value = '';
                    }
                    calculateAmount(this.closest('.input-row'));
                });
            }

            // Fungsi untuk mengaktifkan dropdown unit (jika pakai library seperti Choices.js)
            function initializeDropdown(select) {
                if (select.hasAttribute('data-choices')) {
                    new Choices(select, {
                        shouldSort: true
                    });
                }
            }

            // Fungsi untuk menambahkan event listener ke input
            function addRowEvents(row) {
                row.querySelector('.qty-input').addEventListener('input', () => calculateAmount(row));
                formatPriceInput(row.querySelector('.price-input'));

                row.querySelector('.delete-row').addEventListener('click', function() {
                    row.remove();
                    if (inputRows.children.length === 0) {
                        addButton.click();
                    }
                    calculateTotals();
                });

                // Inisialisasi dropdown unit
                initializeDropdown(row.querySelector('.unit-select'));
            }

            taxInput.addEventListener('input', calculateTotals);
            discountInput.addEventListener('input', calculateTotals);

            // Tambah baris baru menggunakan innerHTML
            addButton.addEventListener('click', function(e) {
                e.preventDefault();

                const newRowHTML = `
                <div class="row input-row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label"><span class="text-danger">*</span>Barang</label>
                            <input list="barangList" name="barang[]" class="form-control barang-input" required>
                            <datalist id="barangList">
                                @foreach ($barang as $b)
                                    <option value="{{ $b->nama }}" data-id="{{ $b->id }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"><span class="text-danger">*</span>Qty</label>
                            <input type="number" name="qty[]" class="form-control qty-input" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><span class="text-danger">*</span>Unit</label>
                            <select name="unit[]" class="form-select unit-select" data-choices
                                data-choices-sorting="true" required>
                                <option selected disabled>Choose...</option>
                                @foreach ($unit as $units)
                                    <option value="{{ $units->satuan }}">{{ $units->satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan[]" class="form-control keterangan-input">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"><span class="text-danger">*</span>Unit Price</label>
                            <input type="text" name="price[]" class="form-control price-input" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Amount Price</label>
                            <input type="text" class="form-control amount-price" disabled>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-danger delete-row" type="button"><i
                                    class="ri-delete-bin-2-line"></i></button>
                        </div>
                    </div>
            `;

                // Menambahkan baris baru menggunakan innerHTML
                inputRows.insertAdjacentHTML('beforeend', newRowHTML);

                // Ambil elemen baris baru dan tambahkan event listener
                const newRow = inputRows.lastElementChild;
                addRowEvents(newRow);
            });

            // Inisialisasi event listener untuk baris pertama
            inputRows.querySelectorAll('.input-row').forEach(addRowEvents);
        });
    </script>

    {{-- Preview Button --}}
    <script>
        document.getElementById('preview-btn').addEventListener('click', function() {
            // Konversi tanda tangan menjadi gambar Base64
            const signatureCanvas = document.getElementById('signatureCanvas');
            const signatureDataURL = signatureCanvas.toDataURL('image/png'); // Mengubah ke Base64 PNG

            // Ambil elemen select cabang
            const cabangSelect = document.getElementById('cabang_po');
            // Ambil option yang dipilih
            const selectedOption = cabangSelect.options[cabangSelect.selectedIndex];

            // Gunakan dataset (atau getAttribute jika diperlukan)
            const cabang_id = selectedOption.dataset.id || selectedOption.getAttribute('data-id');
            const cabang_alamat = selectedOption.dataset.alamat || selectedOption.getAttribute('data-alamat');
            const cabang_telepon = selectedOption.dataset.telepon || selectedOption.getAttribute('data-telepon');

            // Kumpulkan data formulir
            const formData = {
                no_po: document.getElementById('no_po').value,
                cabang: cabangSelect.value,
                cabang_id: cabang_id,
                cabang_alamat: cabang_alamat,
                cabang_telepon: cabang_telepon,
                supplier: document.getElementById('supplier_po').value,
                address: document.getElementById('address_po').value,
                phone: document.getElementById('phone_po').value,
                fax: document.getElementById('fax_po').value,
                up: document.getElementById('up_po').value,
                date: document.getElementById('date_po').value,
                estimate_date: document.getElementById('estimatedate_po').value,
                remarks: document.getElementById('remarks_po').value,
                tax: document.getElementById('tax-input').value,
                discount: parseFloat(document.getElementById('discount-input').value.replace(/\./g, '')) || 0,
                category: document.getElementById('category_po').value,
                subtotal: parseFloat(document.getElementById('subtotal').textContent.replace(/\./g, '')) || 0,
                grandtotal: parseFloat(document.getElementById('grand-total').textContent.replace(/\./g, '')) ||
                    0,
                signature: signatureDataURL,
                nama_pembuat: document.getElementById('namapembuat_po').value,
                barang: []
            };

            // Ambil data barang dari baris dinamis
            document.querySelectorAll('.input-row').forEach(row => {
                const barangData = {
                    barang: row.querySelector('.barang-input').value,
                    qty: row.querySelector('.qty-input').value,
                    unit: row.querySelector('.unit-select').value,
                    keterangan: row.querySelector('.keterangan-input').value,
                    price: parseFloat(row.querySelector('.price-input').value.replace(/\./g, '')) || 0,
                    amount: parseFloat(row.querySelector('.amount-price').value.replace(/\./g, '')) || 0
                };
                formData.barang.push(barangData);
            });

            // Tampilkan data di console sebelum dikirim
            console.log('Data yang dikirim:', formData);

            // Kirim data ke server untuk membuat PDF
            fetch('{{ url('/admin/input-po-map/preview') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.blob()) // Menerima respons dalam bentuk blob (PDF)
                .then(blob => {
                    const url = URL.createObjectURL(blob); // Membuat URL untuk blob PDF
                    document.getElementById('pdf-frame').src = url; // Menampilkan di iframe
                    new bootstrap.Modal(document.getElementById('previewModal')).show(); // Menampilkan modal
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    {{-- Create Button --}}
    <script>
        document.getElementById('po-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form dikirim secara default

            // Konversi tanda tangan menjadi gambar Base64
            const signatureCanvas = document.getElementById('signatureCanvas');
            const signatureDataURL = signatureCanvas.toDataURL('image/png'); // Ubah ke Base64 PNG
            console.log("Signature Data URL:", signatureDataURL);

            // Kumpulkan data form menggunakan FormData
            const formData = new FormData(this);
            formData.append('signature', signatureDataURL);

            // --- Buat mapping object untuk dropdown ---
            const cabangMapping = {
                @foreach ($cabang as $c)
                    "{{ $c->nama }}": "{{ $c->id_cabang }}",
                @endforeach
            };

            const categoryMapping = {
                @foreach ($category as $cat)
                    "{{ $cat->nama }}": "{{ $cat->id }}",
                @endforeach
            };

            const unitMapping = {
                @foreach ($unit as $u)
                    "{{ $u->satuan }}": "{{ $u->id }}",
                @endforeach
            };

            // --- Mapping data untuk field yang perlu id ---

            // 1. Mapping Supplier: cari data-id berdasarkan value di datalist supplierList
            const supplierInput = document.getElementById('supplier_po');
            let supplier_id = null;
            const supplierOptions = document.getElementById('supplierList').options;
            for (let option of supplierOptions) {
                if (option.value.trim() === supplierInput.value.trim()) {
                    supplier_id = option.getAttribute('data-id');
                    break;
                }
            }
            formData.append('supplier_id', supplier_id);

            // 2. Mapping Cabang: gunakan mapping object untuk mendapatkan id berdasarkan nama yang dipilih
            const cabangSelect = document.getElementById('cabang_po');
            const selectedCabangName = cabangSelect.value;
            let cabang_id = cabangMapping[selectedCabangName] || selectedCabangName;
            formData.append('cabang_id', cabang_id);

            // 3. Mapping Category: gunakan mapping object untuk mendapatkan id berdasarkan nama yang dipilih
            const categorySelect = document.getElementById('category_po');
            const selectedCategoryName = categorySelect.value;
            let category_id = categoryMapping[selectedCategoryName] || selectedCategoryName;
            formData.append('category_id', category_id);

            // --- Hitung data barang, amount price, dan subtotal ---
            let subtotal = 0;
            const barangList = [];
            document.querySelectorAll('.input-row').forEach(row => {
                // Ambil nilai barang, qty, unit, keterangan, dan price
                const barangInput = row.querySelector('.barang-input');
                const qtyInput = row.querySelector('.qty-input');
                const unitSelect = row.querySelector('.unit-select');
                const keteranganInput = row.querySelector('.keterangan-input');
                const priceInput = row.querySelector('.price-input');

                // Mapping barang_id dari datalist barangList
                let barang_id = null;
                const barangOptions = document.getElementById('barangList').options;
                for (let option of barangOptions) {
                    if (option.value.trim() === barangInput.value.trim()) {
                        barang_id = option.getAttribute('data-id');
                        break;
                    }
                }

                // Mapping unit_id: gunakan mapping object untuk unit
                let unit_id = unitMapping[unitSelect.value] || unitSelect.value;

                // Konversi qty dan price ke angka
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value.replace(/\./g, '').replace(/,/g, '')) || 0;
                const amount = qty * price;

                // Update subtotal
                subtotal += amount;

                // Bangun objek data untuk baris barang
                const barangData = {
                    category_id: category_id,
                    category: categorySelect.value,
                    barang_id: barang_id,
                    barang: barangInput.value,
                    qty: qty,
                    unit_id: unit_id,
                    unit: unitSelect.value,
                    keterangan: keteranganInput.value,
                    unit_price: price,
                    amount_price: amount
                };

                barangList.push(barangData);

                // (Opsional) Update tampilan amount price pada input (jika ingin menampilkan nilai terformat)
                const amountPriceInput = row.querySelector('.amount-price');
                if (amountPriceInput) {
                    amountPriceInput.value = amount;
                }

                console.log("Barang Data per Row:", barangData);
            });
            console.log("Barang List:", barangList);
            // Tambahkan data barang sebagai JSON string
            formData.append('barang', JSON.stringify(barangList));

            // --- Hitung total berdasarkan subtotal, pajak, dan diskon ---
            const taxInput = document.getElementById('tax-input');
            const discountInput = document.getElementById('discount-input');
            const taxPercentage = parseFloat(taxInput.value) || 0;
            const taxAmount = subtotal * (taxPercentage / 100);
            const discount = parseFloat(discountInput.value.replace(/\./g, '').replace(/,/g, '')) || 0;
            const total = subtotal + taxAmount - discount;
            formData.append('sub_total', subtotal);
            formData.append('tax_amount', taxAmount);
            formData.append('discount', discount);
            formData.append('total', total);

            // Debug: tampilkan data form di console
            console.log("Form Data:");
            for (const [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            const csrfToken = '{{ csrf_token() }}';
            console.log("CSRF Token:", csrfToken);

            console.log("URL yang digunakan:", '{{ route('admin.po-map-store') }}');

            // --- Kirim data menggunakan fetch AJAX ---
            fetch('{{ route('admin.po-map-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    // Convert FormData ke objek plain sebelum di-JSON-kan.
                    body: JSON.stringify(Object.fromEntries(formData))
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
                                window.location.href = "{{ route('admin.statuspo-map') }}";
                            }
                        });
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
                        text: 'Terjadi kesalahan saat mengirim data. Pastikan semua data telah terisi.',
                        showConfirmButton: true
                    });
                });
        });
    </script>

    {{-- Signature --}}
    <script>
        // Inisialisasi SignaturePad (menggantikan kode event manual)
        const canvas = document.getElementById('signatureCanvas');
        // Inisialisasi SignaturePad; atur backgroundColor jika diperlukan
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)' // atau gunakan 'white'
        });

        // (Opsional) Fungsi untuk mengupdate status atau melakukan sesuatu setelah tanda tangan selesai
        function updateButtonState() {
            // Contoh: jika ingin memeriksa apakah canvas sudah tidak kosong
            if (!signaturePad.isEmpty()) {
                console.log('Tanda tangan telah dibuat.');
            } else {
                console.log('Canvas kosong.');
            }
        }

        // Panggil updateButtonState setiap kali pengguna selesai menggambar tanda tangan
        signaturePad.onEnd = updateButtonState;

        // Tombol Clear Signature
        document.getElementById('clearSignature').addEventListener('click', () => {
            signaturePad.clear();
            updateButtonState();
        });
    </script>

    {{-- New PO --}}
    <script>
        // Fungsi untuk menambah 1 ke nomor PO terakhir
        document.getElementById('newPoBtn').addEventListener('click', function() {
            let lastPo = '{{ $noPoTerakhir }}'; // Ambil no_po terakhir
            let newPo = incrementPo(lastPo);
            document.getElementById('no_po').value = newPo; // Set input dengan no PO baru
        });

        // Fungsi untuk increment nomor PO (misalnya PL000001 menjadi PL000002)
        function incrementPo(po) {
            let poNumber = po.replace('PL', ''); // Ambil nomor setelah 'PLO'
            let newPoNumber = parseInt(poNumber) + 1; // Tambah 1

            // Pastikan nomor PO baru memiliki 6 digit
            let newPoNumberFormatted = newPoNumber.toString().padStart(6, '0');

            return 'PL' + newPoNumberFormatted; // Kembalikan dengan format 'PLO' di depan
        }
    </script>

@endsection
