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
    Schema::table('messages', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_message_id')->nullable()->after('content');
        $table->foreign('parent_message_id')->references('id')->on('messages')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
