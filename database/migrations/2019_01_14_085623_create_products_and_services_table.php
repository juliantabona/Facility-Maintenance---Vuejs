<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsAndServicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products_and_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->float('cost_per_item')->nullable()->default(0);
            $table->float('unit_price')->nullable()->default(0);
            $table->float('unit_sale_price')->nullable()->default(0);
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->boolean('allow_inventory')->default(0);
            $table->boolean('auto_track_inventory')->default(1);
            $table->json('variants')->nullable();
            $table->json('variant_attributes')->nullable();
            $table->boolean('allow_variants')->default(0);
            $table->boolean('allow_downloads')->default(0);
            $table->boolean('show_on_store')->default(1);
            $table->unsignedInteger('company_branch_id');
            $table->unsignedInteger('company_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('products_and_services');
    }
}
