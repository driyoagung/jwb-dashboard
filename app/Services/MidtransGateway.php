<?php

namespace App\Services;

use App\Models\PosTransaction;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;
use Throwable;

class MidtransGateway
{
    public function configured(): bool
    {
        return filled(config('services.midtrans.server_key')) && filled(config('services.midtrans.client_key'));
    }

    /** @return array{token: string, url: string} */
    public function create(PosTransaction $transaction): array
    {
        if (! $this->configured()) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY harus diisi pada .env.');
        }
        $this->configure();
        Config::$paymentIdempotencyKey = $transaction->idempotency_key;

        try {
            $response = Snap::createTransaction([
                'transaction_details' => [
                    'order_id' => $transaction->transaction_number,
                    'gross_amount' => $transaction->grand_total,
                ],
                'customer_details' => [
                    'first_name' => $transaction->cashier->name,
                    'email' => $transaction->cashier->email,
                ],
                'callbacks' => ['finish' => route('pos.transactions.show', $transaction)],
            ]);
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Sesi pembayaran Midtrans gagal dibuat. Coba lagi.', previous: $exception);
        } finally {
            Config::$paymentIdempotencyKey = null;
        }

        $url = $response->redirect_url ?? null;
        $token = $response->token ?? null;
        $expectedHost = config('services.midtrans.is_production') ? 'app.midtrans.com' : 'app.sandbox.midtrans.com';
        if (! is_string($url) || parse_url($url, PHP_URL_SCHEME) !== 'https' || parse_url($url, PHP_URL_HOST) !== $expectedHost || ! is_string($token) || $token === '') {
            throw new RuntimeException('Sesi pembayaran Midtrans tidak valid.');
        }

        return ['token' => $token, 'url' => $url];
    }

    /** @return array<string, mixed>|null */
    public function status(PosTransaction $transaction): ?array
    {
        $this->configure();

        try {
            return (array) Transaction::status($transaction->transaction_number);
        } catch (Throwable $exception) {
            if ((int) $exception->getCode() === 404) {
                // Snap may not expose a Core API status until a payment method is selected.
                return null;
            }
            report($exception);
            throw new RuntimeException('Status Midtrans belum dapat diperiksa. Coba lagi.', previous: $exception);
        }
    }

    private function configure(): void
    {
        if (! filled(config('services.midtrans.server_key'))) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY harus diisi pada .env.');
        }

        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$curlOptions = [CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => ['User-Agent: Griyo-POS']];
    }
}
