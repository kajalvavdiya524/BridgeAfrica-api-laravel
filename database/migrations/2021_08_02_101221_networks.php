<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Networks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('networks', function(Blueprint $table) {
            
            $table->string('business_address');
            $table->string('business_image');
            $table->boolean('allow_business')->default(0);
            $table->boolean('is_approve')->default(0);
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('networks', function(Blueprint $table){
            $table->dropColumn('business_address');
            $table->dropColumn('business_image');
            $table->dropColumn('allow_business');
            $table->dropColumn('is_approve');
        });   
    }
}