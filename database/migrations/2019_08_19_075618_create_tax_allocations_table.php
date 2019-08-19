<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tax_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tax_id');
            $table->unsignedInteger('taxable_id');
            $table->string('taxable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tax_allocations');
    }
}
