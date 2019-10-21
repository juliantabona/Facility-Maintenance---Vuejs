<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriorityAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('priority_allocations', function (Blueprint $table) {

            $table->increments('id');

            /*  Priority Details  */
            $table->unsignedInteger('priority_id');

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
        Schema::dropIfExists('priority_allocations');
    }
}
