<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTPLog extends Model {
  protected $table = "otp_logs";
  protected $fillable = array(
    'mobile'
  );
}
