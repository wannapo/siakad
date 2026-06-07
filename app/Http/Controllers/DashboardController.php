<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Mahasiswa::count(),
            'aktif'    => Mahasiswa::where('status', 'Aktif')->count(),
            'jurusan'  => Mahasiswa::distinct('jurusan')->count('jurusan'),
            'angkatan' => Mahasiswa::distinct('angkatan')->count('angkatan'),
        ];

        $recent = Mahasiswa::latest()->limit(5)->get();
        $logs   = ActivityLog::latest()->limit(5)->get();

        return view('mahasiswa.dashboard', compact('stats', 'recent', 'logs'));
    }
}
