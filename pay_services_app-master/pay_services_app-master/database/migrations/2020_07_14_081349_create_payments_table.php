<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->bigInteger('id');
      $table->bigInteger('order_id');
      $table->string('amount');
      $table->string('payment_method');
      $table->string('payment_meta');
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
    Schema::dropIfExists('payments');
  }
}
