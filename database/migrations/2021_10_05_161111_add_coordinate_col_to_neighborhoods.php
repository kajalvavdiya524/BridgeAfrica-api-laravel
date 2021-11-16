<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordinateColToNeighborhoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->float('lat',11,8)->nullable();
            $table->float('lng',11,8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('neighborhoods', function (Blueprint $table) {
            //
        });
    }
}
