@extends('layouts.app')

@section('title', 'Data Mahasiswa')
@section('page-title', 'Data Mahasiswa')
@section('page-subtitle', 'Kelola data mahasiswa — CRUD, Search, Sort')

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div class="row g-3 mb-4">
    @php
        $stats = [
            ['label'=>'Total Mahasiswa', 'value'=>$totalData, 'icon'=>'bi-people-fill',    'gradient'=>'linear-gradient(135deg,#4f46e5,#7c3aed)'],
            ['label'=>'Aktif',           'value'=>\App\Models\Mahasiswa::where('status','aktif')->count(),  'icon'=>'bi-person-check-fill','gradient'=>'linear-gradient(135deg,#059669,#10b981)'],
            ['label'=>'Cuti',            'value'=>\App\Models\Mahasiswa::where('status','cuti')->count(),   'icon'=>'bi-pause-circle-fill', 'gradient'=>'linear-gradient(135deg,#d97706,#f59e0b)'],
            ['label'=>'Lulus',           'value'=>\App\Models\Mahasiswa::where('status','lulus')->count(),  'icon'=>'bi-award-fill',        'gradient'=>'linear-gradient(135deg,#0284c7,#38bdf8)'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:{{ $s['gradient'] }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $s['value'] }}</div>
                    <div class="stat-label">{{ $s['label'] }}</div>
                </div>
                <i class="{{ $s['icon'] }} stat-icon"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ===== SEARCH & FILTER FORM ===== --}}
<div class="card mb-4">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="bi bi-search text-primary"></i>
        <strong>Pencarian & Filter Data</strong>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('mahasiswa.index') }}" id="searchForm">
            <div class="row g-3">
                {{-- Keyword --}}
                <div class="col-md-4">
                    <label class="form-label small fw-600 text-secondary">Kata Kunci</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="keyword" class="form-control"
                               placeholder="Cari nama, NIM, email..."
                               value="{{ request('keyword') }}">
                    </div>
                </div>
                {{-- Field pencarian --}}
                <div class="col-md-2">
                    <label class="form-label small fw-600 text-secondary">Cari di Kolom</label>
                    <select name="search_field" class="form-select">
                        <option value="nama"  {{ request('search_field','nama')=='nama'?'selected':'' }}>Nama</option>
                        <option value="nim"   {{ request('search_field')=='nim'?'selected':'' }}>NIM</option>
                        <option value="email" {{ request('search_field')=='email'?'selected':'' }}>Email</option>
                        <option value="prodi" {{ request('search_field')=='prodi'?'selected':'' }}>Prodi</option>
                    </select>
                </div>
                {{-- Algoritma Search --}}
                <div class="col-md-2">
                    <label class="form-label small fw-600 text-secondary">Algoritma Search</label>
                    <select name="search_algo" class="form-select">
                        <option value="linear"     {{ request('search_algo','linear')=='linear'?'selected':'' }}>Linear Search</option>
                        <option value="binary"     {{ request('search_algo')=='binary'?'selected':'' }}>Binary Search</option>
                        <option value="sequential" {{ request('search_algo')=='sequential'?'selected':'' }}>Sequential Search</option>
                    </select>
                </div>
                {{-- Algoritma Sort --}}
                <div class="col-md-2">
                    <label class="form-label small fw-600 text-secondary">Algoritma Sort</label>
                    <select name="sort_algo" class="form-select">
                        <option value="">-- Tanpa Sort --</option>
                        <option value="bubble"    {{ request('sort_algo')=='bubble'?'selected':'' }}>Bubble Sort</option>
                        <option value="selection" {{ request('sort_algo')=='selection'?'selected':'' }}>Selection Sort</option>
                        <option value="insertion" {{ request('sort_algo')=='insertion'?'selected':'' }}>Insertion Sort</option>
                    </select>
                </div>
                {{-- Sort Field & Order --}}
                <div class="col-md-1">
                    <label class="form-label small fw-600 text-secondary">Kolom Sort</label>
                    <select name="sort_field" class="form-select">
                        <option value="nama"     {{ request('sort_field','nama')=='nama'?'selected':'' }}>Nama</option>
                        <option value="nim"      {{ request('sort_field')=='nim'?'selected':'' }}>NIM</option>
                        <option value="angkatan" {{ request('sort_field')=='angkatan'?'selected':'' }}>Angkatan</option>
                        <option value="ipk"      {{ request('sort_field')=='ipk'?'selected':'' }}>IPK</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-600 text-secondary">Urutan</label>
                    <select name="sort_order" class="form-select">
                        <option value="asc"  {{ request('sort_order','asc')=='asc'?'selected':'' }}>A→Z</option>
                        <option value="desc" {{ request('sort_order')=='desc'?'selected':'' }}>Z→A</option>
                    </select>
                </div>
            </div>
            <div class="row g-2 mt-2">
                {{-- Filter status & prodi --}}
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach(['aktif','cuti','lulus','keluar'] as $s)
                            <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="prodi" class="form-select form-select-sm">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p }}" {{ request('prodi')==$p?'selected':'' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Cari & Urutkan
                    </button>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== ESTIMASI WAKTU (sebelum search) ===== --}}
