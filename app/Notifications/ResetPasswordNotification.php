<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url('/password/reset/' . $this->token . '?email=' . urlencode($this->email));

        return (new MailMessage)
            ->subject('Відновлення пароля')
            ->greeting('Вітаємо!')
            ->line('Ви отримали цей лист, тому що ми отримали запит на відновлення пароля для вашого акаунту.')
            ->action('Відновити пароль', $resetUrl)
            ->line('Це посилання дійсне протягом 60 хвилин.')
            ->salutation('Якщо ви не запитували відновлення пароля, просто проігноруйте цей лист.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
