<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Slip Gaji - PT Prima Utama Sultra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a3a5c 0%, #0d2137 50%, #1a3a5c 100%); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .hero { padding: 4rem 0 2rem; text-align: center; color: #fff; }
        .hero h1 { font-weight: 800; font-size: 2rem; }
        .hero p { opacity: .8; }
        .portal-card { background: #fff; border-radius: 20px; padding: 2.5rem; box-shadow: 0 30px 80px rgba(0,0,0,.3); max-width: 500px; margin: 0 auto; }
        .step-badge { width: 32px; height: 32px; border-radius: 50%; background: #1a3a5c; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; margin-right: 10px; }
        .form-control:focus, .form-select:focus { border-color: #1a3a5c; box-shadow: 0 0 0 .25rem rgba(26,58,92,.2); }
        .btn-verify { background: #1a3a5c; color: #fff; padding: .85rem; font-weight: 700; border-radius: 12px; }
        .btn-verify:hover { background: #0d2137; color: #fff; }
        .info-box { background: rgba(26,58,92,.08); border-left: 4px solid #1a3a5c; border-radius: 0 8px 8px 0; padding: 1rem; margin-top: 1.5rem; }
        .footer-text { text-align: center; color: rgba(255,255,255,.5); padding: 1.5rem 0 .5rem; font-size: .85rem; }
        .footer-dev { text-align: center; color: rgba(255,255,255,.3); padding: .5rem 0 2rem; font-size: .75rem; }
        .footer-dev a { color: rgba(255,255,255,.45); text-decoration: none; transition: color .2s; }
        .footer-dev a:hover { color: rgba(255,255,255,.85); }
        .period-available { background: #f0f9f0; border: 1px solid #c3e6cb; border-radius: 8px; padding: .6rem 1rem; margin-bottom: .5rem; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="hero">
    <div class="container">
        <div class="mb-3">
            <img src="{{ asset('images/logo_pus_white.png') }}" alt="PT Prima Utama Sultra"
                 style="height:64px;object-fit:contain;filter:drop-shadow(0 2px 6px rgba(0,0,0,.3))">
        </div>
        <h1></i>Portal Slip Gaji</h1>
        <p class="lead">Akses slip gaji Anda secara mudah dan aman</p>
        
    </div>
</div>

<div class="container pb-5">
<div class="portal-card">
    <h5 class="fw-bold mb-1">Verifikasi Identitas</h5>
    <p class="text-muted small mb-4">Masukkan NIK dan tanggal lahir Anda untuk mengakses slip gaji</p>

    @if($errors->any())
    <div class="alert alert-danger py-2 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
    </div>
    @endif

    @if($periods->isEmpty())
    <div class="text-center py-4">
        <i class="fas fa-clock fa-3x text-muted d-block mb-3"></i>
        <h6 class="text-muted">Belum ada data gaji yang tersedia</h6>
        <p class="small text-muted">Hubungi HRD untuk informasi lebih lanjut</p>
    </div>
    @else

    <form action="{{ route('employee.verify') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">
                <span class="step-badge">1</span>NIK Karyawan
            </label>
            <input type="text" class="form-control" name="nik" value="{{ old('nik') }}"
                placeholder="Contoh: PUS220003" required style="text-transform:uppercase">
            <small class="text-muted">NIK berawalan PUS (sesuai kontrak kerja)</small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                <span class="step-badge">2</span>Tanggal Lahir
            </label>
            <input type="date" class="form-control" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
            <small class="text-muted">Sebagai verifikasi identitas</small>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">
                <span class="step-badge">3</span>Pilih Periode Gaji
            </label>
            <select class="form-select" name="period_id" required>
                <option value="">-- Pilih Periode --</option>
                @foreach($periods as $p)
                <option value="{{ $p->id }}" {{ old('period_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->period_label }} ({{ $p->period_start->format('d/m/Y') }} - {{ $p->period_end->format('d/m/Y') }})
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-verify w-100">
            <i class="fas fa-shield-alt me-2"></i>Verifikasi & Lihat Slip Gaji
        </button>
    </form>

    @endif
</div>

<div class="info-box mt-3" style="max-width:500px;margin:1rem auto 0;">
    <i class="fas fa-lock me-2"></i>
    <strong>Keamanan:</strong> Data Anda diverifikasi dengan NIK + tanggal lahir. Tidak ada akun yang dibutuhkan.
</div>

</div>

<div class="footer-text">
    <a href="{{ route('login') }}" class="text-decoration-none" style="color:rgba(255,255,255,.4)">
        <i class="fas fa-user-shield me-1"></i>Admin Login
    </a>
    <span class="mx-3">|</span>
    © {{ date('Y') }} PT Prima Utama Sultra
</div>
<div class="footer-dev">
    Developed by&nbsp;
    <a href="https://github.com/eddyyucca" target="_blank" rel="noopener">
        <i class="fab fa-github me-1"></i>Eddy Adha Saputra
    </a>
    &nbsp;&bull;&nbsp;
    <a href="https://www.linkedin.com/in/eddyyucca/" target="_blank" rel="noopener">
        <i class="fab fa-linkedin me-1"></i>LinkedIn
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.querySelector('input[name="nik"]')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

const periodSelect = document.querySelector('select[name="period_id"]');
if (periodSelect) {
    new TomSelect(periodSelect, {
        allowEmptyOption: true,
        placeholder: '-- Pilih Periode --'
    });
}
</script>
</body>
</html>
