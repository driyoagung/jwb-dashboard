<?php

namespace App\Services;

use App\Models\PosTransaction;
use Illuminate\Support\Facades\Http;
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

    public function cancel(PosTransaction $transaction): void
    {
        $this->configure();
        try {
            Transaction::cancel($transaction->transaction_number);
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Pembatalan Midtrans belum dikonfirmasi. Stok tetap ditahan; cek status atau coba lagi.', previous: $exception);
        }
    }

    public function cancelSession(PosTransaction $transaction): bool
    {
        $this->configure();
        $base = config('services.midtrans.is_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
        try {
            $response = Http::acceptJson()->asJson()->connectTimeout(5)->timeout(15)
                ->withHeaders(['Authorization' => (string) config('services.midtrans.server_key')])
                ->post($base.'/snap/v1/transactions/'.rawurlencode($transaction->midtrans_snap_token).'/cancel');
            if ($response->successful() && is_string($response->json('canceled_at'))) {
                return true;
            }
            $errors = $response->json('error_messages', []);
            if (is_array($errors) && in_array('token already canceled', $errors, true)) {
                return true;
            }
            if (is_array($errors) && in_array('Transaction is on progress', $errors, true)) {
                return false;
            }
        } catch (Throwable $exception) {
            report($exception);
            throw new RuntimeException('Sesi Midtrans belum dapat dibatalkan. Stok tetap ditahan; coba lagi.', previous: $exception);
        }

        throw new RuntimeException('Pembatalan sesi Midtrans belum dikonfirmasi. Stok tetap ditahan; cek status atau coba lagi.');
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
