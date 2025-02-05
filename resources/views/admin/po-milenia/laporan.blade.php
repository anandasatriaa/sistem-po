@extends('admin.layouts.app')

@section('title', 'Purchase Order Milenia | Sistem Purchase Order General Affair')

@section('css')
    <style>
        .bg-milenia {
            background-color: #6f42c1;
        }
        .border-milenia {
            border: 2px solid #6f42c1;
        }

        .nav-border-top-milenia {
            border-top: 4px solid #6f42c1;
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
    <div class="card card-milenia">
        <div class="card-header">
            <div class="row align-items-center">
                <!-- Konten Utama -->
                <div class="col-12 col-md-9"> <!-- Full width di mobile, 9 columns di desktop -->
                    <h5 class="card-title">Laporan Detail & Summary Purchase Order</h5>
                </div>

                <!-- Logo -->
                <div class="col-12 col-md-3 text-center text-md-end mt-3 mt-md-0">
                    <!-- Full width di mobile, 3 columns di desktop -->
                    <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="img-fluid" style="max-width: 80px"
                        alt="Logo Milenia">
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-primary mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#detail" role="tab" aria-selected="false">
                        <i class="ri-survey-line align-middle me-1"></i> Detail
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#summary" role="tab" aria-selected="false">
                        <i class="ri-clipboard-line me-1 align-middle"></i> Summary
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Table Section -->
                <div class="tab-pane active" id="detail" role="tabpanel">
                    <!-- Filter Section -->
                    <form id="filterForm" class="mb-3">
                        <div class="row">
                            <!-- Cabang Filter -->
                            <div class="col-md-4">
                                <label for="cabang">Cabang</label>
                                <select name="cabang[]" id="cabang" class="form-control select2" multiple>
                                    <option value="all"
                                        {{ empty(request('cabang')) || in_array('all', request('cabang', [])) ? 'selected' : '' }}>
                                        All
                                    </option>
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id_cabang }}"
                                            {{ in_array($cabang->id_cabang, request('cabang', [])) ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div class="col-md-4">
                                <label for="category">Category</label>
                                <select name="category[]" id="category" class="form-control select2" multiple>
                                    <option value="all"
                                        {{ empty(request('category')) || in_array('all', request('category', [])) ? 'selected' : '' }}>
                                        All
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, request('category', [])) ? 'selected' : '' }}>
                                            {{ $category->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Filter -->
                            <div class="col-md-4">
                                <label for="date">Date</label>
                                <div class="input-group">
                                    <input type="text" name="date" id="date"
                                        class="form-control border dash-filter-picker" data-provider="flatpickr"
                                        data-range-date="true" data-date-format="d M, Y">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <i class="ri-calendar-2-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Tombol Export PDF -->
                    <a href="{{ route('admin.laporanpo-milenia.pdf-detail') }}" id="pdf-detail"
                        class="btn btn-outline-danger mb-3">
                        <i class="ri-file-pdf-2-line"></i> Export PDF
                    </a>
                    @include('admin.po-milenia.partials.table-detail')
                </div>

                <div class="tab-pane" id="summary" role="tabpanel">
                    <div class="row mb-3">
                        <!-- Cabang Filter -->
                        <div class="col-md-4">
                            <label for="cabang">Cabang</label>
                            <select name="cabang2[]" id="cabang2" class="form-control select2" multiple>
                                <option value="all"
                                    {{ empty(request('cabang')) || in_array('all', request('cabang', [])) ? 'selected' : '' }}>
                                    All
                                </option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id_cabang }}"
                                        {{ in_array($cabang->id_cabang, request('cabang', [])) ? 'selected' : '' }}>
                                        {{ $cabang->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-4">
                            <label for="category">Category</label>
                            <select name="category2[]" id="category2" class="form-control select2" multiple>
                                <option value="all"
                                    {{ empty(request('category')) || in_array('all', request('category', [])) ? 'selected' : '' }}>
                                    All
                                </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ in_array($category->id, request('category', [])) ? 'selected' : '' }}>
                                        {{ $category->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="col-md-4">
                            <label for="date">Date</label>
                            <div class="input-group">
                                <input type="text" name="date_summary" id="date_summary"
                                    class="form-control border dash-filter-picker" data-provider="flatpickr"
                                    data-range-date="true" data-date-format="d M, Y">
                                <div class="input-group-text bg-primary border-primary text-white">
                                    <i class="ri-calendar-2-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol Export PDF -->
                    <a href="{{ route('admin.laporanpo-milenia.pdf-summary') }}" id="pdf-summary"
                        class="btn btn-outline-danger mb-3">
                        <i class="ri-file-pdf-2-line"></i> Export PDF
                    </a>
                    @include('admin.po-milenia.partials.table-summary')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    {{-- DataTable --}}
    <script>
        $(document).ready(function() {
            $('#table-detail').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });

            $('#table-summary').DataTable({
                scrollX: true,
                responsive: false,
                lengthChange: false,
                paging: true,
                searching: true,
                info: true
            });

            // Event listener untuk tab
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(event) {
                var target = $(event.target).attr("href"); // Ambil ID tab yang aktif

                if (target === "#detail") {
                    $('#table-detail').DataTable().columns.adjust().responsive.recalc();
                } else if (target === "#summary") {
                    $('#table-summary').DataTable().columns.adjust().responsive.recalc();
                }
            });
        });
    </script>

    {{-- Filter dll --}}
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                placeholder: 'Pilih...',
                allowClear: true
            });

            // Inisialisasi Flatpickr
            // Mendapatkan tanggal hari ini
            const today = new Date();

            // Membuat tanggal untuk 1 bulan ke belakang
            const oneMonthAgo = new Date();
            oneMonthAgo.setMonth(today.getMonth() - 1);

            flatpickr("#date", {
                mode: "range",
                dateFormat: "d M, Y",
                defaultDate: [oneMonthAgo, today]
            });

            // Event listener untuk perubahan filter
            $('#cabang, #category, #date').on('change', function() {
                updateExportPdfUrl();
                fetchData();
            });

            // Fungsi untuk mengambil data via AJAX
            function fetchData() {
                const cabang = $('#cabang').val();
                const category = $('#category').val();
                const date = $('#date').val();

                $.ajax({
                    url: "{{ route('admin.laporanpo-milenia') }}",
                    method: "GET",
                    data: {
                        cabang: cabang,
                        category: category,
                        date: date
                    },
                    success: function(response) {
                        let newTbodys = $(response.table_detail).find('tbody')
                            .html(); // Ambil hanya tbody
                        $('#table-detail tbody').html(newTbodys); // Ganti tbody lama dengan yang baru
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            // Fungsi untuk mengupdate URL Export PDF dengan parameter filter
            function updateExportPdfUrl() {
                const cabang = $('#cabang').val();
                const category = $('#category').val();
                const date = $('#date').val();

                // Buat object parameter
                const params = {
                    cabang: cabang,
                    category: category,
                    date: date
                };

                // Encode parameter menjadi query string
                const queryString = $.param(params);

                // Update href tombol PDF
                $('#pdf-detail').attr('href', "{{ route('admin.laporanpo-milenia.pdf-detail') }}" + "?" +
                    queryString);
            }

            // Panggil sekali saat halaman pertama kali dimuat untuk set URL PDF awal
            updateExportPdfUrl();
        });
    </script>

    {{-- Filter Summary --}}
    <script>
        $(document).ready(function() {
            // Fungsi untuk memperbarui URL export PDF dengan query string sesuai filter
            function updatePdfLink() {
                let dateRange = $('#date_summary').val();
                let selectedCabang = $('#cabang2').val();
                let selectedCategory = $('#category2').val();

                let url = "{{ route('admin.laporanpo-milenia.pdf-summary') }}";
                // Membuat query string, jQuery akan mengubah array ke format yang benar (misal: cabang[]=1&cabang[]=2)
                let params = $.param({
                    date: dateRange,
                    cabang: selectedCabang,
                    category: selectedCategory
                });
                $('#pdf-summary').attr('href', url + '?' + params);
            }

            // Panggil updatePdfLink setiap kali filter berubah
            $('#date_summary, #cabang2, #category2').on('change', function() {
                updatePdfLink();

                let dateRange = $('#date_summary').val();
                let selectedCabang = $('#cabang2').val();
                let selectedCategory = $('#category2').val();

                $.ajax({
                    url: "{{ route('admin.laporanpo-milenia.summary') }}",
                    type: "GET",
                    data: {
                        date: dateRange,
                        cabang: selectedCabang,
                        category: selectedCategory
                    },
                    success: function(response) {
                        let newTbody = $(response.table_summary).find('tbody').html();
                        $('#table-summary tbody').html(newTbody);
                    },
                    error: function() {
                        alert("Gagal mengambil data!");
                    }
                });
            });

            // Panggil saat pertama kali agar tombol PDF sudah terupdate
            updatePdfLink();
        });
    </script>


@endsection
