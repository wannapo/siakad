<?php

namespace App\Services;

/**
 * ValidationService
 * Semua validasi menggunakan Regex pattern.
 */
class ValidationService
{
    // ── Regex Patterns ──
    const PATTERN_NIM      = '/^\d{10}$/';
    const PATTERN_NAMA     = '/^[a-zA-Z\s\'\-\.]{3,50}$/';
    const PATTERN_ANGKATAN = '/^20[0-9]{2}$/';
    const PATTERN_EMAIL    = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    const PATTERN_HP       = '/^08[0-9]{8,11}$/';

    /**
     * Validasi NIM — harus 10 digit angka.
     */
    public static function validateNim(string $nim): ?string
    {
        if (empty($nim)) return 'NIM wajib diisi.';
        if (!preg_match(self::PATTERN_NIM, $nim)) {
            return 'NIM harus terdiri dari 10 digit angka. Contoh: 2312345678';
        }
        return null;
    }

    /**
     * Validasi Nama — hanya huruf & spasi, 3–50 karakter.
     */
    public static function validateNama(string $nama): ?string
    {
        if (empty($nama)) return 'Nama wajib diisi.';
        if (!preg_match(self::PATTERN_NAMA, $nama)) {
            return 'Nama hanya boleh berisi huruf dan spasi, minimal 3 karakter.';
        }
        return null;
    }

    /**
     * Validasi Angkatan — format 20XX.
     */
    public static function validateAngkatan(string $angkatan): ?string
    {
        if (empty($angkatan)) return 'Angkatan wajib diisi.';
        if (!preg_match(self::PATTERN_ANGKATAN, $angkatan)) {
            return 'Format angkatan tidak valid. Gunakan format 20XX, contoh: 2023';
        }
        return null;
    }

    /**
     * Validasi Email — format standar email.
     */
    public static function validateEmail(string $email): ?string
    {
        if (empty($email)) return 'Email wajib diisi.';
        if (!preg_match(self::PATTERN_EMAIL, $email)) {
            return 'Format email tidak valid. Contoh: nama@domain.com';
        }
        return null;
    }

    /**
     * Validasi No HP — opsional, tapi jika diisi harus format 08xx.
     */
    public static function validateHp(string $hp): ?string
    {
        if (empty($hp)) return null; // opsional
        if (!preg_match(self::PATTERN_HP, $hp)) {
            return 'Format HP tidak valid. Harus dimulai 08 dan 10–13 digit. Contoh: 081234567890';
        }
        return null;
    }

    /**
     * Validasi Jurusan — harus salah satu dari daftar yang valid.
     */
    public static function validateJurusan(string $jurusan): ?string
    {
        $valid = ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen', 'Akuntansi', 'Hukum'];
        if (empty($jurusan)) return 'Jurusan wajib dipilih.';
        if (!in_array($jurusan, $valid)) {
            return 'Jurusan tidak valid.';
        }
        return null;
    }

    /**
     * Validasi semua field sekaligus.
     * Return array errors (field => pesan), kosong jika valid.
     */
    public static function validateAll(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if ($err = self::validateNim($data['nim'] ?? ''))      $errors['nim']      = $err;
        if ($err = self::validateNama($data['nama'] ?? ''))    $errors['nama']     = $err;
        if ($err = self::validateJurusan($data['jurusan'] ?? '')) $errors['jurusan'] = $err;
        if ($err = self::validateAngkatan($data['angkatan'] ?? '')) $errors['angkatan'] = $err;
        if ($err = self::validateEmail($data['email'] ?? ''))  $errors['email']    = $err;
        if ($err = self::validateHp($data['hp'] ?? ''))        $errors['hp']       = $err;

        return $errors;
    }

    /**
     * Cek apakah format CSV header valid.
     */
    public static function validateCsvHeader(array $header): bool
    {
        $required = ['nim', 'nama', 'jurusan', 'angkatan', 'email'];
        foreach ($required as $col) {
            if (!in_array(strtolower(trim($col)), array_map('strtolower', $header))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Cek apakah format JSON array valid untuk import.
     */
    public static function validateJsonImport($data): ?string
    {
        if (!is_array($data)) return 'Data JSON harus berupa array.';
        if (empty($data))     return 'Array JSON kosong.';

        $required = ['nim', 'nama', 'jurusan', 'angkatan', 'email'];
        $first    = (array) $data[0];
        foreach ($required as $field) {
            if (!array_key_exists($field, $first)) {
                return "Field wajib tidak ditemukan: {$field}";
            }
        }
        return null;
    }
}
