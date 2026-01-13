<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referrer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('referred_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 未来：首单完成后才绑定订单
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // 奖励状态
            $table->boolean('rewarded')->default(false);
            $table->string('reward_type')->nullable(); // points / cashback / voucher
            $table->decimal('reward_amount', 10, 2)->default(0);

            $table->timestamps();

            // 一个下级只能 under 一个上级（防刷）
            $table->unique(['referrer_id', 'referred_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_logs');
    }
};
