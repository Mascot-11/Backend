<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('chat_id')->constrained('chats'); // Chat the message belongs to
        $table->foreignId('sender_id')->constrained('users'); // User or admin who sent the message
        $table->text('content'); // Message content
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
