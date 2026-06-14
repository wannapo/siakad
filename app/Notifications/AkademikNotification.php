<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AkademikNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // Retry & timeout config
    public int $tries   = 3;
    public int $backoff = 60;
    public int $timeout = 30;

    public function __construct(
        public string  $type,         // 'pengumuman' | 'nilai' | 'tagihan'
        public string  $judul,
        public string  $pesan,
        public ?string $actionUrl   = null,
        public ?string $actionLabel = null,
    ) {}

    /**
     * Channel yang dipakai: email + simpan ke database (notif in-app)
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Format email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $icon = match ($this->type) {
            'nilai'    => '📋',
            'tagihan'  => '🧾',
            default    => '📢',
        };

        $mail = (new MailMessage)
            ->subject("{$icon} {$this->judul}")
            ->greeting('Halo, ' . ($notifiable->nama ?? $notifiable->name) . '!')
            ->line($this->pesan);

        if ($this->actionUrl) {
            $mail->action($this->actionLabel ?? 'Lihat Detail', $this->actionUrl);
        }

        return $mail
            ->line('Jika ada pertanyaan, silakan hubungi admin akademik.')
            ->salutation('Salam, Tim SISMAKA');
    }

    /**
     * Format simpan ke tabel notifications (in-app bell icon)
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'   => $this->type,
            'judul'  => $this->judul,
            'pesan'  => $this->pesan,
            'url'    => $this->actionUrl,
        ];
    }

    /**
     * Kalau semua retry gagal, catat di log
     */
    public function failed(\Throwable $e): void
    {
        Log::error('AkademikNotification gagal', [
            'judul' => $this->judul,
            'type'  => $this->type,
            'error' => $e->getMessage(),
        ]);
    }
}
