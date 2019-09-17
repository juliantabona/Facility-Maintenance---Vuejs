<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('Messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->json('meta');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('messageable_id');
            $table->string('messageable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('Messages');
    }
}
