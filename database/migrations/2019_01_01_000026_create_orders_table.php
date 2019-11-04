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
            $table->string('currency')->nullable();
            $table->timestampTz('created_date')->nullable();
            $table->string('manual_status')->nullable();

            /*  Item Info  */
            $table->json('item_lines')->nullable();

            /*  Taxes, Disounts & Coupon Info  */
            $table->json('tax_lines')->nullable();
            $table->json('discount_lines')->nullable();
            $table->json('coupon_lines')->nullable();

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

            /*  Reference Info  */
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('reference_ip_address')->nullable();
            $table->string('reference_user_agent')->nullable();

            /*  Customer Info  */
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_note')->nullable();
            $table->json('billing_info')->nullable();
            $table->json('shipping_info')->nullable();
            
            /*  Merchant Info  */
            $table->unsignedInteger('merchant_id');
            $table->string('merchant_type');
            $table->json('merchant_info')->nullable();

            /*  Meta Data  */
            $table->json('metadata')->nullable();

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
