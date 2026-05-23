@extends('layouts.app')
@section('title', 'Import Data')
@section('page-title', 'Import Data Mahasiswa')
@section('page-subtitle', 'Upload file CSV atau JSON untuk import massal')

@section('content')
<div class="row g-4">
    {{-- Form Import --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <i class="bi bi-upload text-primary"></i>
                <strong>Upload File Import</strong>
            </div>
            <div class="card-body p-4">

                {{-- Error alert --}}
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
                </div>
                @endif

                <form method="POST" action="{{ route('mahasiswa.import') }}" enctype="multipart/form-data" id="importForm">
                    @csrf

                    {{-- Drop zone --}}
                    <div id="dropZone" class="border-2 border-dashed rounded-3 p-5 text-center mb-3"
                         style="border-color:#cbd5e1;cursor:pointer;transition:all .2s"
                         onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-cloud-upload display-5 text-muted d-block mb-2"></i>
                        <p class="mb-1 fw-600">Drag & Drop atau Klik untuk Upload</p>
                        <p class="text-muted small mb-0">Format: <strong>CSV</strong> atau <strong>JSON</strong> · Maks: 5MB</p>
                        <div id="fileName" class="mt-2 text-primary small d-none"></div>
                    </div>
                    <input type="file" name="file" id="fileInput" accept=".csv,.json,.txt"
                           class="d-none @error('file') is-invalid @enderror"
                           onchange="handleFile(this)">
                    @error('file')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-primary w-100" id="importBtn" disabled>
                        <i class="bi bi-upload me-2"></i>Mulai Import
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Panduan format --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header py-2"><strong class="small">📄 Format CSV</strong></div>
            <div class="card-body p-3">
                <pre class="small mb-0" style="background:#f8fafc;padding:.75rem;border-radius:8px;font-size:.72rem">nim,nama,email,no_hp,prodi,fakultas,angkatan,status,ipk,alamat
12345678,Budi Santoso,budi@email.com,081234567890,Teknik Informatika,Ilmu Komputer,2022,aktif,3.75,Jl. Merdeka No.1
87654321,Siti Rahayu,siti@email.com,082345678901,Sistem Informasi,Ilmu Komputer,2021,aktif,3.50,</pre>
                <small class="text-muted d-block mt-2">Kolom wajib: nim, nama, email, prodi, fakultas, angkatan</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-2"><strong class="small">🗂️ Format JSON</strong></div>
            <div class="card-body p-3">
                <pre class="small mb-0" style="background:#f8fafc;padding:.75rem;border-radius:8px;font-size:.72rem">[
  {
    "nim": "12345678",
    "nama": "Budi Santoso",
    "email": "budi@email.com",
    "prodi": "Teknik Informatika",
    "fakultas": "Ilmu Komputer",
    "angkatan": 2022,
    "status": "aktif",
    "ipk": 3.75
  }
]</pre>
            </div>
        </div>

        {{-- Download template --}}
        <div class="mt-3 p-3 rounded" style="background:#eff6ff;border:1px solid #bfdbfe">
            <p class="small fw-600 mb-2"><i class="bi bi-download me-1"></i>Download Template</p>
            <div class="d-flex gap-2">
                <button onclick="downloadTemplate('csv')" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="bi bi-filetype-csv me-1"></i>CSV
                </button>
                <button onclick="downloadTemplate('json')" class="btn btn-sm btn-outline-info flex-fill">
                    <i class="bi bi-filetype-json me-1"></i>JSON
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function handleFile(input) {
    const file = input.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (!['csv','json','txt'].includes(ext)) {
        alert('❌ Format tidak valid! Hanya CSV dan JSON yang diizinkan.');
        input.value = '';
        return;
    }
    if (file.size > maxSize) {
        alert('❌ File terlalu besar! Maksimal 5MB.');
        input.value = '';
        return;
    }

    const fileNameEl = document.getElementById('fileName');
    fileNameEl.textContent = `✅ ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
    fileNameEl.classList.remove('d-none');
    document.getElementById('importBtn').disabled = false;
    document.getElementById('dropZone').style.borderColor = '#4f46e5';
}

// Drag & Drop
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background='#eff6ff'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.background=''; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.background = '';
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    const input = document.getElementById('fileInput');
    input.files = dt.files;
    handleFile(input);
});

// Download template
function downloadTemplate(format) {
    if (format === 'csv') {
        const csv = 'nim,nama,email,no_hp,prodi,fakultas,angkatan,status,ipk,alamat,tanggal_lahir\n12345678,Contoh Nama,contoh@email.com,081234567890,Teknik Informatika,Ilmu Komputer,2022,aktif,3.75,Jl. Contoh No.1,2002-01-15';
        download('template_mahasiswa.csv', csv, 'text/csv');
    } else {
        const json = JSON.stringify([{nim:"12345678",nama:"Contoh Nama",email:"contoh@email.com",no_hp:"081234567890",prodi:"Teknik Informatika",fakultas:"Ilmu Komputer",angkatan:2022,status:"aktif",ipk:3.75,alamat:"Jl. Contoh No.1",tanggal_lahir:"2002-01-15"}], null, 2);
        download('template_mahasiswa.json', json, 'application/json');
    }
}

function download(filename, content, type) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([content], {type}));
    a.download = filename;
    a.click();
}
</script>
@endpush
