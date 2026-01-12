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
        Schema::create('popup_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();          // admin 备注
            $table->string('image');                      // storage path
            $table->string('link')->nullable();           // 点击跳转
            $table->boolean('is_active')->default(false); // active/inactive
            $table->unsignedInteger('cooldown_days')->default(7); // 几天后再弹
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_banners');
    }
};
