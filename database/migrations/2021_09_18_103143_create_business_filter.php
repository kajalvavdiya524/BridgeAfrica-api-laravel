<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessFilter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_filter', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('business_id');
            $table->unsignedInteger('filter_id');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('filter_id')->references('id')->on('filters');
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
        Schema::dropIfExists('business_filters');
    }
}
