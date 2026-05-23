@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 50%, #7c3aed 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                {{-- Logo / Brand --}}
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:64px;height:64px;background:rgba(255,255,255,.15);backdrop-filter:blur(10px)">
                        <i class="bi bi-mortarboard-fill text-white" style="font-size:1.8rem"></i>
                    </div>
                    <h4 class="text-white fw-700 mb-0">SIAKAD</h4>
                    <p class="text-white-50 small">Sistem Informasi Akademik</p>
                </div>

                {{-- Card Login --}}
                <div class="card shadow-lg border-0" style="border-radius:16px">
                    <div class="card-body p-4">
                        <h5 class="fw-700 mb-1" style="color:#1e293b">Masuk ke Sistem</h5>
                        <p class="text-muted small mb-4">Masukkan email dan password Anda</p>

                        {{-- Error Alert --}}
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {!! session('error') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger py-2">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{-- Form Login --}}
                        <form method="POST" action="{{ route('login.post') }}" novalidate>
                            @csrf

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           placeholder="admin@siakad.ac.id"
                                           value="{{ old('email') }}"
                                           autocomplete="email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" name="password" id="passwordInput"
                                           class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                           placeholder="••••••••" required>
                                    <button type="button" class="btn btn-light border"
                                            onclick="togglePassword()">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Remember Me --}}
                            <div class="mb-4 d-flex align-items-center">
                                <input type="checkbox" name="remember" class="form-check-input me-2" id="remember">
                                <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                            </div>

                            <button type="submit" class="btn w-100 text-white fw-600"
                                    style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border:none; padding:.75rem; border-radius:10px">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>
                        </form>

                        {{-- Demo credentials --}}
                        <div class="mt-3 p-2 rounded text-center" style="background:#f8fafc;font-size:.75rem;color:#64748b">
                            <strong>Demo:</strong> admin@siakad.ac.id / password
                        </div>
                    </div>
                </div>

                <p class="text-center text-white-50 small mt-3">
                    &copy; {{ date('Y') }} SIAKAD — Manajemen Data Mahasiswa
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
