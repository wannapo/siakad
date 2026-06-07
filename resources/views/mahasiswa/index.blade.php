@extends('layouts.app')

@section('title', 'Data Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('topbar-actions')
    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> Tambah Mahasiswa
    </a>
@endsection

@section('content')

{{-- Toolbar: Search + Filter --}}
<div class="toolbar">
    <form method="GET" action="{{ route('mahasiswa.index') }}" class="toolbar-form" id="filter-form">
        <div class="search-wrap">
            <i class="ti ti-search"></i>
            <input
                type="text"
                name="q"
                class="search-input"
                placeholder="Cari nama, NIM, atau jurusan..."
                value="{{ request('q') }}"
                autocomplete="off"
            >
        </div>
        <select name="jurusan" class="filter-select" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Jurusan</option>
            @foreach($jurusanList as $j)
                <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
        <select name="angkatan" class="filter-select" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Angkatan</option>
            @foreach($angkatanList as $a)
                <option value="{{ $a }}" {{ request('angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <select name="status" class="filter-select" onchange="document.getElementById('filter-form').submit()">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
            <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm"><i class="ti ti-filter"></i> Filter</button>
        @if(request()->hasAny(['q','jurusan','angkatan','status']))
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm"><i class="ti ti-x"></i> Reset</a>
        @endif
    </form>
</div>

{{-- Sort Controls --}}
<div class="sort-controls">
    <span class="label">Sort:</span>
    <a href="{{ route('mahasiswa.index', array_merge(request()->query(), ['sort' => 'nama', 'algo' => 'bubble', 'dir' => request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
       class="btn btn-ghost btn-sm {{ request('sort') == 'nama' ? 'active' : '' }}">
        <i class="ti ti-sort-ascending-letters"></i> Bubble (Nama)
    </a>
    <a href="{{ route('mahasiswa.index', array_merge(request()->query(), ['sort' => 'nim', 'algo' => 'selection'])) }}"
       class="btn btn-ghost btn-sm {{ request('sort') == 'nim' ? 'active' : '' }}">
        <i class="ti ti-sort-ascending-numbers"></i> Selection (NIM)
    </a>
    <a href="{{ route('mahasiswa.index', array_merge(request()->query(), ['sort' => 'angkatan', 'algo' => 'bubble'])) }}"
       class="btn btn-ghost btn-sm {{ request('sort') == 'angkatan' ? 'active' : '' }}">
        <i class="ti ti-calendar-stats"></i> Angkatan
    </a>
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm">
        <i class="ti ti-refresh"></i> Reset Sort
    </a>
    @if(isset($sortInfo))
        <span class="complexity-badge">
            <i class="ti ti-clock"></i>
            {{ $sortInfo['algo'] }} — {{ $sortInfo['time_ms'] }}ms — {{ $sortInfo['complexity'] }}
        </span>
    @endif
</div>

{{-- Table --}}
<div class="table-wrap" style="margin-top:12px">
    <div class="table-header">
        <span class="table-title">Data Mahasiswa</span>
        <span class="table-count">{{ $mahasiswas->total() }} data</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>
                    <a href="{{ route('mahasiswa.index', array_merge(request()->query(), ['sort' => 'nim', 'dir' => request('sort') == 'nim' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="th-sort">
                        NIM <i class="ti ti-selector"></i>
                    </a>
                </th>
                <th>
                    <a href="{{ route('mahasiswa.index', array_merge(request()->query(), ['sort' => 'nama', 'dir' => request('sort') == 'nama' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="th-sort">
                        NAMA <i class="ti ti-selector"></i>
                    </a>
                </th>
                <th>JURUSAN</th>
                <th>ANGKATAN</th>
                <th>EMAIL</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $m)
                <tr>
                    <td class="nim">{{ $m->nim }}</td>
                    <td class="name">{{ $m->nama }}</td>
                    <td><span class="badge badge-blue">{{ $m->jurusan }}</span></td>
                    <td><span class="mono">{{ $m->angkatan }}</span></td>
                    <td style="font-size:12px">{{ $m->email }}</td>
                    <td>@include('components.status-badge', ['status' => $m->status])</td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('mahasiswa.edit', $m->id) }}" class="btn btn-ghost btn-sm" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button
                                class="btn btn-danger btn-sm"
                                title="Hapus"
                                onclick="confirmDelete({{ $m->id }}, '{{ addslashes($m->nama) }}')"
                            >
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-database-off"></i>
                            <p>Tidak ada data ditemukan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($mahasiswas->hasPages())
        <div class="pagination">
            {{-- Previous --}}
            @if($mahasiswas->onFirstPage())
                <button class="pag-btn" disabled>‹</button>
            @else
                <a href="{{ $mahasiswas->previousPageUrl() }}" class="pag-btn">‹</a>
            @endif

            {{-- Pages --}}
            @foreach($mahasiswas->getUrlRange(1, $mahasiswas->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="pag-btn {{ $mahasiswas->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            {{-- Next --}}
            @if($mahasiswas->hasMorePages())
                <a href="{{ $mahasiswas->nextPageUrl() }}" class="pag-btn">›</a>
            @else
                <button class="pag-btn" disabled>›</button>
            @endif

            <span class="pag-info">{{ $mahasiswas->firstItem() }}–{{ $mahasiswas->lastItem() }} / {{ $mahasiswas->total() }}</span>
        </div>
    @endif
</div>

{{-- Delete Confirm Modal --}}
<div class="modal-overlay" id="modal-delete" style="display:none">
    <div class="modal" style="max-width:360px">
        <div class="modal-head">
            <span class="modal-title" style="color:var(--red)">
                <i class="ti ti-alert-triangle"></i> Hapus Data
            </span>
            <button class="modal-close" onclick="closeDeleteModal()"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:var(--text2);line-height:1.6">
                Apakah kamu yakin ingin menghapus data mahasiswa
                <strong id="delete-name" style="color:var(--text)"></strong>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Batal</button>
            <form id="delete-form" method="POST" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, nama) {
    document.getElementById('delete-name').textContent = nama;
    document.getElementById('delete-form').action = `/mahasiswa/${id}`;
    document.getElementById('modal-delete').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('modal-delete').style.display = 'none';
}
document.getElementById('modal-delete').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
