<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'gateway',
        'event_type',
        'payload',
        'status', // processing, processed, failed
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
