<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {

            $table->increments('id');

            /*  Category Details  */
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('parent_category_id')->nullable();
            $table->string('type');

            /*  Ownership Information  */
            $table->unsignedInteger('owner_id');
            $table->string('owner_type');

            /*  Timestamps  */
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
