<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhonesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->increments('id');

            /*  Address Details  */
            $table->string('type')->nullable();
            $table->json('calling_code')->nullable();
            $table->unsignedInteger('number');
            $table->string('provider')->nullable();
            $table->boolean('default')->nullable()->default(false);

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id');
            $table->string('owner_type');

            /*  Timestamps  */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('phones');
    }
}
