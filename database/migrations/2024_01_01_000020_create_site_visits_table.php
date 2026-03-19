<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);
            $table->date('date');
            $table->string('device_type', 10)->default('desktop'); // desktop, mobile, tablet
            $table->string('referrer', 500)->nullable();
            $table->string('referrer_domain', 200)->nullable();
            $table->string('path', 500)->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index('device_type');
            $table->index(['ip', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
