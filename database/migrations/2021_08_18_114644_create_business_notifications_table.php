<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id');
            $table->integer('is_read')->nullable(false)->default(0);
            $table->integer('mark_as_read');
            $table->string('reference_type', 255);
            $table->integer('reference_id');
            $table->string('notification_text', 500);
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
        Schema::dropIfExists('business_notifications');
    }
}
