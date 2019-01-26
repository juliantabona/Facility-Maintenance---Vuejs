<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->json('calling_code')->nullable();
            $table->unsignedInteger('number');
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('company_branch_id');
            $table->unsignedInteger('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('phones');
    }
}
