<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_allocations', function (Blueprint $table) {
            
            $table->increments('id');

            /*  User Details  */
            $table->unsignedInteger('user_id');
            $table->string('type');

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
        Schema::dropIfExists('user_allocations');
    }
}
