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
            $table->json('meta_data')->nullable();

            /*  Item Info  */
            $table->json('items')->nullable();

            /*  Shop Info  */
            $table->json('shop_taxes')->nullable();
            $table->json('shop_discounts')->nullable();
            $table->json('shop_coupons')->nullable();

            /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
            $table->float('sub_total')->nullable();
            $table->float('cart_tax_total')->nullable();
            $table->float('shop_tax_total')->nullable();
            $table->float('grand_tax_total')->nullable();
            $table->float('cart_discount_total')->nullable();
            $table->float('shop_discount_total')->nullable();
            $table->float('grand_discount_total')->nullable();
            $table->float('shipping_total')->nullable();
            $table->float('grand_total')->nullable();

            /*  Customer Info  */
            $table->unsignedInteger('client_id')->nullable();
            $table->string('client_type')->nullable();
            $table->string('customer_ip_address')->nullable();
            $table->string('customer_user_agent')->nullable();
            $table->string('customer_note')->nullable();
            $table->json('billing_info')->nullable();
            $table->json('shipping_info')->nullable();

            /*  Store & Company Info  */
            $table->unsignedInteger('store_id')->nullable();
            $table->unsignedInteger('company_branch_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();

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
