<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetAlbumMediaColumnNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('album_media', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->change();
            $table->unsignedInteger('reference_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('album_media', function (Blueprint $table) {
            $table->dropColumn('reference_type');
            $table->dropColumn('reference_id');
        });
    }
}
