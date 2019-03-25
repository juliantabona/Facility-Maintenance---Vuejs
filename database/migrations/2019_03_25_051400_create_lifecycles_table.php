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
            $table->text('stages')->nullable();
            $table->boolean('selected')->nullable();
            $table->boolean('template')->nullable();
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->unsignedInteger('company_branch_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
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
