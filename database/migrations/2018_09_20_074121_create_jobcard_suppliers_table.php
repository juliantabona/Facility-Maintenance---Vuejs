<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobcardSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jobcard_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jobcard_id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('quotation_doc_id')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->boolean('selected')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('jobcard_suppliers');
    }
}
