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
        // database/migrations/xxxx_xx_xx_create_events_table.php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->dateTime('date');
    $table->decimal('price', 8, 2);
    $table->integer('available_tickets');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
