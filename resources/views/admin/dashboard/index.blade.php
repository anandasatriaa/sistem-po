@extends('admin.layouts.app')

@section('title', 'Dashboard | Sistem Purchase Order General Affair')

@section('content')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Good Morning, {{ Auth::user()->Nama }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with purchase order
                                    today.</p>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row row-cols-xl-5 row-cols-lg-3 row-cols-md-2 row-cols-1 g-4">
                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Cabang</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="{{ $jumlahCabang }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.cabang-index') }}"
                                            class="link-secondary text-decoration-underline">View Cabang</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="ri-building-2-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Supplier</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="{{ $jumlahSupplier }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.supplier-index') }}"
                                            class="link-secondary text-decoration-underline">View Supplier</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="ri-store-2-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Category</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="{{ $jumlahCategory }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.category-index') }}"
                                            class="link-secondary text-decoration-underline">View Category</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="ri-archive-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Unit</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="{{ $jumlahUnit }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.unit-index') }}"
                                            class="link-secondary text-decoration-underline">View Unit</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="ri-projector-2-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Barang</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            <span class="counter-value" data-target="{{ $jumlahBarang }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.barang-index') }}"
                                            class="link-secondary text-decoration-underline">View Barang</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="ri-box-3-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Statistics</h4>
                            </div><!-- end card header -->

                            <div class="card-header p-0 border-0 bg-light-subtle">
                                <div class="row g-0 text-center">
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value"
                                                    data-target="{{ $jumlahPRMilenia }}">0</span></h5>
                                            <p class="text-muted mb-0">Purchase Request Milenia</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value"
                                                    data-target="{{ $jumlahPOMilenia }}">0</span></h5>
                                            <p class="text-muted mb-0">Purchase Order Milenia</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value"
                                                    data-target="{{ $jumlahPRMap }}">0</span></h5>
                                            <p class="text-muted mb-0">Purchase Request MAP</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1"><span class="counter-value"
                                                    data-target="{{ $jumlahPOMap }}">0</span></h5>
                                            <p class="text-muted mb-0">Purchase Order MAP</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                            </div>


                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="stacked_bar_chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div>
    </div> <!-- end .h-100-->

    </div> <!-- end col -->
    </div>
@endsection


@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: [{
                        name: "PR Milenia",
                        data: @json($dataPRMilenia)
                    },
                    {
                        name: "PO Milenia",
                        data: @json($dataPOMilenia)
                    },
                    {
                        name: "PR MAP",
                        data: @json($dataPRMap)
                    },
                    {
                        name: "PO MAP",
                        data: @json($dataPOMap)
                    }
                ],
                chart: {
                    type: "bar",
                    height: 350,
                    stacked: true
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "45%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: "12px",
                        colors: ["#fff"] // Warna teks di dalam bar
                    },
                    formatter: function(val) {
                        return val > 0 ? val : ""; // Hanya tampilkan jika lebih dari 0
                    }
                },
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
                        "Dec"
                    ]
                },
                colors: ["#25a0e3", "#32ccfe", "#ffbc0b", "#f06548"],
                legend: {
                    position: "top"
                },
                fill: {
                    opacity: 1
                }
            };

            var chart = new ApexCharts(document.querySelector("#stacked_bar_chart"), options);
            chart.render();
        });
    </script>
@endsection
