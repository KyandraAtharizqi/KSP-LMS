<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KnowledgeInvitation extends Notification
{
    use Queueable;

    protected $pengajuan;

    public function __construct($pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function via($notifiable)
    {
        // Notifikasi bisa via email dan database
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Undangan Knowledge Sharing')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Anda diundang untuk mengikuti Knowledge Sharing:')
            ->line('Judul: '.$this->pengajuan->judul)
            ->line('Pemateri: '.$this->pengajuan->pemateri)
            ->line('Tanggal: '.$this->pengajuan->tanggal_mulai.' s/d '.$this->pengajuan->tanggal_selesai)
            ->action('Lihat Detail', url('/knowledge/pengajuan/'.$this->pengajuan->id))
            ->line('Terima kasih.');
    }

    public function toArray($notifiable)
    {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'judul' => $this->pengajuan->judul,
            'pesan' => 'Anda diundang mengikuti Knowledge Sharing.',
            'type' => 'knowledge_sharing',
            'tanggal' => $this->pengajuan->tanggal_mulai,
            'read' => false
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'judul' => $this->pengajuan->judul,
            'pesan' => 'Anda diundang mengikuti Knowledge Sharing.',
            'type' => 'knowledge_sharing',
            'tanggal' => $this->pengajuan->tanggal_mulai,
            'read' => false
        ];
    }
}
