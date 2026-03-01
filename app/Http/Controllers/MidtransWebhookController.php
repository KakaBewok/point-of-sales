<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
    ) {}

    /**
     * Handle incoming Midtrans webhook/notification.
     */
    public function handle(Request $request)
    {
        try {
            // Verify the signature
            $serverKey = config('midtrans.server_key');
            $payload = $request->all();

            $orderId = $payload['order_id'] ?? '';
            $statusCode = $payload['status_code'] ?? '';
            $grossAmount = $payload['gross_amount'] ?? '';
            $signatureKey = $payload['signature_key'] ?? '';

            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans webhook: Invalid signature', [
                    'order_id' => $orderId,
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Process the notification
            $this->midtransService->handleNotification($payload);

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);

            // Always return 200 to Midtrans to prevent retry loops
            return response()->json(['message' => 'Processed with errors'], 200);
        }
    }
}
