<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessNeighborhoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_neighborhood', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('business_id');
            $table->unsignedInteger('neighborhood_id');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods');
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
        Schema::dropIfExists('business_neighborhood');
    }
}
