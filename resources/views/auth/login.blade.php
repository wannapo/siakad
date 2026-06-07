<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SISMAKA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-body">

<div class="login-wrap">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <div class="logo-icon"><i class="ti ti-school"></i></div>
            </div>
            <h1 class="login-title">SISMAKA</h1>
            <p class="login-sub">SIstem Manajemen Akademik</p>
        </div>

        {{-- Error Alert --}}
        @if($errors->any() || session('error'))
            <div class="alert alert-error">
                <i class="ti ti-alert-circle"></i>
                <div>
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrap">
                    <i class="ti ti-mail input-icon"></i>
                    <input
                        type="email"
                        name="email"
                        class="form-input with-icon {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="admin@mail.com"
                        autocomplete="email"
                        required
                    >
                </div>
                @error('email')
                    <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="ti ti-lock input-icon"></i>
                    <input
                        type="password"
                        name="password"
                        id="password-field"
                        class="form-input with-icon with-icon-right {{ $errors->has('password') ? 'error' : '' }}"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="input-icon-right" onclick="togglePassword()" title="Tampilkan password">
                        <i class="ti ti-eye" id="eye-icon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-check-row">
                <label class="form-check">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="ti ti-login"></i> Masuk
            </button>
        </form>

        <div class="login-footer">
            <span class="login-version">v1.0.0</span>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('password-field');
    const icon = document.getElementById('eye-icon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        field.type = 'password';
        icon.className = 'ti ti-eye';
    }
}
</script>
</body>
</html>
