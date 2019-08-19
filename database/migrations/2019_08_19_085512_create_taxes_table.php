<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->float('rate')->nullable();
            $table->unsignedInteger('company_branch_id');
            $table->unsignedInteger('company_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
}
