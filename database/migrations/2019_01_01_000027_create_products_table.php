<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            /*  Product Details  */
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->float('cost_per_item')->nullable()->default(0);
            $table->float('unit_regular_price')->nullable()->default(0);
            $table->float('unit_sale_price')->nullable()->default(0);
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->boolean('allow_stock_management')->default(0);
            $table->boolean('auto_manage_stock')->default(1);
            $table->json('variant_attributes')->nullable();
            $table->unsignedInteger('parent_product_id')->nullable();
            $table->boolean('allow_variants')->default(0);
            $table->boolean('allow_downloads')->default(0);
            $table->boolean('show_on_store')->default(1);
            $table->boolean('is_new')->default(1);
            $table->boolean('is_featured')->default(1);

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();

            /*  Timestamps  */
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
