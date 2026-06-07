<?php

namespace App\Services;

use App\Models\ActivityLog;

/**
 * LogService
 * Catat semua aktivitas ke tabel activity_logs.
 */
class LogService
{
    public static function log(string $aksi, string $keterangan, ?float $durasiMs = null): void
    {
        try {
            ActivityLog::create([
                'aksi'       => $aksi,
                'keterangan' => $keterangan,
                'durasi_ms'  => $durasiMs,
                'user_id'    => auth()->id(),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai gagal log menghentikan proses utama
        }
    }

    public static function crud(string $msg): void   { self::log('crud',   $msg); }
    public static function search(string $msg, float $ms): void { self::log('search', $msg, $ms); }
    public static function sort(string $msg, float $ms): void   { self::log('sort',   $msg, $ms); }
    public static function import(string $msg): void { self::log('import', $msg); }
    public static function error(string $msg): void  { self::log('error',  $msg); }
}
