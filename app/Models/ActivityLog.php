<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ActivityLog
 * 
 * Model untuk mencatat semua aktivitas user di sistem.
 * Build line / log setiap aksi: CRUD, search, import, export.
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'target',
        'description',
        'algorithm',
        'execution_time',
        'complexity',
        'data_count',
        'user_comment',
        'ip_address',
    ];

    protected $casts = [
        'execution_time' => 'float',
        'data_count'     => 'integer',
    ];

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: label warna berdasarkan aksi
     */
    public function getActionBadgeAttribute(): string
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'search' => 'info',
            'import' => 'primary',
            'export' => 'secondary',
            'login'  => 'dark',
            default  => 'light',
        };
    }

    /**
     * Accessor: ikon berdasarkan aksi
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'create' => 'bi-plus-circle',
            'update' => 'bi-pencil',
            'delete' => 'bi-trash',
            'search' => 'bi-search',
            'import' => 'bi-upload',
            'export' => 'bi-download',
            'login'  => 'bi-box-arrow-in-right',
            default  => 'bi-activity',
        };
    }

    /**
     * Static helper: simpan log aktivitas
     *
     * @param string     $action
     * @param string     $description
     * @param array      $extra Data tambahan (target, algorithm, time, dll)
     * @return ActivityLog
     */
    public static function record(string $action, string $description, array $extra = []): self
    {
        return self::create(array_merge([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ], $extra));
    }
}
