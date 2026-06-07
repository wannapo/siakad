@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('topbar-actions')
    <form method="POST" action="{{ route('log.clear') }}" style="margin:0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('Hapus semua log?')">
            <i class="ti ti-trash"></i> Hapus Log
        </button>
    </form>
@endsection

@section('content')

<div class="log-panel">
    <div class="log-head">
        <span class="log-title">// ACTIVITY LOG — SEMUA AKTIVITAS</span>
        <span class="table-count">{{ $logs->total() }} entri</span>
    </div>
    <div class="log-list" style="max-height:none">
        @forelse($logs as $log)
            <div class="log-item">
                <span class="log-time">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                <span class="log-msg">
                    {{ $log->keterangan }}
                    <span class="log-tag {{ $log->aksi }}">{{ strtoupper($log->aksi) }}</span>
                    @if($log->durasi_ms)
                        <span style="color:var(--text3);font-size:10px;font-family:var(--font-mono);margin-left:6px">{{ $log->durasi_ms }}ms</span>
                    @endif
                </span>
            </div>
        @empty
            <div class="log-item">
                <span class="log-msg" style="color:var(--text3)">Log kosong.</span>
            </div>
        @endforelse
    </div>
    @if($logs->hasPages())
        <div class="pagination">
            @if($logs->onFirstPage())
                <button class="pag-btn" disabled>‹</button>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="pag-btn">‹</a>
            @endif
            @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="pag-btn {{ $logs->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="pag-btn">›</a>
            @else
                <button class="pag-btn" disabled>›</button>
            @endif
            <span class="pag-info">{{ $logs->firstItem() }}–{{ $logs->lastItem() }} / {{ $logs->total() }}</span>
        </div>
    @endif
</div>

@endsection
