<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('days_after')->nullable();
            $table->string('type')->nullable();
            $table->boolean('can_sms')->default(0);
            $table->boolean('can_email')->default(0);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
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
        Schema::dropIfExists('reminders');
    }
}
