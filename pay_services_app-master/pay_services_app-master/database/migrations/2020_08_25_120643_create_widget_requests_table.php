<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidgetRequestsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('widget_requests', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id');
      $table->bigInteger('widget_id');
      $table->text('comments');
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
    Schema::dropIfExists('widget_requests');
  }
}
