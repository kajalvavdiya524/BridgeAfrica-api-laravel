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
        Schema::create('networks', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            //$table->integer('id')->nullable(false)->unique();
            $table->integer('user_id');
            $table->integer('business_id');
            $table->string('name', 255)->default(null);
            $table->text('description');
            $table->string('purpose', 255)->default(null);
            $table->string('special_needs', 255)->default(null);
            $table->boolean('allow_business')->default(0);
            $table->boolean('is_approve')->default(0);
            // $table->time('created_at')->nullable()->default(\DB::raw('CURRENT_TIMESTAMP'));
            // $table->time('updated_at')->nullable()->default(\DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
            $table->primary('id');
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
        Schema::dropIfExists('network');
    }
}
