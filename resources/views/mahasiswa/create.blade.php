@extends('layouts.app')
@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')
@section('page-subtitle', 'Isi form berikut untuk menambah data mahasiswa baru')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill text-primary"></i>
        <strong>Form Tambah Mahasiswa</strong>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('mahasiswa.store') }}" novalidate id="mahasiswaForm">
            @csrf
            <div class="row g-3">

                {{-- NIM --}}
                <div class="col-md-4">
                    <label class="form-label fw-600 small">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" id="nim"
                           class="form-control @error('nim') is-invalid @enderror"
                           value="{{ old('nim') }}" placeholder="Contoh: 12345678"
                           pattern="[0-9]{8,12}" maxlength="12">
                    <div class="form-text">8–12 digit angka</div>
                    @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="invalid-feedback" id="nim-error"></div>
                </div>

                {{-- Nama --}}
                <div class="col-md-8">
                    <label class="form-label fw-600 small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="nama"
                           class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama') }}" placeholder="Nama sesuai KTP">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="invalid-feedback" id="nama-error"></div>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="mahasiswa@email.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- No HP --}}
                <div class="col-md-6">
                    <label class="form-label fw-600 small">No. HP</label>
                    <input type="text" name="no_hp" id="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}" placeholder="08xx-xxxx-xxxx">
                    <div class="form-text">Format Indonesia: 08xx / +62xx</div>
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Prodi --}}
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="prodi"
                           class="form-control @error('prodi') is-invalid @enderror"
                           value="{{ old('prodi') }}" placeholder="Teknik Informatika" list="prodiList">
                    <datalist id="prodiList">
                        <option>Teknik Informatika</option>
                        <option>Sistem Informasi</option>
                        <option>Teknik Elektro</option>
                        <option>Manajemen</option>
                        <option>Akuntansi</option>
                    </datalist>
                    @error('prodi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Fakultas --}}
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Fakultas <span class="text-danger">*</span></label>
                    <input type="text" name="fakultas"
                           class="form-control @error('fakultas') is-invalid @enderror"
                           value="{{ old('fakultas') }}" placeholder="Ilmu Komputer" list="fakultasList">
                    <datalist id="fakultasList">
                        <option>Ilmu Komputer</option>
                        <option>Teknik</option>
                        <option>Ekonomi dan Bisnis</option>
                        <option>Hukum</option>
                    </datalist>
                    @error('fakultas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Angkatan --}}
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Angkatan <span class="text-danger">*</span></label>
                    <input type="number" name="angkatan"
                           class="form-control @error('angkatan') is-invalid @enderror"
                           value="{{ old('angkatan', date('Y')) }}"
                           min="2000" max="{{ date('Y') }}">
                    @error('angkatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['aktif','cuti','lulus','keluar'] as $s)
                            <option value="{{ $s }}" {{ old('status','aktif')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- IPK --}}
                <div class="col-md-3">
                    <label class="form-label fw-600 small">IPK <span class="text-danger">*</span></label>
                    <input type="number" name="ipk" step="0.01" min="0" max="4"
                           class="form-control @error('ipk') is-invalid @enderror"
                           value="{{ old('ipk', '0.00') }}" placeholder="3.50">
                    <div class="form-text">Rentang: 0.00 – 4.00</div>
                    @error('ipk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                           class="form-control @error('tanggal_lahir') is-invalid @enderror"
                           value="{{ old('tanggal_lahir') }}"
                           max="{{ date('Y-m-d', strtotime('-15 years')) }}">
                    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Alamat --}}
                <div class="col-12">
                    <label class="form-label fw-600 small">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="form-control @error('alamat') is-invalid @enderror"
                              placeholder="Jl. Contoh No. 1, Kota...">{{ old('alamat') }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-2"></i>Simpan Data
                </button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Validasi Regex real-time di client side
const rules = {
    nim:   { pattern: /^[0-9]{8,12}$/, msg: 'NIM harus 8–12 digit angka.' },
    nama:  { pattern: /^[a-zA-Z\s'.,-]{3,100}$/, msg: 'Nama hanya boleh huruf dan spasi.' },
    no_hp: { pattern: /^(\+62|62|0)[0-9]{8,13}$/, msg: 'Format HP tidak valid (08xx / +62xx).' },
};

Object.keys(rules).forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('blur', function() {
        const val = this.value.trim();
        if (!val) return; // kosong = abaikan (nullable)
        if (!rules[id].pattern.test(val)) {
            this.classList.add('is-invalid');
            const errEl = document.getElementById(id + '-error');
            if (errEl) errEl.textContent = rules[id].msg;
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
});
</script>
@endpush
