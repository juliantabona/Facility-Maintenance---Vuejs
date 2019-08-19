<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('discount_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('discount_id');
            $table->unsignedInteger('discountable_id');
            $table->string('discountable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('discount_allocations');
    }
}
