@extends('layouts.app')

@section('title', 'Pencarian Data')
@section('page-title', 'Pencarian Data')

@section('content')

{{-- Search Config Box --}}
<div class="section-card">
    <div class="section-label">KONFIGURASI PENCARIAN</div>
    <form method="GET" action="{{ route('search.index') }}" id="search-form">
        <div style="display:grid;grid-template-columns:1fr auto auto;gap:10px;align-items:end">
            <div>
                <label class="form-label">Kata Kunci</label>
                <input
                    type="text"
                    name="q"
                    class="form-input"
                    id="search-keyword"
                    value="{{ request('q') }}"
                    placeholder="Masukkan NIM, nama, atau jurusan..."
                    autocomplete="off"
                >
            </div>
            <div>
                <label class="form-label">Algoritma</label>
                <select name="algo" class="form-select">
                    <option value="linear" {{ request('algo', 'linear') == 'linear' ? 'selected' : '' }}>
                        Linear Search — O(n)
                    </option>
                    <option value="binary" {{ request('algo') == 'binary' ? 'selected' : '' }}>
                        Binary Search — O(log n)
                    </option>
                    <option value="sequential" {{ request('algo') == 'sequential' ? 'selected' : '' }}>
                        Sequential Search — O(n)
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-player-play"></i> Jalankan
            </button>
        </div>
    </form>
</div>

{{-- Search Result Info Bar --}}
@if(isset($searchResult))
    <div class="search-result-bar">
        <i class="ti ti-clock"></i>
        <span>
            <strong>{{ $searchResult['algo_name'] }}</strong>
            — ditemukan <strong>{{ count($searchResult['results']) }}</strong> hasil
            dari <strong>{{ $searchResult['total_data'] }}</strong> data
            dalam <span class="time">{{ $searchResult['time_ms'] }}ms ({{ $searchResult['time_sec'] }}s)</span>
            dengan <strong>{{ $searchResult['iterations'] }}</strong> iterasi
            — <span class="complexity-badge">{{ $searchResult['complexity'] }}</span>
        </span>
    </div>
@endif

{{-- Results Table --}}
<div class="table-wrap">
    <div class="table-header">
        <span class="table-title">Hasil Pencarian</span>
        <span class="table-count">
            @if(isset($searchResult))
                {{ count($searchResult['results']) }} hasil ditemukan
            @else
                Belum ada pencarian
            @endif
        </span>
    </div>
    <table>
        <thead>
            <tr>
                <th>NIM</th>
                <th>NAMA</th>
                <th>JURUSAN</th>
                <th>ANGKATAN</th>
                <th>STATUS</th>
                <th>ITERASI KE-</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($searchResult) && count($searchResult['results']) > 0)
                @foreach($searchResult['results'] as $item)
                    <tr class="highlighted">
                        <td class="nim">{{ $item['nim'] }}</td>
                        <td class="name">{{ $item['nama'] }}</td>
                        <td><span class="badge badge-blue">{{ $item['jurusan'] }}</span></td>
                        <td><span class="mono">{{ $item['angkatan'] }}</span></td>
                        <td>@include('components.status-badge', ['status' => $item['status']])</td>
                        <td><span class="mono" style="color:var(--accent)">#{{ $item['iteration'] }}</span></td>
                    </tr>
                @endforeach
            @elseif(isset($searchResult))
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ti ti-mood-sad"></i>
                            <p>Tidak ada data yang cocok dengan "<strong>{{ request('q') }}</strong>"</p>
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ti ti-search"></i>
                            <p>Masukkan kata kunci dan pilih algoritma untuk mulai pencarian.</p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Time Complexity Estimation --}}
<div class="section-card">
    <div class="section-label">ESTIMASI TIME COMPLEXITY</div>
    <div class="complexity-grid">
        <div class="stat-card" style="border-color:var(--border)">
            <div class="stat-label">BEST CASE</div>
            <div class="stat-value" style="font-size:16px;color:var(--green)">
                {{ $searchResult['tc']['best'] ?? '—' }}
            </div>
            <div class="stat-meta">{{ $searchResult['tc']['best_note'] ?? '—' }}</div>
        </div>
        <div class="stat-card" style="border-color:var(--border)">
            <div class="stat-label">AVERAGE CASE</div>
            <div class="stat-value" style="font-size:16px;color:var(--yellow)">
                {{ $searchResult['tc']['avg'] ?? '—' }}
            </div>
            <div class="stat-meta">{{ $searchResult['tc']['avg_note'] ?? '—' }}</div>
        </div>
        <div class="stat-card" style="border-color:var(--border)">
            <div class="stat-label">WORST CASE</div>
            <div class="stat-value" style="font-size:16px;color:var(--red)">
                {{ $searchResult['tc']['worst'] ?? '—' }}
            </div>
            <div class="stat-meta">{{ $searchResult['tc']['worst_note'] ?? '—' }}</div>
        </div>
    </div>

    @if(isset($searchResult))
    <div style="margin-top:14px;background:var(--bg3);border-radius:var(--radius);padding:12px">
        <div class="section-label" style="margin-bottom:8px">DETAIL EKSEKUSI</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px">
            <div class="info-row"><span class="key">Algoritma</span><span class="val">{{ $searchResult['algo_name'] }}</span></div>
            <div class="info-row"><span class="key">Total Data</span><span class="val">{{ $searchResult['total_data'] }}</span></div>
            <div class="info-row"><span class="key">Iterasi</span><span class="val">{{ $searchResult['iterations'] }}</span></div>
            <div class="info-row"><span class="key">Hasil</span><span class="val">{{ count($searchResult['results']) }} data</span></div>
            <div class="info-row"><span class="key">Waktu (ms)</span><span class="val">{{ $searchResult['time_ms'] }}ms</span></div>
            <div class="info-row"><span class="key">Waktu (s)</span><span class="val">{{ $searchResult['time_sec'] }}s</span></div>
        </div>
    </div>
    @endif
</div>

@endsection
