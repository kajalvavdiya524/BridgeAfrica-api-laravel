<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNetworkColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('is_approve');
            $table->renameColumn('business_image', 'image');
            $table->renameColumn('business_address', 'address');
            $table->string('city');
            $table->integer('country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('network_category_id');
            $table->dropColumn('image');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('country_id');
        });
    }
}
