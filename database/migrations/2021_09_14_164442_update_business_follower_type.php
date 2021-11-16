<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessFollowerType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_followers', function (Blueprint $table) {
            $table->integer('business_id')->nullable()->change();
            $table->integer('follower_id')->nullable()->change();
            $table->string('type', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_followers', function (Blueprint $table) {
            $table->dropColumn('business_id');
            $table->dropColumn('follower_id');
            $table->dropColumn('type');
            $table->dropColumn('follower_type');
        });
    }
}
