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
    <style>
        :root { --pus-dark: #1a3a5c; --pus-accent: #f0a500; }
        .brand-link { background: #142d47 !important; border-bottom: 1px solid rgba(255,255,255,.12) !important; }
        .brand-text { color: #f0a500 !important; font-size: 1rem !important; letter-spacing: .5px; }
        .main-sidebar { background: #1a3a5c !important; }
        [class*="sidebar-dark-"] .nav-sidebar > .nav-item > .nav-link.active,
        [class*="sidebar-dark-"] .nav-sidebar > .nav-item > .nav-link.active:hover {
            background: #f0a500 !important; color: #fff !important;
        }
        [class*="sidebar-dark-"] .nav-sidebar .nav-link:hover {
            background: rgba(255,255,255,.1) !important;
        }
        [class*="sidebar-dark-"] .nav-header { color: rgba(255,255,255,.4) !important; }
        [class*="sidebar-dark-"] .user-panel > .info > a { color: rgba(255,255,255,.8); }
        .content-wrapper { background: #f4f6f9; }
        .card { border: none; border-radius: 8px; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
        .card-header { border-radius: 8px 8px 0 0 !important; }
        .small-box { border-radius: 8px; }
        .small-box h3 { font-size: 2rem; }
        .info-box { border-radius: 8px; }
        .main-header { box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .breadcrumb { background: transparent; padding: 0; }
        .content-header { padding: 12px 15px 0; }
        .content-header h1 { font-size: 1.4rem; font-weight: 600; color: #333; }
        table.table th { font-size: .78rem; text-transform: uppercase; letter-spacing: .4px; color: #6c757d; font-weight: 600; }
        table.table td { vertical-align: middle; font-size: .88rem; }
        .badge { font-size: .77rem; }
        .nav-sidebar .nav-link p { font-size: .9rem; }
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
<aside class="main-sidebar elevation-3" style="background:#1a3a5c">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <div class="d-flex align-items-center pl-2">
            <i class="fas fa-building text-warning mr-2" style="font-size:1.2rem"></i>
            <span class="brand-text font-weight-bold">PUS Payroll</span>
        </div>
        <small class="pl-2" style="color:rgba(255,255,255,.45);font-size:.7rem;display:block;margin-top:-2px">PT Prima Utama Sultra</small>
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
                        <p>Download Template</p>
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
    <div class="float-right d-none d-sm-inline-block text-muted small">
        <i class="fas fa-code mr-1"></i>v1.0
    </div>
</footer>
</div>{{-- /.wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
