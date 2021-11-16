<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMessagesAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function(Blueprint $table){
            $table->renameColumn('room_id', 'receiver_id');
            $table->string('attachment')->nullable();
            $table->integer('sender_id')->nullable()->change(); 
            $table->renameColumn('business_Id', 'sender_business_id');
            $table->integer('receiver_business_id')->nullable();
            $table->integer('sender_network_id')->nullable();
            $table->integer('receiver_network_id')->nullable();
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function(Blueprint $table){
            $table->dropColumn('receiver_id');
            $table->dropColumn('attachment');
            $table->dropColumn('sender_business_id');
            $table->dropColumn('receiver_business_id');
            $table->dropColumn('sender_network_id');
            $table->dropColumn('receiver_network_id');
        });
    }
}
