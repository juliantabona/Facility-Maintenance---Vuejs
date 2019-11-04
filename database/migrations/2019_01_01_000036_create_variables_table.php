<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {

            $table->increments('id');

            /*  Variable Details  */
            $table->text('name')->nullable();
            $table->text('value')->nullable();

            /*  Ownership Information  */
            $table->unsignedInteger('product_id')->nullable();

            /*  Timestamps  */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('variables');
    }
}
