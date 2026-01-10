<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                // SAVE10
            $table->string('name');                          // Save RM10
            $table->enum('type', ['fixed', 'percent']);      // fixed or percent
            $table->decimal('value', 10, 2);                 // 10 / 15 (%)
            $table->decimal('min_spend', 10, 2)->nullable(); // min cart subtotal
            $table->integer('usage_limit')->nullable();      // total usage cap
            $table->integer('used_count')->default(0);       // total used
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
