<?php

// use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBridgeAfricaDbInitialMigration extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('audience_settings', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->integer('reference_id')->default(null);
            $table->string('key', 100)->default(null);
            $table->string('value', 100)->default(null);
            $table->timestamps();
        });

        Schema::create('business_albums', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('business_id')->default(null);
            $table->string('name', 255)->default(null);
            $table->timestamps();
        });

        Schema::create('business_followers', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('business_id');
            $table->integer('follower_id');
            $table->timestamps();
        });

        Schema::create('business_media', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('reference_type', 100)->default(null);
            $table->integer('reference_id')->default(null);
            $table->string('media_url', 255)->default(null);
            $table->string('media_type', 50)->default(null);
            $table->timestamps();
        });

        Schema::create('business_open_hours', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('business_id');
            $table->string('day', 255);
            $table->time('opening_time');
            $table->time('closing_time');
            $table->timestamps();
        });

        Schema::create('businesses', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('logo_path', 255)->default(null);
            $table->string('name', 255)->default(null);
            $table->string('name_alias', 255)->default(null);
            $table->string('category', 100)->default(null);
            $table->string('keywords', 255)->default(null);
            $table->string('language', 100)->default(null);
            $table->string('timezone', 45)->default(null);
            $table->text('about_business');
            $table->string('phone', 15)->default(null);
            $table->string('website', 255)->default(null);
            $table->string('email', 255)->default(null);
            $table->integer('city_id')->default(null);
            $table->string('neighbourhood', 255)->default(null);
            $table->string('location_description', 255)->default(null);
            $table->integer('latitude')->default(null);
            $table->integer('longitude')->default(null);
            $table->timestamps();
        });

        Schema::create('items', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 255);
            $table->text('description');
            $table->string('picture', 255);
            $table->decimal('price', 11, 2);
            $table->boolean('on_discount')->default(null);
            $table->decimal('discount_price', 11, 2)->default(null);
            $table->string('condition', 100)->default(null);
            $table->boolean('is_service')->default(null);
            $table->boolean('in_stock')->default(null);
            $table->boolean('status')->default(null);
            $table->integer('business_id');
            $table->timestamps();
        });

        Schema::create('network_members', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id');
            $table->integer('network_id');
             $table->timestamps();
        });

        Schema::create('networks', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id');
            $table->integer('business_id');
            $table->string('name', 255)->default(null);
            $table->text('description');
            $table->string('purpose', 255)->default(null);
            $table->string('special_needs', 255)->default(null);
            $table->timestamps();
        });

        Schema::create('netwrok_requests', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id');
            $table->integer('network_id');
            $table->boolean('status')->default(null);
            $table->timestamps();
        });

        Schema::create('notifications', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('reference_type', 255);
            $table->integer('reference_id');
            $table->string('notification_text', 500);
            $table->timestamps();
        });

        Schema::create('post_comments', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('post_id');
            $table->integer('user_id');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('post_likes', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('post_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::create('posts', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id');
            $table->text('content');
            $table->string('type', 50)->default(null);
            $table->timestamps();
        });

        Schema::create('subscriptions', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id');
            $table->integer('business_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount', 11, 2);
            $table->string('transaction_id', 45);
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create('user_addresses', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('address', 500)->default(null);
            $table->integer('city_id')->default(null);
            $table->integer('state_id')->default(null);
            $table->integer('country_id')->default(null);
            $table->string('type', 50)->default(null);
            $table->timestamps();
        });

        Schema::create('user_albums', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('name', 255)->default(null);
            $table->timestamps();
        });

        Schema::create('user_business_visits', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('business_name', 255)->default(null);



            $table->timestamps();
        });

        Schema::create('user_contacts', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('phone_number', 20)->default(null);
            $table->string('type', 50)->default(null);



            $table->timestamps();
        });

        Schema::create('user_education_details', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('school_name', 255)->default(null);
            $table->integer('start_year')->default(null);
            $table->integer('end_year')->default(null);
            $table->boolean('is_graduated')->default(null);
            $table->text('description');
            $table->json('major_subjects')->default(null);



            $table->timestamps();
        });

        Schema::create('user_followers', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('follower_id');
            $table->timestamps();
        });

        Schema::create('user_media', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('reference_type', 100)->default(null);
            $table->integer('reference_id')->default(null);
            $table->string('media_url', 255)->default(null);
            $table->string('media_type', 50)->default(null);
            $table->timestamps();
        });

        Schema::create('user_social_profiles', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('social_link', 255)->default(null);
            $table->string('type', 45)->default(null);
            $table->timestamps();
        });

        Schema::create('user_websites', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('website_url', 255)->default(null);
            $table->timestamps();
        });

        Schema::create('user_work_experiences', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(null);
            $table->string('company_name', 255)->default(null);
            $table->string('position', 255)->default(null);
            $table->string('city_town', 255)->default(null);
            $table->text('job_responsibilities')->default(null);
            $table->integer('start_year')->default(null);
            $table->integer('start_month')->default(null);
            $table->integer('start_day')->default(null);
            $table->boolean('currently_working')->default(null);
            $table->integer('end_year')->default(null);
            $table->integer('end_month')->default(null);
            $table->integer('end_day')->default(null);
            $table->timestamps();
        });

        Schema::create('users', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 255)->default(null);
            $table->string('email', 255)->default(null);
            $table->string('phone', 20)->default(null);
            $table->string('password', 255)->default(null);
            $table->date('dob')->default(null)->nullable();
            $table->string('gender', 10)->default(null);
            $table->string('language', 45)->default(null);
            $table->string('verification_token', 255)->default(null);
            $table->boolean('status')->default('0');
            $table->time('verified_at')->nullable()->default(null);
            $table->string('profile_picture', 255)->default(null);
            $table->timestamps();
        });

        Schema::create('messages', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('thread_id')->nullable(false);
            $table->integer('sender_id')->nullable(false);
            $table->integer('network_id')->nullable(false);
            $table->string('subject', 255)->default(null);
            $table->text('message')->nullable(false);
            $table->date('date_sent')->nullable(false);
            $table->timestamps();
        });

        Schema::create('message_recipients', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->nullable(false);
            $table->integer('thread_id')->nullable(false);
            $table->integer('unread_count')->nullable(false);
            $table->integer('sender_only')->nullable(false);
            $table->boolean('is_deleted')->nullable()->default(0);
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
        Schema::drop('users');
        Schema::drop('user_work_experiences');
        Schema::drop('user_websites');
        Schema::drop('user_social_profiles');
        Schema::drop('user_media');
        Schema::drop('user_followers');
        Schema::drop('user_education_details');
        Schema::drop('user_contacts');
        Schema::drop('user_business_visits');
        Schema::drop('user_albums');
        Schema::drop('user_addresses');
        Schema::drop('subscriptions');
        Schema::drop('posts');
        Schema::drop('post_likes');
        Schema::drop('post_comments');
        Schema::drop('notifications');
        Schema::drop('netwrok_requests');
        Schema::drop('networks');
        Schema::drop('network_members');
        Schema::drop('items');
        Schema::drop('businesses');
        Schema::drop('business_open_hours');
        Schema::drop('business_media');
        Schema::drop('business_followers');
        Schema::drop('business_albums');
        Schema::drop('audience_settings');
        Schema::drop('messages');
        Schema::drop('message_recipients');
    }
}
