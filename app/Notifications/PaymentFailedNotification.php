<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscription;
    protected $retryCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserSubscription $subscription, int $retryCount)
    {
        $this->subscription = $subscription;
        $this->retryCount = $retryCount;
    }

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
            ->subject('Payment Failed - Action Required')
            ->line('We were unable to process your subscription payment.')
            ->line("This was attempt number {$this->retryCount} of 3.")
            ->line('We will attempt to charge your card again soon. Please ensure your payment method has sufficient funds.')
            ->action('Manage Subscription', route('subscriber.subscription.index'))
            ->line('If payments continue to fail, your subscription may be suspended.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'retry_count' => $this->retryCount,
            'status' => 'past_due',
            'message' => 'Payment failed. We will retry soon.',
        ];
    }
}
