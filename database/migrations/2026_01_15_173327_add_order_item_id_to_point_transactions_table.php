<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // ✅ 先加字段（nullable 方便兼容旧数据）
            if (!Schema::hasColumn('point_transactions', 'order_item_id')) {
                $table->unsignedBigInteger('order_item_id')->nullable()->after('order_id');
                $table->index('order_item_id');
            }
        });

        // ✅ 删除旧 unique（名字就是你报错那个）
        Schema::table('point_transactions', function (Blueprint $table) {
            // 旧 key: ptx_unique_source_order_user
            $table->dropUnique('ptx_unique_source_order_user');
        });

        // ✅ 新 unique：同 user 同 order_item 同 source 只能一次
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->unique(['source', 'order_item_id', 'user_id'], 'ptx_unique_source_item_user');
        });
    }

    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropUnique('ptx_unique_source_item_user');

            // 还原旧 unique
            $table->unique(['source', 'order_id', 'user_id'], 'ptx_unique_source_order_user');

            $table->dropIndex(['order_item_id']);
            $table->dropColumn('order_item_id');
        });
    }
};
