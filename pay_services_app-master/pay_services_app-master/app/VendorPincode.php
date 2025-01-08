<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPincode extends Model {
  protected $fillable = array(
    'vendor_id', 'pincode'
  );
}
