<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sakina Kantin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="{{ url('/legacy/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-mini-width: 92px;
            --brand: #4e73df;
            --brand-dark: #2e59d9;
        }

        body {
            font-family: "Nunito", sans-serif;
            background: #f8f9fc;
            color: #5a5c69;
        }

        a:focus-visible,
        button:focus-visible,
        .btn:focus-visible,
        .nav-link:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: 2px solid #1d4ed8;
            outline-offset: 2px;
            box-shadow: none !important;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, .28);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .22s ease, visibility .22s ease;
            z-index: 1035;
        }

        .app-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--brand) 10%, var(--brand-dark) 100%);
            color: #fff;
            padding: 1rem 0;
            flex-shrink: 0;
            transition: transform .2s ease-in-out;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, .35) transparent;
        }

        .app-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .app-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .3);
            border-radius: 999px;
        }

        .app-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: .02em;
            padding: .75rem 1rem 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, .14);
            margin-bottom: .8rem;
        }

        .app-brand .label,
        .app-sidebar .label {
            display: inline-block;
            transition: opacity .15s;
        }

        .app-sidebar .nav-link {
            color: rgba(255, 255, 255, .9);
            font-weight: 700;
            border-radius: .5rem;
            margin: 0 .75rem .25rem;
            padding: .6rem .8rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .app-sidebar .nav-link:hover,
        .app-sidebar .nav-link.active {
            background: rgba(255, 255, 255, .16);
            color: #fff;
        }

        .app-sidebar > .nav-link.active,
        .app-sidebar nav > .nav-link.active {
            box-shadow: inset 3px 0 0 rgba(255, 255, 255, .8);
        }

        .app-sidebar .menu-title {
            color: rgba(255, 255, 255, .65);
            text-transform: uppercase;
            font-size: .74rem;
            letter-spacing: .08em;
            margin: 1rem 1.1rem .5rem;
            font-weight: 800;
        }

        .app-sidebar .collapse-toggle::after {
            content: "\F282";
            font-family: "bootstrap-icons";
            margin-left: auto;
            transition: transform .2s;
            font-size: .75rem;
        }

        .app-sidebar .collapse-toggle[aria-expanded="true"]::after {
            transform: rotate(90deg);
        }

        .app-sidebar .collapse {
            transition: height .2s cubic-bezier(.22, .61, .36, 1);
        }

        .app-sidebar .collapsing {
            height: 0;
            overflow: hidden;
            transition: height .2s cubic-bezier(.22, .61, .36, 1);
        }

        .sidebar-sub {
            margin: .1rem .9rem .5rem;
            background: #fff;
            border-radius: .35rem;
            padding: .35rem;
        }

        .sidebar-sub .nav-link {
            margin: 0;
            border-radius: .35rem;
            font-weight: 700;
            font-size: .86rem;
            padding: .42rem .65rem;
            color: #4e73df;
            position: relative;
            padding-left: 1rem;
        }

        .sidebar-sub .nav-link:hover,
        .sidebar-sub .nav-link.active {
            background: #eaecf4;
            color: #224abe;
        }

        .sidebar-sub .nav-link::before {
            content: "";
            width: .34rem;
            height: .34rem;
            border-radius: 999px;
            background: #9fb3ef;
            position: absolute;
            left: .52rem;
            top: 50%;
            transform: translateY(-50%);
            transition: transform .15s ease, background-color .15s ease;
        }

        .sidebar-sub .nav-link.active::before,
        .sidebar-sub .nav-link:hover::before {
            background: #224abe;
            transform: translateY(-50%) scale(1.08);
        }

        .sidebar-sub-title {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #858796;
            font-weight: 800;
            padding: .3rem .65rem .22rem;
            margin-top: .1rem;
        }

        .sidebar-toggle-wrap {
            margin-top: .6rem;
            padding: 0 1rem;
            text-align: center;
        }

        .sidebar-toggle-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 100%;
            border: 0;
            color: rgba(255, 255, 255, .8);
            background: rgba(255, 255, 255, .2);
            padding: 0;
            font-weight: 700;
            font-size: .95rem;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, .28);
        }

        .app-main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .55rem 1rem;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .08);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: .65rem;
            min-width: 0;
        }

        .topbar-title {
            min-width: 0;
        }

        .topbar .hello {
            font-weight: 700;
            color: #4e73df;
            line-height: 1.15;
        }

        .topbar .sub {
            font-size: .74rem;
            color: #858796;
            line-height: 1.1;
        }

        #sidebarToggleTop {
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .user-chip {
            border: 1px solid #e3e6f0;
            background: #fff;
            color: #4b5563;
            font-weight: 700;
            font-size: .84rem;
            padding: .32rem .55rem .32rem .65rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: .42rem;
        }

        .user-chip:hover {
            background: #f8f9fc;
        }

        .topbar .img-profile {
            width: 1.85rem;
            height: 1.85rem;
            object-fit: cover;
            border: 1px solid #dbe3f3;
        }

        .topbar .dropdown-menu {
            border: 1px solid #e3e6f0;
            border-radius: .55rem;
            padding: .35rem;
        }

        .main-wrap {
            padding: .95rem 1rem;
        }

        .page-title {
            color: #5a5c69;
            font-weight: 800;
            margin-bottom: .2rem;
        }

        .breadcrumb-wrap {
            margin-bottom: 1rem;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: .86rem;
        }

        .card .card-header {
            font-weight: 800;
            background: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: .72rem 1rem;
        }

        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .08);
            border-radius: .6rem;
        }

        .card .card-body {
            padding: .85rem .95rem;
        }

        .table thead th {
            white-space: nowrap;
            background: #f8f9fc;
        }

        .table-head-soft th {
            background: #eaecf4 !important;
            color: #5a5c69 !important;
            font-weight: 800;
            border-color: #dde1ee !important;
        }

        .table td,
        .table th {
            font-size: .86rem;
            vertical-align: middle;
            padding: .52rem .6rem;
        }

        .table caption {
            caption-side: top;
            color: #5a5c69;
            padding-top: 0;
            padding-bottom: .55rem;
            font-size: .86rem;
        }

        .btn.btn-sm {
            font-size: .78rem;
            padding: .26rem .5rem;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding-top: .22rem;
        }

        .dt-container .dt-search input,
        .dt-container .dt-length select {
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding: .3rem .45rem;
            font-size: .82rem;
        }

        .dt-container .dt-search,
        .dt-container .dt-length,
        .dt-container .dt-info,
        .dt-container .dt-paging {
            font-size: .82rem;
        }

        .status-chip {
            font-size: .74rem;
            font-weight: 700;
            border-radius: 999px;
            padding: .35rem .62rem;
        }

        .table-actions {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            flex-wrap: wrap;
        }

        .table-actions .btn {
            min-width: 2rem;
            height: 2rem;
            border-radius: .42rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 .48rem;
        }

        .table-actions .btn i {
            font-size: .84rem;
        }

        .table-actions form {
            margin: 0;
        }

        .legacy-pill {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            font-size: .74rem;
            font-weight: 700;
            border-radius: 999px;
            padding: .32rem .62rem;
            color: #fff;
        }

        .legacy-pill.success { background: #1cc88a; }
        .legacy-pill.danger { background: #e74a3b; }
        .legacy-pill.info { background: #36b9cc; }
        .legacy-pill.primary { background: #4e73df; }
        .legacy-pill.secondary { background: #858796; }

        .app-badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            border-radius: 999px;
            padding: .24rem .58rem;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .01em;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .app-badge.success {
            color: #065f46;
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .app-badge.danger {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .app-badge.info {
            color: #1e3a8a;
            background: #dbeafe;
            border-color: #bfdbfe;
        }

        .app-badge.warning {
            color: #7c2d12;
            background: #ffedd5;
            border-color: #fed7aa;
        }

        .app-badge.secondary {
            color: #374151;
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .legacy-form-compact .form-label {
            margin-bottom: .25rem;
            font-size: .82rem;
            font-weight: 700;
            color: #5a5c69;
        }

        .legacy-form-compact .form-control,
        .legacy-form-compact .form-select {
            min-height: 2.1rem;
            padding-top: .28rem;
            padding-bottom: .28rem;
            font-size: .86rem;
        }

        .legacy-form-compact .btn {
            min-height: 2.1rem;
            padding: .33rem .72rem;
            font-size: .82rem;
            font-weight: 700;
            border-radius: .35rem;
        }

        .legacy-form-compact .btn i {
            font-size: .82rem;
        }

        .alert.alert-info {
            border-color: #b6d4fe;
            background: #e9f2ff;
            color: #334155;
            font-size: .83rem;
            padding: .5rem .7rem;
            margin-bottom: .65rem;
        }

        .modal-content {
            border: 1px solid #e3e6f0;
            border-radius: .42rem;
            box-shadow: 0 .35rem 1.2rem rgba(58, 59, 69, .22);
        }

        .app-modal .modal-content {
            border-radius: .7rem;
            overflow: hidden;
        }

        .modal-header {
            background: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: .72rem .95rem;
        }

        .app-modal .modal-header {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            color: #fff;
            border-bottom: 0;
        }

        .app-modal .btn-close {
            filter: invert(1) grayscale(1);
            opacity: .85;
        }

        .modal-title {
            font-weight: 800;
            color: #5a5c69;
            font-size: 1rem;
        }

        .app-modal .modal-title {
            color: #fff;
            font-size: .96rem;
            letter-spacing: .01em;
        }

        .modal-body {
            padding: .9rem .95rem;
        }

        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: .65rem .95rem;
        }

        .app-modal .modal-footer {
            background: #f8fafc;
            display: flex;
            gap: .45rem;
        }

        .modal .form-label {
            margin-bottom: .22rem;
            font-size: .82rem;
            font-weight: 700;
        }

        .legacy-report-table {
            width: 100% !important;
            table-layout: fixed;
            font-size: 12px;
        }

        .legacy-report-table th,
        .legacy-report-table td {
            white-space: normal !important;
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding: 6px;
            vertical-align: middle;
        }

        .app-toast-stack {
            z-index: 1095;
            top: 74px;
        }

        .report-loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .82);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 5;
            backdrop-filter: blur(1.5px);
        }

        .report-loading-overlay.active {
            display: flex;
        }

        @media (min-width: 993px) {
            .app-sidebar {
                width: var(--sidebar-mini-width) !important;
                flex: 0 0 var(--sidebar-mini-width);
            }

            .app-brand {
                justify-content: center;
            }

            .app-brand .label,
            .app-sidebar .label,
            .app-sidebar .menu-title,
            .app-sidebar .collapse-toggle::after,
            .sidebar-toggle-btn .label {
                display: inline-block;
                opacity: 0;
                max-width: 0;
                overflow: hidden;
                white-space: nowrap;
                visibility: hidden;
                pointer-events: none;
            }

            .app-sidebar .nav-link {
                justify-content: center;
            }

            .app-sidebar .collapse {
                display: none !important;
            }

            .app-main {
                margin-left: 0;
            }

            body.sidebar-hover-open .app-sidebar {
                width: var(--sidebar-width) !important;
                flex: 0 0 var(--sidebar-width);
            }

            body.sidebar-hover-open .app-brand .label,
            body.sidebar-hover-open .label,
            body.sidebar-hover-open .menu-title,
            body.sidebar-hover-open .collapse-toggle::after,
            body.sidebar-hover-open .sidebar-toggle-btn .label {
                opacity: 1;
                max-width: 220px;
                overflow: visible;
                visibility: visible;
                pointer-events: auto;
            }

            body.sidebar-hover-open .app-sidebar .nav-link {
                justify-content: flex-start;
            }

            body.sidebar-hover-open .app-sidebar .collapse.show {
                display: block !important;
            }
        }

        @media (max-width: 992px) {
            .app-shell {
                display: flex;
            }

            .app-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1040;
                width: min(86vw, 300px);
                box-shadow: .3rem 0 1.1rem rgba(17, 24, 39, .28);
                transition: transform .24s cubic-bezier(.22, .61, .36, 1);
            }

            body.sidebar-collapsed .app-sidebar {
                transform: translateX(-100%);
            }

            .app-main {
                width: 100%;
            }

            body.sidebar-toggled .app-sidebar {
                transform: translateX(-100%);
            }

            body:not(.sidebar-collapsed) .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            .topbar {
                min-height: 60px;
                padding: .42rem .7rem;
            }

            .topbar .hello {
                font-size: .94rem;
                max-width: 52vw;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .user-chip {
                padding: .24rem .42rem;
                border-radius: .7rem;
            }

            .user-chip img {
                margin-left: 0 !important;
            }

            .main-wrap {
                padding: .75rem .65rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .app-sidebar,
            .sidebar-backdrop,
            .app-sidebar .collapse,
            .app-sidebar .collapsing {
                transition: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body id="page-top">
<div class="app-shell" id="wrapper">
    <aside class="app-sidebar sidebar sidebar-dark accordion bg-gradient-primary" id="accordionSidebar">
        <a href="{{ url('/app/home') }}" class="app-brand">
            <i class="bi bi-shop"></i>
            <span class="label">Sakina Kantin</span>
        </a>

        <div class="menu-title">Utama</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->is('app/home') ? 'active' : '' }}" href="{{ url('/app/home') }}"><i class="bi bi-house-door"></i> <span class="label">Home</span></a>
        </nav>
        <hr class="sidebar-divider my-0">

        <nav class="nav flex-column">
            <a class="nav-link collapse-toggle {{ request()->is('app/menu*') || request()->is('app/orders*') ? 'active' : '' }}"
               data-bs-toggle="collapse"
               href="#menuTransaksi"
               role="button"
               aria-expanded="{{ request()->is('app/menu*') || request()->is('app/orders*') ? 'true' : 'false' }}"
               aria-controls="menuTransaksi">
                <i class="bi bi-cash-coin"></i> <span class="label">Transaksi & Menu</span>
            </a>
            <div class="collapse {{ request()->is('app/menu*') || request()->is('app/orders*') ? 'show' : '' }}" id="menuTransaksi">
                <div class="sidebar-sub">
                    <a class="nav-link {{ request()->is('app/menu*') ? 'active' : '' }}" href="{{ url('/app/menu') }}">Daftar Menu</a>
                    <a class="nav-link {{ request()->is('app/orders*') ? 'active' : '' }}" href="{{ url('/app/orders') }}">Buat Order Baru</a>
                </div>
            </div>

            <a class="nav-link collapse-toggle {{ request()->is('app/reports/*') ? 'active' : '' }}"
               data-bs-toggle="collapse"
               href="#menuLaporan"
               role="button"
               aria-expanded="{{ request()->is('app/reports/*') ? 'true' : 'false' }}"
               aria-controls="menuLaporan">
                <i class="bi bi-graph-up"></i> <span class="label">Laporan Penjualan</span>
            </a>
                <div class="collapse {{ request()->is('app/reports/*') ? 'show' : '' }}" id="menuLaporan">
                <div class="sidebar-sub">
                    <div class="sidebar-sub-title">Detail Pendapatan</div>
                    <a class="nav-link {{ request()->is('app/reports/orders*') ? 'active' : '' }}" href="{{ url('/app/reports/orders') }}">Pendapatan Detail</a>
                    <a class="nav-link {{ request()->is('app/reports/rs*') ? 'active' : '' }}" href="{{ url('/app/reports/rs') }}">Pendapatan Kantin Detail</a>
                    <a class="nav-link {{ request()->is('app/reports/toko*') ? 'active' : '' }}" href="{{ url('/app/reports/toko') }}">Pendapatan Toko Detail</a>
                    <div class="sidebar-sub-title">Rekapitulasi</div>
                    <a class="nav-link {{ request()->is('app/reports/menu*') ? 'active' : '' }}" href="{{ url('/app/reports/menu') }}">Rekap Toko</a>
                    <a class="nav-link {{ request()->is('app/reports/rekap-rs*') ? 'active' : '' }}" href="{{ url('/app/reports/rekap-rs') }}">Rekap RS</a>
                    <a class="nav-link {{ request()->is('app/reports/rekap-menu-rs*') ? 'active' : '' }}" href="{{ url('/app/reports/rekap-menu-rs') }}">Rekap Kantin</a>
                    <a class="nav-link {{ request()->is('app/reports/finance-detail*') ? 'active' : '' }}" href="{{ url('/app/reports/finance-detail') }}">Rekap Keuangan</a>
                    <a class="nav-link {{ request()->is('app/reports/finance-menu*') ? 'active' : '' }}" href="{{ url('/app/reports/finance-menu') }}">Rekap Keuangan Menu</a>
                </div>
            </div>
        </nav>

        @if((int) session('level_kantin') === 1)
            <hr class="sidebar-divider">
            <nav class="nav flex-column">
                <a class="nav-link collapse-toggle {{ request()->is('app/users*') || request()->is('app/kios*') ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   href="#menuPengaturan"
                   role="button"
                   aria-expanded="{{ request()->is('app/users*') || request()->is('app/kios*') ? 'true' : 'false' }}"
                   aria-controls="menuPengaturan">
                    <i class="bi bi-gear-fill"></i> <span class="label">Pengaturan Sistem</span>
                </a>
                <div class="collapse {{ request()->is('app/users*') || request()->is('app/kios*') ? 'show' : '' }}" id="menuPengaturan">
                    <div class="sidebar-sub">
                        <div class="sidebar-sub-title">Manajemen Data</div>
                        <a class="nav-link {{ request()->is('app/users*') ? 'active' : '' }}" href="{{ url('/app/users') }}">Manajemen User</a>
                        <a class="nav-link {{ request()->is('app/kios*') ? 'active' : '' }}" href="{{ url('/app/kios') }}">Manajemen Toko</a>
                    </div>
                </div>
            </nav>
        @endif

        <hr class="sidebar-divider d-none d-md-block">
    </aside>
    <div id="sidebarBackdrop" class="sidebar-backdrop d-lg-none"></div>

    <main class="app-main">
        <header class="topbar navbar navbar-expand navbar-light bg-white mb-4 static-top shadow">
            <div class="topbar-left">
                <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggleTop">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">
                    <div class="hello">Dashboard Sakina Kantin</div>
                    <div class="sub d-none d-md-block">@yield('page_title', 'Dashboard')</div>
                </div>
            </div>
            <div class="dropdown">
                <button class="user-chip dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-lg-inline">{{ session('username_kantin', 'Guest') }}</span>
                    <img class="img-profile rounded-circle ms-1" src="{{ url('/legacy/img/undraw_profile.svg') }}" alt="profile">
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><span class="dropdown-item-text small text-muted">Level: {{ session('level_kantin', '-') }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="post" action="{{ url('/app/logout') }}" class="px-3 py-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="main-wrap container-fluid">
            <h1 class="h4 page-title">@yield('page_title', 'Dashboard')</h1>
            <div class="breadcrumb-wrap">
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @else
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/app/home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('page_title', 'Dashboard')</li>
                        </ol>
                    </nav>
                @endif
            </div>

            @if(session('ok') || $errors->any())
                <div class="toast-container position-fixed end-0 p-3 app-toast-stack">
                    @if(session('ok'))
                        <div class="toast align-items-center text-bg-success border-0 js-app-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2800">
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ session('ok') }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="toast align-items-center text-bg-danger border-0 js-app-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3600">
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>{{ $errors->first() }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('accordionSidebar');
        let sidebarHoverTimer = null;

        function applyDesktopSidebarState() {
            if (window.matchMedia('(min-width: 993px)').matches) {
                // Desktop mini state is handled purely by CSS.
            } else {
                document.body.classList.remove('sidebar-hover-open');
            }
        }

        applyDesktopSidebarState();
        window.addEventListener('resize', applyDesktopSidebarState);

        if (sidebar) {
            sidebar.addEventListener('mouseenter', function () {
                if (!window.matchMedia('(min-width: 993px)').matches) {
                    return;
                }
                if (sidebarHoverTimer) {
                    clearTimeout(sidebarHoverTimer);
                }
                sidebarHoverTimer = setTimeout(function () {
                    document.body.classList.add('sidebar-hover-open');
                }, 60);
            });

            sidebar.addEventListener('mouseleave', function () {
                if (!window.matchMedia('(min-width: 993px)').matches) {
                    return;
                }
                if (sidebarHoverTimer) {
                    clearTimeout(sidebarHoverTimer);
                }
                document.body.classList.remove('sidebar-hover-open');
            });

            sidebar.addEventListener('click', function (event) {
                if (!window.matchMedia('(min-width: 993px)').matches) {
                    return;
                }
                if (event.target.closest('.collapse') || event.target.closest('[data-bs-toggle="collapse"]')) {
                    document.body.classList.add('sidebar-hover-open');
                }
            });
        }

        if (window.matchMedia('(max-width: 992px)').matches) {
            document.body.classList.add('sidebar-collapsed', 'sidebar-toggled');
        }

        function closeMobileSidebar() {
            document.body.classList.add('sidebar-collapsed', 'sidebar-toggled');
        }

        const sidebarToggleTop = document.getElementById('sidebarToggleTop');
        if (sidebarToggleTop) {
            sidebarToggleTop.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-collapsed');
                document.body.classList.toggle('sidebar-toggled');
            });
        }

        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function () {
                closeMobileSidebar();
            });
        }

        document.addEventListener('click', function (event) {
            if (!window.matchMedia('(max-width: 992px)').matches) {
                return;
            }

            const sidebar = document.getElementById('accordionSidebar');
            const toggleBtn = document.getElementById('sidebarToggleTop');
            const isOpen = !document.body.classList.contains('sidebar-collapsed');
            if (!sidebar || !isOpen) {
                return;
            }

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedToggle = toggleBtn && toggleBtn.contains(event.target);
            if (!clickedInsideSidebar && !clickedToggle) {
                closeMobileSidebar();
            }
        });

        if (window.$) {
            $('.js-select2').select2({
                width: '100%',
            });
        }

        if (window.DataTable) {
            if (window.DataTable.ext) {
                window.DataTable.ext.errMode = 'none';
            }
            if (window.$ && $.fn && $.fn.dataTable && $.fn.dataTable.ext) {
                $.fn.dataTable.ext.errMode = 'none';
            }

            document.querySelectorAll('.js-datatable').forEach(function (el) {
                const alreadyInitedByApi = typeof window.DataTable.isDataTable === 'function' && window.DataTable.isDataTable(el);
                if (!el.dataset.dtInited && !alreadyInitedByApi) {
                    new DataTable(el, {
                        pageLength: 10,
                        language: {
                            search: 'Cari:',
                            lengthMenu: 'Tampil _MENU_ data',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            infoEmpty: 'Tidak ada data',
                            zeroRecords: 'Data tidak ditemukan',
                            paginate: {
                                first: 'Awal',
                                last: 'Akhir',
                                next: 'Lanjut',
                                previous: 'Sebelum'
                            }
                        }
                    });
                    el.dataset.dtInited = '1';
                }
            });

            if (window.$) {
                $(document).on('error.dt', function (_e, _settings, _techNote, message) {
                    console.warn('DataTable warning:', message);
                });
            }
        }

        if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
            document.querySelectorAll('.js-app-toast').forEach(function (el) {
                window.bootstrap.Toast.getOrCreateInstance(el).show();
            });
        }

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (!submitBtn || submitBtn.dataset.loadingBound === '1') {
                    return;
                }
                submitBtn.dataset.loadingBound = '1';
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Memproses...';
            });
        });

        document.querySelectorAll('.js-report-filter').forEach(function (form) {
            form.addEventListener('submit', function () {
                const targetId = form.dataset.loadingTarget || '';
                if (!targetId) {
                    return;
                }
                const overlay = document.getElementById(targetId);
                if (overlay) {
                    overlay.classList.add('active');
                }
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
