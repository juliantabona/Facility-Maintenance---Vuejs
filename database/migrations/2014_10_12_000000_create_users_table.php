<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_ext')->nullable();
            $table->string('phone_num')->nullable();
            $table->string('email')->nullable();
            $table->string('additional_email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('verified')->default(false);
            $table->json('settings')->nullable();
            $table->json('tutorial_status')->nullable();
            $table->integer('company_branch_id')->unsigned()->nullable();
            $table->string('position')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('accessibility')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
