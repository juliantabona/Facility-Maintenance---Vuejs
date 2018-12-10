<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobcardContractorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jobcard_contractors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jobcard_id')->unsigned();
            $table->integer('contractor_id')->unsigned();
            $table->integer('quotation_doc_id')->unsigned()->nullable();
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
        Schema::dropIfExists('jobcard_contractors');
    }
}
