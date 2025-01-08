<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorDetailsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('vendor_details', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('vendor_id');
      $table->string('father_name')->nullable();
      $table->string('dob', 50)->nullable();
      $table->string('alt_mobile', 50)->nullable();
      $table->text('cur_address')->nullable();
      $table->text('per_address')->nullable();
      $table->string('village', 100)->nullable();
      $table->string('town', 100)->nullable();
      $table->string('city', 100)->nullable();
      $table->string('district', 100)->nullable();
      $table->string('pincode', 100)->nullable();
      $table->string('qualification', 100)->nullable();
      
      $table->string('aadhar_front')->nullable();
      $table->string('aadhar_back')->nullable();
      $table->string('driving_license')->nullable();
      $table->string('pan_card')->nullable();
      $table->string('photo')->nullable();
      $table->string('cheque')->nullable();
      $table->string('signature')->nullable();
      $table->string('insurance')->nullable();

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
    Schema::dropIfExists('vendor_details');
  }
}
