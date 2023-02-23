<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status');
            $table->string('avatar')->nullable();
            $table->longText('webToken')->nullable();
            $table->timestamp('activatedAt')->nullable();
            $table->boolean('isLogin')->nullable();
            $table->timestamp('lastLoginAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
