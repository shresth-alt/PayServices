<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up() {
    Schema::create('orders', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id');
      $table->bigInteger('vendor_id')->nullable();
      $table->bigInteger('service_id');
      $table->date('service_date');
      $table->string('status')->default('pending');
      $table->text('address');
      $table->string('landmark')->nullable();
      $table->string('city');
      $table->string('postal_code');
      $table->float('order_amount')->default(0);
      $table->string('image')->nullable();
      $table->string('payment_status')->default('na');
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
    Schema::dropIfExists('orders');
  }
}
