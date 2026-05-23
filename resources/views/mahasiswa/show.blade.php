@extends('layouts.app')
@section('title', 'Detail: ' . $mahasiswa->nama)
@section('page-title', 'Detail Mahasiswa')

@section('content')
<div class="row g-4">
    {{-- Info Mahasiswa --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill text-primary"></i>
                    <strong>{{ $mahasiswa->nama }}</strong>
                    <span class="badge bg-{{ $mahasiswa->status_badge }}">{{ ucfirst($mahasiswa->status) }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('mahasiswa.destroy', $mahasiswa) }}"
                          onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Hapus</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $fields = [
                            ['label'=>'NIM',            'value'=>$mahasiswa->nim,              'icon'=>'bi-card-text'],
                            ['label'=>'Email',           'value'=>$mahasiswa->email,            'icon'=>'bi-envelope'],
                            ['label'=>'No. HP',          'value'=>$mahasiswa->no_hp ?? '-',     'icon'=>'bi-phone'],
                            ['label'=>'Program Studi',   'value'=>$mahasiswa->prodi,            'icon'=>'bi-book'],
                            ['label'=>'Fakultas',        'value'=>$mahasiswa->fakultas,         'icon'=>'bi-building'],
                            ['label'=>'Angkatan',        'value'=>$mahasiswa->angkatan,         'icon'=>'bi-calendar'],
                            ['label'=>'IPK',             'value'=>$mahasiswa->ipk_formatted,    'icon'=>'bi-star'],
                            ['label'=>'Tanggal Lahir',   'value'=>$mahasiswa->tanggal_lahir?->format('d M Y') ?? '-', 'icon'=>'bi-cake'],
                            ['label'=>'Alamat',          'value'=>$mahasiswa->alamat ?? '-',    'icon'=>'bi-geo-alt'],
                            ['label'=>'Terdaftar',       'value'=>$mahasiswa->created_at->format('d M Y H:i'), 'icon'=>'bi-clock'],
                        ];
                    @endphp
                    @foreach($fields as $f)
                    <div class="col-md-6">
                        <div class="d-flex gap-2 align-items-start">
                            <i class="{{ $f['icon'] }} text-primary mt-1" style="font-size:.9rem;width:16px"></i>
                            <div>
                                <div class="text-muted small">{{ $f['label'] }}</div>
                                <div class="fw-600">{{ $f['value'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-secondary"></i>
                <strong>Riwayat Aktivitas</strong>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($logs as $log)
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-{{ $log->action_badge }} mt-1" style="font-size:.65rem">
                                {{ strtoupper($log->action) }}
                            </span>
                            <div style="flex:1;min-width:0">
                                <div class="small">{{ $log->description }}</div>
                                @if($log->execution_time)
                                    <span class="text-muted" style="font-size:.72rem">
                                        ⏱ {{ round($log->execution_time * 1000, 2) }} ms
                                        @if($log->complexity) · {{ $log->complexity }} @endif
                                    </span>
                                @endif
                                <div class="text-muted" style="font-size:.7rem">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4 small">
                        Belum ada aktivitas tercatat
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
    </a>
</div>
@endsection
