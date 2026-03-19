<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // role enum 확장: user→subscriber, 신규 author/editor 추가
        // 1) 임시로 모든 값 허용
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','subscriber','author','editor','admin') NOT NULL DEFAULT 'subscriber'");
        // 2) 기존 'user' 값을 'subscriber'로 전환
        DB::statement("UPDATE users SET role = 'subscriber' WHERE role = 'user'");
        // 3) 최종 enum으로 교체 (user 제거)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('subscriber','author','editor','admin') NOT NULL DEFAULT 'subscriber'");

        // 기사 댓글 테이블
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('article_comments')->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_comments');
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'subscriber'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user'");
    }
};
