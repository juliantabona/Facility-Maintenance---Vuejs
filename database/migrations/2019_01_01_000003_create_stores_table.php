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
            $table->string('abbreviation', 10)->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->string('industry')->nullable();

            /*  Account Info  */
            $table->boolean('setup')->default(0);

            /*  Social Info  */
            $table->string('website_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('youtube_link')->nullable();

            /*  Currency Info  */
            $table->json('currency')->nullable();
        
            /*  Ownership Information  */
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();
            
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
        Schema::dropIfExists('stores');
    }
}
