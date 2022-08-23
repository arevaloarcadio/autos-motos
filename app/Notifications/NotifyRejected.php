<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyRejected extends Notification
{
    use Queueable;

    public $title;
    public $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title,$message)
    {
        $this->title = $title;
        $this->message = $message;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Su anuncio ha sido rechazado')
            ->line('Lamento informarle que su anuncio: ')
            ->line($this->title)
            ->line('Ha sido rechazado')
            ->line('Razón del rechazo: ')
            ->line($this->message)
            ->action('Ingrese a Autos Motos',env('URL_FRONT'))
            ->line(' ')
            ->salutation('Gracias por usar nuestra web');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
