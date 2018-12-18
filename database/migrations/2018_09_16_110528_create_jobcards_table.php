<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobcardsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jobcards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->json('description')->nullable();
            $table->timestampTz('start_date')->nullable();
            $table->timestampTz('end_date')->nullable();
            $table->unsignedInteger('company_branch_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->boolean('is_public')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('jobcards');
    }
}
