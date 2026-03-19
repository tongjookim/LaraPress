<?php
// database/migrations/2024_01_01_000002_create_boards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('board_id')->unique();
            $table->string('board_name');
            $table->string('skin')->default('basic');
            $table->integer('posts_per_page')->default(20);
            $table->boolean('use_comment')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
