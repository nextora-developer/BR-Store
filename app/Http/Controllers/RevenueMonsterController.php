<?php

namespace App\Http\Controllers;

use App\Mail\AdminOrderNotificationMail;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RevenueMonsterController extends Controller
{
    /**
     * Create Hosted Payment Checkout
     * POST https://(sb-)open.revenuemonster.my/v3/payment/online
     */
    public function pay(Order $order)
    {
        abort_unless(auth()->check(), 403);

        if (!empty($order->user_id)) {
            abort_unless((int) $order->user_id === (int) auth()->id(), 403);
        }

        if (strtolower((string) $order->status) !== 'pending') {
            return redirect()
                ->route('account.orders.show', $order)
                ->with('error', 'This order is not payable.');
        }

        // ✅ Amount (cents)
        $amountCents = (int) round(((float) ($order->total ?? 0)) * 100);

        Log::info('RM amount debug', [
            'order_no'         => $order->order_no,
            'total_raw'        => $order->total,
            'total_float'      => (float) $order->total,
            'amount_cents'     => $amountCents,
        ]);

        if ($amountCents <= 0) {
            Log::error('RM amount invalid', [
                'order_no' => $order->order_no,
                'total'    => $order->total,
            ]);
            return back()->with('error', 'Order amount invalid. Please contact support.');
        }

        // ✅ RM requires order.id to be 24 chars
        $rmOrderId = Str::padLeft((string) $order->id, 24, '0');

        // ✅ Config
        $storeId    = (string) config('services.rm.store_id');
        $apiBase    = (string) config('services.rm.api_base');
        $returnUrl  = (string) config('services.rm.return_url');
        $webhookUrl = (string) config('services.rm.webhook_url');

        // ✅ OAuth token
        $accessToken = $this->rmAccessToken();

        Log::info('RM config snapshot', [
            'store_id'       => $storeId,
            'api_base'       => $apiBase,
            'return_url'     => $returnUrl,
            'webhook_url'    => $webhookUrl,
            'order_no'       => $order->order_no,
            'rm_order_id_24' => $rmOrderId,
            'amount_cents'   => $amountCents,
        ]);

        // ✅ Payload
        $payload = [
            'storeId'       => $storeId,
            'redirectUrl' => $returnUrl . '?order_no=' . urlencode($order->order_no),
            'notifyUrl'     => $webhookUrl,
            'layoutVersion' => 'v4',
            'type'          => 'WEB_PAYMENT',
            'order' => [
                'id'             => $rmOrderId,
                'title'          => Str::limit('Order ' . $order->order_no, 32, ''),
                'currencyType'   => 'MYR',
                'amount'         => $amountCents,
                'detail'         => null,
                'additionalData' => (string) $order->order_no,
            ],
            'customer' => [
                'email'       => $order->customer_email ?? $order->email ?? null,
                'countryCode' => '60',
                'phoneNumber' => $order->customer_phone ?? $order->phone ?? null,
            ],
        ];

        $endpoint  = rtrim($apiBase, '/') . '/v3/payment/online';
        $nonceStr  = Str::random(32);
        $timestamp = (string) time();
        $signType  = 'sha256';

        Log::info('RM signing request', [
            'endpoint'    => $endpoint,
            'nonce_len'   => strlen($nonceStr),
            'timestamp'   => $timestamp,
            'sign_type'   => $signType,
            'payload_md5' => md5(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ]);

        // ✅ Signature body (base64), header must include "sha256 " prefix
        $signatureBody = $this->signRequest(
            payload: $payload,
            method: 'post',
            nonceStr: $nonceStr,
            timestamp: $timestamp,
            signType: $signType,
            requestUrl: $endpoint // ✅ full URL as RM doc
        );

        Log::info('RM signature generated', [
            'signature_len'   => strlen($signatureBody),
            'signature_head8' => substr($signatureBody, 0, 8),
            'signature_tail8' => substr($signatureBody, -8),
        ]);

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
            'X-Nonce-Str'   => $nonceStr,
            'X-Timestamp'   => $timestamp,
            'X-Sign-Type'   => $signType,
            'X-Signature'   => strtolower($signType) . ' ' . $signatureBody,
        ];

        Log::info('RM request headers snapshot', [
            'has_auth'  => !empty($accessToken),
            'endpoint'  => $endpoint,
            'nonce'     => $nonceStr,
            'timestamp' => $timestamp,
        ]);

        $res  = Http::withHeaders($headers)->post($endpoint, $payload);
        $data = $res->json();

        Log::info('RM response snapshot', [
            'http'     => $res->status(),
            'ok'       => $res->ok(),
            'json'     => $data,
            'body'     => $res->body(),
            'order_no' => $order->order_no,
        ]);

        if (!$res->ok() || data_get($data, 'code') !== 'SUCCESS') {
            Log::error('RM create checkout failed', [
                'http'     => $res->status(),
                'json'     => $data,
                'order_no' => $order->order_no,
                'store_id' => $storeId,
            ]);

            return back()->with('error', data_get($data, 'error.message') ?? 'Unable to start payment.');
        }

        $redirectUrl = data_get($data, 'item.url');
        if (!$redirectUrl) {
            Log::error('RM missing item.url', ['json' => $data]);
            return back()->with('error', 'Unable to start payment.');
        }

        return redirect()->away($redirectUrl);
    }

    public function handleReturn(Request $request)
    {
        // ✅ Return page: show message only, status update relies on webhook
        return redirect()
            ->route('account.orders.index')
            ->with('success', 'We received your payment return. Your order will update once confirmed.');
    }

    public function handleWebhook(Request $request)
    {
        Log::info('RM webhook headers', $request->headers->all());

        $rawBody = $request->getContent();
        $headers = $request->headers->all();
        $payload = $request->all();

        // ✅ 1) Verify signature
        if (!$this->verifySignatureCallback($rawBody, $headers)) {
            Log::warning('RM webhook signature invalid', ['payload' => $payload]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        // ✅ 2) Find order (prefer additionalData = order_no)
        $order = null;

        $orderNo = data_get($payload, 'data.order.additionalData');
        if ($orderNo) {
            $order = Order::where('order_no', $orderNo)->first();
        }

        if (!$order) {
            $rmOrderId = data_get($payload, 'data.order.id');
            if ($rmOrderId) {
                $numericId = (int) ltrim((string) $rmOrderId, '0');
                if ($numericId > 0) {
                    $order = Order::find($numericId);
                }
            }
        }

        if (!$order) {
            Log::warning('RM webhook order not found', [
                'rmOrderId' => data_get($payload, 'data.order.id'),
                'orderNo'   => $orderNo,
                'payload'   => $payload,
            ]);
            return response()->json(['ok' => true]);
        }

        // ✅ 3) Idempotent
        if (strtolower((string) $order->status) === 'paid') {
            return response()->json(['ok' => true]);
        }

        // ✅ 4) Status & amount validation
        $status      = strtoupper((string) (data_get($payload, 'data.status') ?? data_get($payload, 'status')));
        $finalAmount = (int) (data_get($payload, 'data.finalAmount') ?? 0); // cents
        $expected    = (int) round(((float) ($order->total ?? 0)) * 100);

        if ($finalAmount && $finalAmount !== $expected) {
            Log::warning('RM finalAmount mismatch', [
                'order_no' => $order->order_no,
                'expected' => $expected,
                'got'      => $finalAmount,
                'status'   => $status,
            ]);
            return response()->json(['ok' => true]);
        }

        $success = ['SUCCESS', 'PAID', 'COMPLETED'];
        $failed  = ['FAILED', 'CANCELLED', 'EXPIRED'];

        if (in_array($status, $success, true)) {
            $order->update([
                'status' => 'paid',
                // 'paid_at' => now(), // enable if you have this field
            ]);

            $this->sendOrderEmailsSafely($order);
            return response()->json(['ok' => true]);
        }

        if (in_array($status, $failed, true)) {
            if (strtolower((string) $order->status) === 'pending') {
                $order->update(['status' => 'failed']);
            }
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * OAuth: client_credentials -> accessToken (cached)
     */
    private function rmAccessToken(): string
    {
        return cache()->remember('rm_access_token', now()->addDays(25), function () {
            $clientId     = (string) config('services.rm.client_id');
            $clientSecret = (string) config('services.rm.client_secret');
            $oauthBase    = (string) config('services.rm.oauth_base');

            if (!$clientId || !$clientSecret) {
                throw new \RuntimeException('RM client_id / client_secret missing.');
            }

            $basic    = base64_encode($clientId . ':' . $clientSecret);
            $tokenUrl = rtrim($oauthBase, '/') . '/v1/token';

            $res = Http::asJson()
                ->withHeaders([
                    'Authorization' => 'Basic ' . $basic,
                ])
                ->post($tokenUrl, [
                    'grantType' => 'client_credentials',
                ]);

            $json = $res->json();

            if (!$res->ok() || empty($json['accessToken'])) {
                Log::error('RM OAuth token failed', [
                    'http' => $res->status(),
                    'json' => $json,
                    'body' => $res->body(),
                ]);
                throw new \RuntimeException('RM OAuth token failed.');
            }

            Log::info('RM OAuth token obtained', [
                'expiresIn' => $json['expiresIn'] ?? null,
            ]);

            return (string) $json['accessToken'];
        });
    }

    /**
     * Sign request (RSA SHA256) following RM convention
     */
    private function signRequest(
        array $payload,
        string $method,
        string $nonceStr,
        string $timestamp,
        string $signType,
        string $requestUrl // ✅ full URL
    ): string {
        $sorted = $this->ksortRecursive($payload);

        $compact = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($compact === false) {
            throw new \RuntimeException('RM json_encode failed.');
        }

        // RM doc: replace special chars
        $compact = str_replace(
            ['<', '>', '&'],
            ['\u003c', '\u003e', '\u0026'],
            $compact
        );

        $dataB64 = base64_encode($compact);

        // IMPORTANT: param order
        $plain = 'data=' . $dataB64
            . '&method=' . strtolower($method)
            . '&nonceStr=' . $nonceStr
            . '&requestUrl=' . $requestUrl
            . '&signType=' . strtolower($signType)
            . '&timestamp=' . $timestamp;

        $privKey = $this->loadPrivateKeyForRm();

        $sig = '';
        $ok = openssl_sign($plain, $sig, $privKey, OPENSSL_ALGO_SHA256);

        if (!$ok || $sig === '') {
            throw new \RuntimeException('RM openssl_sign failed.');
        }

        return base64_encode($sig);
    }

    /**
     * Load private key safely from env/config
     */
    private function loadPrivateKeyForRm(): \OpenSSLAsymmetricKey
    {
        $k = (string) config('services.rm.private_key');
        if ($k === '') {
            throw new \RuntimeException('RM private key missing.');
        }

        $k = str_replace(["\r\n", "\r"], "\n", $k);
        $k = str_replace("\\n", "\n", $k);
        $k = trim($k, " \t\n\r\0\x0B\"'");

        $res = openssl_pkey_get_private($k);
        if ($res !== false) {
            return $res;
        }

        while ($m = openssl_error_string()) {
            Log::error('OpenSSL: ' . $m);
        }

        throw new \RuntimeException('RM private key invalid.');
    }

    private function loadPublicKeyForRm(): \OpenSSLAsymmetricKey
    {
        $k = (string) config('services.rm.public_key');
        if ($k === '') {
            throw new \RuntimeException('RM public key missing.');
        }

        // 统一换行 / 处理 env 里的 \n
        $k = str_replace(["\r\n", "\r"], "\n", $k);
        $k = str_replace("\\n", "\n", $k);
        $k = trim($k, " \t\n\r\0\x0B\"'");

        // ✅ 必须转成 OpenSSL key resource
        $res = openssl_pkey_get_public($k);
        if ($res !== false) return $res;

        while ($m = openssl_error_string()) {
            Log::error('OpenSSL(pub): ' . $m);
        }

        throw new \RuntimeException('RM public key invalid.');
    }


    private function sendOrderEmailsSafely(Order $order): void
    {
        // Customer
        if (!empty($order->customer_email)) {
            try {
                Mail::to($order->customer_email)->send(new OrderPlacedMail($order));
            } catch (\Throwable $e) {
                Log::error('RM: customer email failed', [
                    'order' => $order->order_no,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Admin
        $admin = (string) config('mail.admin_address');
        if (!empty($admin)) {
            try {
                Mail::to($admin)->send(new AdminOrderNotificationMail($order));
            } catch (\Throwable $e) {
                Log::error('RM: admin email failed', [
                    'order' => $order->order_no,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Verify callback(webhook)
     * Note: RM webhook X-Signature might contain "sha256 <base64>" or just "<base64>"
     */
    private function verifySignatureCallback(string $rawBody, array $headers): bool
    {
        $nonceStr  = $this->headerValue($headers, 'x-nonce-str');
        $timestamp = $this->headerValue($headers, 'x-timestamp');
        $sigHeader = $this->headerValue($headers, 'x-signature');
        $signType  = strtolower($this->headerValue($headers, 'x-sign-type') ?? 'sha256');

        if (!$nonceStr || !$timestamp || !$sigHeader) {
            return false;
        }

        // x-signature: "sha256 <base64>" OR "<base64>"
        $sigHeader = trim($sigHeader);
        if (str_contains($sigHeader, ' ')) {
            [, $signatureBody] = explode(' ', $sigHeader, 2);
            $signatureBody = trim($signatureBody);
        } else {
            $signatureBody = $sigHeader;
        }

        $sigBin = base64_decode($signatureBody, true);
        if ($sigBin === false) {
            return false;
        }

        // ✅ 文档：data = base64(排序后的整个body compact json)
        $decoded = json_decode($rawBody, true);
        if (!is_array($decoded)) {
            return false;
        }

        $sorted  = $this->ksortRecursive($decoded);
        $compact = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($compact === false) {
            return false;
        }

        // RM doc: replace special chars
        $compact = str_replace(['<', '>', '&'], ['\u003c', '\u003e', '\u0026'], $compact);
        $dataB64 = base64_encode($compact);

        // ✅ callback 验签：requestUrl 可以 skip（按你贴的 doc）
        // ✅ 参数顺序尽量跟 doc 一样
        $plain = 'data=' . $dataB64
            . '&method=post'
            . '&nonceStr=' . $nonceStr
            . '&signType=' . $signType
            . '&timestamp=' . $timestamp;

        try {
            $pubKey = $this->loadPublicKeyForRm(); // ✅ 转成 OpenSSL key
        } catch (\Throwable $e) {
            Log::error('RM public key load failed', ['err' => $e->getMessage()]);
            return false;
        }

        return openssl_verify($plain, $sigBin, $pubKey, OPENSSL_ALGO_SHA256) === 1;
    }


    private function headerValue(array $headers, string $key): ?string
    {
        $keyLower = strtolower($key);

        foreach ($headers as $k => $vals) {
            if (strtolower($k) === $keyLower) {
                return is_array($vals) ? (string) ($vals[0] ?? null) : (string) $vals;
            }
        }

        return null;
    }

    private function ksortRecursive(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->ksortRecursive($v);
            }
        }

        ksort($data);

        return $data;
    }
}
