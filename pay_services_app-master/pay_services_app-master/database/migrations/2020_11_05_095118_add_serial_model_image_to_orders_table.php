<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialModelImageToOrdersTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->string('serial_model_image')->nullable()->after('image');
      $table->string('billing_email')->nullable()->after('payment_status');
      $table->string('billing_mobile')->nullable()->after('billing_email');
      $table->string('billing_alt_mobile', 50)->nullable()->after('billing_mobile');
      $table->string('billing_village')->nullable()->after('billing_alt_mobile');
      $table->string('billing_landmark')->nullable()->after('billing_village');
      $table->string('billing_city')->nullable()->after('billing_landmark');
      $table->string('billing_state')->nullable()->after('billing_city');
      $table->string('billing_pincode')->nullable()->after('billing_state');
    });
  }
  
  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropColumn('serial_model_image');
      $table->dropColumn('billing_email');
      $table->dropColumn('billing_mobile');
      $table->dropColumn('billing_alt_mobile');
      $table->dropColumn('billing_village');
      $table->dropColumn('billing_landmark');
      $table->dropColumn('billing_city');
      $table->dropColumn('billing_state');
      $table->dropColumn('billing_pincode');
      
    });
  }
}
