<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAKAD') — Sistem Informasi Akademik</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-width: 260px;
        }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        /* ---- SIDEBAR ---- */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            position: fixed; top: 0; left: 0; z-index: 100;
            transition: width .3s ease;
        }
        #sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        #sidebar .brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: .95rem; }
        #sidebar .brand small { color: #a5b4fc; font-size: .75rem; }
        #sidebar .nav-link {
            color: #c7d2fe; padding: .65rem 1.25rem;
            border-radius: 8px; margin: 2px 10px;
            font-size: .875rem; display: flex; align-items: center; gap: .6rem;
            transition: all .2s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background: rgba(255,255,255,.15); color: #fff;
        }
        #sidebar .nav-link i { font-size: 1rem; width: 20px; text-align: center; }
        #sidebar .section-label {
            color: #818cf8; font-size: .7rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .08em;
            padding: 1rem 1.5rem .25rem;
        }

        /* ---- MAIN CONTENT ---- */
        #main { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .875rem 1.5rem; position: sticky; top: 0; z-index: 99;
        }
        .page-content { padding: 1.5rem; }

        /* ---- CARDS ---- */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; border-radius: 12px 12px 0 0 !important; }

        /* ---- STAT CARDS ---- */
        .stat-card { border-radius: 12px; padding: 1.25rem; color: #fff; }
        .stat-card .stat-icon { font-size: 2rem; opacity: .8; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label { font-size: .8rem; opacity: .85; }

        /* ---- ALERT AUTO DISMISS ---- */
        .alert-auto { animation: fadeIn .4s ease; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(-10px); } to { opacity:1; transform: translateY(0); } }

        /* ---- BADGE ALGORITMA ---- */
        .algo-badge { font-size: .7rem; padding: .25rem .6rem; border-radius: 20px; font-weight: 600; }

        /* ---- SEARCH META BOX ---- */
        .search-meta {
            background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%);
            border: 1px solid #bfdbfe; border-radius: 10px; padding: 1rem 1.25rem;
        }
        .search-meta .meta-item { display: flex; align-items: center; gap: .4rem; font-size: .82rem; }

        /* ---- TABLE ---- */
        .table th { font-size: .78rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em; color: #64748b; background: #f8fafc; }
        .table td { vertical-align: middle; font-size: .875rem; }
        .table tbody tr:hover { background: #f8faff; }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 768px) {
            #sidebar { width: 0; overflow: hidden; }
            #main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@auth
{{-- ======= SIDEBAR ======= --}}
<nav id="sidebar">
    <div class="brand">
        <h5><i class="bi bi-mortarboard-fill me-2"></i>SIAKAD</h5>
        <small>Sistem Informasi Akademik</small>
    </div>

    <div class="pt-2">
        <p class="section-label">Menu Utama</p>
        <a href="{{ route('mahasiswa.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.index') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Data Mahasiswa
        </a>
        <a href="{{ route('mahasiswa.create') }}" class="nav-link {{ request()->routeIs('mahasiswa.create') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i> Tambah Mahasiswa
        </a>

        <p class="section-label mt-2">Import / Export</p>
        <a href="{{ route('mahasiswa.import.form') }}" class="nav-link {{ request()->routeIs('mahasiswa.import.form') ? 'active' : '' }}">
            <i class="bi bi-upload"></i> Import CSV / JSON
        </a>
        <a href="{{ route('mahasiswa.export', ['format' => 'csv']) }}" class="nav-link">
            <i class="bi bi-filetype-csv"></i> Export CSV
        </a>
        <a href="{{ route('mahasiswa.export', ['format' => 'json']) }}" class="nav-link">
            <i class="bi bi-filetype-json"></i> Export JSON
        </a>

        <p class="section-label mt-2">Sistem</p>
        <a href="{{ route('logs.index') }}" class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Activity Log
        </a>
    </div>

    {{-- User info di bawah --}}
    <div style="position: absolute; bottom: 0; width: 100%; padding: 1rem; border-top: 1px solid rgba(255,255,255,.1);">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-indigo-500 d-flex align-items-center justify-content-center"
                 style="width:34px;height:34px;background:#6366f1;flex-shrink:0">
                <i class="bi bi-person-fill text-white" style="font-size:.9rem"></i>
            </div>
            <div style="overflow:hidden">
                <div style="color:#fff;font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ Auth::user()->name }}
                </div>
                <div style="color:#a5b4fc;font-size:.7rem">{{ Auth::user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,.1);color:#c7d2fe;font-size:.78rem">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- ======= MAIN AREA ======= --}}
<div id="main">
    {{-- Topbar --}}
    <div class="topbar d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-0 fw-600" style="color:#1e293b">@yield('page-title', 'Dashboard')</h6>
            <small class="text-muted">@yield('page-subtitle', '')</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success">Online</span>
            <small class="text-muted">{{ now()->format('d M Y, H:i') }}</small>
        </div>
    </div>

    {{-- Notifikasi Alert --}}
    <div class="page-content pb-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible alert-auto fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible alert-auto fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('import_errors'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-circle me-1"></i>Detail Error Import:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <div class="page-content">
        @yield('content')
    </div>
</div>

@else
    @yield('content')
@endauth

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-dismiss alert setelah 5 detik
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            document.querySelectorAll('.alert-auto').forEach(el => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            });
        }, 5000);
    });
</script>

@stack('scripts')
</body>
</html>
