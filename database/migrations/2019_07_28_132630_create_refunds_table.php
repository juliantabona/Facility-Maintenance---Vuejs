<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->float('amount');
            $table->string('reason');
            $table->unsignedInteger('refundable_id');
            $table->string('refundable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('refunds');
    }
}
