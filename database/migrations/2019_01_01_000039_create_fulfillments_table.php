<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('fulfillments', function (Blueprint $table) {

            $table->increments('id');

            //  Fulfillment Details
            $table->string('notes')->nullable();

            //  Item Info
            $table->json('item_lines')->nullable();

            //  Recipient name 
            $table->string('recipient_name')->nullable();

            //  Recipient name 
            $table->string('recipient_contact')->nullable();

            //  Fulfillment Notes
            $table->string('notes')->nullable();

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();

            /*  Timestamps  */
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('fulfillments');
    }
}
