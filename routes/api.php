<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HitpayController;
use App\Http\Controllers\RevenueMonsterController;


// 测试用，看 API 路由有没有开
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// HitPay Webhook
Route::post('/hitpay/webhook', [HitpayController::class, 'handleWebhook'])
    ->name('hitpay.webhook');

// Revenue Monster Webhook
Route::post('/payment/rm/webhook', [RevenueMonsterController::class, 'handleWebhook'])
    ->name('payment.rm.webhook');
