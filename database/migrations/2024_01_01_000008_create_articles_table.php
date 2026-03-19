<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 300);
            $table->string('slug', 300)->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->enum('status', ['draft', 'pending', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('article_categories')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('status');
            $table->index('published_at');
            $table->index('category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
