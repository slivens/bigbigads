<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Refund;
use Log;

class RefundNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $refund;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $refund = $this->refund;
        return (new MailMessage)
                    ->line("Your refund of order '{$refund->payment->number}' is handled, refund result:{$refund->status}. ")
                    ->line("If you have want to know more, please contact us in our website or just reply this email")
                    ->action('See My Billings', env('APP_URL') . 'app/profile?active=1');
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
            'refund_id' => $this->refund->id
        ];
    }
}
