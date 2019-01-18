<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsAndServicesTaxesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products_and_services_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_service_id');
            $table->unsignedInteger('tax_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('products_and_services_taxes');
    }
}
