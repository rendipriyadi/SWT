<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1" />
    <meta name="description" content="Siemens Safety Walk and Talk - Sistem Pelaporan Masalah Safety dan 5S" />
    <meta name="theme-color" content="#009999" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Safety Walk and Talk')</title>
    
    <!-- Favicon - Must be early in head -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <meta name="msapplication-TileImage" content="{{ asset('favicon.ico') }}?v={{ time() }}">
    
    <!-- Preload Critical Assets -->
    <link rel="preload" href="{{ asset('css/styles.css') }}" as="style">

    <!-- Google Fonts - Inter (Optimized) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Stylesheets -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/datatables.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/style-components.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('css/master-data.css') }}?v={{ time() }}" rel="stylesheet" />
    <!-- DataTables Responsive CSS -->
    <link href="{{ asset('css/datatables-responsive.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Datepicker CSS -->
    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Elegant Datepicker CSS -->
    <link href="{{ asset('css/elegant-datepicker.css') }}?v={{ time() }}" rel="stylesheet">
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- CSRF Token injected above -->
    
    @stack('head')

    <script>
    // Initialize theme and sidebar state immediately to prevent layout shift
    (function() {
        // Apply theme from localStorage or system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
        
        if (shouldUseDark) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        // Initialize sidebar state (desktop only)
        const isDesktop = window.innerWidth >= 992;
        const savedCollapsed = localStorage.getItem('sidebar-collapsed') === '1';
        if (isDesktop && savedCollapsed) {
            document.documentElement.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
        document.body.classList.add('sidebar-initialized');
    })();
    </script>
    
    <style>
    /* Default state: expanded (full sidebar) to prevent flash */

    body:not(.sidebar-initialized) #layoutSidenav_nav {
        width: var(--sidebar-width) !important;
    }

    /* Override for collapsed state when needed (applied by early script) */
    body.sidebar-collapsed:not(.sidebar-initialized) #layoutSidenav_content {
        margin-left: var(--sidebar-collapsed-width) !important;
        width: calc(100% - var(--sidebar-collapsed-width)) !important;
        transition: none !important;
    }
    body.sidebar-collapsed:not(.sidebar-initialized) #layoutSidenav_nav {
        width: var(--sidebar-collapsed-width) !important;
        transition: none !important;
    }

    /* Disable transitions during page load */
    body:not(.sidebar-initialized) #layoutSidenav_content,
    body:not(.sidebar-initialized) #layoutSidenav_nav { transition: none !important; }

    /* Modern UI Improvements */
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --background-color: #f8fafc;
        --surface-color: #ffffff;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        /* Layout sizing */
        --header-height: 56px;
        /* Sidebar width override so brand text fits */
        --sidebar-width: 240px;
        --sidebar-collapsed-width: 64px;
        --sidebar-collapsed-height: 44px;
        --content-padding-x: 1.5rem;
        --content-padding-y: 1.5rem;
    }

    html, body { height: 100%; }
    body {
        background-color: var(--background-color);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        font-weight: 400;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    @media (max-width: 767.98px) {
        body {
            padding-top: var(--header-height);
        }
    }

    /* Layout Structure */
    #layoutSidenav {
        display: flex;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        overflow: hidden;
    }

    @media (max-width: 767.98px) {
        #layoutSidenav {
            position: static;
            display: block;
            height: calc(100vh - var(--header-height));
        }
    }

    /* Modern Navbar */
    .sb-topnav {
        background: #ffffff !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        right: 0;
        z-index: 1030;
        height: var(--header-height);
        transition: left 0.25s ease, width 0.25s ease;
    }

    /* Avoid layout collision during initial paint */
    body:not(.sidebar-ready) .sb-topnav,
    body:not(.sidebar-ready) #layoutSidenav_nav,
    body:not(.sidebar-ready) #layoutSidenav_content {
        transition: none !important;
    }

    @media (min-width: 992px) {
        .sb-topnav {
            left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: left 0.25s ease, width 0.25s ease;
        }
        body.sidebar-collapsed .sb-topnav {
            left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }
        /* Desktop: keep sidebar docked, not overlay */
        #layoutSidenav_nav { transform: none !important; display: block; }
        body.sidebar-collapsed #layoutSidenav_nav { transform: none !important; }

        /* Ensure desktop layout always aligns using html or body flag */
        html:not(.sidebar-collapsed) #layoutSidenav_nav { width: var(--sidebar-width) !important; }
        html.sidebar-collapsed #layoutSidenav_nav { width: var(--sidebar-collapsed-width) !important; }
    }

    /* Navbar left group (logo + hamburger) */
    .navbar-left-group {
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .navbar-left-group .brand-mini {
        width: 28px; height: 28px; border-radius: 6px; object-fit: cover;
    }
    .navbar-left-group #sidebarToggle {
        padding: .25rem .5rem;
    }

    /* Safety Quotes Styling */
    .navbar-quotes {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-left: 1rem;
        max-width: 420px;
    }
    
    .quote-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: #374151;
        text-align: center;
        white-space: nowrap;
        opacity: 0.9;
        transition: opacity 0.3s ease;
        display: block;
    }
    
    .quote-text:hover {
        opacity: 1;
    }

    /* Navbar container adjustments */
    .sb-topnav .container-fluid {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        gap: .75rem;
    }


    /* Modern Sidebar */
    #layoutSidenav_nav {
        background: #ffffff;
        border-right: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        width: var(--sidebar-width);
        min-width: 0;
        flex-shrink: 0;
        position: fixed;
        height: 100vh;
        z-index: 1040;
        left: 0;
        top: 0;
        transition: width 0.3s ease;
        will-change: width;
    }

    /* Desktop collapsed state */
    @media (min-width: 992px) {
        body.sidebar-collapsed #layoutSidenav_nav {
            width: var(--sidebar-collapsed-width);
        }
        /* Main content follows same logic as navbar */
        #layoutSidenav_content {
            flex-grow: 1 !important;
            overflow-y: auto !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: calc(100vh - var(--header-height)) !important; /* allow taller pages, no overflow glitch */
            margin-left: var(--sidebar-width) !important;
            width: calc(100% - var(--sidebar-width)) !important;
            /* margin-top removed to avoid double height accounting */
            transition: margin-left 0.3s ease, width 0.3s ease !important;
        }
        body.sidebar-collapsed #layoutSidenav_content {
            margin-left: var(--sidebar-collapsed-width) !important;
            width: calc(100% - var(--sidebar-collapsed-width)) !important;
            transition: margin-left 0.3s ease, width 0.3s ease !important;
        }
        /* Mirror rules when the flag is on html instead of body */
        html.sidebar-collapsed #layoutSidenav_content {
            margin-left: var(--sidebar-collapsed-width) !important;
            width: calc(100% - var(--sidebar-collapsed-width)) !important;
        }
        html:not(.sidebar-collapsed) #layoutSidenav_content {
            margin-left: var(--sidebar-width) !important;
            width: calc(100% - var(--sidebar-width)) !important;
        }
        body.sidebar-collapsed .sb-sidenav .px-3.py-3 .brand-text,
        body.sidebar-collapsed .sb-sidenav .nav-text {
            opacity: 0;
            visibility: hidden;
            width: 0;
            margin: 0;
            padding: 0;
        }
        /* Ensure brand text fully hidden in collapsed (desktop) */
        body.sidebar-collapsed .sb-sidenav .px-3.py-3 .brand-text { display: none !important; }
        body.sidebar-collapsed .sb-sidenav .px-3.py-3 { justify-content: center; }
        body.sidebar-collapsed .sb-sidenav .sb-nav-link-icon { margin-right: 0; display: inline-flex; }
        body.sidebar-collapsed .sb-sidenav .nav-link {
            justify-content: center;
            padding: 0.45rem 0.3rem;
            margin: 0.15rem 0; /* keep within rail */
            background: transparent;
            width: 100%;
            text-align: center;
            transform: none !important;
        }
        body.sidebar-collapsed .sb-sidenav .nav-link::before { display: none; }
        body.sidebar-collapsed .sb-sidenav .nav-link:hover { 
            background: rgba(37, 99, 235, 0.1); 
            transform: none; 
            box-shadow: none; 
        }
        
        body.sidebar-collapsed .sb-sidenav .nav-link:hover .sb-nav-link-icon {
            color: #1f2937;
        }
        body.sidebar-collapsed .sb-sidenav .nav-link.active { 
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%) !important;
            color: #ffffff !important;
            transform: none; 
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        body.sidebar-collapsed .sb-sidenav .nav-link .nav-text { display: none !important; }
        body.sidebar-collapsed .sb-sidenav .nav { align-items: stretch; }
        body.sidebar-collapsed .sb-sidenav .sb-nav-link-icon {
            color: #6b7280;
            font-size: 1.15rem;
            width: 24px;
            height: 24px;
        }
        
        /* Active icon color for collapsed sidebar */
        body.sidebar-collapsed .sb-sidenav .nav-link.active .sb-nav-link-icon {
            color: #ffffff !important;
        }
        /* Keep S logo visible in collapsed mode */
        body.sidebar-collapsed #layoutSidenav_nav .sb-sidenav .px-3.py-3 img { display: inline-block; width: 22px; height: 22px; }
        body.sidebar-collapsed #layoutSidenav_nav .sb-sidenav .px-3.py-3 {
            padding: .35rem !important;
            margin-bottom: .25rem;
            justify-content: center;
            min-height: var(--sidebar-collapsed-height);
        }
    }

    .sb-sidenav {
        background: transparent;
        color: #374151;
    }

    .sb-sidenav .nav-link {
        display: flex;
        align-items: center;
        gap: .75rem;
        border-radius: var(--radius-lg);
        margin: 0.25rem 0.75rem;
        padding: 0.875rem 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #6b7280;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .sb-sidenav .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: var(--radius-lg);
    }

    .sb-sidenav .nav-link:hover {
        background: rgba(37, 99, 235, 0.1);
        color: #1f2937;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .sb-sidenav .nav-link:hover::before {
        opacity: 1;
    }

    .sb-sidenav .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        transform: translateX(4px);
    }

    .sb-sidenav .nav-link.active::before {
        opacity: 0;
    }

    .sb-nav-link-icon {
        color: #9ca3af;
        transition: color 0.3s ease;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .sb-sidenav .nav-link:hover .sb-nav-link-icon {
        color: #1f2937;
    }
    .sb-sidenav .nav-link.active .sb-nav-link-icon {
        color: #ffffff;
    }

    /* Sidebar Brand */
    .sb-sidenav .px-3.py-3 {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(59, 130, 246, 0.02) 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* match navbar */
        margin-bottom: 1rem;
        padding: 0 .75rem !important; /* tighter to align with navbar */
        min-height: var(--header-height); /* match navbar height */
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Unified logo size to match navbar brand (26px) */
    .sb-sidenav .px-3.py-3 img { width: 26px; height: 26px; object-fit: contain; border-radius: 6px; display: block; }

    /* Collapsed rail: keep same border and vertical alignment */
    body.sidebar-collapsed #layoutSidenav_nav .sb-sidenav .px-3.py-3 {
        padding: 0.5rem !important;
        margin-bottom: .25rem;
        justify-content: center;
        min-height: var(--header-height); /* match navbar */
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    body.sidebar-collapsed #layoutSidenav_nav .sb-sidenav .px-3.py-3 img { width: 26px; height: 26px; }

    .sb-sidenav .px-3.py-3 .fw-semibold {
        color: #1f2937 !important;
        font-weight: 600;
        font-size: 1.1rem;
        white-space: nowrap; /* keep brand on one line */
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        min-width: 0;
        line-height: 1.2;
    }

    /* Hide brand text when sidebar is collapsed (mini-rail) */
    body.sidebar-collapsed #layoutSidenav_nav .brand-text {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    html.sidebar-collapsed #layoutSidenav_nav .brand-text {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .sb-sidenav .brand-toggle {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        color: #6b7280;
        background: transparent;
        transition: all 0.3s ease;
    }
    .sb-sidenav .brand-toggle:hover {
        color: #1f2937;
        background: rgba(0, 0, 0, 0.05);
    }

    /* Mobile sidebar brand */
    @media (max-width: 767.98px) {
        .sb-sidenav .px-3.py-3 .fw-semibold {
            font-size: 1rem;
            white-space: nowrap; /* prevent reflow when opening */
        }
    }

    .sb-sidenav .px-3.py-3 img {
        border-radius: var(--radius-md);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Navigation divider and header styles */
    .nav-divider {
        height: 1px;
        background: rgba(0, 0, 0, 0.1);
        margin: 0.5rem 1rem;
    }

    .nav-header {
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0.5rem 0;
    }

    /* Hide section headers/dividers when sidebar is collapsed (desktop) */
    @media (min-width: 992px) {
        body.sidebar-collapsed #layoutSidenav_nav .nav-header,
        body.sidebar-collapsed #layoutSidenav_nav .nav-divider {
            display: none !important;
        }
    }

    /* Override Bootstrap conflicts */
    body #layoutSidenav_content {
        position: relative !important;
        z-index: 1 !important;
    }

    /* Ensure content wrapper doesn't interfere */
    .content-wrapper {
        padding: var(--content-padding-y) var(--content-padding-x);
        flex: 1 0 auto; /* grow to fill, but allow page to extend */
        background: var(--background-color);
        min-height: auto; /* let actual content height decide */
        box-sizing: border-box;
        width: 100%;
        position: relative;
        z-index: 1;
        padding-bottom: 1rem; /* spacing above sticky footer */
        margin-top: var(--header-height); /* account for fixed navbar */
    }

    /* Footer sits at bottom after content; when content is short, flex pushes it down */
    #layoutSidenav_content > footer { 
        margin-top: auto; 
        background: var(--surface-color);
        border-top: 1px solid var(--border-color);
        padding: 0.75rem 1rem; /* larger vertical rhythm */
        font-size: 0.95rem; /* slightly larger text */
    }
    #layoutSidenav_content > footer .text-muted {
        color: var(--text-secondary) !important;
        font-weight: 500; /* a bit bolder for readability */
        letter-spacing: 0.01em;
    }

    /* Modern Cards */
    .card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        background: var(--surface-color);
        transition: all 0.2s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
    }

    /* Modern Buttons */
    .btn {
        border-radius: var(--radius-md);
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        padding: 0.5rem 1rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Modern Tables */
    .table {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        width: 100%;
    }

    .table thead th {
        background: linear-gradient(135deg, var(--background-color) 0%, #f1f5f9 100%);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        white-space: nowrap; /* keep headings readable */
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        font-size: 0.875rem;
        word-break: normal;
        overflow-wrap: anywhere; /* allow long words to wrap when needed */
    }

    /* Keep narrow columns compact */
    .table th:nth-child(1), .table td:nth-child(1) { width: 52px; }
    .table th:nth-child(2), .table td:nth-child(2) { width: 140px; }
    .table th:nth-child(3), .table td:nth-child(3) { width: 100px; }
    /* Photo column */
    .table th:nth-child(3) img, .table td:nth-child(3) img, .thumb-sm { max-width: 60px; height: 60px; object-fit: cover; }

    /* DataTables wrapper: allow horizontal scroll and full width */
    .dataTables_wrapper { overflow-x: visible; width: 100%; }
    .dataTables_scrollBody { overflow-x: auto !important; }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Table Striped Effect */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-striped tbody tr:nth-of-type(odd):hover {
        background: rgba(37, 99, 235, 0.06);
    }

    /* Status Badges */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-sm);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge-ditugaskan {
        background-color: var(--warning-color);
        color: #92400e;
    }

    .badge-selesai {
        background-color: var(--success-color);
        color: white;
    }

    .badge-pending {
        background-color: var(--secondary-color);
        color: white;
    }

    /* Action Buttons */
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        border-radius: var(--radius-sm);
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
        background: var(--surface-color);
        color: var(--text-secondary);
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }

    /* Photo Thumbnails */
    .thumb-sm {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--radius-md);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .thumb-sm:hover {
        transform: scale(1.1);
        box-shadow: var(--shadow-md);
    }

    /* Stats Cards */
    .stats-card {
        background: linear-gradient(135deg, var(--surface-color) 0%, #f8fafc 100%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .stats-card h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stats-card .number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        /* Hide quotes on mobile */
        .navbar-quotes {
            display: none !important;
        }

        /* Layout adjustments for mobile */
        #layoutSidenav {
            position: static;
            display: block !important;
            height: auto;
            width: 100% !important;
        }

        /* Navbar always visible on mobile */
        .sb-topnav {
            display: flex !important;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        /* Neutralize any desktop offsets if class lingered */
        body.sidebar-collapsed .sb-topnav,
        html.sidebar-collapsed .sb-topnav { left: 0 !important; width: 100% !important; }

        /* Sidebar positioning for mobile - hidden by default */
        #layoutSidenav_nav {
            position: fixed;
            top: var(--header-height);
            left: -280px; /* hidden by default */
            bottom: 0;
            width: 280px;
            transform: none;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1060; /* above overlay */
            background: #ffffff;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }
        /* When hidden, remove visual edge */
        body:not(.sb-sidenav-toggled) #layoutSidenav_nav { box-shadow: none; border-right: none; }
        
        /* Show sidebar when toggled */
        body.sb-sidenav-toggled #layoutSidenav_nav,
        html.sb-sidenav-toggled #layoutSidenav_nav {
            left: 0 !important;
        }

        /* Overlay for mobile sidebar */
        #sidebarOverlay {
            position: fixed;
            top: var(--header-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050; /* sits above content but below sidebar */
            backdrop-filter: blur(4px);
            display: none;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .sb-sidenav-toggled #sidebarOverlay { display: block; opacity: 1; pointer-events: auto; }

        /* Main content adjustments for mobile */
        #layoutSidenav_content {
            flex: 1 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important; /* pastikan tidak ketarik sidebar */
            margin-right: 0 !important;
            display: block !important;
            position: relative !important;
            z-index: 1040 !important; /* below overlay */
        }

        /* Ensure content wrapper and inner containers span full width */
        #layoutSidenav_content .content-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            display: block !important;
            flex: 1 !important;
            box-sizing: border-box;
        }
        /* Broaden to any descendant containers */
        #layoutSidenav_content .content-wrapper .container,
        #layoutSidenav_content .content-wrapper .container-fluid,
        #layoutSidenav_content > .container,
        #layoutSidenav_content > .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: var(--content-padding-x);
            padding-right: var(--content-padding-x);
        }
        /* Ensure rows don't create unexpected horizontal shift */
        #layoutSidenav_content .row { margin-left: 0 !important; margin-right: 0 !important; }

        /* Z-index: keep content above sidebar background when closed */
        #layoutSidenav_content { z-index: 1 !important; position: relative !important; }
        #layoutSidenav_nav { z-index: 1050 !important; }
        body:not(.sb-sidenav-toggled) #sidebarOverlay { display: none !important; opacity: 0 !important; pointer-events: none !important; }
        /* Keep content full width even when sidebar is toggled */
        html.sb-sidenav-toggled #layoutSidenav_content,
        body.sb-sidenav-toggled #layoutSidenav_content {
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        body.sb-sidenav-toggled main.content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        /* Prevent body scroll when sidebar open */
        html.sb-sidenav-toggled, body.sb-sidenav-toggled { overflow: hidden; }

        /* Show labels on mobile when sidebar is open */
        .sb-sidenav-toggled #layoutSidenav_nav .sb-sidenav .px-3.py-3 .brand-text,
        .sb-sidenav-toggled #layoutSidenav_nav .sb-sidenav .nav-text { 
            display: block; 
        }
        
        /* Mobile sidebar brand styling */
        .sb-sidenav-toggled #layoutSidenav_nav .sb-sidenav .px-3.py-3 {
            padding: 0.75rem !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile sidebar links */
        .sb-sidenav-toggled #layoutSidenav_nav .sb-sidenav .nav-link { 
            justify-content: flex-start; 
            padding: 0.875rem 1.25rem; 
            margin: 0.25rem 0.75rem;
        }
        
        .sb-sidenav-toggled #layoutSidenav_nav .sb-sidenav .sb-nav-link-icon { 
            margin-right: 0.75rem; 
        }

        /* Stats cards responsive */
        .stats-card {
            padding: 1rem;
        }

        .stats-card .number {
            font-size: 1.5rem;
        }

        .sb-sidenav .nav-link {
            margin: 0.25rem 0.5rem;
            padding: 0.75rem 1rem;
        }

        /* Dashboard header mobile */
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .dashboard-header h1 {
            font-size: 1.5rem;
        }

        /* Page header mobile */
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }
    }

    /* Modal fixes */
    .modal {
        z-index: 1050 !important;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    @media (min-width: 768px) {
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- Apply sidebar state immediately to prevent flicker -->
    <script>
        (function() {
            const isDesktop = window.innerWidth >= 992;
            if (isDesktop) {
                const shouldBeCollapsed = localStorage.getItem('sidebar-collapsed') === '1';
                if (shouldBeCollapsed) {
                    document.body.classList.add('sidebar-collapsed');
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            }
        })();
    </script>
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-light">
        <div class="container-fluid">
            <!-- Left side: Toggle + Quotes -->
            <div class="d-flex align-items-center">
        <!-- Mobile Sidebar Toggle -->
                <button class="btn btn-link btn-sm me-1" id="sidebarToggle" href="#!" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars text-dark"></i>
        </button>
            </div>

        <!-- Right side: Theme Toggle + Profile -->
            <ul class="navbar-nav ms-auto align-items-center">
            <!-- Theme Toggle Button -->
            <li class="nav-item me-3">
                <button class="btn btn-outline-secondary btn-sm" id="themeToggle" title="Toggle Dark Mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2 text-dark fw-semibold">Admin</span>
                    <i class="fa-regular fa-circle-user text-dark" style="font-size: 2rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        </div>
    </nav>

    <!-- Sidebar and Main Content -->
    <div id="layoutSidenav">
        <!-- Mobile overlay -->
        <div id="sidebarOverlay" class="d-md-none"></div>
        <!-- Sidebar -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="px-3 py-3 border-bottom d-flex align-items-center gap-2">
                    <img src="{{ asset('images/static/favicon.ico') }}" alt="S" style="width:28px;height:28px;border-radius:6px;object-fit:contain;">
                    <div class="fw-semibold brand-text" style="line-height:1.1;">Safety Walk and Talk</div>
                </div>
                <div class="sb-sidenav-menu">
                    <div class="nav flex-column">
                        <!-- HOME Section -->
                        <div class="nav-header text-muted small fw-bold px-3 py-2">HOME</div>
                        <a class="nav-link py-2 {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        
                        <!-- MASTER DATA Section (Always visible - Admin mode) -->
                        <div class="nav-divider my-2"></div>
                        <div class="nav-header text-muted small fw-bold px-3 py-2">MASTER DATA</div>
                        <a class="nav-link py-2 {{ Request::is('master-data/department*') ? 'active' : '' }}" href="{{ route('master-data.department.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                            <span class="nav-text">Department</span>
                        </a>
                        <a class="nav-link py-2 {{ Request::is('master-data/area*') ? 'active' : '' }}" href="{{ route('master-data.area.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <span class="nav-text">Area</span>
                        </a>
                        <a class="nav-link py-2 {{ Request::is('master-data/problem-category*') ? 'active' : '' }}" href="{{ route('master-data.problem-category.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            <span class="nav-text">Problem Category</span>
                        </a>
                        
                        <!-- TRANSACTION Section -->
                        <div class="nav-divider my-2"></div>
                        <div class="nav-header text-muted small fw-bold px-3 py-2">TRANSACTION</div>
                        <a class="nav-link py-2 {{ Request::is('laporan') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                            <div class="sb-nav-link-icon"><i class="far fa-file-alt"></i></div>
                            <span class="nav-text">Report</span>
                        </a>
                        <a class="nav-link py-2 {{ Request::is('sejarah') ? 'active' : '' }}" href="{{ route('sejarah.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            <span class="nav-text">History</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main class="content-wrapper">
                @yield('content')
            </main>
            <footer class="py-1 mt-auto">
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
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script>
      // Fallback to CDN if local jQuery is unavailable
      if (typeof window.jQuery === 'undefined') {
        var s = document.createElement('script');
        s.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        document.head.appendChild(s);
      }
    </script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Bootstrap Datepicker JS -->
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Elegant Datepicker JS -->
    <script src="{{ asset('js/elegant-datepicker.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/area-station.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filter-area-station.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filters.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/toast-init.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/datatables-bootstrap5.min.js') }}"></script>
    <!-- DataTables Responsive JS -->
    <script src="{{ asset('js/datatables-responsive.min.js') }}"></script>
    <script src="{{ asset('js/responsive-bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/datatables-init.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/filter-icon.js') }}"></script>
    <!-- SweetAlert2 for reports and history only -->
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/delete-handler.js') }}"></script>
    <script src="{{ asset('js/modal-handlers.js') }}?v={{ time() }}_{{ rand(1000, 9999) }}"></script>
    <script src="{{ asset('js/status-badge.js') }}?v={{ time() }}"></script>
    
    <!-- Global Route Configuration for JavaScript -->
    <script>
        window.routes = {
            supervisor: "{{ route('supervisor', ['id' => ':id']) }}",
            penyelesaian: "{{ route('laporan.penyelesaian', ['id' => ':id']) }}".replace(':id', ':encryptedId'),
            stations: "{{ route('penanggung.jawab') }}",
            allPenanggungJawab: "{{ route('api.all-penanggung-jawab') }}",
            laporanShow: "{{ route('laporan.show', ['id' => ':id']) }}",
            sejarahDownload: "{{ route('sejarah.download') }}"
        };
    </script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;
        
        if (themeToggle && themeIcon) {
            // Update icon based on current theme
            const updateThemeIcon = () => {
                const isDark = html.getAttribute('data-theme') === 'dark';
                themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            };
            
            // Initialize icon
            updateThemeIcon();
            
            // Theme toggle handler
            themeToggle.addEventListener('click', () => {
                const isDark = html.getAttribute('data-theme') === 'dark';
                const newTheme = isDark ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon();
            });
        }

        // Close sidebar button for mobile
        $('#sidebarClose').on('click', function() {
            $('body').removeClass('sb-sidenav-toggled');
        });
        // Close when tapping overlay on mobile
        const overlayEl = document.getElementById('sidebarOverlay');
        if (overlayEl) {
            overlayEl.addEventListener('click', function(){
                document.body.classList.remove('sb-sidenav-toggled');
                document.documentElement.classList.remove('sb-sidenav-toggled');
            });
        }

        // Also close sidebar when clicking any content area on mobile (guard for null)
        const contentEl = document.getElementById('layoutSidenav_content');
        if (contentEl) {
            contentEl.addEventListener('click', function(){
                if (document.body.classList && document.body.classList.contains('sb-sidenav-toggled')) {
                    document.body.classList.remove('sb-sidenav-toggled');
                    document.documentElement.classList.remove('sb-sidenav-toggled');
                }
            }, true);
        }

        // Desktop/mobile sidebar toggle from navbar hamburger
        const bodyEl = document.body;
        const navbarToggle = document.getElementById('sidebarToggle');

        // Apply initial state based on viewport - run immediately
        const applyInitialSidebarState = () => {
            const isDesktop = window.innerWidth >= 992;
            if (isDesktop) {
                const shouldBeCollapsed = localStorage.getItem('sidebar-collapsed') === '1';
                bodyEl.classList.toggle('sidebar-collapsed', shouldBeCollapsed);
                document.documentElement.classList.toggle('sidebar-collapsed', shouldBeCollapsed);
                bodyEl.classList.remove('sb-sidenav-toggled');
            } else {
                // Mobile: ensure sidebar is closed by default
                bodyEl.classList.remove('sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-collapsed');
                bodyEl.classList.remove('sb-sidenav-toggled');
            }
        };

        // Apply state immediately, before any rendering
        applyInitialSidebarState();

        // Enable transitions after initial state to avoid jump
        requestAnimationFrame(() => { document.body.classList.add('sidebar-ready'); });

        // Add tooltips for collapsed sidebar
        const addCollapsedTooltips = () => {
            const isDesktop = window.innerWidth >= 992;
            const isCollapsed = bodyEl.classList.contains('sidebar-collapsed');
            
            if (isDesktop && isCollapsed) {
                // Add tooltip attributes to nav links
                const navLinks = document.querySelectorAll('#layoutSidenav_nav .nav-link');
                navLinks.forEach(link => {
                    const textElement = link.querySelector('.nav-text');
                    if (textElement) {
                        const tooltipText = textElement.textContent.trim();
                        link.setAttribute('data-bs-toggle', 'tooltip');
                        link.setAttribute('data-bs-placement', 'right');
                        link.setAttribute('title', tooltipText);
                    }
                });
                
                // Initialize tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            } else {
                // Remove tooltips when expanded
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    const tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                });
            }
        };

        if (navbarToggle) {
            navbarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isDesktop = window.innerWidth >= 992;
                if (!isDesktop) {
                    bodyEl.classList.toggle('sb-sidenav-toggled');
                    document.documentElement.classList.toggle('sb-sidenav-toggled');
                    
                    // Notify DataTables and other components that sidebar has toggled
                    try {
                        window.dispatchEvent(new CustomEvent('sidebar:toggled'));
                    } catch (e) {}
                } else {
                    const isCollapsed = bodyEl.classList.contains('sidebar-collapsed');
                    bodyEl.classList.toggle('sidebar-collapsed', !isCollapsed ? true : false);
                    document.documentElement.classList.toggle('sidebar-collapsed', !isCollapsed ? true : false);
                    localStorage.setItem('sidebar-collapsed', (!isCollapsed) ? '1' : '0');
                    // Ensure any mobile class is cleared on desktop toggle
                    bodyEl.classList.remove('sb-sidenav-toggled');
                    document.documentElement.classList.remove('sb-sidenav-toggled');
                    
                    // Notify DataTables and other components that sidebar has toggled
                    try {
                        window.dispatchEvent(new CustomEvent('sidebar:toggled'));
                    } catch (e) {}
                    
                    // Update tooltips after toggle
                    setTimeout(() => {
                        addCollapsedTooltips();
                    }, 300); // Wait for transition to complete
                }
            });
        }

        // Initialize tooltips on page load
        addCollapsedTooltips();

        // Re-apply correct state on resize with debounce to avoid flicker
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                applyInitialSidebarState();
                addCollapsedTooltips();
            }, 120);
        });

        // Persist state on navigation
        window.addEventListener('beforeunload', function() {
            const isCollapsed = bodyEl.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed ? '1' : '0');
        });
    });
    </script>


    <!-- Global Modals -->
    <div class="modal fade description-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="descriptionModalLabel">
              <i class="fas fa-file-alt me-2 text-primary"></i>Problem Description Details
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="descriptionModalBody" style="max-height: 60vh; overflow-y: auto;">
            <!-- Content will be loaded dynamically -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Close
            </button>
          </div>
        </div>
      </div>
    </div>

    @stack('scripts')
</body>
</html>