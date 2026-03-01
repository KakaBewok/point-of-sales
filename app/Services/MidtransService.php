<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Get Midtrans client key for frontend.
     */
    public function getClientKey(): string
    {
        return config('midtrans.client_key');
    }

    /**
     * Create a QRIS payment via Midtrans.
     */
    public function createQrisPayment(Transaction $transaction): Payment
    {
        $orderId = 'POS-QRIS-' . $transaction->id . '-' . time();

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $transaction->grand_total,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        try {
            $response = CoreApi::charge($params);

            return Payment::create([
                'transaction_id' => $transaction->id,
                'method' => 'qris',
                'amount' => $transaction->grand_total,
                'status' => 'pending',
                'midtrans_transaction_id' => $response->transaction_id ?? null,
                'midtrans_order_id' => $orderId,
                'midtrans_response' => json_decode(json_encode($response), true),
                'qris_url' => $this->extractQrisUrl($response),
                'expires_at' => now()->addMinutes(15),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat pembayaran QRIS. Silakan coba lagi.');
        }
    }

    /**
     * Create a Virtual Account payment via Midtrans.
     */
    public function createVaPayment(Transaction $transaction, string $bank = 'bca'): Payment
    {
        $orderId = 'POS-VA-' . $transaction->id . '-' . time();

        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $transaction->grand_total,
            ],
            'bank_transfer' => [
                'bank' => $bank,
            ],
        ];

        try {
            $response = CoreApi::charge($params);

            return Payment::create([
                'transaction_id' => $transaction->id,
                'method' => 'va',
                'amount' => $transaction->grand_total,
                'status' => 'pending',
                'midtrans_transaction_id' => $response->transaction_id ?? null,
                'midtrans_order_id' => $orderId,
                'midtrans_response' => json_decode(json_encode($response), true),
                'va_number' => $this->extractVaNumber($response, $bank),
                'expires_at' => now()->addHours(24),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans VA Error: ' . $e->getMessage());
            throw new \Exception('Gagal membuat pembayaran VA. Silakan coba lagi.');
        }
    }

    /**
     * Handle Midtrans webhook notification.
     */
    public function handleNotification(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            Log::warning('Midtrans notification without order_id', $payload);
            return;
        }

        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (!$payment) {
            Log::warning('Payment not found for order_id: ' . $orderId);
            return;
        }

        // Store the full notification response
        $payment->update([
            'midtrans_response' => $payload,
            'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
        ]);

        // Process based on transaction status
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $this->handlePaymentSuccess($payment);
            }
        } elseif ($transactionStatus === 'pending') {
            // Payment is still pending, do nothing
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $this->handlePaymentFailed($payment);
        } elseif ($transactionStatus === 'expire') {
            $this->handlePaymentExpired($payment);
        }
    }

    /**
     * Handle successful payment.
     */
    protected function handlePaymentSuccess(Payment $payment): void
    {
        $payment->markAsSuccess();

        $transaction = $payment->transaction;

        if ($transaction && $transaction->isPending()) {
            $transactionService = app(TransactionService::class);
            $transactionService->completeTransaction($transaction);
        }

        Log::info("Payment success for order: {$payment->midtrans_order_id}");
    }

    /**
     * Handle failed payment.
     */
    protected function handlePaymentFailed(Payment $payment): void
    {
        $payment->markAsFailed();
        Log::info("Payment failed for order: {$payment->midtrans_order_id}");
    }

    /**
     * Handle expired payment.
     */
    protected function handlePaymentExpired(Payment $payment): void
    {
        $payment->markAsExpired();

        $transaction = $payment->transaction;
        if ($transaction && $transaction->isPending()) {
            $transactionService = app(TransactionService::class);
            $transactionService->cancelTransaction($transaction);
        }

        Log::info("Payment expired for order: {$payment->midtrans_order_id}");
    }

    /**
     * Extract QRIS URL from Midtrans response.
     */
    private function extractQrisUrl(object $response): ?string
    {
        // Midtrans returns actions array with QR code URL
        if (isset($response->actions)) {
            foreach ($response->actions as $action) {
                if ($action->name === 'generate-qr-code') {
                    return $action->url;
                }
            }
        }

        return null;
    }

    /**
     * Extract VA number from Midtrans response.
     */
    private function extractVaNumber(object $response, string $bank): ?string
    {
        if (isset($response->va_numbers) && is_array($response->va_numbers)) {
            foreach ($response->va_numbers as $va) {
                if ($va->bank === $bank) {
                    return $va->va_number;
                }
            }
        }

        // Permata bank uses a different structure
        if (isset($response->permata_va_number)) {
            return $response->permata_va_number;
        }

        return null;
    }
}
