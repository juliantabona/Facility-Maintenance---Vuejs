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

            /*  Cost Center Details  */
            $table->string('name');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('cost_centers');
    }
}
