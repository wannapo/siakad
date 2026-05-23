@extends('layouts.app')
@section('title', 'Edit Mahasiswa')
@section('page-title', 'Edit Data Mahasiswa')
@section('page-subtitle', 'Ubah data mahasiswa: {{ $mahasiswa->nama }}')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square text-warning"></i>
        <strong>Edit: {{ $mahasiswa->nama }}</strong>
        <span class="badge bg-secondary ms-auto">NIM: {{ $mahasiswa->nim }}</span>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa) }}" novalidate>
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-600 small">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror"
                           value="{{ old('nim', $mahasiswa->nim) }}" pattern="[0-9]{8,12}" maxlength="12">
                    @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-600 small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $mahasiswa->nama) }}">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $mahasiswa->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600 small">No. HP</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $mahasiswa->no_hp) }}">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="prodi" class="form-control @error('prodi') is-invalid @enderror"
                           value="{{ old('prodi', $mahasiswa->prodi) }}">
                    @error('prodi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-600 small">Fakultas <span class="text-danger">*</span></label>
                    <input type="text" name="fakultas" class="form-control @error('fakultas') is-invalid @enderror"
                           value="{{ old('fakultas', $mahasiswa->fakultas) }}">
                    @error('fakultas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Angkatan <span class="text-danger">*</span></label>
                    <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror"
                           value="{{ old('angkatan', $mahasiswa->angkatan) }}" min="2000" max="{{ date('Y') }}">
                    @error('angkatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['aktif','cuti','lulus','keluar'] as $s)
                            <option value="{{ $s }}" {{ old('status',$mahasiswa->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small">IPK <span class="text-danger">*</span></label>
                    <input type="number" name="ipk" step="0.01" min="0" max="4"
                           class="form-control @error('ipk') is-invalid @enderror"
                           value="{{ old('ipk', $mahasiswa->ipk) }}">
                    @error('ipk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-600 small">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                           value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}">
                    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 small">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-floppy me-2"></i>Simpan Perubahan
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
