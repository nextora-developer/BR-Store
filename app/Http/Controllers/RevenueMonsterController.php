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
     * 创建 Hosted Payment Checkout (v3/payment/online)
     * POST https://(sb-)open.revenuemonster.my/v3/payment/online
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

            $basic   = base64_encode($clientId . ':' . $clientSecret);
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

    public function pay(Order $order)
    {
        abort_unless(auth()->check(), 403);
        if (!empty($order->user_id)) {
            abort_unless((int) $order->user_id === (int) auth()->id(), 403);
        }

        if (strtolower((string) $order->status) !== 'pending') {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'This order is not payable.');
        }

        $amountCents = (int) round(((float) $order->total) * 100);
        $rmOrderId   = Str::padLeft((string) $order->id, 24, '0');

        // 🔍 金额诊断（你之前出现过 0）
        Log::info('RM amount debug', [
            'grand_total_raw'   => $order->total,
            'grand_total_float' => (float) $order->total,
            'amount_cents'      => $amountCents,
        ]);

        // 读取配置
        $storeId    = (string) config('services.rm.store_id');
        $apiBase    = (string) config('services.rm.api_base');
        $returnUrl  = (string) config('services.rm.return_url');
        $webhookUrl = (string) config('services.rm.webhook_url');

        // OAuth access token（✅ 关键）
        $accessToken = $this->rmAccessToken();

        Log::info('RM access token snapshot', [
            'token_length' => strlen($accessToken),
            'token_head8'  => substr($accessToken, 0, 8),
            'token_tail8'  => substr($accessToken, -8),
        ]);

        Log::info('RM config snapshot', [
            'store_id'       => $storeId,
            'api_base'       => $apiBase,
            'return_url'     => $returnUrl,
            'webhook_url'    => $webhookUrl,
            'order_no'       => $order->order_no,
            'rm_order_id_24' => $rmOrderId,
            'amount_cents'   => $amountCents,
        ]);

        $payload = [
            'storeId'       => $storeId,
            'redirectUrl'   => $returnUrl,
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
        $requestPath = '/v3/payment/online';

        Log::info('RM signing request', [
            'endpoint'    => $endpoint,
            'request_path' => $requestPath,
            'nonce_len'   => strlen($nonceStr),
            'timestamp'   => $timestamp,
            'sign_type'   => $signType,
            'payload_md5' => md5(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ]);

        $signature = $this->signRequest(
            payload: $payload,
            method: 'post',
            nonceStr: $nonceStr,
            timestamp: $timestamp,
            signType: $signType,
            requestUrl: $requestPath
        );

        Log::info('RM signature generated', [
            'signature_len'   => strlen($signature),
            'signature_head8' => substr($signature, 0, 8),
            'signature_tail8' => substr($signature, -8),
        ]);

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken, // ✅ 正确
            'X-Nonce-Str'   => $nonceStr,
            'X-Timestamp'   => $timestamp,
            'X-Sign-Type'   => $signType,
            'X-Signature'   => $signature,
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
        // ✅ return 只做提示，不改 paid（以 webhook 为准）
        return redirect()
            ->route('account.orders.index')
            ->with('success', 'We received your payment return. Your order will update once confirmed.');
    }


    public function handleWebhook(Request $request)
    {
        Log::info('RM webhook headers', $request->headers->all());

        $raw     = $request->getContent();
        $headers = $request->headers->all();
        $payload = $request->all();

        // ✅ 1) 验签（callback：requestUrl 可 skip）
        if (!$this->verifySignatureCallback($raw, $headers)) {
            Log::warning('RM webhook signature invalid', ['payload' => $payload]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        /**
         * ✅ 2) 找订单（你不想加 rm_order_id）
         * 优先：additionalData = 你的 order_no
         * 兜底：order.id = 24 chars padded numeric id
         */
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

        // ✅ 3) 幂等
        if (strtolower((string) $order->status) === 'paid') {
            return response()->json(['ok' => true]);
        }

        // ✅ 4) 状态与金额
        $status = strtoupper((string) (data_get($payload, 'data.status') ?? data_get($payload, 'status')));
        $finalAmount = (int) (data_get($payload, 'data.finalAmount') ?? 0); // cents
        $expected = (int) round(((float) $order->total) * 100);

        if ($finalAmount && $finalAmount !== $expected) {
            Log::warning('RM finalAmount mismatch', [
                'order_no'  => $order->order_no,
                'expected'  => $expected,
                'got'       => $finalAmount,
                'status'    => $status,
            ]);
            return response()->json(['ok' => true]);
        }

        $success = ['SUCCESS', 'PAID', 'COMPLETED'];
        $failed  = ['FAILED', 'CANCELLED', 'EXPIRED'];

        if (in_array($status, $success, true)) {
            $order->update([
                'status' => 'paid',
                // 'paid_at' => now(), // 有字段才开
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


    private function sendOrderEmailsSafely(Order $order): void
    {
        // Customer
        if (!empty($order->customer_email)) {
            try {
                Mail::to($order->customer_email)->send(new OrderPlacedMail($order));
            } catch (\Throwable $e) {
                Log::error('RM: customer email failed', ['order' => $order->order_no, 'error' => $e->getMessage()]);
            }
        }

        // Admin
        $admin = config('mail.admin_address');
        if (!empty($admin)) {
            try {
                Mail::to($admin)->send(new AdminOrderNotificationMail($order));
            } catch (\Throwable $e) {
                Log::error('RM: admin email failed', ['order' => $order->order_no, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * ✅ Sign request: sort JSON (nested) -> compact -> base64
     * plain text: data=...&method=post&nonceStr=...&signType=sha256&timestamp=...&requestUrl=...
     * sign with PRIVATE KEY (RSA SHA256), return base64 signature
     */
    private function signRequest(
        array $payload,
        string $method,
        string $nonceStr,
        string $timestamp,
        string $signType,
        string $requestUrl // ✅ 这里请传 PATH，例如 /v3/payment/online
    ): string {
        $sorted  = $this->ksortRecursive($payload);

        // ✅ JSON 必须稳定（不要 pretty、不要多余空格）
        $compact = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($compact === false) {
            throw new \RuntimeException('RM json_encode failed.');
        }

        $dataB64 = base64_encode($compact);

        // ✅ requestUrl 必须是 path（保险：如果传进来是 full url，也自动取 path）
        if (str_starts_with($requestUrl, 'http')) {
            $parsed = parse_url($requestUrl);
            $requestUrl = $parsed['path'] ?? $requestUrl;
        }

        $plain = 'data=' . $dataB64
            . '&method=' . strtolower($method)
            . '&nonceStr=' . $nonceStr
            . '&signType=' . strtolower($signType)
            . '&timestamp=' . $timestamp
            . '&requestUrl=' . $requestUrl;

        $priv = $this->loadPrivateKeyForRm(); // ✅ 用下面给你的 loader

        $signature = '';
        $ok = openssl_sign($plain, $signature, $priv, OPENSSL_ALGO_SHA256);

        if (!$ok || $signature === '') {
            throw new \RuntimeException('RM openssl_sign failed.');
        }

        return base64_encode($signature);
    }


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
        if ($res !== false) return $res;

        while ($m = openssl_error_string()) {
            Log::error('OpenSSL: ' . $m);
        }

        throw new \RuntimeException('RM private key invalid.');
    }

    /**
     * ✅ Verify callback(webhook): requestUrl can be skip
     * plain: (data=...&)?method=post&nonceStr=...&signType=sha256&timestamp=...
     */
    private function verifySignatureCallback(string $rawBody, array $headers): bool
    {
        $nonceStr  = $this->headerValue($headers, 'x-nonce-str') ?? $this->headerValue($headers, 'nonceStr');
        $timestamp = $this->headerValue($headers, 'x-timestamp') ?? $this->headerValue($headers, 'timestamp');
        $signType  = strtolower($this->headerValue($headers, 'x-sign-type') ?? $this->headerValue($headers, 'signType') ?? 'sha256');
        $signature = $this->headerValue($headers, 'x-signature')
            ?? $this->headerValue($headers, 'signature')
            ?? $this->headerValue($headers, 'sign');

        if (!$nonceStr || !$timestamp || !$signature) return false;

        $parts = [];

        $rawBody = trim($rawBody);
        if ($rawBody !== '') {
            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                $sorted = $this->ksortRecursive($decoded);
                $compact = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $compact = $rawBody;
            }
            $parts[] = 'data=' . base64_encode($compact);
        }

        $parts[] = 'method=post';
        $parts[] = 'nonceStr=' . $nonceStr;
        $parts[] = 'signType=' . $signType;
        $parts[] = 'timestamp=' . $timestamp;

        $plain = implode('&', $parts);

        $pubKey = config('services.rm.public_key');
        if (!$pubKey) return false;

        $sigBin = base64_decode($signature, true);
        if ($sigBin === false) return false;

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
            if (is_array($v)) $data[$k] = $this->ksortRecursive($v);
        }
        ksort($data);
        return $data;
    }
}
