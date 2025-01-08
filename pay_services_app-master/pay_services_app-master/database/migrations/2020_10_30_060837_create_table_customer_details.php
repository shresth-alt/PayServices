<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomerDetails extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('customer_details', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('customer_id');
      $table->string('alt_mobile', 50)->nullable();
      $table->string('village')->nullable();
      $table->string('landmark')->nullable();
      $table->string('city')->nullable();
      $table->string('pincode')->nullable();
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
    Schema::dropIfExists('table_customer_details');
  }
}
