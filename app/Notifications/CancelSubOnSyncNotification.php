<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Subscription;

class CancelSubOnSyncNotification extends Notification implements  ShouldQueue
{
    use Queueable;
    private $subscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
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
        $sub = $this->subscription;

        return (new MailMessage)
                    ->subject("Cancel Notifcation Request Caused By bigbigads's Sync Subscriptions")
                    ->line("agreement-id:{$sub->agreement_id} is not user {$sub->user->email} 's current subscription")
                    ->line('Please cancel it with command:')
                    ->line("php artisan bba:cancel {$sub->user->email}")
                    ->to(env('ADMIN_EMAIL'));
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
