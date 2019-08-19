<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comment_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_id');
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comment_allocations');
    }
}
