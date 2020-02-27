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
            $table->string('about_us')->nullable();
            $table->string('contact_us')->nullable();
            $table->string('call_to_action')->nullable();
            $table->string('code')->nullable();
            $table->boolean('live_mode')->nullable()->default(false);
            $table->json('metadata')->nullable();

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();

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
