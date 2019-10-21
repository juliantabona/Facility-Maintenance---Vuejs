<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUssdInterfacesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ussd_interfaces', function (Blueprint $table) {
            
            $table->increments('id');

            /*  Basic Info  */
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('code')->nullable();
            $table->boolean('live_mode')->default(0);

            /*  Ownership Information  */
            $table->unsignedInteger('store_id');

            /*  Timestamps  */
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ussd_interfaces');
    }
}
