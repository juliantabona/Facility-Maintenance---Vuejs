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
            $table->float('price')->nullable();
            $table->boolean('buy')->default(0);
            $table->boolean('sell')->default(0);
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
