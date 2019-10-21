<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {

            $table->increments('id');

            /*  Document Details  */
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->string('url')->nullable();

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
        Schema::dropIfExists('documents');
    }
}
