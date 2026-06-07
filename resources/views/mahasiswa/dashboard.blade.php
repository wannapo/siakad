@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> Tambah Mahasiswa
    </a>
@endsection

@section('content')

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">TOTAL MAHASISWA</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-meta">Terdaftar</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">AKTIF</div>
        <div class="stat-value">{{ $stats['aktif'] }}</div>
        <div class="stat-meta">Status aktif</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">JURUSAN</div>
        <div class="stat-value">{{ $stats['jurusan'] }}</div>
        <div class="stat-meta">Program studi</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">ANGKATAN</div>
        <div class="stat-value">{{ $stats['angkatan'] }}</div>
        <div class="stat-meta">Tahun berbeda</div>
    </div>
</div>

<div class="grid-2col">

    {{-- Recent Data Table --}}
    <div class="table-wrap" style="grid-column: span 2">
        <div class="table-header">
            <span class="table-title">Data Terbaru</span>
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>NAMA</th>
                    <th>JURUSAN</th>
                    <th>ANGKATAN</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $m)
                    <tr>
                        <td class="nim">{{ $m->nim }}</td>
                        <td class="name">{{ $m->nama }}</td>
                        <td><span class="badge badge-blue">{{ $m->jurusan }}</span></td>
                        <td><span class="mono">{{ $m->angkatan }}</span></td>
                        <td>@include('components.status-badge', ['status' => $m->status])</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="ti ti-database-off"></i>
                                <p>Belum ada data mahasiswa.</p>
                                <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm" style="margin-top:10px">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Activity Log Panel --}}
<div class="log-panel" style="margin-top:16px">
    <div class="log-head">
        <span class="log-title">// ACTIVITY LOG</span>
        <a href="{{ route('log.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>
    <div class="log-list">
        @forelse($logs as $log)
            <div class="log-item">
                <span class="log-time">{{ $log->created_at->format('H:i:s') }}</span>
                <span class="log-msg">
                    {{ $log->keterangan }}
                    <span class="log-tag {{ $log->aksi }}">{{ strtoupper($log->aksi) }}</span>
                </span>
            </div>
        @empty
            <div class="log-item">
                <span class="log-msg" style="color:var(--text3)">Belum ada aktivitas.</span>
            </div>
        @endforelse
    </div>
</div>

@endsection
