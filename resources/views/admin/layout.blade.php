<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | PUS Payroll System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css">
    <style>
        /* ── Sidebar colour override ── */
        .main-sidebar,
        .main-sidebar .sidebar,
        .layout-fixed .main-sidebar { background-color: #1a3a5c !important; }

        .main-sidebar .brand-link {
            background-color: #142d47 !important;
            border-bottom: 1px solid rgba(255,255,255,.12) !important;
            padding: .8rem 1rem !important;
        }
        .main-sidebar .brand-text { color: #f0a500 !important; font-size: .95rem !important; }
        .main-sidebar .brand-link:hover { background-color: #0f2235 !important; }

        /* Nav links */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link { color: rgba(255,255,255,.78) !important; }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover { background: rgba(255,255,255,.1) !important; color: #fff !important; }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active { background: #f0a500 !important; color: #fff !important; }
        .sidebar-dark-primary .nav-sidebar .nav-icon { color: rgba(255,255,255,.6) !important; }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active .nav-icon { color: #fff !important; }
        .sidebar-dark-primary .nav-header { color: rgba(255,255,255,.35) !important; font-size: .68rem; letter-spacing: 1px; }
        .sidebar-dark-primary .user-panel { border-bottom: 1px solid rgba(255,255,255,.1) !important; }
        .sidebar-dark-primary .user-panel .info a { color: rgba(255,255,255,.82) !important; font-size: .84rem; }
        .sidebar-dark-primary .sidebar-form input { background-color: rgba(255,255,255,.1); }

        /* Topbar */
        .main-header.navbar { border-bottom: 1px solid #e0e4ea; box-shadow: 0 1px 4px rgba(0,0,0,.06); }

        /* Content */
        .content-wrapper { background: #f0f2f5; }
        .content-header { padding: 14px 16px 0; }
        .content-header h1 { font-size: 1.35rem; font-weight: 700; color: #2d3a4a; }
        .breadcrumb { background: transparent; padding: 0; margin: 0; }

        /* Cards */
        .card { border: none; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,.07); }
        .card-header { border-radius: 8px 8px 0 0 !important; }

        /* Widgets */
        .small-box { border-radius: 8px; overflow: hidden; }
        .small-box h3 { font-size: 2rem; font-weight: 700; }
        .info-box { border-radius: 8px; }

        /* Tables */
        table.table th { font-size: .76rem; text-transform: uppercase; letter-spacing: .4px; color: #5a6776; font-weight: 700; }
        table.table td { vertical-align: middle; font-size: .875rem; }

        /* Misc */
        .badge { font-size: .75rem; }
        .nav-sidebar .nav-link p { font-size: .875rem; }
        .main-footer { font-size: .82rem; color: #6c757d; }
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

{{-- ═══ NAVBAR ═══ --}}
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-muted small">
                <i class="fas fa-home mr-1"></i>Dashboard
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item d-none d-md-inline-block mr-2">
            <small class="text-muted">
                <i class="far fa-calendar-alt mr-1"></i>{{ now()->isoFormat('dddd, D MMMM Y') }}
            </small>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning mr-2"
                         style="width:30px;height:30px">
                        <i class="fas fa-user-tie" style="font-size:12px;color:#fff"></i>
                    </div>
                    <span class="d-none d-sm-inline font-weight-600" style="font-size:.88rem">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="badge badge-warning ml-1 d-none d-sm-inline">{{ Auth::user()->role }}</span>
                    <i class="fas fa-caret-down ml-1 text-muted" style="font-size:.75rem"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" style="min-width:200px">
                <div class="px-3 py-2 border-bottom">
                    <div class="font-weight-bold" style="font-size:.88rem">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="dropdown-item text-danger py-2" type="submit">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

{{-- ═══ SIDEBAR ═══ --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link" style="padding:.6rem 1rem !important">
        <img src="{{ asset('images/logo_pus_white.png') }}" alt="PUS Logo"
             style="height:40px;width:auto;max-width:168px;display:block">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <div class="d-flex align-items-center justify-content-center img-circle"
                     style="width:34px;height:34px;background:#f0a500;border-radius:50%">
                    <i class="fas fa-user" style="color:#fff;font-size:13px"></i>
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block" style="font-size:.85rem">{{ Auth::user()->name }}</a>
                <small style="color:rgba(255,255,255,.45)">{{ ucfirst(Auth::user()->role) }}</small>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">MENU UTAMA</li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header">PENGGAJIAN</li>
                <li class="nav-item">
                    <a href="{{ route('admin.salary.index') }}"
                       class="nav-link {{ request()->routeIs('admin.salary.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Daftar Periode</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.salary.upload.form') }}"
                       class="nav-link {{ request()->routeIs('admin.salary.upload.form') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-upload"></i>
                        <p>Upload Excel</p>
                    </a>
                </li>
                <li class="nav-header">LAINNYA</li>
                <li class="nav-item">
                    <a href="{{ route('admin.salary.template') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-excel"></i>
                        <p>Template Kosong</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.salary.sample') }}" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Contoh Data (10 Karyawan)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employee.portal') }}" class="nav-link" target="_blank">
                        <i class="nav-icon fas fa-external-link-alt"></i>
                        <p>Portal Karyawan <i class="fas fa-external-link-alt ml-1" style="font-size:.65rem"></i></p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- ═══ CONTENT WRAPPER ═══ --}}
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle mr-2"></i><strong>Berhasil!</strong> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-circle mr-2"></i><strong>Error!</strong> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible shadow-sm">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Validasi Gagal:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </section>
</div>

<footer class="main-footer">
    <strong>PT Prima Utama Sultra</strong> &copy; {{ date('Y') }} Payroll System
    <div class="float-right d-none d-sm-inline-block" style="font-size:.8rem;color:#6c757d">
        Developed by
        <a href="https://github.com/eddyyucca" target="_blank" rel="noopener"
           style="color:#1a3a5c;font-weight:600;text-decoration:none">Eddy Adha Saputra</a>
        &nbsp;
        <a href="https://github.com/eddyyucca" target="_blank" rel="noopener" title="GitHub" style="color:#333">
            <i class="fab fa-github"></i>
        </a>
        &nbsp;
        <a href="https://www.linkedin.com/in/eddyyucca/" target="_blank" rel="noopener" title="LinkedIn" style="color:#0077b5">
            <i class="fab fa-linkedin"></i>
        </a>
    </div>
</footer>
</div>{{-- /.wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('select:not(.no-tomselect)').forEach(function (el) {
        var autoSubmit = el.dataset.autosubmit === 'true';
        new TomSelect(el, {
            allowEmptyOption: true,
            placeholder: el.querySelector('option[value=""]')?.textContent || 'Pilih...',
            onChange: autoSubmit ? function () {
                el.closest('form').submit();
            } : undefined
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
