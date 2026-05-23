<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mahasiswa
 * 
 * Model OOP untuk data mahasiswa.
 * Menggunakan Eloquent ORM Laravel untuk interaksi database.
 * 
 * @property int $id
 * @property string $nim
 * @property string $nama
 * @property string $email
 * @property string $no_hp
 * @property string $prodi
 * @property string $fakultas
 * @property int $angkatan
 * @property string $status
 * @property float $ipk
 * @property string $alamat
 * @property string $tanggal_lahir
 */
class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal (mass assignment)
     * @var array<string>
     */
    protected $fillable = [
        'nim',
        'nama',
        'email',
        'no_hp',
        'prodi',
        'fakultas',
        'angkatan',
        'status',
        'ipk',
        'alamat',
        'tanggal_lahir',
    ];

    /**
     * Cast tipe data kolom
     * @var array<string, string>
     */
    protected $casts = [
        'ipk'           => 'decimal:2',
        'angkatan'      => 'integer',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Scope: filter mahasiswa aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope: filter berdasarkan prodi
     */
    public function scopeByProdi($query, string $prodi)
    {
        return $query->where('prodi', $prodi);
    }

    /**
     * Scope: filter berdasarkan angkatan
     */
    public function scopeByAngkatan($query, int $angkatan)
    {
        return $query->where('angkatan', $angkatan);
    }

    /**
     * Accessor: format NIM dengan prefix
     */
    public function getNimFormattedAttribute(): string
    {
        return strtoupper($this->nim);
    }

    /**
     * Accessor: label status dengan badge warna
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'aktif'  => 'success',
            'cuti'   => 'warning',
            'lulus'  => 'info',
            'keluar' => 'danger',
            default  => 'secondary',
        };
    }

    /**
     * Accessor: format IPK 2 desimal
     */
    public function getIpkFormattedAttribute(): string
    {
        return number_format((float)$this->ipk, 2);
    }

    /**
     * Konversi ke array untuk export CSV/JSON
     */
    public function toExportArray(): array
    {
        return [
            'nim'           => $this->nim,
            'nama'          => $this->nama,
            'email'         => $this->email,
            'no_hp'         => $this->no_hp ?? '',
            'prodi'         => $this->prodi,
            'fakultas'      => $this->fakultas,
            'angkatan'      => $this->angkatan,
            'status'        => $this->status,
            'ipk'           => $this->ipk,
            'alamat'        => $this->alamat ?? '',
            'tanggal_lahir' => $this->tanggal_lahir ? $this->tanggal_lahir->format('Y-m-d') : '',
        ];
    }
}
