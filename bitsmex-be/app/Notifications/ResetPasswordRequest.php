<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
class ResetPasswordRequest extends Notification implements ShouldQueue
{
    use Queueable;
    protected $token;
    /**
    * Create a new notification instance.
    *
    * @return void
    */
    public function __construct($token)
    {
        $this->token = $token;
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
        $clientDomain = env('APP_BE');
        $url = ($clientDomain.'/reset?token=' . $this->token);
        
        return (new MailMessage)
            ->line('We are so sorry about your forgotten password issue. However, do not feel worried, you can reset your Bitsmex password by clicking the button below.')
            ->action('Reset Password', url($url))
            ->line('If you did not request a password reset, feel free to delete this email and carry on trading!');
    }
}