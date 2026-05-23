@extends('layouts.app')
@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Riwayat seluruh aktivitas di sistem')

@section('content')
<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="bi bi-journal-text text-primary"></i>
        <strong>Build Log — Semua Aktivitas</strong>
        <span class="badge bg-secondary ms-auto">{{ $logs->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>Algoritma</th>
                    <th>Waktu Eksekusi</th>
                    <th>Komentar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-muted small">{{ $log->id }}</td>
                    <td class="small text-muted" style="white-space:nowrap">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-{{ $log->action_badge }}">
                            <i class="{{ $log->action_icon }} me-1"></i>{{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td class="small">
                        {{ $log->description }}
                        @if($log->target)
                            <br><code class="text-primary small">{{ $log->target }}</code>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->algorithm)
                            <span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:.7rem">
                                {{ $log->algorithm }}
                            </span>
                            @if($log->complexity)
                                <br><code class="text-muted" style="font-size:.7rem">{{ $log->complexity }}</code>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->execution_time)
                            <span class="fw-600 text-primary">{{ round($log->execution_time * 1000, 4) }} ms</span>
                            <br><span class="text-muted" style="font-size:.7rem">{{ $log->execution_time }} s</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        @if($log->user_comment)
                            <div class="p-1 rounded" style="background:#f1f5f9;font-size:.78rem;max-width:150px">
                                💬 {{ $log->user_comment }}
                            </div>
                        @else
                            {{-- Form tambah komentar --}}
                            <form method="POST" action="{{ route('logs.comment', $log) }}" class="d-flex gap-1">
                                @csrf
                                <input type="text" name="comment" class="form-control form-control-sm"
                                       placeholder="Tambah komentar..." style="min-width:120px;font-size:.75rem">
                                <button type="submit" class="btn btn-sm btn-outline-primary py-0" title="Simpan">
                                    <i class="bi bi-send" style="font-size:.7rem"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $log->user?->name ?? 'System' }}
                        @if($log->data_count)
                            <br><span style="font-size:.7rem">n={{ $log->data_count }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x display-6 d-block mb-2"></i>
                        Belum ada aktivitas tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
