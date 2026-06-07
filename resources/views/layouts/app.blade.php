<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SISMAKA - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<div class="app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-badge">
                <div class="logo-icon">
                    <i class="ti ti-school"></i>
                </div>
                <div>
                    <div class="logo-text">SISMAKA</div>
                    <div class="logo-sub">v1.0.0-stable</div>
                </div>
            </div>
        </div>

        <nav class="nav">
            <div class="nav-group-label">MENU UTAMA</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i> Dashboard
            </a>
            <a href="{{ route('mahasiswa.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                <i class="ti ti-users"></i> Mahasiswa
                <span class="nav-badge">{{ \App\Models\Mahasiswa::count() }}</span>
            </a>
            <a href="{{ route('search.index') }}" class="nav-item {{ request()->routeIs('search.*') ? 'active' : '' }}">
                <i class="ti ti-search"></i> Pencarian
            </a>

            <div class="nav-group-label" style="margin-top:12px">DATA</div>
            <a href="{{ route('import-export.index') }}" class="nav-item {{ request()->routeIs('import-export.*') ? 'active' : '' }}">
                <i class="ti ti-database-import"></i> Import / Export
            </a>
            <a href="{{ route('log.index') }}" class="nav-item {{ request()->routeIs('log.*') ? 'active' : '' }}">
                <i class="ti ti-list-details"></i> Activity Log
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="icon-btn" title="Logout"><i class="ti ti-logout"></i></button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main">
        <div class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                <a href="{{ route('import-export.index') }}" class="btn btn-ghost btn-sm">
                    <i class="ti ti-database-import"></i> Import
                </a>
                @yield('topbar-actions')
            </div>
        </div>

        {{-- TOAST AREA --}}
        <div class="toast-container" id="toast-container">
            @if(session('success'))
                <div class="toast toast-success" data-auto-close>
                    <i class="ti ti-circle-check"></i>
                    <span class="toast-msg">{{ session('success') }}</span>
                    <button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="toast toast-error" data-auto-close>
                    <i class="ti ti-alert-circle"></i>
                    <span class="toast-msg">{{ session('error') }}</span>
                    <button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>
                </div>
            @endif
            @if(session('info'))
                <div class="toast toast-info" data-auto-close>
                    <i class="ti ti-info-circle"></i>
                    <span class="toast-msg">{{ session('info') }}</span>
                    <button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>
                </div>
            @endif
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
