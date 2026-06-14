<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Mahasiswa extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'nim',
        'nama',
        'jurusan',
        'angkatan',
        'email',
        'hp',
        'status',
    ];

    // ── Status options ──
    const STATUS_AKTIF = 'Aktif';
    const STATUS_CUTI  = 'Cuti';
    const STATUS_LULUS = 'Lulus';

    public static function statusList(): array
    {
        return [self::STATUS_AKTIF, self::STATUS_CUTI, self::STATUS_LULUS];
    }

    public static function jurusanList(): array
    {
        return ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen', 'Akuntansi', 'Hukum'];
    }
}
