<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ActivityLog;
use App\Notifications\AkademikNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmailController extends Controller
{
    public function broadcast(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:pengumuman,nilai,tagihan',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'url'     => 'nullable|string|max:255',
        ]);

        $mahasiswaList = Mahasiswa::whereNotNull('email')->get();

        if ($mahasiswaList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mahasiswa dengan email terdaftar.',
            ], 422);
        }

        Notification::send($mahasiswaList, new AkademikNotification(
            type:        $request->type,
            judul:       $request->subject,
            pesan:       $request->message,
            actionUrl:   $request->url ?: null,
            actionLabel: 'Lihat Detail',
        ));

        ActivityLog::create([
            'aksi'       => 'EMAIL',
            'keterangan' => "Broadcast email '{$request->subject}' dikirim ke {$mahasiswaList->count()} mahasiswa",
            'user_id'    => auth()->id(),
            'durasi_ms'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Email sedang dikirim ke {$mahasiswaList->count()} mahasiswa.",
        ]);
    }

    public function individual(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer|exists:mahasiswas,id',
            'type'    => 'required|in:pengumuman,nilai,tagihan',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'url'     => 'nullable|string|max:255',
        ]);

        $mahasiswaList = Mahasiswa::whereIn('id', $request->ids)
            ->whereNotNull('email')
            ->get();

        if ($mahasiswaList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa yang dipilih tidak memiliki email terdaftar.',
            ], 422);
        }

        Notification::send($mahasiswaList, new AkademikNotification(
            type:        $request->type,
            judul:       $request->subject,
            pesan:       $request->message,
            actionUrl:   $request->url ?: null,
            actionLabel: 'Lihat Detail',
        ));

        $names = $mahasiswaList->pluck('nama')->join(', ');
        ActivityLog::create([
            'aksi'       => 'EMAIL',
            'keterangan' => "Email '{$request->subject}' dikirim ke: {$names}",
            'user_id'    => auth()->id(),
            'durasi_ms'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Email sedang dikirim ke {$mahasiswaList->count()} mahasiswa.",
        ]);
    }
}