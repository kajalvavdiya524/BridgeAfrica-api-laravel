<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            $table->integer('id')->autoIncrement()->change();
            $table->string('gender', 10)->nullable()->change();
            $table->string('phone', 20)->nullable()->change();
            $table->string('language', 45)->nullable()->change();
            $table->string('password', 255)->nullable()->change();
            $table->string('verification_token', 255)->nullable()->change();
            $table->string('profile_picture', 255)->nullable()->change();

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
            
        });
    }
}
