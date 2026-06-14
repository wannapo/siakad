<?php

namespace App\Observers;

use App\Models\Mahasiswa;
use App\Models\ActivityLog;
use App\Events\LogCreated;

class MahasiswaObserver
{
    public function created(Mahasiswa $mahasiswa)
    {
        $this->saveLog("Menambah mahasiswa baru: {$mahasiswa->nama}", 'create');
    }

    public function updated(Mahasiswa $mahasiswa)
    {
        $this->saveLog("Mengubah data: {$mahasiswa->nama}", 'update');
    }

    public function deleted(Mahasiswa $mahasiswa)
    {
        $this->saveLog("Menghapus data: {$mahasiswa->nama}", 'delete');
    }

    private function saveLog($keterangan, $aksi)
    {
        // 1. Simpan ke Database
        $log = ActivityLog::create([
            'keterangan' => $keterangan,
            'aksi'       => $aksi,
            'user_id'    => auth()->id() ?? 1, // Fallback ke ID 1 jika belum login / testing
        ]);

        // 2. Lempar ke Pusher biar Real-time di frontend
        broadcast(new LogCreated($log));
    }
}