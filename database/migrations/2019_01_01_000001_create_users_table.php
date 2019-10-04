<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            
            $table->increments('id');
            
            /*  Basic Info  */
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->timestampTz('date_of_birth')->nullable();
            $table->string('bio')->nullable();

            /*  Account Info  */
            $table->string('email')->nullable();
            $table->string('additional_email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('verified')->default(0);
            $table->boolean('setup')->default(0);
            $table->string('account_type')->default('basic');   //  SuperAdmin

            /*  Social Info  */
            $table->string('facebook_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('youtube_link')->nullable();

            /*  Company Info  */
            $table->unsignedInteger('company_id')->nullable();
            
            $table->rememberToken();
            $table->softDeletes();
            
            /*  Timestamps  */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
