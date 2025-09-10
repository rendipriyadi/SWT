<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1" />
    <meta name="description" content="Siemens Safety Walk and Talk - Sistem Pelaporan Masalah Safety dan 5S" />
    <meta name="theme-color" content="#00205c" />
    <title>@yield('title', 'Safety Walk and Talk')</title>
    
    <!-- Preload Critical Assets -->
    <link rel="preload" href="{{ asset('css/styles.css') }}" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/datatables.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/style-components.css') }}?v={{ time() }}" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
    /* Fix cepat untuk layout di semua perangkat */
    @media (max-width: 767.98px) {
        #layoutSidenav_content {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        .sb-sidenav-toggled #layoutSidenav_nav {
            transform: translateX(0) !important;
        }
    }
    
    /* Fix untuk modal backdrop */
    .modal {
        z-index: 1050 !important;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    /* Pastikan body scrollbar tetap ada saat modal terbuka pada perangkat besar */
    @media (min-width: 768px) {
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    }
    </style>
</head>
{{-- <body class="sb-nav-fixed"> --}}
<body class="sb-nav-fixed {{ request()->routeIs('login') ? 'auth-layout' : '' }}">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-1" id="sidebarToggle" href="#!" aria-label="Toggle Sidebar">
            <i class="fas fa-bars text-white"></i>
        </button>
        <a class="navbar-brand ps-1 d-flex align-items-center" href="{{ url('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Siemens Logo" class="me-2" style="height:26px; filter: brightness(0) invert(1);">
            <span class="d-none d-sm-inline">Safety Walk and Talk</span>
            <span class="d-inline d-sm-none">Safety WnT</span>
        </a>

        <!-- Spacer to push right-side items -->
        <div class="flex-grow-1"></div>

        <!-- Right side: Profile -->
         <ul class="navbar-nav ms-auto me-2 align-items-center">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" id="userDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-regular fa-circle-user text-white" style="font-size: 1.5rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Sidebar and Main Content -->
    <div id="layoutSidenav">
        <!-- Sidebar -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav flex-column">
                        <a class="nav-link py-2 {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link py-2 {{ Request::is('laporan') ? 'active' : '' }}" href="{{ url('laporan') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Report
                        </a>
                        <a class="nav-link py-2 {{ Request::is('sejarah') ? 'active' : '' }}" href="{{ url('sejarah') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            History
                        </a>
                    </div>
                </div>
                <!-- Mobile close button -->
                <div class="d-block d-md-none text-center mt-2 mb-2">
                    <button class="btn btn-light btn-sm" id="sidebarClose">
                        <i class="fas fa-times"></i> Tutup Menu
                    </button>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main class="content-wrapper">
                @yield('content')
            </main>
            <footer class="py-1 bg-light mt-auto">
                <div class="container-fluid px-2">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">SPS</div>
                        <div class="text-muted">Siemens</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Toast Container -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 start-50 translate-middle-x p-2" style="z-index: 1080">
        <div id="mainToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <span id="mainToastIcon" class="me-2"></span>
                    <span id="mainToastBody"></span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/area-station.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filter-area-station.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filters.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/toast-init.js') }}?v={{ time() }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/datatables-init.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filter-icon.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/delete-handler.js') }}"></script>
    <script src="{{ asset('js/modal-handlers.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/status-badge.js') }}?v={{ time() }}"></script>
    
    <script>
    // Modal cleanup fix - improved version
    document.addEventListener('DOMContentLoaded', function() {
        // Saat modal selesai ditutup, periksa apakah masih ada modal lain yang terbuka
        $(document).on('hidden.bs.modal', '.modal', function() {
            const stillOpen = $('.modal.show').length > 0;
            
            // Jika tidak ada modal yang terbuka, bersihkan elemen backdrop dan class modal
            if (!stillOpen) {
                setTimeout(function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('padding-right', '');
                    $('body').css('overflow', '');
                }, 50);
            }
            
            console.log('Modal closed, others still open:', stillOpen);
        });
        
        // Memastikan sidebar mobile menutup saat klik di luar sidebar
        $(document).on('click', function(e) {
            const windowWidth = window.innerWidth;
            if (windowWidth < 768 && 
                !$('#layoutSidenav_nav').get(0).contains(e.target) && 
                !$('#sidebarToggle').get(0).contains(e.target) &&
                $('body').hasClass('sb-sidenav-toggled')) {
                $('body').removeClass('sb-sidenav-toggled');
            }
        });
    });
    </script>

    <!-- Global Modals -->
    <div class="modal fade description-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h5 class="modal-title fs-6" id="descriptionModalLabel">Problem Description Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body small" id="descriptionModalBody">
            <!-- Content will be loaded dynamically -->
          </div>
          <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    @stack('scripts')
</body>
</html>