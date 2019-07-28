<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingAndShippingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('billing_and_shipping_addresses', function (Blueprint $table) {
            $table->increments('id');

            /*  Basic Info  */
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            /*  Account Info  */
            $table->string('email')->nullable();

            /*  Address Info  */
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_or_zipcode')->nullable();

            $table->string('type')->nullable();
            
            $table->unsignedInteger('trackable_id');
            $table->string('trackable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('billing_and_shipping_addresses');
    }
}
