<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use App\Services\Payment\StripeService;
use App\Jobs\ProcessWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function handle(Request $request): Response
    {
        $payloadRaw = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        if (!$this->stripeService->verifyWebhookSignature($payloadRaw, $signature)) {
            Log::warning('Stripe webhook signature verification failed');
            return response('Invalid signature', 401);
        }

        $payload = json_decode($payloadRaw, true);
        $event = $payload['type'] ?? 'unknown';

        // Unique Event ID logic
        $eventId = $payload['id'] ?? time();

        // Idempotency: Check if we have seen this event_id before
        if (WebhookEvent::where('event_id', $eventId)->exists()) {
            Log::info("Webhook Event $eventId already received.");
            return response('OK', 200);
        }

        // Store Event
        $webhookEvent = WebhookEvent::create([
            'event_id' => $eventId,
            'gateway' => 'stripe',
            'event_type' => $event,
            'payload' => $payload,
            'status' => 'pending', // Queue it
        ]);

        // Dispatch Job
        ProcessWebhook::dispatch($eventId);

        return response('OK', 200);
    }
}
