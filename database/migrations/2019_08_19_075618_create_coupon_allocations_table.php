<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('coupon_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('couponable_id');
            $table->string('couponable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('coupon_allocations');
    }
}
