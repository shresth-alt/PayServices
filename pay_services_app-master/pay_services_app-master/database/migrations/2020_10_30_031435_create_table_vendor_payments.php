/**
* @author [author]
* @email [example@mail.com]
* @create date 2020-10-30 08:44:49
* @modify date 2020-10-30 08:44:49
* @desc [description]
*/
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableVendorPayments extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('vendor_payments', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('payment_type'); /** commision, wallet_topup */
      $table->bigInteger('order_id')->default(0);
      $table->float('amount')->default(0);
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
    Schema::dropIfExists('table_vendor_payments');
  }
}
