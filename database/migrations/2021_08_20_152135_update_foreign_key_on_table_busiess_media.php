<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyOnTableBusiessMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_media', function (Blueprint $table) {
            $table->integer('business_album_id')->unsigned()->change();
            $table->integer('reference_id')->nullable()->change();
            $table->foreign('business_album_id')->references('id')->on('business_albums')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_media', function (Blueprint $table) {
            //
        });
    }
}
