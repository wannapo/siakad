<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class LogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::latest()->paginate(30);
        return view('log.index', compact('logs'));
    }

    public function clear()
    {
        ActivityLog::truncate();
        return redirect()->route('log.index')->with('success', 'Activity log berhasil dihapus.');
    }
}
