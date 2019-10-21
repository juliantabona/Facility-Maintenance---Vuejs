<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            
            $table->increments('id');

             /*  Tax Details  */
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('description')->nullable();
            $table->float('rate')->nullable();

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
        Schema::dropIfExists('taxes');
    }
}
