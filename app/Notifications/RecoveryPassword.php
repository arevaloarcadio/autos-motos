<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecoveryPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    
    public $user;
    public $code;

    public function __construct($user,$code)
    {
        $this->user = $user;
        $this->code = $code;
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
            ->subject('Recuperar Contraseña Autos Motos')
            ->line('Hola '.$this->user->first_name.' '.$this->user->last_name)
            ->line('Hemos recibido una solicitud para restablecer la contraseña')
            ->line('Su código de recuperación es:')
            ->line(new \Illuminate\Support\HtmlString('<p style="font-size:20px;text-align:center; letter-spacing: 2px;"><b>'.$this->code.'</b></p>'))
            //->action('Ingrese a Autos Motos','https://automotos.dattatech.com/recovery')
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
