@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')

@section('content')

<div class="form-page-wrap">
    <div class="form-card">
        <div class="form-card-header">
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            <span class="form-card-title">Data Mahasiswa Baru</span>
        </div>

        <form method="POST" action="{{ route('mahasiswa.store') }}" novalidate id="mahasiswa-form">
            @csrf

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:16px">
                    <i class="ti ti-alert-circle"></i>
                    <div>
                        <strong>Terdapat {{ $errors->count() }} kesalahan:</strong>
                        <ul style="margin:4px 0 0 16px">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="form-section-label">IDENTITAS MAHASISWA</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">NIM <span class="required">*</span></label>
                    <input
                        type="text"
                        name="nim"
                        class="form-input {{ $errors->has('nim') ? 'error' : '' }}"
                        value="{{ old('nim') }}"
                        placeholder="2312345678"
                        maxlength="10"
                        pattern="\d{10}"
                    >
                    @error('nim')
                        <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                    @enderror
                    <div class="form-hint">10 digit angka. Contoh: 2312345678</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input
                        type="text"
                        name="nama"
                        class="form-input {{ $errors->has('nama') ? 'error' : '' }}"
                        value="{{ old('nama') }}"
                        placeholder="Nama Lengkap Mahasiswa"
                    >
                    @error('nama')
                        <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jurusan <span class="required">*</span></label>
                    <select name="jurusan" class="form-select {{ $errors->has('jurusan') ? 'error' : '' }}">
                        <option value="">— Pilih Jurusan —</option>
                        @foreach(['Informatika','Sistem Informasi','Teknik Elektro','Manajemen','Akuntansi','Hukum'] as $j)
                            <option value="{{ $j }}" {{ old('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jurusan')
                        <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Angkatan <span class="required">*</span></label>
                    <input
                        type="text"
                        name="angkatan"
                        class="form-input {{ $errors->has('angkatan') ? 'error' : '' }}"
                        value="{{ old('angkatan') }}"
                        placeholder="2023"
                        maxlength="4"
                        pattern="20[0-9]{2}"
                    >
                    @error('angkatan')
                        <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                    @enderror
                    <div class="form-hint">Format: 20XX</div>
                </div>
            </div>

            <div class="form-section-label" style="margin-top:8px">KONTAK</div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input
                    type="email"
                    name="email"
                    class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="mahasiswa@email.ac.id"
                >
                @error('email')
                    <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">No. HP</label>
                    <input
                        type="text"
                        name="hp"
                        class="form-input {{ $errors->has('hp') ? 'error' : '' }}"
                        value="{{ old('hp') }}"
                        placeholder="08xxxxxxxxxx"
                        maxlength="15"
                    >
                    @error('hp')
                        <div class="form-error"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                    @enderror
                    <div class="form-hint">Format: 08XXXXXXXXXX</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ old('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check"></i> Simpan Mahasiswa
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
