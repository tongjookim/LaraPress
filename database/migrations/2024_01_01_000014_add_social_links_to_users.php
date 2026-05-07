<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('social_facebook')->nullable()->after('author_box_enabled');
            $table->string('social_x')->nullable()->after('social_facebook');
            $table->string('social_instagram')->nullable()->after('social_x');
            $table->string('social_linkedin')->nullable()->after('social_instagram');
            $table->string('social_website')->nullable()->after('social_linkedin');
            $table->string('social_blog')->nullable()->after('social_website');
            $table->string('social_pixabay')->nullable()->after('social_blog');
            $table->string('social_wikipedia')->nullable()->after('social_pixabay');
            $table->string('social_email')->nullable()->after('social_wikipedia');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'social_facebook', 'social_x', 'social_instagram', 'social_linkedin',
                'social_website', 'social_blog', 'social_pixabay', 'social_wikipedia', 'social_email',
            ]);
        });
    }
};
