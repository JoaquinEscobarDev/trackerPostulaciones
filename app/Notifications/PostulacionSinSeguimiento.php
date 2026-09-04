<?php

namespace App\Notifications;

use App\Models\Postulacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostulacionSinSeguimiento extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Postulacion $postulacion,
        public int $diasSinSeguimiento,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Seguimiento pendiente: {$this->postulacion->empresa}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Hace {$this->diasSinSeguimiento} días que postulaste a **{$this->postulacion->cargo}** en **{$this->postulacion->empresa}** y sigue en estado \"Postulado\".")
            ->line('Puede ser un buen momento para hacer seguimiento con la empresa.')
            ->action('Ver mis postulaciones', url('/postulaciones'))
            ->line('Este es un recordatorio automático de Tracker de Postulaciones.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'postulacion_id' => $this->postulacion->id,
            'empresa' => $this->postulacion->empresa,
            'cargo' => $this->postulacion->cargo,
            'dias_sin_seguimiento' => $this->diasSinSeguimiento,
        ];
    }
}
