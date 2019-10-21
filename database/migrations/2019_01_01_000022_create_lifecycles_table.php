<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifecyclesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lifecycles', function (Blueprint $table) {

            $table->increments('id');

            /*  Priority Details  */
            $table->text('stages')->nullable();
            $table->boolean('default')->nullable();
            $table->string('type');

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
        Schema::dropIfExists('lifecycles');
    }
}
