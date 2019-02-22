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
            $table->string('type')->nullable();
            $table->text('activity')->nullable();
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->unsignedInteger('created_by')->nullable();
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
        Schema::dropIfExists('recent_activities');
    }
}
