<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_media', function (Blueprint $table) {
            $table->id();
            $table->integer('network_id');
            $table->integer('reference_type');
            $table->integer('reference_id');
            $table->string('media_url');
            $table->string('media_type');
            $table->enum('is_shared',['0', '1'])->default('0');
            $table->integer('network_album_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_media');
    }
}
