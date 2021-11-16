<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessCouncilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_council', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('business_id');
            $table->unsignedInteger('council_id');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('council_id')->references('id')->on('councils');
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
        Schema::dropIfExists('business_council');
    }
}
