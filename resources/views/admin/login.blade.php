<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PUS Payroll Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a3a5c, #0d2137); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .login-logo { text-align: center; margin-bottom: 1.5rem; }
        .login-logo .icon { width: 70px; height: 70px; background: #1a3a5c; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; }
        .form-control:focus { border-color: #1a3a5c; box-shadow: 0 0 0 .25rem rgba(26,58,92,.2); }
        .btn-login { background: #1a3a5c; color: #fff; border: none; padding: .75rem; font-weight: 600; }
        .btn-login:hover { background: #0d2137; color: #fff; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon mb-3">
            <i class="fas fa-building fa-2x text-white"></i>
        </div>
        <h4 class="fw-bold" style="color:#1a3a5c">PT Prima Utama Sultra</h4>
        <p class="text-muted mb-0">Sistem Penggajian - Admin Panel</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="admin@pus.co.id" required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" class="form-control" name="password" placeholder="••••••••" required>
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember">
            <label class="form-check-label text-muted" for="remember">Ingat saya</label>
        </div>
        <button type="submit" class="btn btn-login w-100 rounded-3">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('employee.portal') }}" class="text-muted small">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Portal Karyawan
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
