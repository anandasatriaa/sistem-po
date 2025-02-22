<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>@yield('title', ' | Sistem Purchase Order General Affair')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Sweet Alert css-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Swiper slider css -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App Css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Css -->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .hover-tooltip {
            position: relative;
            text-decoration: none;
            /* Menghapus garis bawah tautan */
            color: inherit;
            /* Menggunakan warna teks bawaan */
            cursor: pointer;
        }

        .hover-tooltip::after {
            content: "Ananda Satria Ariyanto";
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            white-space: nowrap;
            font-size: 12px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .hover-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }

        .hover-tooltip:hover {
            color: #007bff;
            /* Opsional: warna teks berubah saat dihover */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .nav-link img {
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .nav-link:hover img,
        .nav-link.active img {
            filter: grayscale(0%);
            opacity: 1;
        }
    </style>

    @yield('css')

</head>

<body>
    <div id="page-loader"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
        <i class="ri-loader-4-line" style="font-size: 50px; color: white; animation: spin 1s linear infinite;"></i>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        {{-- <div class="navbar-brand-box horizontal-logo">
                            <a href="index.html" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="assets/images/logo-dark.png" alt="" height="17">
                                </span>
                            </a>

                            <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="assets/images/logo-light.png" alt="" height="17">
                                </span>
                            </a>
                        </div> --}}

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="" alt="">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->Nama }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Role Admin</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ Auth::user()->Nama }}!</h6>
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle" data-key="t-logout">Logout</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548"
                                style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-milenia-2.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-milenia.png') }}" alt="" height="60">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-milenia-2.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-milenia.png') }}" alt="" height="60">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-dashboard">Dashboard</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard-index') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard-index') }}">
                                <i data-feather="home" class="icon-dual"></i> <span
                                    data-key="t-dashboards">Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-title"><span data-key="t-employee">Employee</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.employee-index') ? 'active' : '' }}"
                                href="{{ route('admin.employee-index') }}">
                                <i data-feather="users" class="icon-dual"></i> <span data-key="t-employees">Employee
                                    Data</span>
                            </a>
                        </li>
                        <li class="menu-title"><span data-key="t-masterdata">Master Data</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#masterData" data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="{{ request()->routeIs('admin.cabang-index', 'admin.supplier-index', 'admin.category-index', 'admin.unit-index', 'admin.barang-index') ? 'true' : 'false' }}"
                                aria-controls="masterData">
                                <i data-feather="share-2" class="icon-dual"></i> <span
                                    data-key="t-masterdatas">Master
                                    data</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.cabang-index', 'admin.supplier-index', 'admin.category-index', 'admin.unit-index', 'admin.barang-index') ? 'show' : '' }}"
                                id="masterData">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cabang-index') }}"
                                            class="nav-link menu-link {{ request()->routeIs('admin.cabang-index') ? 'active' : '' }}"
                                            data-key="t-cabang"> Cabang </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.supplier-index') }}"
                                            class="nav-link menu-link {{ request()->routeIs('admin.supplier-index') ? 'active' : '' }}"
                                            data-key="t-supplier"> Supplier </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.category-index') }}"
                                            class="nav-link menu-link {{ request()->routeIs('admin.category-index') ? 'active' : '' }}"
                                            data-key="t-category"> Category </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.unit-index') }}"
                                            class="nav-link menu-link {{ request()->routeIs('admin.unit-index') ? 'active' : '' }}"
                                            data-key="t-unit"> Unit </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.barang-index') }}"
                                            class="nav-link menu-link {{ request()->routeIs('admin.barang-index') ? 'active' : '' }}"
                                            data-key="t-barang"> Barang </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="menu-title"><span data-key="t-milenia">PT. Milenia Mega mandiri</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.pr-milenia') ? 'active' : '' }}"
                                href="{{ route('admin.pr-milenia') }}">
                                <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="me-2"
                                    width="20px" alt="">
                                <span data-key="t-pr">Purchase Request</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.po-milenia') ? 'active' : '' }}"
                                href="{{ route('admin.po-milenia') }}">
                                <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="me-2"
                                    width="20px" alt="">
                                <span data-key="t-po">Input Purchase Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.statuspo-milenia') ? 'active' : '' }}"
                                href="{{ route('admin.statuspo-milenia') }}">
                                <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="me-2"
                                    width="20px" alt="">
                                <span data-key="t-statuspo">Status Purchase Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.laporanpo-milenia') ? 'active' : '' }}"
                                href="{{ route('admin.laporanpo-milenia') }}">
                                <img src="{{ asset('assets/images/logo-milenia-2.png') }}" class="me-2"
                                    width="20px" alt="">
                                <span data-key="t-laporanpo">Laporan Detail & Summary</span>
                            </a>
                        </li>

                        <li class="menu-title"><span data-key="t-map">PT. Mega Auto Prima</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.pr-map') ? 'active' : '' }}"
                                href="{{ route('admin.pr-map') }}">
                                <img src="{{ asset('assets/images/map-logo.png') }}" class="me-2" width="20px"
                                    alt="">
                                <span data-key="t-prmap">Purchase Request</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.po-map') ? 'active' : '' }}"
                                href="{{ route('admin.po-map') }}">
                                <img src="{{ asset('assets/images/map-logo.png') }}" class="me-2" width="20px"
                                    alt="">
                                <span data-key="t-po">Input Purchase Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.statuspo-map') ? 'active' : '' }}"
                                href="{{ route('admin.statuspo-map') }}">
                                <img src="{{ asset('assets/images/map-logo.png') }}" class="me-2" width="20px"
                                    alt="">
                                <span data-key="t-statuspo">Status Purchase Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.laporanpo-map') ? 'active' : '' }}"
                                href="{{ route('admin.laporanpo-map') }}">
                                <img src="{{ asset('assets/images/map-logo.png') }}" class="me-2" width="20px"
                                    alt="">
                                <span data-key="t-laporanpomap">Laporan Detail & Summary</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Milenia Group.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="https://anandasatriaa.github.io/" target="_blank"
                                    class="hover-tooltip">
                                    IT Milenia Group
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <!-- ApexCharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- barcharts init -->
    <script src="{{ asset('assets/js/pages/apexcharts-bar.init.js') }}"></script>

    <!-- Vector map -->
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

    <!-- Swiper slider js -->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- prismjs plugin -->
    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Library Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@5.0.4/dist/signature_pad.umd.min.js"></script>

    <!-- Sertakan PDF.js dan worker-nya -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    </script>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('page-loader').style.display = 'none';
            }, 1000);
        });
    </script>

    @yield('script')

</body>

</html>
