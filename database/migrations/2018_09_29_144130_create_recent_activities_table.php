<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecentActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('recent_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->text('activity')->nullable();
            $table->integer('trackable_id')->unsigned();
            $table->string('trackable_type');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('company_branch_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('recent_activities');
    }
}
