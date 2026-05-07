<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nav_menus', function (Blueprint $table) {
            $table->string('location')->default('header')->after('target'); // 'header' | 'footer'
            $table->string('group')->nullable()->after('location');         // footer group name
        });
    }

    public function down(): void
    {
        Schema::table('nav_menus', function (Blueprint $table) {
            $table->dropColumn(['location', 'group']);
        });
    }
};
