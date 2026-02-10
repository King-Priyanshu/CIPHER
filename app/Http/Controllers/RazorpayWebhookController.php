<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use App\Services\Payment\RazorpayService;
use App\Jobs\ProcessWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function handle(Request $request): Response
    {
        $payloadRaw = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        if (!$this->razorpayService->verifyWebhookSignature($payloadRaw, $signature)) {
            Log::warning('Razorpay webhook signature verification failed');
            return response('Invalid signature', 401);
        }

        $payload = json_decode($payloadRaw, true);
        $event = $payload['event'] ?? 'unknown';
        // Unique Event ID logic
        $accountId = $payload['account_id'] ?? '';
        $entityId = $payload['payload']['payment']['entity']['id'] 
            ?? $payload['payload']['subscription']['entity']['id'] 
            ?? $payload['payload']['invoice']['entity']['id'] 
            ?? time();
        
        $eventId = "{$accountId}_{$event}_{$entityId}";

        // Idempotency: Check if we have seen this event_id before
        if (WebhookEvent::where('event_id', $eventId)->exists()) {
             Log::info("Webhook Event $eventId already received.");
             return response('OK', 200);
        }

        // Store Event
        $webhookEvent = WebhookEvent::create([
            'event_id' => $eventId,
            'gateway' => 'razorpay',
            'event_type' => $event,
            'payload' => $payload,
            'status' => 'pending', // Queue it
        ]);

        // Dispatch Job
        ProcessWebhook::dispatch($eventId);

        return response('OK', 200);
    }
}
