<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {

            $table->increments('id');

            /*  Review Details  */
            $table->float('rating');
            $table->string('text');
            $table->unsignedInteger('user_id');

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
        Schema::dropIfExists('reviews');
    }
}
