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

        // ✅ order.id 必须 24 chars：生成并存到 orders.rm_order_id
        if (empty($order->rm_order_id)) {
            $order->rm_order_id = Str::random(24);
            $order->payment_method = 'revenue_monster';
            $order->save();
        }

        $amountCents = (int) round(((float) $order->grand_total) * 100);

        // ✅ 按你贴的文档参数结构
        $payload = [
            'storeId'       => config('services.rm.store_id'),
            'redirectUrl'   => config('services.rm.return_url'),
            'notifyUrl'     => config('services.rm.webhook_url'),
            'layoutVersion' => 'v4',
            'type'          => 'WEB_PAYMENT', // 网站用 WEB_PAYMENT；手机 app 可用 MOBILE_PAYMENT
            // 'method'      => ['FPX_MY', 'TNG_MY'], // 可选：限制支付方式
            'order' => [
                'id'           => $order->rm_order_id,             // ✅ 24 chars
                'title'        => Str::limit('Order ' . $order->order_no, 32, ''),
                'currencyType' => 'MYR',
                'amount'       => $amountCents,
                'detail'       => null,
                'additionalData' => (string) $order->order_no,     // ✅ 用这个回查你的订单
            ],
            'customer' => [
                'email'       => $order->customer_email ?? $order->email ?? null,
                'countryCode' => '60',
                'phoneNumber' => $order->customer_phone ?? $order->phone ?? null,
            ],
        ];

        $endpoint = rtrim((string) config('services.rm.api_base'), '/') . '/v3/payment/online';

        // ✅ RM 要求：Data object 递归排序 -> compact -> base64 -> plain text -> RSA sign
        $nonceStr  = Str::random(32);
        $timestamp = (string) time();
        $signType  = 'sha256';

        $signature = $this->signRequest(
            payload: $payload,
            method: 'post',
            nonceStr: $nonceStr,
            timestamp: $timestamp,
            signType: $signType,
            requestUrl: $endpoint
        );

        $res = Http::withHeaders([
            'Accept'      => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . (string) config('services.rm.api_key'),

            // ✅ 常见 RM header（你 portal 示例如果不同就按实际改）
            'X-Nonce-Str' => $nonceStr,
            'X-Timestamp' => $timestamp,
            'X-Sign-Type' => $signType,
            'X-Signature' => $signature,
        ])->post($endpoint, $payload);

        $data = $res->json();

        if (!$res->ok() || data_get($data, 'code') !== 'SUCCESS') {
            Log::error('RM create checkout failed', [
                'http' => $res->status(),
                'body' => $res->body(),
                'json' => $data,
                'order_no' => $order->order_no,
            ]);
            return back()->with('error', data_get($data, 'error.message') ?? 'Unable to start payment.');
        }

        // ✅ 文档：item.url
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

    /**
     * Notify(Webhook) - 文档写：success / fail / refund 不一定都触发 notify
     * 你贴的 notify payload：eventType + data.order.id + data.status + data.finalAmount
     */
    public function handleWebhook(Request $request)
    {
        Log::info('RM webhook headers', $request->headers->all());

        $raw     = $request->getContent();
        $headers = $request->headers->all();
        $payload = $request->all();

        // ✅ 1) 验签（callback：requestUrl 可 skip）
        if (!$this->verifySignatureCallback($raw, $headers)) {
            Log::warning('RM webhook signature invalid');
            return response()->json(['message' => 'invalid signature'], 401);
        }

        // ✅ 2) 取 RM order id（24 chars）
        $rmOrderId = data_get($payload, 'data.order.id');
        if (!$rmOrderId) {
            Log::warning('RM webhook missing data.order.id', ['payload' => $payload]);
            return response()->json(['ok' => true]);
        }

        $order = Order::where('rm_order_id', $rmOrderId)->first();

        // 兜底：如果你没加 rm_order_id，也可以用 additionalData 回查
        if (!$order) {
            $orderNo = data_get($payload, 'data.order.additionalData');
            if ($orderNo) {
                $order = Order::where('order_no', $orderNo)->first();
            }
        }

        if (!$order) {
            Log::warning('RM webhook order not found', ['rmOrderId' => $rmOrderId]);
            return response()->json(['ok' => true]);
        }

        // ✅ 3) 幂等
        if (strtolower((string) $order->status) === 'paid') {
            return response()->json(['ok' => true]);
        }

        // ✅ 4) 状态与金额
        $status = strtoupper((string) data_get($payload, 'data.status'));
        $finalAmount = (int) data_get($payload, 'data.finalAmount'); // cents
        $expected = (int) round(((float) $order->grand_total) * 100);

        if ($finalAmount && $finalAmount !== $expected) {
            Log::warning('RM finalAmount mismatch', [
                'order_no' => $order->order_no,
                'expected' => $expected,
                'got' => $finalAmount,
            ]);
            return response()->json(['ok' => true]);
        }

        $success = ['SUCCESS', 'PAID', 'COMPLETED'];
        $failed  = ['FAILED', 'CANCELLED', 'EXPIRED'];

        if (in_array($status, $success, true)) {
            // paid_at 没字段就注释掉
            $order->update([
                'status'  => 'paid',
                // 'paid_at' => now(),
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
        if (!$privateKey) {
            throw new \RuntimeException('RM private key missing (RM_PRIVATE_KEY).');
        }

        $signature = null;
        $ok = openssl_sign($plain, $signature, $privateKey, OPENSSL_ALGO_SHA256);
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
