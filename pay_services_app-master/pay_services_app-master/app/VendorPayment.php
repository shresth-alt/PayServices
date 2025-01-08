<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model {
  protected $fillable = array(
    'vendor_id', 'order_id', 'pay_type', 'amount'
  );
}
