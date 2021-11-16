<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAndAddUserTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country','region','neighbor']);
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('region_id')->nullable();
            $table->unsignedInteger('neighborhood_id')->nullable();
            $table->unsignedInteger('division_id')->nullable();
            $table->unsignedInteger('council_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
