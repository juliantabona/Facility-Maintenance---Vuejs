<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileStoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mobile_stores', function (Blueprint $table) {

            $table->increments('id');

            /*  Basic Info  */
            $table->string('about_us', 500)->nullable();
            $table->string('contact_us', 500)->nullable();
            $table->string('call_to_action')->nullable();
            $table->boolean('allow_delivery')->nullable()->default(false);
            $table->string('delivery_policy', 500)->nullable();
            $table->boolean('live_mode')->nullable()->default(false);
            $table->string('offline_message')->nullable();

            /*  Metadata  */
            $table->json('metadata')->nullable();

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();

            /*  Timestamps  */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mobile_stores');
    }
}
