<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('description')->nullable();
            $table->timestampTz('date_of_incorporation')->nullable();
            $table->string('type')->nullable();
            $table->string('industry')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('provience')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_or_zipcode')->nullable();
            $table->string('email')->nullable();
            $table->string('additional_email')->nullable();
            $table->string('website_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->text('bio')->nullable();
            $table->json('currency_type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
