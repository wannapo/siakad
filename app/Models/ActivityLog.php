<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'aksi',
        'keterangan',
        'durasi_ms',
        'user_id',
    ];

    // Aksi types
    const AKSI_CRUD   = 'crud';
    const AKSI_SEARCH = 'search';
    const AKSI_SORT   = 'sort';
    const AKSI_IMPORT = 'import';
    const AKSI_ERROR  = 'error';
}
