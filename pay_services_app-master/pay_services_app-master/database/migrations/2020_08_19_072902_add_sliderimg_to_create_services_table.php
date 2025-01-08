<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSliderimgToCreateServicesTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::table('services', function (Blueprint $table) {
      $table->bigInteger('slider_img')->default(0)->after('icon');
    });
  }
  
  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::table('services', function (Blueprint $table) {
      $table->dropColumn('slider_img');
    });
  }
}
