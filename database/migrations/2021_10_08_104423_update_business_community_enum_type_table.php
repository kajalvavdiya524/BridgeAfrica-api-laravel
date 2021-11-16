<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessCommunityEnumTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_community', function(Blueprint $table){
            $table->enum('type', ['follower', 'following']);
            $table->enum('follower_type', ['user', 'business', 'network']);
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('type');
        $table->dropColumn('follower_type');
    }
}
