<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('location')->after('available_tickets')->nullable();
            $table->string('time')->after('date')->nullable(); // or use ->time() if you're storing only time
            $table->json('image_urls')->after('location')->nullable(); // JSON for multiple image URLs
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['location', 'time', 'image_urls']);
        });
    }
};
