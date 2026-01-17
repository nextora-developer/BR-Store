<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('rm_status')->nullable()->index();
            $table->string('rm_transaction_id')->nullable()->unique();
            $table->string('rm_reference_id')->nullable()->index();
            $table->unsignedBigInteger('rm_final_amount')->nullable(); // cents
            $table->string('rm_currency', 10)->nullable();
            $table->timestamp('rm_transaction_at')->nullable();

            // optional (建议先留着)
            $table->json('rm_raw_payload')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'rm_status',
                'rm_transaction_id',
                'rm_reference_id',
                'rm_final_amount',
                'rm_currency',
                'rm_transaction_at',
                'rm_raw_payload',
            ]);
        });
    }
};
