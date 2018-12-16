<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostCentersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('costcenter_id')->unsigned();
            $table->string('costcenter_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('cost_centers');
    }
}
