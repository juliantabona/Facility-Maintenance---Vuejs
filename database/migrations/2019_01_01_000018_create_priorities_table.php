<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('priorities', function (Blueprint $table) {

            $table->increments('id');

            /*  Priority Details  */
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color_code')->nullable();
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
        Schema::dropIfExists('priorities');
    }
}
