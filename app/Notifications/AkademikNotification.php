<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AkademikNotification extends Notification
{
    public function __construct(
        public string  $type,
        public string  $judul,
        public string  $pesan,
        public ?string $actionUrl   = null,
        public ?string $actionLabel = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

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

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'   => $this->type,
            'judul'  => $this->judul,
            'pesan'  => $this->pesan,
            'url'    => $this->actionUrl,
        ];
    }

    public function failed(\Throwable $e): void
    {
        Log::error('AkademikNotification gagal', [
            'judul' => $this->judul,
            'type'  => $this->type,
            'error' => $e->getMessage(),
        ]);
    }
}
