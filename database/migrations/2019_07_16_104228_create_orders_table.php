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
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('number')->nullable();
            $table->string('order_key')->nullable();
            $table->string('status')->nullable();
            $table->string('currency')->nullable();
            $table->string('cart_hash')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestampTz('date_completed')->nullable();

            /*  Item Info  */
            $table->json('line_items')->nullable();

            /*  Shipping Info  */
            $table->json('shipping_lines')->nullable();

            /*  Grand Total, Subtotal, Shipping Total, Discount Total  */
            $table->float('cart_total')->nullable();
            $table->float('shipping_total')->nullable();
            $table->float('discount_total')->nullable();
            $table->float('grand_total')->nullable();

            /*  Tax Info  */
            $table->float('cart_tax')->nullable();
            $table->float('shipping_tax')->nullable();
            $table->float('discount_tax')->nullable();
            $table->float('grand_total_tax')->nullable();
            $table->boolean('prices_include_tax')->default(0);
            $table->json('tax_lines')->nullable();

            /*  Customer Info  */
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_ip_address')->nullable();
            $table->string('customer_user_agent')->nullable();
            $table->string('customer_note')->nullable();
            $table->json('billing')->nullable();
            $table->json('shipping')->nullable();

            /*  Payment Info  */
            $table->string('payment_method')->nullable();
            $table->string('payment_method_title')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->timestampTz('date_paid')->nullable();

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
