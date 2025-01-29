<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTattooGalleryTable extends Migration
{
    public function up()
    {
        Schema::create('tattoo_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->text('description')->nullable();  // Added description column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tattoo_galleries');
    }
}
