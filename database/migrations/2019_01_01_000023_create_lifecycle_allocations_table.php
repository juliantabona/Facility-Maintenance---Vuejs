<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifecycleAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lifecycle_allocations', function (Blueprint $table) {

            $table->increments('id');

            /*  Lifecycle Details  */
            $table->unsignedInteger('lifecycle_id');

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
        Schema::dropIfExists('lifecycle_allocations');
    }
}
