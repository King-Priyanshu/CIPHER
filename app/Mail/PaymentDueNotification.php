<?php

namespace App\Mail;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentDueNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $dueDate;

    /**
     * Create a new message instance.
     */
    public function __construct(UserSubscription $subscription, $dueDate)
    {
        $this->subscription = $subscription;
        $this->dueDate = $dueDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Upcoming Payment Reminder - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_due',
            with: [
                'planName' => $this->subscription->plan->name ?? 'Subscription',
                'amount' => $this->subscription->amount,
                'dueDate' => $this->dueDate->format('M d, Y'),
            ],
        );
    }
}
