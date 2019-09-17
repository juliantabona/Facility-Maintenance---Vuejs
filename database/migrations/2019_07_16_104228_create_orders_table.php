<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            /*  Basic Info  */
            $table->string('number')->nullable();
            $table->string('currency_type')->nullable();
            $table->timestampTz('created_date')->nullable();

            /*  Item Info  */
            $table->json('items')->nullable();

            /*  Store Info  */
            $table->json('taxes')->nullable();
            $table->json('discounts')->nullable();
            $table->json('coupons')->nullable();

            /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
            $table->float('sub_total')->nullable();
            $table->float('item_tax_total')->nullable();
            $table->float('global_tax_total')->nullable();
            $table->float('grand_tax_total')->nullable();
            $table->float('item_discount_total')->nullable();
            $table->float('global_discount_total')->nullable();
            $table->float('grand_discount_total')->nullable();
            $table->float('shipping_total')->nullable();
            $table->float('grand_total')->nullable();

            /*  Customer Info  */
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('reference_ip_address')->nullable();
            $table->string('reference_user_agent')->nullable();
            $table->string('customer_note')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_type')->nullable();
            $table->json('billing_info')->nullable();
            $table->json('shipping_info')->nullable();
            
            /*  Company Info  */
            $table->json('company_info')->nullable();

            /*  Allocation Details  */
            $table->unsignedInteger('owner_id');
            $table->string('owner_type');

            /*  Meta Data  */
            $table->json('meta')->nullable();

            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
