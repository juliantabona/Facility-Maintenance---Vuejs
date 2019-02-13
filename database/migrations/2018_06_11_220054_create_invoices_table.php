<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable();
            $table->string('heading')->nullable();
            $table->string('reference_no_title')->nullable();
            $table->unsignedInteger('reference_no_value')->nullable();
            $table->string('created_date_title')->nullable();
            $table->date('created_date_value')->nullable();
            $table->string('expiry_date_title')->nullable();
            $table->date('expiry_date_value')->nullable();
            $table->string('sub_total_title')->nullable();
            $table->float('sub_total_value')->nullable();
            $table->string('grand_total_title')->nullable();
            $table->float('grand_total_value')->nullable();
            $table->json('currency_type')->nullable();
            $table->json('calculated_taxes')->nullable();
            $table->string('invoice_to_title')->nullable();
            $table->json('customized_company_details')->nullable();
            $table->json('customized_client_details')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->json('table_columns')->nullable();
            $table->json('items')->nullable();
            $table->json('notes')->nullable();
            $table->json('colors')->nullable();
            $table->string('footer')->nullable();
            $table->unsignedInteger('quotation_id')->nullable();
            $table->unsignedInteger('trackable_id')->nullable();
            $table->string('trackable_type')->nullable();
            $table->unsignedInteger('company_branch_id');
            $table->unsignedInteger('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
