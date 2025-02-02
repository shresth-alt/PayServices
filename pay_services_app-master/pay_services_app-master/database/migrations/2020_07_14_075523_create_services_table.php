<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('services', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->string('description')->nullable();
      $table->bigInteger('parent_id')->default(0);
      $table->bigInteger('icon')->default(0);
      $table->boolean('is_active')->default(true);
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
    Schema::dropIfExists('services');
  }
}
