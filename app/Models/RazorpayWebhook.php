<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_type',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    const STATUS_RECEIVED = 'received';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';

    /**
     * Mark webhook as processed.
     */
    public function markProcessed(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSED,
        ]);
    }

    /**
     * Mark webhook as failed with error.
     */
    public function markFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }

    /**
     * Check if event was already processed.
     */
    public static function wasProcessed(string $eventId): bool
    {
        return self::where('event_id', $eventId)
            ->where('status', self::STATUS_PROCESSED)
            ->exists();
    }
}
