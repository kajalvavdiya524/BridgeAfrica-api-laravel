<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsOfBusinessToNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->change();
            $table->integer('user_id')->nullable()->change();
            $table->string('logo_path', 255)->nullable()->change();
            $table->string('name', 255)->nullable()->change();
            $table->string('name_alias', 255)->nullable()->change();
            $table->string('category', 100)->nullable()->change();
            $table->string('keywords', 255)->nullable()->change();
            $table->string('language', 100)->nullable()->change();
            $table->string('timezone', 45)->nullable()->change();
            $table->text('about_business')->nullable()->change();
            $table->string('phone', 15)->nullable()->change();
            $table->string('website', 255)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
            $table->integer('city_id')->nullable()->change();
            $table->string('neighbourhood', 255)->nullable()->change();
            $table->string('location_description', 255)->nullable()->change();
            $table->integer('latitude')->nullable()->change();
            $table->integer('longitude')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            //
        });
    }
}
