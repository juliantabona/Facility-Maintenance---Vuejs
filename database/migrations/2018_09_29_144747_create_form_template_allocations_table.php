<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTemplateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('form_template_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->json('template');
            $table->unsignedTinyInteger('step')->default(1);
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
        Schema::dropIfExists('form_template_allocations');
    }
}
