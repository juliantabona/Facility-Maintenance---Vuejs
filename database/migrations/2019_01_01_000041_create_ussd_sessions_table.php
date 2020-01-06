<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUssdSessionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->increments('id');

            /*  Session Details  */
            $table->string('session_id')->nullable();
            $table->string('service_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('status')->nullable();
            $table->string('text')->nullable();

            /*  Meta Data  */
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
        Schema::dropIfExists('ussd_sessions');
    }
}
