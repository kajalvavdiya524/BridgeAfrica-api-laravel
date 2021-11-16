<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_media', function (Blueprint $table) {
            $table->string('reference_type', 100)->nullable()->change();
            $table->string('media_url', 255)->nullable()->change();
            $table->string('media_type', 50)->nullable()->change();
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
