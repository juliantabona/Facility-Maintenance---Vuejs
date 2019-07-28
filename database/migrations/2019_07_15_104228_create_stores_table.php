<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            
            $table->increments('id');

            /*  Basic Info  */
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->string('industry')->nullable();
            
            /*  Address Info  */
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_or_zipcode')->nullable();

            /*  Account Info  */
            $table->string('email')->nullable();
            $table->string('additional_email')->nullable();
            $table->boolean('verified')->default(0);
            $table->boolean('setup')->default(0);

            /*  Social Info  */
            $table->string('website_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('youtube_link')->nullable();

            /*  Currency Info  */
            $table->json('currency_type')->nullable();

            /*  Payment Gateway Info  */
            $table->unsignedInteger('vcs_terminal_id')->nullable();

            /*  Company Info  */
            $table->unsignedInteger('company_branch_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
