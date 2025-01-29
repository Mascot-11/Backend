<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTattooGalleryTable extends Migration
{
    public function up()
    {
        Schema::table('tattoo_galleries', function (Blueprint $table) {
            // Add new fields if necessary (example)
            $table->string('image_url')->nullable()->change();  // Example: ensure 'image_url' is nullable if needed
            $table->text('description')->nullable()->change(); // Ensure description is nullable if needed
        });
    }

    public function down()
    {
        Schema::table('tattoo_galleries', function (Blueprint $table) {
            // Revert changes made to the table
            $table->string('image_url')->nullable(false)->change();  // Example revert
            $table->text('description')->nullable(false)->change(); // Example revert
        });
    }
}
