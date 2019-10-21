<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('Contacts', function (Blueprint $table) {

            $table->increments('id');

            /*  Contact Details  */
            $table->string('name')->nullable();
            $table->boolean('is_vendor')->nullable()->default(false);
            $table->boolean('is_customer')->nullable()->default(true);
            $table->boolean('is_individual')->nullable()->default(true);

            /*  Timestamps  */
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('Contacts');
    }
}
