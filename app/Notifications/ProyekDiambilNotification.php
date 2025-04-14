<?php 

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProyekDiambilNotification extends Notification
{
    use Queueable;

    protected $proyek;
    protected $perusahaan;
    protected $jadwalSurvey;

    public function __construct($perusahaan, $proyek, $jadwalSurvey)
    {
        $this->perusahaan = $perusahaan;
        $this->proyek = $proyek;
        $this->jadwalSurvey = $jadwalSurvey;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Perusahaan {$this->perusahaan} mengambil proyek Anda dan sudah mengatur jadwal survey pada {$this->jadwalSurvey}.",
            'PerusahaanID' => $this->proyek->PerusahaanID, 
            'ProyekID' => $this->proyek->ProyekID, 
            'TglSurvey' => $this->jadwalSurvey,
            'url' => route('proyeks.index', $this->proyek->ProyekID),
        ];
    }
}
