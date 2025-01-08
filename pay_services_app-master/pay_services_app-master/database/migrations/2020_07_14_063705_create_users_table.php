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
      $table->bigIncrements('id');
      $table->string('name')->nullable();
      $table->string('mobile', 30);
      $table->string('type', 30);
      $table->string('email')->nullable();
      $table->string('password')->nullable();
      $table->string('user_otp', 30)->nullable();
      $table->string('api_token', 100)->nullable();
      $table->float('wallet_balance')->default(0);
      $table->boolean('is_active')->default(true);
      $table->float('membership_fee')->default(0);
      $table->boolean('is_verified')->default(0);
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
