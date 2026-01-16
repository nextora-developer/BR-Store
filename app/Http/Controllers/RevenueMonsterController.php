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

        $amountCents = (int) round(((float) $order->grand_total) * 100);
        $rmOrderId   = Str::padLeft((string) $order->id, 24, '0');

        // ✅ 读取配置（先取出来方便 log）
        $storeId   = (string) config('services.rm.store_id');
        $apiBase   = (string) config('services.rm.api_base');
        $returnUrl = (string) config('services.rm.return_url');
        $webhookUrl = (string) config('services.rm.webhook_url');
        $apiKey    = (string) config('services.rm.api_key');

        // ✅ 安全诊断：不泄露 key，只打印长度+末尾
        Log::info('RM config snapshot', [
            'store_id'         => $storeId,
            'api_base'         => $apiBase,
            'return_url'       => $returnUrl,
            'webhook_url'      => $webhookUrl,
            'api_key_length'   => strlen($apiKey),
            'api_key_tail4'    => $apiKey ? substr($apiKey, -4) : null,
            'order_no'         => $order->order_no,
            'rm_order_id_24'   => $rmOrderId,
            'amount_cents'     => $amountCents,
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

        $endpoint = rtrim($apiBase, '/') . '/v3/payment/online';

        $nonceStr  = Str::random(32);
        $timestamp = (string) time();
        $signType  = 'sha256';

        // ✅ 签名前 log（不打印 payload 全量也可以）
        Log::info('RM signing request', [
            'endpoint'   => $endpoint,
            'nonce_len'  => strlen($nonceStr),
            'timestamp'  => $timestamp,
            'sign_type'  => $signType,
            'payload_md5' => md5(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ]);

        $signature = $this->signRequest(
            payload: $payload,
            method: 'post',
            nonceStr: $nonceStr,
            timestamp: $timestamp,
            signType: $signType,
            requestUrl: $endpoint
        );

        // ✅ 签名结果 log（只看长度+前/后几位）
        Log::info('RM signature generated', [
            'signature_len'  => strlen($signature),
            'signature_head8' => substr($signature, 0, 8),
            'signature_tail8' => substr($signature, -8),
        ]);

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
            'X-Nonce-Str'   => $nonceStr,
            'X-Timestamp'   => $timestamp,
            'X-Sign-Type'   => $signType,
            'X-Signature'   => $signature,
        ];

        // ✅ 请求前 log（不要打印 Authorization）
        Log::info('RM request headers snapshot', [
            'has_auth'    => !empty($apiKey),
            'nonce'       => $nonceStr,
            'timestamp'   => $timestamp,
            'sign_type'   => $signType,
            'endpoint'    => $endpoint,
        ]);

        $res = Http::withHeaders($headers)->post($endpoint, $payload);

        $data = $res->json();

        // ✅ 无论成功失败都打一次
        Log::info('RM response snapshot', [
            'http'     => $res->status(),
            'ok'       => $res->ok(),
            'json'     => $data,
            'body'     => $res->body(), // 如果太长你可以删掉这行
            'order_no' => $order->order_no,
        ]);

        if (!$res->ok() || data_get($data, 'code') !== 'SUCCESS') {
            Log::error('RM create checkout failed', [
                'http'      => $res->status(),
                'json'      => $data,
                'order_no'  => $order->order_no,
                'endpoint'  => $endpoint,
                // ✅ 关键字段再重复一次
                'store_id'  => $storeId,
                'api_key_len' => strlen($apiKey),
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
        $expected = (int) round(((float) $order->grand_total) * 100);

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
        string $requestUrl
    ): string {
        $sorted  = $this->ksortRecursive($payload);
        $compact = json_encode($sorted, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $dataB64 = base64_encode($compact);

        $plain = 'data=' . $dataB64
            . '&method=' . strtolower($method)
            . '&nonceStr=' . $nonceStr
            . '&signType=' . strtolower($signType)
            . '&timestamp=' . $timestamp
            . '&requestUrl=' . $requestUrl;

        $privateKey = config('services.rm.private_key');

        $privateKey = str_replace(["\r\n", "\r"], "\n", $privateKey);
        $privateKey = str_replace("\\n", "\n", $privateKey);
        $privateKey = trim($privateKey);

        $privKeyRes = openssl_pkey_get_private($privateKey);

        if ($privKeyRes === false) {
            while ($m = openssl_error_string()) {
                Log::error('OpenSSL: ' . $m);
            }
            throw new \RuntimeException('RM private key invalid.');
        }

        $signature = null;
        $ok = openssl_sign($plain, $signature, $privKeyRes, OPENSSL_ALGO_SHA256);

        if (!$ok || !$signature) {
            throw new \RuntimeException('RM openssl_sign failed.');
        }

        return base64_encode($signature);
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
