<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_banners', function (Blueprint $table) {
            $table->id();
            $table->string('text', 500);
            $table->string('link_url', 500)->nullable();
            $table->string('text_color', 20)->default('#ffffff');
            $table->string('bg_color', 20)->default('#1d4ed8');
            $table->unsignedTinyInteger('font_size')->default(14);      // px
            $table->string('font_weight', 10)->default('400');           // 400 | 600 | 700 | 800
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->unsignedSmallInteger('reshow_hours')->default(24);  // 0 = 닫으면 영구 숨김
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_banners');
    }
};