@if(!request('keyword') && $totalData > 0)
<div class="alert alert-info py-2 mb-3" style="border-radius:10px;border-left:4px solid #3b82f6">
    <i class="bi bi-clock me-2"></i>
    <strong>Estimasi Pencarian</strong> — Untuk <strong>{{ $totalData }}</strong> data dengan
    <strong>{{ ucfirst(request('search_algo','linear')) }} Search ({{ $estimate['complexity'] }})</strong>:
    estimasi ≈ <strong>{{ $estimate['estimated_ms'] }} ms</strong>
    ({{ $estimate['estimated_ops'] }} operasi).
</div>
@endif

{{-- ===== HASIL SEARCH META ===== --}}
@if($searchMeta)
<div class="search-meta mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <span class="badge algo-badge" style="background:#4f46e5;color:#fff">
                <i class="bi bi-lightning-fill me-1"></i>{{ $searchMeta['algorithm'] }}
            </span>
        </div>
        <div class="col-auto meta-item">
            <i class="bi bi-check2-circle text-success"></i>
            <strong>{{ $searchMeta['found'] }}</strong> data ditemukan
        </div>
        <div class="col-auto meta-item">
            <i class="bi bi-stopwatch text-primary"></i>
            Waktu: <strong>{{ $searchMeta['time_ms'] }} ms</strong>
            ({{ $searchMeta['time'] }} detik)
        </div>
        <div class="col-auto meta-item">
            <i class="bi bi-graph-up text-warning"></i>
            Kompleksitas: <strong>{{ $searchMeta['complexity'] }}</strong>
        </div>
        <div class="col-auto meta-item">
            <i class="bi bi-hash text-secondary"></i>
            Langkah: <strong>{{ $searchMeta['steps'] }}</strong> dari {{ $searchMeta['n'] }} data
        </div>
    </div>
    <small class="text-muted mt-1 d-block">{{ $searchMeta['description'] }}</small>
</div>
@endif

{{-- ===== SORT META ===== --}}
@if($sortMeta)
<div class="alert alert-warning py-2 mb-3" style="border-radius:10px;font-size:.83rem">
    <i class="bi bi-sort-alpha-down me-2"></i>
    <strong>{{ $sortMeta['algorithm'] }}</strong> — field: <strong>{{ $sortMeta['field'] }}</strong>,
    urutan: <strong>{{ strtoupper($sortMeta['order']) }}</strong>,
    waktu: <strong>{{ $sortMeta['time_ms'] }} ms</strong>,
    komparasi: <strong>{{ $sortMeta['comparisons'] }}</strong>,
    kompleksitas: <strong>{{ $sortMeta['complexity'] }}</strong>
</div>
@endif

{{-- ===== TABEL DATA ===== --}}
<div class="card">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            <strong>Daftar Mahasiswa</strong>
            <span class="badge bg-primary rounded-pill">{{ count($allData) }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('mahasiswa.export', ['format'=>'csv']) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('mahasiswa.export', ['format'=>'json']) }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-filetype-json me-1"></i>Export JSON
            </a>
            <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Angkatan</th>
                        <th>IPK</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allData as $i => $m)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><code class="text-primary">{{ $m['nim'] }}</code></td>
                        <td>
                            <div class="fw-600">{{ $m['nama'] }}</div>
                            <small class="text-muted">{{ $m['email'] }}</small>
                        </td>
                        <td>
                            <div>{{ $m['prodi'] }}</div>
                            <small class="text-muted">{{ $m['fakultas'] }}</small>
                        </td>
                        <td>{{ $m['angkatan'] }}</td>
                        <td>
                            @php $ipk = (float)$m['ipk']; @endphp
                            <span class="fw-600 {{ $ipk >= 3.5 ? 'text-success' : ($ipk >= 2.75 ? 'text-primary' : 'text-warning') }}">
                                {{ number_format($ipk, 2) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $badge = ['aktif'=>'success','cuti'=>'warning','lulus'=>'info','keluar'=>'danger'][$m['status']] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($m['status']) }}</span>
                        </td>
                        <td>
                            @php $mObj = \App\Models\Mahasiswa::where('nim', $m['nim'])->first(); @endphp
                            @if($mObj)
                            <div class="d-flex gap-1">
                                <a href="{{ route('mahasiswa.show', $mObj) }}" class="btn btn-sm btn-outline-info py-0 px-2" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mahasiswa.edit', $mObj) }}" class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('mahasiswa.destroy', $mObj) }}"
                                      onsubmit="return confirm('Hapus data {{ $m['nama'] }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            @if(request('keyword'))
                                Tidak ada data yang cocok dengan pencarian "<strong>{{ request('keyword') }}</strong>"
                            @else
                                Belum ada data mahasiswa.
                                <a href="{{ route('mahasiswa.create') }}">Tambah sekarang</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
